<?php

namespace App\Http\Controllers;

use App\CsvTranslator;
use App\CsvTranslatorHistory;
use App\Exports\CsvTranslatorExport;
use App\Imports\CsvTranslatorImport;
use App\Models\CsvPermissions;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Http\Request;

class CsvTranslatorController extends Controller
{
    public function index(Request $request)
    {
        $cols = array();
        $allCsvPermission = CsvPermissions::where('user_id',\Auth::user()->id)->get();
        $lang = array();

        array_push($lang,array('data'=>'id'));
        array_push($lang,array('data'=>'key'));
        $permissions = array();
        
        foreach($allCsvPermission as $permission){
            $cols[] = $permission['lang_id'];
            $lang[] = array('data'=>$permission['lang_id']);
            $permissions[$permission['lang_id']][] = $permission['action'];
        }
        
        $lang =  json_encode($lang);
        $colums = implode(",",$cols);
        $colums = str_replace(",","','",$colums);
  
        $res = explode(",",$colums);
        if ($request->ajax()) {
            $data = Csvtranslator::all();
            if(auth()->user()->isAdmin()){
                $res = datatables()->of($data)->addIndexColumn();
                    $res->editColumn('en', function ($data) {
                        return $this->commanRadioLoad("en",$data->id,$data->toArray());
                    });
                    $res->editColumn('es', function($data) {
                        return $this->commanRadioLoad("es",$data->id,$data->toArray());    
                    });
                    $res->editColumn('ru', function($data){
                        return $this->commanRadioLoad("ru",$data->id,$data->toArray());
                    });
                    $res->editColumn('ko', function($data){
                        return $this->commanRadioLoad("ko",$data->id,$data->toArray());
                    });
                    $res->editColumn('ja', function($data){
                        return $this->commanRadioLoad("ja",$data->id,$data->toArray());
                    });
                    $res->editColumn('de', function($data){
                        return $this->commanRadioLoad("de",$data->id,$data->toArray());
                    });
                    $res->editColumn('it', function($data){
                        return $this->commanRadioLoad("it",$data->id,$data->toArray());
                    });
                    $res->editColumn('fr', function($data){
                        return $this->commanRadioLoad("fr",$data->id,$data->toArray());
                    });
                    $res->editColumn('nl', function($data){
                        return $this->commanRadioLoad("nl",$data->id,$data->toArray());
                    });
                    $res->editColumn('zh', function($data){
                        return $this->commanRadioLoad("zh",$data->id,$data->toArray());
                    });
                    $res->editColumn('ar', function($data){
                        return $this->commanRadioLoad("ar",$data->id,$data->toArray());
                    });
                    $res->editColumn('ur', function($data){
                        return $this->commanRadioLoad("ur",$data->id,$data->toArray());
                    });
                    return $res->escapeColumns([])->make(true);
            }else{
                if($colums){
                    $data = CsvTranslator::select("*")->get();
                    if($data){
                        $res =  datatables()->of($data)->addIndexColumn();
                        if(isset($permissions['en'])){
                            $res->editColumn('en', function ($data) use ($permissions)  {
                                return $this->commonServiceCheck($permissions,$data,"en");
                            });
                         }
                        if(isset($permissions['es'])){
                            $res->editColumn('es', function($data) use ($permissions){
                                return  $this->commonServiceCheck($permissions,$data,"es");
                            });
                        }
                        if(isset($permissions['ru'])){
                            $res->editColumn('ru', function($data) use ($permissions) {
                                return  $this->commonServiceCheck($permissions,$data,"ru");
                            });
                        }
                        if(isset($permissions['ko'])){
                            $res->editColumn('ko', function($data) use ($permissions) {
                                return  $this->commonServiceCheck($permissions,$data,"ko");  
                            });
                        }
                        if(isset($permissions['ja'])){
                            $res->editColumn('ja', function($data) use ($permissions) {
                                return  $this->commonServiceCheck($permissions,$data,"ja");   
                            });
                        }
                        if(isset($permissions['de'])){
                            $res->editColumn('de', function($data) use ($permissions) {
                                return  $this->commonServiceCheck($permissions,$data,"de");   
                            });
                        }
                        if(isset($permissions['it'])){
                            $res->editColumn('it', function($data) use ($permissions) {
                                return  $this->commonServiceCheck($permissions,$data,"it");   
                            });
                        }
                        if(isset($permissions['fr'])){
                            $res->editColumn('fr', function($data) use ($permissions) {
                                return  $this->commonServiceCheck($permissions,$data,"fr");   
                            });
                        }
                        if(isset($permissions['nl'])){
                            $res->editColumn('nl', function($data) use ($permissions) {
                                return  $this->commonServiceCheck($permissions,$data,"nl");   
                            });
                        }
                        if(isset($permissions['zh'])){
                            $res->editColumn('zh', function($data) use ($permissions) {
                                return  $this->commonServiceCheck($permissions,$data,"zh");   
                            });
                        }
                        if(isset($permissions['ar'])){
                            $res->editColumn('ar', function($data) use ($permissions) {
                                return  $this->commonServiceCheck($permissions,$data,"ar");   
                            });
                        }
                        if(isset($permissions['ur'])){
                            $res->editColumn('ur', function($data) use ($permissions) {
                                return  $this->commonServiceCheck($permissions,$data,"ur");   
                            });
                        }

                        return $res->escapeColumns([])->make(true);
                    
                    }else{
                            return datatables()->of($data)->make(true);
                        }
                }else{
                    return datatables()->of($data)->make(true);
                }
             }
        }
        return view('csv-translator.index',compact('lang'));
    }

