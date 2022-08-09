<?php

namespace App\Http\Controllers;

use App\Models\UicheckHistory;
use App\Uicheck;
use App\UicheckType;



/*use Illuminate\Http\Request;
use App\SiteDevelopment;
use App\SiteDevelopmentArtowrkHistory;
use App\SiteDevelopmentCategory;
use App\SiteDevelopmentMasterCategory;
use App\StoreWebsite;
use DB;
*/
use Auth;
use DB;
use App\User;
use App\UicheckUserAccess;
use App\UicheckAttachement;
use App\StoreWebsite;
use App\UiAdminStatusHistoryLog;
use App\UiDeveloperStatusHistoryLog;
use App\SiteDevelopmentStatus;
use App\SiteDevelopmentCategory;
use App\UiCheckIssueHistoryLog;
use App\UiCheckCommunication;
use App\UiCheckAssignToHistory;
use App\Language;
use App\UiLanguage;
use App\UiDevice;
use App\UicheckLanguageMessageHistory;
use App\UicheckLangAttchment;
use App\UiDeviceHistory;
use App\UicheckDeviceAttachment;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use Storage;
use PDF;
use User as GlobalUser;

class UicheckController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->ajax() || request('draw')) {

            if (Auth::user()->hasRole('Admin')) {
                $q = SiteDevelopmentCategory::query()
                    ->join('site_developments', 'site_development_categories.id', '=', 'site_developments.site_development_category_id')
                    ->leftjoin('uichecks', 'uichecks.site_development_category_id', '=', 'site_development_categories.id')
                    ->leftjoin('uicheck_user_accesses as uua', 'uua.uicheck_id', '=', 'uichecks.id')
                    ->where('site_developments.is_ui', 1)
                    ->where('uichecks.id', '>', 0)
                    ->select(
                        'site_development_categories.*',
                        'site_developments.id AS site_id',
                        'site_developments.website_id',
                        "uichecks.id AS uicheck_id",
                        "uichecks.issue",
                        "uichecks.website_id AS websiteid",
                        "uichecks.uicheck_type_id",
                        "uichecks.dev_status_id",
                        "uichecks.admin_status_id",
                        "uichecks.lock_developer",
                        "uichecks.lock_admin",
                        "uua.user_id as accessuser"
                    );
                if ($s = request('srch_lock_type')) {
                    if ($s == 1) {
                        $q->where('uichecks.lock_developer', 0);
                        $q->where('uichecks.lock_admin', 0);
                    } else if ($s == 2) {
                        $q->where('uichecks.lock_developer', 1);
                        $q->where('uichecks.lock_admin', 1);
                    } else if ($s == 3) {
                        $q->where('uichecks.lock_developer', 0);
                        $q->where('uichecks.lock_admin', 1);
                    } else if ($s == 4) {
                        $q->where('uichecks.lock_developer', 1);
                        $q->where('uichecks.lock_admin', 0);
                    }
                }
            } else {
                $q = SiteDevelopmentCategory::query()
                    ->join('site_developments', 'site_development_categories.id', '=', 'site_developments.site_development_category_id')
                    ->join('uichecks', 'uichecks.site_development_category_id', '=', 'site_development_categories.id')
                    ->leftjoin('uicheck_user_accesses as uua', 'uua.uicheck_id', '=', 'uichecks.id')
                    ->where('uua.user_id', "=", \Auth::user()->id)
                    ->where('site_developments.is_ui', 1)
                    ->where('uichecks.id', '>', 0)
                    ->where('uichecks.lock_developer', '=', 0)
                    ->select(
                        'site_development_categories.*',
                        'site_developments.id AS site_id',
                        'site_developments.website_id',
                        "uichecks.id AS uicheck_id",
                        "uichecks.issue",
                        "uichecks.website_id AS websiteid",
                        "uichecks.uicheck_type_id",
                        "uichecks.dev_status_id",
                        "uichecks.admin_status_id",
                        "uichecks.lock_developer",
                        "uichecks.lock_admin",
                        "uua.user_id as accessuser"
                    );
            }


            //->where('site_development_categories.id','site_developments.site_development_category_id');
            if ($s = request('category_name')) {
                $q = $q->where('uichecks.website_id', $s);
            }
            if ($s = request('sub_category_name')) {
                $q = $q->where('site_development_categories.id', $s);
            }
            if ($s = request('dev_status')) {
                $q = $q->where('uichecks.dev_status_id', $s);
            }
            if ($s = request('admin_status')) {
                $q = $q->where('uichecks.admin_status_id', $s);
            }
            if ($s = request('assign_to')) {
                $q = $q->where('uua.user_id', $s);
            }
            $q->groupBy('uichecks.id');

            if ($s = request('order_by')) {
                //$q->orderBy('uichecks.'.request('order_by'), "desc");
                //$q->orderBy('uichecks.updated_at', "desc");
                $q->orderByRaw("uichecks." . request('order_by') . " DESC, uichecks.updated_at DESC");
            } else {
                $q->orderBy('uichecks.updated_at', "desc");
            }
            $counter = $q->get();
            //dd(count($q->get()));
            //dd($q->count());

