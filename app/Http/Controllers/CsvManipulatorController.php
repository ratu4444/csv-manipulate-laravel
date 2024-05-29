<?php

namespace App\Http\Controllers;

use App\Services\DearService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CsvManipulatorController extends Controller
{
    // The addEmailColumn() method is designed to enhance a CSV file by adding a new column for email addresses.
    public function addEmailColumn()
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $file_name = 'Customer_Order_By_Products_' . $timestamp . '.csv';
        $file_path = public_path($file_name);
        $new_csv_file = fopen($file_path, 'w');

        try {
            $file = public_path('DearCustomerList(Test).csv');
            $csv_data = [];

            if (($open = fopen($file, "r")) !== false) {
                $index = 0;
                while (($data = fgetcsv($open, 1000, ",")) !== false) {
                    $csv_data[$index] = $data;

                    $invoice_unparsed = explode(", ", $data[2]);
                    $invoice = $invoice_unparsed[0];


                    if ($invoice == 'Document'){
                        $email = 'Email';
                        $csv_data[$index][] = $email;
                        continue;
                    }

                    $sale_list_response = DearService::getSaleList(1, 1, $invoice, null);
                    $sale_id = $sale_list_response['data']['SaleList'][0]['SaleID'] ?? null;


                    if (!$sale_id) {

                        $email = '';
                        continue;
                    }


                    sleep(2);


                    $sale_details_response = DearService::getSaleDetails($sale_id);
                    $email = $sale_details_response['Email'] ?? '';
                    $csv_data[$index][] = $email;


                    fputcsv($new_csv_file, $csv_data[$index]);
                    $index++;
                }
                fclose($open);
            }
        }catch (\Exception $exception){
            return [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
        }

        fclose($new_csv_file);

        return 'CSV file created successfully';
    }
    // The addAnotherColumn() method is designed to enhance a CSV file by adding a new column for any data.
    public function addAnotherColumn()
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $file_name = 'Customer_Order_By_Products_' . $timestamp . '.csv';
        $file_path = public_path($file_name);
        $new_csv_file = fopen($file_path, 'w');

        try {
            $file = public_path('DearCustomerList(Test).csv');
            $csv_data = [];


            if (($open = fopen($file, "r")) !== false) {
                $index = 0;
                while (($data = fgetcsv($open, 1000, ",")) !== false) {
                    $csv_data[$index] = $data;

                    $invoice_unparsed = explode(", ", $data[2]);
                    $invoice = $invoice_unparsed[0];

                    $csv_data[$index][] = $invoice;

                    fputcsv($new_csv_file, $csv_data[$index]);
                    $index++;
                }

                fclose($open);
            }

        }
        catch (\Exception $exception){
            return [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
        }

        fclose($new_csv_file);

        return 'CSV file created successfully';

    }

    // Open and Close CSV file.
    public function fileOpenAndClose()
    {
        $file = public_path('DearCustomerList(Test).csv');

        $open = fopen($file,"r");

        fclose($open);

        return 'CSV file Open and close successfully';

    }

    // Create a CSV file.
    public function create()
    {
        $invoices = [];
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $file_name = 'Customer_Order_By_Products_' . $timestamp . '.csv';
        $file_path = public_path($file_name);
        $new_csv_file = fopen($file_path, 'w');

        foreach ($invoices as $invoice) {
            fputcsv($new_csv_file, $invoice);
        }

        fclose($new_csv_file);
    }

    // Get data from CSV file.
    public function getData()
    {
        $file = public_path('DearCustomerList(Test).csv');

        $csv_data = [];

        if (($open = fopen($file, "r")) !== false) {
            while (($data = fgetcsv($open, 1000, ",")) !== false) {
                $csv_data[] = $data;
            }
        }
        fclose($open);
    }
}