    public function commanRadioLoad($lang,$id,$data){
        if($data['status_'.$lang] == 'new'){
            return $data[$lang].'<div class="d-flex"><input type="radio" data-id='.$id.' id="radio1" data-lang='.$lang.' name="radio1" value="checked"><label class="p-3">Accept</label>
            <input type="radio" data-id='.$id.' data-lang='.$lang.' id="radio2" name="radio1" value="unchecked"><label class="p-3">Reject</label><div>';
        }else{
            return $data[$lang];
        }     
    }

    public function upload(Request $request)
    {
        if($request->file->getClientOriginalExtension() === 'csv' || $request->file->getClientOriginalExtension() === 'xlsx'){
            \Excel::import(new CsvTranslatorImport(), $request->file);
            \Session::flash('message', 'Successfully imported');
        }else{
            \Session::flash('error', 'Upload only Csv or Xlsx');
        }
    }

    public function commonServiceCheck($permissions,$data,$lang,$status=""){
        $data =  $data->toArray();
        $key = $data['key'];
        $id = $data['id'];
        $language = $data[$lang]; 
        if(count($permissions[$lang]) == 1){
            if(isset($permissions[$lang]) && isset($permissions[$lang][0]) && $permissions[$lang][0] == 'view'){
                if($data["status_".$lang] === 'checked'){
                    return '<div class="bg-success p-4 text-white">'.$language."<a href='#' class='history_model' data-key='$key' data-lang='$lang' data-id='$id' data-toggle='modal'  data-target='#history'> <i class='fa fa-eye'></i> </a></div>";
                }else{
                    return '<div class="bg-custom-grey p-4">'.$language ."<a href='#' class='history_model' data-key='$key' data-lang='$lang' data-id='$id' data-toggle='modal'  data-target='#history'> <i class='fa fa-eye'></i> </a></div>";
                }
            }
            
            if(isset($permissions[$lang]) && isset($permissions[$lang][0]) && $permissions[$lang][0] == 'edit'){
                if($data["status_".$lang] == 'checked'){
                    return  '<div class="bg-success p-4 text-white">'.$language ."<a href='#' class='editbtn_model' data-value='$language' data-lang='$lang' data-user='".auth()->user()->id."' data-id='$id' data-toggle='modal' data-target='#edit_model'> <i class='fa fa-pencil'></i> </a></div>";
                }else{
                    return  '<div class="bg-custom-grey p-3">'.$language ."<a href='#' class='editbtn_model' data-value='$language' data-lang='$lang' data-user='".auth()->user()->id."' data-id='$id' data-toggle='modal' data-target='#edit_model'> <i class='fa fa-pencil'></i> </a></div>";
                }     
            }    
        }

        if(count($permission[$lang]) == 2){
            if(isset($permissions[$lang]) && isset($permissions[$lang][0]) && $permissions[$lang][0] == 'view'){
                if($data["status_".$lang] == 'checked'){
                    return  '<div class="bg-success p-4 text-white">'.$language ."<a href='#' class='history_model' data-key='$key' data-lang='$lang' data-id='$id' data-toggle='modal'  data-target='#history'> <i class='fa fa-eye'></i> </a></div>";
                }else{
                    return   '<div class="bg-custom-grey p-4">'.$language ."<a href='#' class='history_model' data-key='$key' data-lang='$lang' data-id='$id' data-toggle='modal'  data-target='#history'> <i class='fa fa-eye'></i> </a></div>";
                }
            }
            
            if(isset($permissions[$lang]) && isset($permissions[$lang][0]) && $permissions[$lang][0] == 'edit'){
                if($data["status_".$lang] == 'checked'){
                    return  '<div class="bg-success p-4 text-white">'.$language ."<a href='#' class='editbtn_model' data-value='.$language.' data-lang='$lang' data-user='".auth()->user()->id."' data-id='$id' data-toggle='modal' data-target='#edit_model'> <i class='fa fa-pencil'></i> </a></div>";
                }else{
                    return  '<div class="bg-custom-grey p-3">'.$language ."<a href='#' class='editbtn_model' data-value='.$language.' data-lang='$lang' data-user='".auth()->user()->id."' data-id='$id' data-toggle='modal' data-target='#edit_model'> <i class='fa fa-pencil'></i> </a></div>";
                }     
            }
            
            if(isset($permissions[$lang]) && isset($permissions[$lang][1]) && $permissions[$lang][1] == 'view'){
                if($data["status_".$lang] == 'checked'){
                    return  '<div class="bg-success p-4 text-white">'.$language ."<a href='#' class='history_model' data-key='$key' data-lang='$lang' data-id='$id' data-toggle='modal'  data-target='#history'> <i class='fa fa-eye'></i> </a></div>";
                }else{
                    return   '<div class="bg-custom-grey p-4">'.$language ."<a href='#' class='history_model' data-key='$key' data-lang='$lang' data-id='$id' data-toggle='modal'  data-target='#history'> <i class='fa fa-eye'></i> </a></div>";
                }
            }
            
            if(isset($permissions[$lang]) && isset($permissions[$lang][1]) && $permissions[$lang][1] == 'edit'){
                if($data["status_".$lang] == 'checked'){
                    return  '<div class="bg-success p-4 text-white">'.$language ."<a href='#' class='editbtn_model' data-value='$language' data-lang='$lang' data-user='".auth()->user()->id."' data-id='$id' data-toggle='modal' data-target='#edit_model'> <i class='fa fa-pencil'></i> </a></div>";
                }else{
                    return  '<div class="bg-custom-grey p-3">'.$language ."<a href='#' class='editbtn_model' data-value='$language' data-lang='$lang' data-user='".auth()->user()->id."' data-id='$id' data-toggle='modal' data-target='#edit_model'> <i class='fa fa-pencil'></i> </a></div>";
                }     
            }
        }
    
    }