            // select 
            //     `site_development_categories`.*, 
            //     `site_developments`.`id` as `site_id`, 
            //     `site_developments`.`website_id`, 
            //     `uichecks`.`id` as `uicheck_id`, 
            //     `uichecks`.`issue`, 
            //     `uichecks`.`website_id` as `websiteid`, 
            //     `uichecks`.`uicheck_type_id`, 
            //     `uua`.`user_id` as `accessuser`, 
            //     `uichecks`.`dev_status_id`, 
            //     `uichecks`.`admin_status_id` 
            // from `site_development_categories` 
            // inner join `site_developments` on `site_development_categories`.`id` = `site_developments`.`site_development_category_id` 
            // left join `uichecks` on `uichecks`.`site_development_category_id` = `site_development_categories`.`id` 
            // left join `uicheck_user_accesses` as `uua` on `uua`.`uicheck_id` = `uichecks`.`id` 
            // where 
            //     `site_developments`.`is_ui` = ? group by `site_development_categories`.`id`

            return datatables()->eloquent($q)->toJson();
        } else {
            $data = array();
            $data['all_store_websites'] = StoreWebsite::all();
            $data['users'] = User::select('id', 'name')->get();
            // $data['allTypes'] = UicheckType::all();
            $data['allTypes'] = UicheckType::orderBy('name')->pluck("name", "id")->toArray();
            $data['categories'] = SiteDevelopmentCategory::paginate(20); //all();
            $data['search_website'] = isset($request->store_webs) ? $request->store_webs : '';
            $data['search_category'] = isset($request->categories) ? $request->categories : '';
            $data['user_id'] = isset($request->user_id) ? $request->user_id : '';
            $data['assign_to'] = isset($request->assign_to) ? $request->assign_to : '';
            $data['dev_status'] = isset($request->dev_status) ? $request->dev_status : '';
            $data['admin_status'] = isset($request->admin_status) ? $request->admin_status : '';
            $data['site_development_status_id'] = isset($request->site_development_status_id) ? $request->site_development_status_id : [];
            $data['allStatus'] = SiteDevelopmentStatus::pluck("name", "id")->toArray();
            $store_websites = StoreWebsite::select('store_websites.*')->join('site_developments', 'store_websites.id', '=', 'site_developments.website_id');
            if ($data['search_website'] != '') {
                $store_websites =  $store_websites->where('store_websites.id', $data['search_website']);
            }
            $data['store_websites'] =  $store_websites->where('is_ui', 1)->groupBy('store_websites.id')->get();
            $data['allUsers'] = User::query()
                ->join('role_user', 'role_user.user_id', 'users.id')
                ->join('roles', 'roles.id', 'role_user.role_id')
                ->where('roles.name', 'Developer')
                ->pluck('users.name', 'users.id')->toArray();

            $data['log_user_id'] = \Auth::user()->id ?? '';

            $q = SiteDevelopmentCategory::query()
                ->join('site_developments', 'site_development_categories.id', '=', 'site_developments.site_development_category_id')
                ->leftjoin('uichecks', 'uichecks.site_development_category_id', '=', 'site_development_categories.id')
                ->select(
                    'site_development_categories.*',
                    'site_developments.id AS site_id',
                    'site_developments.website_id',
                    "uichecks.id AS uicheck_id"
                )
                //->where('site_developments.is_ui', 1);
                ->where('uichecks.id', '>', 0);


            //->where('site_development_categories.id','site_developments.site_development_category_id');
            if ($data['search_website'] != '') {
                $q = $q->where('uichecks.website_id', $data['store_websites'][0]->id);
            }
            if ($data['search_category'] != '') {
                $q = $q->where('site_development_categories.id',  $data['search_category']);
            }
            $q->groupBy('uichecks.id');
            //$q->orderBy('site_development_categories.title');
            $q->orderBy('uichecks.updated_at', "desc");
            $data['site_development_categories'] = $q->pluck('site_development_categories.title', 'site_development_categories.id')->toArray();
            $data['record_count'] = count($q->get());
            //echo '<pre>';
            //print_r($data['site_development_categories']);
            // exit;
            $data['languages'] = Language::all();
            return view('uicheck.index', $data);
        }
    }

    public function access(Request $request) {
        $check = UicheckUserAccess::where("uicheck_id", $request->uicheck_id)->first();
        if (!is_null($check)) {
            $access = UicheckUserAccess::find($check->id);
            $access->delete();
        }
        $this->CreateUiAssignToHistoryLog($request, $check);
        $array = array(
            "user_id" => $request->id,
            "uicheck_id" => $request->uicheck_id
        );
        UicheckUserAccess::create($array);
        return response()->json(['code' => 200, 'message' => 'Permission Given!!!']);
    }

    public function typeSave(Request $request) {
        $array = array(
            "uicheck_type_id" => $request->type
        );
        Uicheck::where("id", $request->uicheck_id)->update($array);
        return response()->json(['code' => 200, 'message' => 'Type Updated!!!']);
    }

    public function createDuplicateCategory(Request $request) {
        $uiCheck = Uicheck::where("id", $request->id)->first();
        Uicheck::create([
            "site_development_id" => $uiCheck->site_development_id ?? '',
            "site_development_category_id" => $uiCheck->site_development_category_id ?? '',
            "website_id" => $uiCheck->website_id ?? '',
            "issue" => $uiCheck->issue ?? '',
            "created_at" => \Carbon\Carbon::now(),
        ]);
        return response()->json(['code' => 200, 'message' => 'Category Duplicate Created successfully!!!']);
    }


    public function upload_document(Request $request) {
        $uicheck_id = $request->uicheck_id;
        $subject = $request->subject;
        $description = $request->description;

        if ($uicheck_id > 0 && !empty($subject)) {
            if ($request->hasfile('files')) {
                $path = public_path('uicheckdocs');
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $uicheckDocName = '';
                if ($request->file('files')) {
                    $file = $request->file('files')[0];
                    $uicheckDocName = uniqid() . '_' . trim($file->getClientOriginalName());
                    $file->move($path, $uicheckDocName);
                }
                $docArray = array(
                    "user_id" => \Auth::id(),
                    "filename" => $uicheckDocName,
                    "uicheck_id" => $uicheck_id,
                    "subject" => $subject,
                    "description" => $description
                );
                UicheckAttachement::create($docArray);
                return response()->json(["code" => 200, "success" => "Done!"]);
            } else {
                return response()->json(["code" => 500, "error" => "Oops, Please fillup required fields"]);
            }
        } else {
            return response()->json(["code" => 500, "error" => "Oops, Please fillup required fields"]);
        }
    }

    public function getDocument(Request $request) {
        $id = $request->get("id", 0);

        if ($id > 0) {
            $devDocuments = UicheckAttachement::with("user", "uicheck")->where("uicheck_id", $id)->latest()->get();
            $html = view('uicheck.ajax.document-list', compact("devDocuments"))->render();
            return response()->json(["code" => 200, "data" => $html]);
        } else {
            return response()->json(["code" => 500, "error" => "Oops, id is required field"]);
        }
    }

    public function typeStore(Request $request) {
        // $request->validate($request, [
        //     'name' => 'required|string'
        // ]);
        $data = $request->except('_token');
        UicheckType::create($data);
        return redirect()->back()->with('success', 'You have successfully created a status!');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        try {
            $uicheck = Uicheck::find($request->id);
            if (empty($uicheck))
                $uicheck = new Uicheck();

            $uicheck->site_development_id = $request->site_development_id;
            $uicheck->site_development_category_id = $request->category;

            if ($request->website_id)
                $uicheck->website_id = $request->website_id;
            if ($request->issue) {
                if ($request->issue != $uicheck->issue) {
                    $this->CreateUiissueHistoryLog($request, $uicheck);
                }
                $uicheck->issue = $request->issue;
            }
            if ($request->developer_status) {
                if ($request->developer_status != $uicheck->developer_status) {
                    $this->CreateUiDeveloperStatusHistoryLog($request, $uicheck);
                }
                $uicheck->dev_status_id = $request->developer_status;
            }
            if ($request->admin_status) {
                if ($request->admin_status != $uicheck->admin_status_id) {
                    $this->createUiAdminStatusHistoryLog($request, $uicheck);
                }
                $uicheck->admin_status_id = $request->admin_status;
            }


            $uicheck->save();
            return response()->json(['code' => 200, 'data' => $uicheck, 'message' => 'Updated successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CreateUiAdminStatusHistoryLog  $createUiAdminStatusHistoryLog
     * @return \Illuminate\Http\Response
     */
    public function CreateUiAdminStatusHistoryLog(Request $request, $uicheck) {
        $adminStatusLog = new UiAdminStatusHistoryLog();
        $adminStatusLog->user_id = \Auth::user()->id;
        $adminStatusLog->uichecks_id = $request->id;
        $adminStatusLog->old_status_id = $uicheck->admin_status_id;
        $adminStatusLog->status_id = $request->admin_status;
        $adminStatusLog->save();
    }

    public function getUiAdminStatusHistoryLog(Request $request) {
        $adminStatusLog = UiAdminStatusHistoryLog::select("ui_admin_status_history_logs.*", "users.name as userName", "site_development_statuses.name AS dev_status", "old_stat.name AS old_name")
            ->leftJoin("users", "users.id", "ui_admin_status_history_logs.user_id")
            ->leftJoin("site_development_statuses", "site_development_statuses.id", "ui_admin_status_history_logs.status_id")
            ->leftJoin("site_development_statuses as old_stat", "old_stat.id", "ui_admin_status_history_logs.old_status_id")
            ->where('ui_admin_status_history_logs.uichecks_id', $request->id)
            ->orderBy('ui_admin_status_history_logs.id', "DESC")
            ->get();
        $html = "";
        foreach ($adminStatusLog as $adminStatus) {
            $html .=  '<tr>';
            $html .=  '<td>' . $adminStatus->id . '</td>';
            $html .=  '<td>' . $adminStatus->userName . '</td>';
            $html .=  '<td>' . $adminStatus->old_name . '</td>';
            $html .=  '<td>' . $adminStatus->dev_status . '</td>';
            $html .=  '<td>' . $adminStatus->created_at . '</td>';

            $html .=  '</tr>';
        }
        return response()->json(['code' => 200, 'html' => $html, 'message' => 'Listed successfully!!!']);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\CreateUiDeveloperStatusHistoryLog  $createUiDeveloperStatusHistoryLog
     * @return \Illuminate\Http\Response
     */
    public function CreateUiDeveloperStatusHistoryLog(Request $request, $uicheck) {
        $devStatusLog = new UiDeveloperStatusHistoryLog();
        $devStatusLog->user_id = \Auth::user()->id;
        $devStatusLog->uichecks_id = $request->id;
        $devStatusLog->old_status_id = $uicheck->dev_status_id;
        $devStatusLog->status_id = $request->developer_status;
        $devStatusLog->save();
    }

    public function getUiDeveloperStatusHistoryLog(Request $request) {
        $adminStatusLog = UiDeveloperStatusHistoryLog::select("ui_developer_status_history_logs.*", "users.name as userName", "site_development_statuses.name AS dev_status", "old_stat.name AS old_name")
            ->leftJoin("users", "users.id", "ui_developer_status_history_logs.user_id")
            ->leftJoin("site_development_statuses", "site_development_statuses.id", "ui_developer_status_history_logs.status_id")
            ->leftJoin("site_development_statuses as old_stat", "old_stat.id", "ui_developer_status_history_logs.old_status_id")
            ->where('ui_developer_status_history_logs.uichecks_id', $request->id)
            ->orderBy('ui_developer_status_history_logs.id', "DESC")
            ->get();

        $html = "";
        foreach ($adminStatusLog as $adminStatus) {
            $html .=  '<tr>';
            $html .=  '<td>' . $adminStatus->id . '</td>';
            $html .=  '<td>' . $adminStatus->userName . '</td>';
            $html .=  '<td>' . $adminStatus->old_name . '</td>';
            $html .=  '<td>' . $adminStatus->dev_status . '</td>';
            $html .=  '<td>' . $adminStatus->created_at . '</td>';
            $html .=  '</tr>';
        }
        return response()->json(['code' => 200, 'html' => $html, 'message' => 'Listed successfully!!!']);
    }

    public function CreateUiissueHistoryLog(Request $request, $uicheck) {
        $devStatusLog = new UiCheckIssueHistoryLog();
        $devStatusLog->user_id = \Auth::user()->id;
        $devStatusLog->uichecks_id = $request->id;
        $devStatusLog->old_issue = $uicheck->issue;
        $devStatusLog->issue = $request->issue;
        $devStatusLog->save();
    }

    public function getUiIssueHistoryLog(Request $request) {
        try {
            $getIssueLog = UiCheckIssueHistoryLog::select("ui_check_issue_history_logs.*", "users.name as userName")
                ->leftJoin("users", "users.id", "ui_check_issue_history_logs.user_id")
                ->where('ui_check_issue_history_logs.uichecks_id', $request->id)
                ->orderBy('ui_check_issue_history_logs.id', "DESC")
                ->get();

            $html = "";
            foreach ($getIssueLog as $issueLog) {
                $html .=  '<tr>';
                $html .=  '<td>' . $issueLog->id . '</td>';
                $html .=  '<td>' . $issueLog->userName . '</td>';
                $html .=  '<td>' . $issueLog->old_issue . '</td>';
                $html .=  '<td>' . $issueLog->issue . '</td>';
                $html .=  '<td>' . $issueLog->created_at . '</td>';

                $html .=  '</tr>';
            }
            return response()->json(['code' => 200, 'html' => $html, 'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function getUiCheckMessageHistoryLog(Request $request) {
        try {
            $getMessageLog = UiCheckCommunication::select("ui_check_communications.*", "users.name as userName")
                ->leftJoin("users", "users.id", "ui_check_communications.user_id")
                ->where('ui_check_communications.uichecks_id', $request->id)
                ->orderBy('ui_check_communications.id', "DESC")
                ->get();

            $html = "";
            foreach ($getMessageLog as $messageLog) {
                $html .=  '<tr>';
                $html .=  '<td>' . $messageLog->id . '</td>';
                $html .=  '<td>' . $messageLog->userName . '</td>';
                $html .=  '<td>' . $messageLog->message . '</td>';
                $html .=  '<td>' . $messageLog->created_at . '</td>';
                $html .=  '</tr>';
            }
            return response()->json(['code' => 200, 'html' => $html, 'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function CreateUiMessageHistoryLog(Request $request) {
        $messageLog = new UiCheckCommunication();
        $messageLog->user_id = \Auth::user()->id;
        $messageLog->uichecks_id = $request->id;
        $messageLog->message = $request->message;
        $messageLog->save();
        $uicheck = Uicheck::find($request->id);
        $uicheck->updated_at = \Carbon\Carbon::now();
        $uicheck->save();
        return response()->json(['code' => 200, 'message' => 'Message saved successfully!!!']);
    }

    public function CreateUiAssignToHistoryLog(Request $request, $uicheck) {
        $messageLog = new UiCheckAssignToHistory();
        $messageLog->user_id = \Auth::user()->id;
        $messageLog->uichecks_id = $request->uicheck_id;
        $messageLog->assign_to = $request->id;
        $messageLog->old_assign_to = $uicheck->user_id ?? '';
        $messageLog->save();
        return response()->json(['code' => 200, 'message' => 'Message saved successfully!!!']);
    }

    public function getUiCheckAssignToHistoryLog(Request $request) {
        try {
            $getMessageLog = UiCheckAssignToHistory::select("ui_check_assign_to_histories.*", "users.name as userName", "assignTo.name AS assignToName")
                ->leftJoin("users", "users.id", "ui_check_assign_to_histories.user_id")
                ->leftJoin("users AS assignTo", "assignTo.id", "ui_check_assign_to_histories.assign_to")
                ->where('ui_check_assign_to_histories.uichecks_id', $request->id)
                ->orderBy('ui_check_assign_to_histories.id', "DESC")
                ->get();

            $html = "";
            foreach ($getMessageLog as $messageLog) {
                $html .=  '<tr>';
                $html .=  '<td>' . $messageLog->id . '</td>';
                $html .=  '<td>' . $messageLog->userName . '</td>';
                $html .=  '<td>' . $messageLog->assignToName . '</td>';
                $html .=  '<td>' . $messageLog->created_at . '</td>';
                $html .=  '</tr>';
            }
            return response()->json(['code' => 200, 'html' => $html, 'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function historyAll(Request $request) {
        try {
            $lastDate = request('lastDate') ?: date('Y-m-d H:i:s');

            $whQ = "";
            $whArr = [$lastDate];
            if (!Auth::user()->hasRole('Admin')) {
                $whQ .= " AND listdata.uichecks_id IN ( SELECT uicheck_id FROM uicheck_user_accesses WHERE user_id = ? ) ";
                $whArr[] = \Auth::user()->id;
            }
            if (request('user_id')) {
                $whQ .= " AND listdata.user_id = ?";
                $whArr[] = request('user_id');
            }

            $sql = "SELECT
                    listdata.*,
                    sdc.title AS site_development_category_name,
                    sw.title AS store_website_name,
                    u.name AS addedBy
                FROM (
                    (
                        SELECT
                        curr.uichecks_id,
                        'assign' AS type,
                        curr.old_assign_to AS old_val,
                        curr.assign_to AS new_val,
                        ov.name AS old_disp_val,
                        nv.name AS new_disp_val,
                        curr.user_id,
                        curr.created_at
                        FROM ui_check_assign_to_histories AS curr
                        LEFT JOIN users AS ov ON ov.id = curr.old_assign_to
                        LEFT JOIN users AS nv ON nv.id = curr.assign_to
                    )
                    UNION
                    (
                        SELECT
                        uichecks_id,
                        'issue' AS type,
                        old_issue AS old_val,
                        issue AS new_val,
                        old_issue AS old_disp_val,
                        issue AS new_disp_val,
                        user_id,
                        created_at
                        FROM ui_check_issue_history_logs
                    )
                    UNION
                    (
                        SELECT
                        uichecks_id,
                        'communication' AS type,
                        NULL AS old_val,
                        message AS new_val,
                        NULL AS old_disp_val,
                        message AS new_disp_val,
                        user_id,
                        created_at
                        FROM ui_check_communications
                    )
                    UNION
                    (
                        SELECT
                        curr.uichecks_id,
                        'developer_status' AS type,
                        curr.old_status_id AS old_val,
                        curr.status_id AS new_val,
                        ov.name AS old_disp_val,
                        nv.name AS new_disp_val,
                        curr.user_id,
                        curr.created_at
                        FROM ui_developer_status_history_logs AS curr
                        LEFT JOIN site_development_statuses AS ov ON ov.id = curr.old_status_id
                        LEFT JOIN site_development_statuses AS nv ON nv.id = curr.status_id
                    )
                    UNION
                    (
                        SELECT
                        curr.uichecks_id,
                        'admin_status' AS type,
                        curr.old_status_id AS old_val,
                        curr.status_id AS new_val,
                        ov.name AS old_disp_val,
                        nv.name AS new_disp_val,
                        curr.user_id,
                        curr.created_at
                        FROM ui_admin_status_history_logs AS curr
                        LEFT JOIN site_development_statuses AS ov ON ov.id = curr.old_status_id
                        LEFT JOIN site_development_statuses AS nv ON nv.id = curr.status_id
                    )
                    UNION
                    (
                        SELECT
                        uichecks_id,
                        type,
                        old_val,
                        new_val,
                        old_val AS old_disp_val,
                        new_val AS new_disp_val,
                        user_id,
                        created_at
                        FROM  uichecks_hisotry
                    )
                ) AS listdata
                LEFT JOIN users AS u ON u.id = listdata.user_id
                LEFT JOIN uichecks AS uic ON uic.id = listdata.uichecks_id
                LEFT JOIN site_development_categories AS sdc ON sdc.id = uic.site_development_category_id
                LEFT JOIN store_websites AS sw ON sw.id = uic.website_id
                WHERE listdata.created_at < ? 
                " . $whQ . " 
                ORDER BY listdata.created_at DESC
                LIMIT 10";
            $data = \DB::select($sql, $whArr);

            $html = [];
            if ($data) {
                foreach ($data as $value) {
                    $html[] = implode('', [
                        '<tr>',
                        '<td>' . ($value->uichecks_id ?: '-') . '</td>',
                        '<td>' . ($value->site_development_category_name ?: '-') . '</td>',
                        '<td>' . ($value->store_website_name ?: '-') . '</td>',
                        '<td>' . ($value->type ?: '-') . '</td>',
                        '<td>' . ($value->old_disp_val ?: '-') . '</td>',
                        '<td>' . ($value->new_disp_val ?: '-') . '</td>',
                        '<td>' . ($value->addedBy ?: '-') . '</td>',
                        '<td class="cls-created-date">' . ($value->created_at ?: '') . '</td>',
                        '</tr>',
                    ]);
                }
            }
            return respJson(200, '', [
                'html' => implode('', $html)
            ]);
        } catch (\Throwable $th) {
            return respException($th);
        }
    }
    public function get() {
        try {
            if ($single = Uicheck::find(request('id'))) {
                return respJson(200, '', [
                    'data' => $single
                ]);
            }
            return respJson(404, 'Invalid record.', []);
        } catch (\Throwable $th) {
            return respException($th);
        }
    }

    public function updateDates() {
        try {
            if ($single = Uicheck::find(request('id'))) {
                $single->updateElement('start_time', request('start_time'));
                $single->updateElement('expected_completion_time', request('expected_completion_time'));
                if (\Auth::user()->hasRole('Admin')) {
                    $single->updateElement('actual_completion_time', request('actual_completion_time'));
                }
                return respJson(200, 'Dates updated successfully.', []);
            }
            return respJson(404, 'Invalid record.', []);
        } catch (\Throwable $th) {
            return respException($th);
        }
    }
    public function historyDates() {
        try {
            $data = UicheckHistory::with('updatedBy')->orderBy('id', 'DESC')->get();

            $html = [];
            if ($data->count()) {
                foreach ($data as $value) {
                    $html[] = implode('', [
                        '<tr>',
                        '<td>' . ($value->id ?: '-') . '</td>',
                        '<td>' . ($value->type ?: '-') . '</td>',
                        '<td>' . ($value->old_val ?: '-') . '</td>',
                        '<td>' . ($value->new_val ?: '-') . '</td>',
                        '<td>' . ($value->updatedByName() ?: '-') . '</td>',
                        '<td class="cls-created-date">' . ($value->created_at ?: '') . '</td>',
                        '</tr>',
                    ]);
                }
            } else {
                $html[] = implode('', [
                    '<tr>',
                    '<td colspan="6">No records found.</td>',
                    '</tr>',
                ]);
            }
            return respJson(200, '', [
                'html' => implode('', $html)
            ]);
        } catch (\Throwable $th) {
            return respException($th);
        }
    }

    public function updateLock() {
        try {
            if ($single = Uicheck::find(request('id'))) {
                $key = request('type') == 'developer' ? 'lock_developer' : 'lock_admin';
                $single->updateElement($key, $single->$key ? 0 : 1);
                return respJson(200, 'Record updated successfully.', []);
            }
            return respJson(404, 'Invalid record.', []);
        } catch (\Throwable $th) {
            return respException($th);
        }
    }
    // 

    public function updateLanguage(Request $request) {
        try {
            $uiLanData = UiLanguage::where("languages_id", "=", $request->id)->get();
            
            $uiLan["user_id"] = \Auth::user()->id;
            $uiLan["languages_id"] = $request->id;
            $uiLan["uicheck_id"] = $request->uicheck_id;
            if($request->message){
                $uiLan["message"] = $request->message;
            }
            if($request->uilanstatus){
                $uiLan["status"] = $request->uilanstatus;
            }
            
            if (count($uiLanData) == 0){ 
                $uiLans = UiLanguage::create($uiLan);
                $uiData = UiLanguage::where("languages_id",$uiLans->id)->first();
            } else {
                $uiData = UiLanguage::where("languages_id",$request->id)->first();
                $uiLans = UiLanguage::where("languages_id",$request->id)->update($uiLan);
            }
            
            $uiMess = $uiLanData[0]->message ?? "";
            $uiLan["ui_languages_id"] = $uiData->id ?? $request->id;
            if($request->message != $uiMess){
                $reData = $this->uicheckLanUpdateHistory($uiLan);
            }
            $uistatus = $uiData->status ?? "";
            if($request->uilanstatus != $uistatus){
                //$this->uicheckLanUpdateHistory($uiLan);
            }

            return respJson(200, 'Record updated successfully.', []);
        } catch (\Throwable $th) {
            return respException($th);
        }
    }

    public function uicheckLanUpdateHistory($data) {
        try{
            $createdHistory = UicheckLanguageMessageHistory::create(
                $data
            );            
        }catch(\Exception $e){
            return respException($e);
        }
    }

    public function getuicheckLanUpdateHistory(Request $request) {
        try{
            $getHistory = UicheckLanguageMessageHistory::leftJoin("users", "users.id", "uicheck_language_message_histories.user_id")
            ->leftJoin("site_development_statuses AS sds", "sds.id", "uicheck_language_message_histories.status")
            ->select("uicheck_language_message_histories.*", "users.name As userName", "sds.name as status_name")
            ->where("languages_id", $request->id)
            ->where("uicheck_id", $request->uicheck_id)
            ->orderBy("id", "desc")->get();         
            //dd($getHistory);
            $html = [];
            if ($getHistory->count()) {
                foreach ($getHistory as $value) {
                    $html[] = implode('', [
                        '<tr>',
                        '<td>' . ($value->id ?: '-') . '</td>',
                        '<td>' . ($value->userName ?: '-') . '</td>',
                        '<td class="expand-row-msg" data-name="lan_message" data-id="'.$value->id.'" >
                            <span class="show-short-lan_message-'.$value->id.'">'.str_limit($value->message, 5, "...").'</span>
                            <span style="word-break:break-all;" class="show-full-lan_message-'.$value->id.' hidden">'.$value->message.'</span>
                        </td>',
                        '<td>' . ($value->status_name ?: '-') . '</td>',
                        '<td class="cls-created-date">' . ($value->created_at ?: '') . '</td>',
                        '</tr>',
                    ]);
                }
            } else {
                $html[] = implode('', [
                    '<tr>',
                    '<td colspan="6">No records found.</td>',
                    '</tr>',
                ]);
            }
            return respJson(200, '', [
                'html' => implode('', $html)
            ]);   
        }catch(\Exception $e){
            return respException($e);
        }
    }
   
    public function saveDocuments(Request $request)
    {

        $documents = $request->input('document', []);
        if (!empty($documents)) {
            $uiDevData = UiLanguage::where("languages_id", "=", $request->id)->where('uicheck_id', "=", $request->uicheck_id)->first();
            
            foreach ($request->input('document', []) as $file) {
                $path  = storage_path('tmp/uploads/' . $file);
                $media = MediaUploader::fromSource($path)
                    ->toDirectory('uicheckAttach/' . floor($request->id / config('constants.image_per_folder')))
                    ->upload();
                //$receipt->attachMedia($media, config('constants.media_tags'));
                $attachment = UicheckLangAttchment::create([
                    "languages_id" => $request->id,
                    "user_id" => \Auth::user()->id,
                    'uicheck_id' => $request->uicheck_id ?? '',
                    "attachment" => $media
                ]);
            }

            return response()->json(["code" => 200, "data" => [], "message" => "Done!"]);
        } else {
            return response()->json(["code" => 500, "data" => [], "message" => "No documents for upload"]);
        }

    }

    public function listDocuments(Request $request)
    {
        $uicheckAttch = UicheckLangAttchment::where("languages_id", $request->id)
        ->where("uicheck_id", $request->uicheck_id)
        ->get();
        
        $userList = [];

        $records = [];
        if ($uicheckAttch) {
            foreach ($uicheckAttch as $media) {
                // Convert JSON string to Object
                $imagepath = json_decode($media->attachment);
                $records[] = [
                    "id"            => $media->id,
                    'url'           => "uploads/".$imagepath->directory."/".$imagepath->filename.".".$imagepath->extension,
                    'ui_attach_id'  => $media->id,
                ];
            }
        }

        return response()->json(["code" => 200, "data" => $records]);
    }

    public function deleteDocument(Request $request)
    {
        if ($request->id != null) {
            $uicheckAttch = UicheckLangAttchment::where("id", $request->id)->delete();
            return response()->json(["code" => 200, "message" => "Document delete succesfully"]);
        }
        return response()->json(["code" => 500, "message" => "No document found"]);
    }



    public function updateDevice(Request $request) {
        try {
            
            $uiDevData = UiDevice::where("uicheck_id", "=", $request->uicheck_id)->where('device_no', "=", $request->device_no)->first();
            //dd($uiDevData);
            $uiDev["user_id"] = \Auth::user()->id;
            $uiDev["device_no"] = $request->device_no;
            $uiDev["uicheck_id"] = $request->uicheck_id;
            if($request->message){
                $uiDev["message"] = $request->message;
            }
            if($request->uidevstatus){
                $uiDev["status"] = $request->uidevstatus;
            }
            $uiDevid = $uiDevData->id ?? '';
            if ($uiDevid == ''){ 
                $uiDevs = UiDevice::create($uiDev);
                $uiData = UiDevice::where("id",$uiDevs->id)->first();

            } else {
                $uiData = $uiDevData;
                $uiLans = UiDevice::where("id",$uiDevData->id)->update($uiDev);
            }
            
            $uiMess = $uiDevData->message ?? "";
            $uiDev["ui_devices_id"] = $uiData->id;
            if($request->message != $uiMess){
                $reData = $this->uicheckDevUpdateHistory($uiDev);
               
            }
            $uistatus = $uiData->status ?? "";
            if($request->uidevstatus != $uistatus){
                //$this->uicheckLanUpdateHistory($uiDev);
            }

            return respJson(200, 'Record updated successfully.', []);
        } catch (\Throwable $th) {
            return respException($th);
        }
    }


    public function uicheckDevUpdateHistory($data) {
        
        try{
            $createdHistory = UiDeviceHistory::create(
                $data
            );            
        }catch(\Exception $e){
            return respException($e);
        }
    }

    public function uploadDocuments(Request $request)
    {
        $path = storage_path('tmp/uploads');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function saveDevDocuments(Request $request)
    {

        $documents = $request->input('document', []);
        if (!empty($documents)) {
            $uiDevData = UiDevice::where("uicheck_id", "=", $request->uicheck_id)->where('device_no', "=", $request->device_no)->first();
            

            foreach ($request->input('document', []) as $file) {
                $path  = storage_path('tmp/uploads/' . $file);
                $media = MediaUploader::fromSource($path)
                    ->toDirectory('uicheckAttach/dev/' . floor($request->id / config('constants.image_per_folder')))
                    ->upload();
                //$receipt->attachMedia($media, config('constants.media_tags'));
                $attachment = UicheckDeviceAttachment::create([
                    "device_no" => $request->device_no ?? '',
                    "uicheck_id" => $request->uicheck_id,
                    "ui_devices_id" => $uiDevData->id ?? '',
                    "user_id" => \Auth::user()->id,
                    "attachment" => $media
                ]);
            }

            return response()->json(["code" => 200, "data" => [], "message" => "Done!"]);
        } else {
            return response()->json(["code" => 500, "data" => [], "message" => "No documents for upload"]);
        }

    }

    public function devListDocuments(Request $request)
    {
        $uicheckAttch = UicheckDeviceAttachment::leftJoin("users", "users.id", "uicheck_device_attachments.user_id")
        ->select("uicheck_device_attachments.*", "users.name as userName")
        ->where("device_no", $request->device_no)->where("device_no", $request->device_no)
        ->where("uicheck_id", $request->ui_check_id)
        ->get();
        
        $userList = [];

        $records = [];
        if ($uicheckAttch) {
            foreach ($uicheckAttch as $media) {
                // Convert JSON string to Object
                $imagepath = json_decode($media->attachment);
                $records[] = [
                    "id"            => $media->id,
                    'url'           => "uploads/".$imagepath->directory."/".$imagepath->filename.".".$imagepath->extension,
                    "userName"      => $media->userName,
                    'ui_attach_id'  => $media->id,
                ];
            }
        }

        return response()->json(["code" => 200, "data" => $records]);
    }

    public function deleteDevDocument(Request $request)
    {
        if ($request->id != null) {
            $uicheckAttch = UicheckDeviceAttachment::where("id", $request->id)->delete();
            return response()->json(["code" => 200, "message" => "Document delete succesfully"]);
        }
        return response()->json(["code" => 500, "message" => "No document found"]);
    }

    public function getuicheckDevUpdateHistory(Request $request) {
        try{
            $getHistory = UiDeviceHistory::leftJoin("users", "users.id", "ui_device_histories.user_id")
            ->leftJoin("site_development_statuses AS sds", "sds.id", "ui_device_histories.status")
            ->select("ui_device_histories.*", "users.name As userName", "sds.name AS status_name")
            ->where("ui_device_histories.device_no", $request->device_no)
            ->where("ui_device_histories.uicheck_id", $request->uicheck_id)
            ->orderBy("id", "desc")->get();         
            //dd($getHistory);
            $html = [];
            if ($getHistory->count()) {
                foreach ($getHistory as $value) {
                    $html[] = implode('', [
                        '<tr>',
                        '<td>' . ($value->id ?: '-') . '</td>',
                        '<td>' . ($value->userName ?: '-') . '</td>',
                        '<td class="expand-row-msg" data-name="dev_message" data-id="'.$value->id.'" >
                            <span class="show-short-dev_message-'.$value->id.'">'.str_limit($value->message, 5, "...").'</span>
                            <span style="word-break:break-all;" class="show-full-dev_message-'.$value->id.' hidden">'.$value->message.'</span>
                        </td>',
                        '<td>' . ($value->status_name ?: '-') . '</td>',
                        '<td class="cls-created-date">' . ($value->created_at ?: '') . '</td>',
                        '</tr>',
                    ]);
                }
            } else {
                $html[] = implode('', [
                    '<tr>',
                    '<td colspan="6">No records found.</td>',
                    '</tr>',
                ]);
            }
            return respJson(200, '', [
                'html' => implode('', $html)
            ]);   
        }catch(\Exception $e){
            return respException($e);
        }
    }
}
