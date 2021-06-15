<?php

namespace App\Http\Controllers\Logging;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;

class WhatsappLogsController extends Controller
{

    public function getWhatsappLog(Request $request)
    {
        // app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi(971502609192, 7487848215, 'test data');
        // dd('end');

        $path = base_path() . '/';

        $escaped = str_replace('/', '\/', $path);

        $errorData = array();

        $files = Storage::disk('logs')->files('whatsapp');
        // dd(storage_path('logs/whatsapp/'));
        // $files = File::allfiles(storage_path('logs/whatsapp/'));
        $array = [];
        $files = array_reverse($files);
        array_pop($files);
        array_pop($files);
        foreach ($files as $file) {

            $total_log = 0;
            $yesterday = strtotime('yesterday');
            $today = strtotime('today');
            $path = base_path() . '/';
            $content = Storage::disk('logs')->get($file);
            $escaped = str_replace('/', '\/', $path);
            $matches = [];
            $rows = preg_split('/\r\n|\r|\n/', $content);
            $rows = array_reverse($rows);
            // dump(count($rows));
//            foreach ($rows as $key => $row) {
//                dump($row);
//                $resend_details = explode('|', $row)[1] ?? '';
//                $row = explode('|', $row)[0];
//
//                $total_log++;
//                $origin = $row;
//                $date = substr($row, 1, 19);
//                dump($date);
//                $row = str_replace($date, '', $row);
//                $row = str_replace(substr($row, 1, 103), '', $row);
//                // dump([$date, $row]);
//                $new_row = explode(':', $row);
//                $c = count($new_row);
//                // dump($c);
//                if ($c == 6) {
//                    $arr1['date'] = $date;
//                    $arr1['number'] = explode(' ', $new_row[0])[5];
//                    $ind_rows = explode(',', explode('{', $row)[1]);
//                    foreach ($ind_rows as $r) {
//                        $end = explode(':', $r);
//                        if ($end[0] == '"sent"') {
//                            $arr1['sent'] = $end[1];
//                        } else if ($end[0] == '"message"') {
//                            $arr1['message'] = $end[1];
//                        } elseif ($end[0] == '"id"') {
//                            $arr1['id'] = $end[1];
//                        } elseif ($end[0] == '"queueNumber"') {
//                            $end[1] = str_replace('}  ', '', $end[1]);
//                            $arr1['queueNumber'] = $end[1];
//                        }
//                    }
//                    $arr1['type'] = 1;
//                    $arr1['resend_details'] = '';
//                    $array[] = $arr1;
//                    // dump([$origin, $array]);
//                } else if ($c == 4) {
//                    $arr2['date'] = $date;
//                    $arr2['error_message1'] = substr($row, 1, 47);
//                    $last_row = str_replace(substr($row, 1, 51), '', $row);
//                    $last_row = str_replace('[', '', $last_row);
//                    $last_row = explode(',', $last_row);
//                    // dd($last_row);
//                    $arr2['sent'] = explode(':', $last_row[0])[1];
//                    $arr2['error_message2'] = explode(':', $last_row[1])[1] . ', ' . explode(':', $last_row[2])[0];
//                    $arr2['type'] = 2;
//                    $arr2['resend_details'] = $resend_details ? $resend_details : '';
//                    $array[] = $arr2;
//                    // dump([$origin, $array]);
//                } else if ($c == 3) {
//                    $arr3['date'] = $date;
//                    $arr3['error_message1'] = substr($row, 1, 26);
//                    $last_row = str_replace(substr($row, 1, 29), '', $row);
//                    $last_row = str_replace('[', '', $last_row);
//                    $arr3['instance'] = substr($last_row, 20, 6);
//                    $arr3['error_message2'] = str_replace('"}  ', '', explode(':', $last_row)[1]);
//                    $arr3['error_message2'] = str_replace('\\', '', $arr3['error_message2']);
//                    $arr3['error_message2'] = str_replace('"', '', $arr3['error_message2']);
//                    $arr3['type'] = 3;
//                    $arr3['resend_details'] = $resend_details ? $resend_details : '';
//                    $array[] = $arr3;
//                    // dump([$origin, $arr3]);
//                } else if ($c == 1) {
//                    // dump($origin, $row);
//                    $arr4['date'] = $date;
//                    $arr4['error_message1'] = trim(substr($row, 1, 26));
//                    $arr4['type'] = 4;
//                    $resend_details = json_decode(str_replace('resend_details:', '', $resend_details));
//                    for ($i = 0; $i < count((array)$resend_details); $i++) {
//                        // $resend_details[$i] == null ? $resend_details[$i] = '' : '';
//                    }
//                    if ($resend_details) {
//                        // dd($resend_details);
//                    }
//                    $arr4['resend_details'] = $resend_details ? json_encode($resend_details) : '';
//                    $array[] = $arr4;
//                    // dump($origin, $arr4, strlen($row));
//                }
//
//
//            }
            foreach ($rows as $key => $row) {
                if($row && $row !== ''){

                    $data = [];
                    $date = substr($row, 1, 19);
                    $data['date'] = $date;
                    $message = substr($row, 155, strlen($row));
                    $data['error_message1'] = $message;
                    $data['error_message2'] = '';
                    array_push($array, $data);
//                    dd($data);
                }
            }

        }

        /* chat api*/
//        dd($array);

        $files = Storage::disk('logs')->files('chatapi');
        // dd(storage_path('logs/whatsapp/'));
        // $files = File::allfiles(storage_path('logs/whatsapp/'));
        // dd($files);
        $chatapiarray = [];
        $files = array_reverse($files);
        foreach ($files as $file) {
            $total_log = 0;
            $yesterday = strtotime('yesterday');
            $today = strtotime('today');
            $path = base_path() . '/';
            $content = Storage::disk('logs')->get($file);
//          dd($content);
            $escaped = str_replace('/', '\/', $path);
            $matches = [];
            $rows = preg_split('/\n+/', $content);
//          $rows = explode('/\n', $content);
//          $rows = array_reverse($rows);
//            dump($rows);
            // dump(count($rows));

            $finaldata = [];
            foreach ($rows as $key => $row) {

                if (substr($row, 0, 1) === '[') {

                    $date = preg_match('#\[(.*?)\]#', $row, $match);
//                  dd($match[1], $row);
                    $finaldata['date'] = isset($match[1]) ? $match[1] : '';;
                    $message = preg_match('/{(.*?)}/', $row, $match);
                    $finaldata['error_message1'] = isset($match[1]) ? $match[1] : '';
                }

                if (substr($row, 0, 7) === 'Message') {

                    $message = substr($row, 8, strlen($row));
                    $finaldata['error_message2'] = $message;
                    $finaldata['resend_details'] = '';
                    $finaldata['type'] = 2;

                    array_push($chatapiarray, $finaldata);
                    $finaldata = [];
                }

            }


        }
        $chatapiarray = array_reverse($chatapiarray);

        $farray = array_merge($chatapiarray, $array);
        usort($farray, function ($element1, $element2) {
            $datetime1 = strtotime($element1['date']);
            $datetime2 = strtotime($element2['date']);
            return $datetime2 - $datetime1;
        });
//        dd($farray);
        /* end chat api */

        $page = $request->page;
        if ($page == null) {
            $page = 1;
        }

        $array = array_slice($farray, ($page * 10 - 10), 10);

        if ($request->ajax()) {

            return view('logging.whatsapp-grid', compact('array'));
        }

        return view('logging.whatsapp-logs', compact('array'));

    }
}