     public function exportData(Request $request)
     {
         \Excel::import(new CsvTranslatorImport(), $request->file);
         \Session::flash('message', 'Successfully imported');
     }

    public function update(Request $request)
    {
        $record = CsvTranslator::find($request->record_id);
        $oldRecord = $record->{$request->lang_id};
        $oldStatus = $record->status;
        $key = $record->key;
        $record->updated_by_user_id = $request->update_by_user_id;
     
        if($request->lang_id == 'en'){
            $record->en = $request->update_record;
            $record->status_en = "new";
        }
        if ($request->lang_id == 'es') {
            $record->es = $request->update_record;
            $record->status_es = "new";
        }
        if ($request->lang_id == 'ru') {
            $record->ru = $request->update_record;
            $record->status_ru = "new";
        }
        if ($request->lang_id == 'ko') {
            $record->ko = $request->update_record;
            $record->status_ko = "new";
        }
        if ($request->lang_ja == 'ja') {
            $record->en = $request->update_record;
            $record->status_ja = "new";
        }
        if ($request->lang_id == 'it') {
            $record->it = $request->update_record;
            $record->status_it = "new";
        }
        if ($request->lang_id == 'de') {
            $record->de = $request->update_record;
            $record->status_de = "new";
        }
        if ($request->lang_id == 'fr') {
            $record->fr = $request->update_record;
            $record->status_fr = "new";
        }
        if ($request->lang_id == 'nl') {
            $record->nl = $request->update_record;
            $record->status_nl = "new";
        }
        if ($request->lang_id == 'zh') {
            $record->zh = $request->update_record;
            $record->status_zh = "new";
        }
        if ($request->lang_id == 'ar') {
            $record->ar = $request->update_record;
            $record->status_ar = "new";
        }
        if ($request->lang_id == 'ur') {
            $record->ur = $request->update_record;
            $record->status_ur = "new";
        }
        $record->update();
    
        $historyData = array();
        $historyData['csv_translator_id'] = $record->id;
        $historyData['updated_by_user_id'] = $request->update_by_user_id;
        $historyData['key'] = $key;
        $historyData["status_".$request->lang_id]  = "new";
        $historyData[$request->lang_id] = $oldRecord;
        $historyData['created_at'] = \Carbon\Carbon::now();
        CsvTranslatorHistory::insert($historyData);
        
        return redirect()->route('csvTranslator.list')->with(['success' => 'Successfully Updated']);
    }

