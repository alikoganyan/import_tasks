<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\ContactRequest;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ContactsController extends Controller
{
    /**
     * @param ContactRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ContactRequest $request)
    {
        $fild = [
            'team_id' => $request->team_id,
            'unsubscribed_status' => $request->unsubscribed_status,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'sticky_phone_number_id' => $request->sticky_phone_number_id,
            'twitter_id' => $request->twitter_id,
            'fb_messenger_id' => $request->fb_messenger_id,
            'time_zone' => $request->time_zone,
        ];

        $file = $request->file('file');
        $customRows = [];

        // File Details
        $extension = $file->getClientOriginalExtension();
        $fileSize = $file->getSize();

        // Valid File Extensions
        $valid_extension = array("csv");

        // 2MB in Bytes
        $maxFileSize = 2097152;

        // Check file extension
        if (in_array(strtolower($extension), $valid_extension)) {

            // Check file size
            if ($fileSize <= $maxFileSize) {
                $all_rows = $this->add_valid_rows_to_array($this->csv_to_array($file), $fild);

                ini_set('max_execution_time', 400);
                // Insert to MySQL database
                DB::beginTransaction();
                try {
                    foreach ($all_rows as $key => $importData) {

                        $customRows[$key] = $importData['custom_rows'];

                        unset($importData['custom_rows']);

                        $contactId = DB::table('contacts')->insertGetId($importData);
                        $customRows[$key]['contact_id'] = $contactId;
                    }
                    DB::commit();
                    $this->insert_records_into_custom_attributes_tables($customRows);
                    return response()->json(['success' => 'File is already uploaded!'], 201);

                } catch (QueryException $e) {
                    DB::rollback();

                    return response()->json(['error' => $e->getPrevious()->errorInfo[2]], 500);
                }
            }
            return response()->json(['errorInfo' => 'The file extension must be .csv'], 400);
        }
        return response()->json(['errorInfo' => 'The file size must be 2MB'], 400);
    }

    /**
     * @param $reader
     * @param $headings
     * @return array
     */
    public function add_valid_rows_to_array($reader, $headings): array
    {
        $allContactRows = [];

        foreach ($reader as $index => $row) {
            $values = [];
            foreach ($headings as $key => $position) {
                if (!isset($row[$position]) || is_null($row[$position])) {
                    $row[$position] = null;
                }
                $values[0][$key] = $row[$position];
            }
            $allContactRows[] = $values[0];
            $allContactRows[$index]['custom_rows'] = array_diff_key($row, $values[0]);
        }

        return $allContactRows;

    }

    /**
     * @param string $filename
     * @param string $delimiter
     * @return array|bool
     */
    private function csv_to_array($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    /**
     * @param array $allCustomAttributes
     * @return bool|\Illuminate\Http\JsonResponse
     */
    private function insert_records_into_custom_attributes_tables(array $allCustomAttributes)
    {

        if (empty($allCustomAttributes) && count($allCustomAttributes) === 0)
            return false;
        try {
            foreach ($allCustomAttributes as $item) {

                foreach ($item as $key => $set) {

                    if ($key === 'contact_id') {
                        break;
                    }
                    // Insert to MySQL database
                    DB::table('custom_attributes')->insert([
                        'contact_id' => $item['contact_id'],
                        'key' => $key,
                        'value' => $set
                    ]);
                }
            }
            DB::commit();

            return response()->json(['success' => 'File is already uploaded!'], 200);

        } catch (QueryException $e) {
            DB::rollback();
            return response()->json(['error' => $e->getPrevious()->errorInfo[2]], 500);
        }
    }
}
