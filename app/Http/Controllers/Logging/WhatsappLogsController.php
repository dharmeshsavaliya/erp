<?php

namespace App\Http\Controllers\Logging;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;

class WhatsappLogsController extends Controller
{
  
  public function getWhatsappLog()
  {

    $path =  base_path() . '/';

    $escaped = str_replace('/', '\/', $path);

    $errorData = array();

    $files =    Storage::disk('logs')->files('whatsapp');
    // dd(storage_path('logs/whatsapp/'));
    // $files = File::allfiles(storage_path('logs/whatsapp/'));
    // dd($files);
    $array = [];

    foreach ($files as $file) {
        $total_log = 0;
        $yesterday = strtotime('yesterday');
        $today = strtotime('today');
        $path =  base_path() . '/';
        $content = Storage::disk('logs')->get($file);
        $escaped = str_replace('/', '\/', $path);
        $matches = [];
        $rows = preg_split('/\r\n|\r|\n/', $content);
        // dump(count($rows));
        foreach($rows as $key => $row){
          $total_log++ ;
          $origin = $row;
          $date = substr($row, 1, 19);
          $row = str_replace($date, '', $row);
          $row = str_replace(substr($row, 1, 103), '', $row);
          // dump([$date, $row]);
          $new_row = explode(':', $row);
          $c = count($new_row);
          // dump($c);
          if($c == 6){
            $arr1['date'] = $date;
            $arr1['number'] = explode(' ', $new_row[0])[5];
            $ind_rows = explode(',', explode('{', $row)[1]);
              foreach($ind_rows as $r){
                $end = explode(':', $r);
                if($end[0] == '"sent"'){
                  $arr1['sent'] = $end[1];
                }else if($end[0] == '"message"'){
                  $arr1['message'] = $end[1];
                }elseif($end[0] == '"id"'){
                  $arr1['id'] = $end[1];
                }elseif($end[0] == '"queueNumber"'){
                $end[1] = str_replace('}  ', '', $end[1]);
                $arr1['queueNumber'] = $end[1];
                }
              }
              $arr1['type'] = 1;
              $array[] = $arr1;
            // dump([$origin, $array]);
          }else if($c == 4){
            $arr2['date'] = $date;
            $arr2['error_message1'] = substr($row, 1, 47);
            $last_row = str_replace(substr($row, 1, 51), '', $row);
            $last_row = str_replace('[', '', $last_row);
            $last_row = explode(',', $last_row);
            // dd($last_row);
            $arr2['sent'] = explode(':', $last_row[0])[1];
            $arr2['error_message2'] = explode(':', $last_row[1])[1] . ', ' . explode(':', $last_row[2])[0]; 
            $arr2['type'] = 2;
            $array[] = $arr2;
            // dump([$origin, $array]);
          }else if($c == 3){
            $arr3['date'] = $date;
            $arr3['error_message1'] = substr($row, 1, 26);
            $last_row = str_replace(substr($row, 1, 29), '', $row);
            $last_row = str_replace('[', '', $last_row);
            $arr3['instance'] = substr($last_row, 20, 6);
            $arr3['error_message2'] = str_replace('"}  ', '', explode(':', $last_row)[1]);
            $arr3['error_message2'] = str_replace('\\', '', $arr3['error_message2']);
            $arr3['error_message2'] = str_replace('"', '', $arr3['error_message2']);
            $arr3['type'] = 3;
            $array[] = $arr3;
            // dump([$origin, $arr3]);
          }else if($c == 1){
            $arr4['date'] = $date;
            $arr4['error_message1'] = substr($row, 1, 61);
            $arr4['type'] = 4;
            $array[] = $arr4;
            // dump($origin, $arr4);
          }




        }
        // dump('-----');
        // dump($array);
        // dump($total_log);

 
  }
        // dd($array);
        return view('logging.whatsapp-logs', compact('array'));
  
}
    public function resendWhatsapp(Request $request){

//        $data = json_decode($request->data);



        return response()->json(json_encode([
            'number' => '$number',
            'whatsapp_number' => '$sendNumber',
            'message' => '$text',
            'validation' => '$validation',
            'chat_message_id' => '$chat_message_id',
        ]));


    }
}