    public function approvedByAdmin(Request $request){
        $record = CsvTranslator::where("id",$request->id)->first();
        $record['status_'.$request->lang] = $request->status;
        $record['approved_by_user_id'] = \Auth::user()->id;
        $record->update();

        $record_history = CsvTranslatorHistory::where('csv_translator_id',$request->id)->where($request->lang,"!=","")->orderBy('id','desc')->first();
        $record_history['status_'.$request->lang] = $request->status;
        $record_history['approved_by_user_id'] = \Auth::user()->id;
        $record_history->update();
        return response()->json(['status' => 200]);
    }

    public function history(Request $request)
    {
        $key = $request->key;
        $language = $request->language;
        $history = CsvTranslatorHistory::where([
            'csv_translator_id' => $request->id,
            'key' => $request->key,
        ])->whereRaw('status_'.$request->language.' is not null')->get();
        if(count($history) > 0){
            foreach($history as $key=> $historyData){
                $history[$key]['updater'] = User::where('id',$historyData['updated_by_user_id'])->pluck('name')->first();
                $history[$key]['approver'] = User::where('id',$historyData['approved_by_user_id'])->pluck('name')->first();
            }
        }

        return response()->json(['status' => 200, 'data' => $history]);
    }

    public function filterCsvTranslator(Request $request)
    {
        if ($request->ajax()) {
            $userId = $request->user;
            $language = $request->lang;
            $status = $request->status;
            $lang = array();
            $query = CsvTranslator::select('*');
            
            if(isset($userId)){
                $query->where('updated_by_user_id',$userId);
                // $query->orwhere('approved_by_user_id',$userId);
            }
            if(isset($language)){
                $query->whereNotNull($language);
            }
            if(isset($status) && isset($language)){
                $query->where('status_'.$language,$status);
            }

            $data = $query->get();

            $cols = array();
            $allCsvPermission = CsvPermissions::where('user_id',\Auth::user()->id)->get();
          
    
            array_push($lang,array('data'=>'id'));
            array_push($lang,array('data'=>'key'));
            $permissions = array();
            
            foreach($allCsvPermission as $permission){
                $cols[] = $permission['lang_id'];
                $lang[] = array('data'=>$permission['lang_id']);
                $permissions[$permission['lang_id']][] = $permission['action'];
            }
        
            $dataTable =  datatables()->of($data);
            if(isset($language)){
                if(isset($permissions[$language])){
                    $dataTable->editColumn($language, function ($data) use ($permissions,$language)  {
                        return $this->commonServiceCheck($permissions,$data,$language);
                    });
                 }
            }
            return $dataTable->escapeColumns([])->make(true);
        }
    }

    public function userPermissions(Request $request){
        if ($request->ajax()) {
            $data = $request->only('user_id','lang_id','action');
            $checkExists = CsvPermissions::where('user_id',$data['user_id'])->where('lang_id',$data['lang_id'])->where('action',$data['action'])->first();
            
            if($checkExists){
                return response()->json(['status' => 412]);
            }

            CsvPermissions::insert($data);
            $data =  CsvPermissions::where('user_id',\Auth::user()->id)->get();
            return response()->json(['status' => 200]);
        }
    }
}
