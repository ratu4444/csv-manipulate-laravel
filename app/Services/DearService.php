<?php
namespace App\Services;

use App\Models\Category;
use App\Models\CustomerReport;
use App\Models\CustomerReportSync;
use App\Models\Setting;
use App\Models\Workflow;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DearService
{
    public static function getBaseUrl(): string
    {
        return 'https://inventory.dearsystems.com/ExternalApi/v2';
    }

    public static function getHeaders(): array
    {
        $dear_credentials = formatCredentials(Setting::value('dear_credentials'), ['id', 'secret']);

        return [
            'Content-Type'              => 'application/json',
            'Accept'                    => 'application/json',
            'api-auth-accountid'        => $dear_credentials['id'],
            'api-auth-applicationkey'   => $dear_credentials['secret']
        ];
    }

    public static function getSaleDetails($sale_id)
    {
        $api = self::getBaseUrl().'/sale';
        $headers = self::getHeaders();
        $params = [
            'ID' => $sale_id
        ];

        $response = callExternalGetApi($api, $headers, $params);

        $data = null;
        if ($response['success']) {
            $data = $response['data'];
        }

        return $data;
    }

    public static function getSaleList($limit, $page, $search, $created_since): array
    {
        $api = self::getBaseUrl().'/saleList';
        $headers = self::getHeaders();

        $params = [
            'Limit'     => $limit,
            'Page'      => $page,
        ];

        if ($search) $params['Search'] = $search;
        if ($created_since) $params['CreatedSince'] = $created_since;

        return callExternalGetApi($api, $headers, $params);
    }
}
