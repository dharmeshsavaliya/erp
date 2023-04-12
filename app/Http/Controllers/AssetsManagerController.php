<?php

namespace App\Http\Controllers;

use App\AssetMagentoDevScripUpdateLog;
use App\AssetManamentUpdateLog;
use App\AssetPlateForm;
use App\AssetsManager;
use App\assetUserChangeHistory;
use App\CashFlow;
use App\EmailAddress;
use App\StoreWebsite;
use App\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Email;
use Auth;
use App\ChatMessage;
use App\AssetManagerLinkUser;

class AssetsManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $archived = 0;
        if ($request->archived == 1) {
            $archived = 1;
        }

        $category = DB::table('assets_category')->get();

        $search = request('search', '');
        $paymentCycle = request('payment_cycle', '');
        $assetType = request('asset_type', '');
        $purchaseType = request('purchase_type', '');
        $website_id = request('website_id');
        $asset_plate_form_id = request('asset_plate_form_id');
        $email_address_id = request('email_address_id');
        $whatsapp_config_id = request('whatsapp_config_id');

        $assets = new AssetsManager;
        $assets = $assets->leftJoin('store_websites', 'store_websites.id', 'assets_manager.website_id')
                ->leftJoin('asset_plate_forms AS apf', 'apf.id', 'assets_manager.asset_plate_form_id')
                ->leftJoin('email_addresses As ea', 'ea.id', 'assets_manager.email_address_id')
                ->leftJoin('whatsapp_configs AS wc', 'wc.id', 'assets_manager.whatsapp_config_id')
                ->leftJoin('assets_manager_link_user as linkuser', 'linkuser.asset_manager_id', 'assets_manager.id');

        if(!Auth::user()->hasRole('Admin')){
            $assets->where('assets_manager.created_by', Auth::user()->id)->orWhere('linkuser.user_id', Auth::user()->id);
        }

        if (! empty($search)) {
            $assets = $assets->where(function ($q) use ($search) {
                $q->where('assets_manager.name', 'LIKE', '%'.$search.'%')->orWhere('provider_name', 'LIKE', '%'.$search.'%');
            });
        }

        if (! empty($paymentCycle)) {
            $assets = $assets->where('assets_manager.payment_cycle', $paymentCycle);
        }

        if (! empty($assetType)) {
            $assets = $assets->where('assets_manager.asset_type', $assetType);
        }

        if (! empty($purchaseType)) {
            $assets = $assets->where('assets_manager.purchase_type', $purchaseType);
        }
        //////////////////////////////////////////////////////////
        if (! empty($website_id)) {
            $assets = $assets->where('assets_manager.website_id', $website_id);
        }

        if (! empty($asset_plate_form_id)) {
            $assets = $assets->where('assets_manager.asset_plate_form_id', $asset_plate_form_id);
        }

        if (! empty($email_address_id)) {
            $assets = $assets->where('assets_manager.purchase_type', $email_address_id);
        }

        if (! empty($whatsapp_config_id)) {
            $assets = $assets->where('assets_manager.purchase_type', $whatsapp_config_id);
        }
        // $assets = $assets->orderBy("due_date", "ASC");

        $assetsIds = $assets->select('assets_manager.id')->get()->toArray();
        $assets = $assets->select(\DB::raw('DISTINCT assets_manager.*, linkuser.asset_manager_id'), 'store_websites.website AS website_name', 'apf.name AS plateform_name', 'ea.from_address', 'wc.number');
        $assets = $assets->orderBy('assets_manager.due_date', 'asc')->paginate(25);
        $websites = StoreWebsite::all();
        $plateforms = AssetPlateForm::all();
        $emailAddress = EmailAddress::all();
        $whatsappCon = \DB::table('whatsapp_configs')->get();

        //Cash Flows
        $cashflows = \App\CashFlow::whereIn('cash_flow_able_id', $assetsIds)->where(['cash_flow_able_type' => \App\AssetsManager::class])->get();
        $users = User::get()->toArray();
        //dd($users);
        return view('assets-manager.index', compact('assets', 'category', 'cashflows', 'users', 'websites', 'plateforms', 'whatsappCon', 'emailAddress'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'asset_type' => 'required',
            'start_date' => 'required',
            'category_id' => 'required',
            'purchase_type' => 'required',
            'payment_cycle' => 'required',
            'amount' => 'required',
        ]);

        $othercat = $request->input('other');
        $category_id = $request->input('category_id');
        $catid = '';
        if ($othercat != '' && $category_id != '') {
            $dataCat = DB::table('assets_category')
                ->Where('cat_name', $othercat)
                ->first();

            if (! empty($dataCat) && $dataCat->id != '') {
                $catid = $dataCat->id;
            } else {
                $catid = DB::table('assets_category')->insertGetId(
                    ['cat_name' => $othercat]
                );
            }
        }

        $data = $request->except('_token');
        if ($catid != '') {
            $data['category_id'] = $catid;
        }
        $data['start_date'] = ($request->input('start_date') == '') ? $request->input('old_start_date') : $request->input('start_date');
        $data['ip_name'] = $request->ip_name;
        $data['server_password'] = $request->server_password;
        $data['folder_name'] = json_encode($request->folder_name);
        $data['website_id'] = $request->website_id;
        $data['asset_plate_form_id'] = $request->asset_plate_form_id;
        $data['email_address_id'] = $request->email_address_id;
        $data['whatsapp_config_id'] = $request->whatsapp_config_id;
        $data['created_by'] = Auth::user()->id;
        $insertData = AssetsManager::create($data);
        if ($request->input('payment_cycle') == 'One time') {
            //create entry in table cash_flows
            \App\CashFlow::create(
                [
                    'description' => 'Asset Manager Payment for '.$insertData->name,
                    'date' => date('Y-m-d'),
                    'amount' => $request->input('amount'),
                    'type' => 'pending',
                    'currency' => $insertData->currency,
                    'cash_flow_able_type' => \App\AssetsManager::class,
                    'cash_flow_able_id' => $insertData->id,

                ]
            );
        }

        return redirect()->route('assets-manager.index')
            ->with('success', 'Assets created successfully');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function show($id)
    {
    $assets = AssetsManager::find($id);
    $reply_categories = ReplyCategory::all();
    $users_array = Helpers::getUserArray(User::all());
    $emails = [];

    return view('assets-manager.show', [
    'assets'  => $assets,
    'reply_categories'  => $reply_categories,
    'users_array'  => $users_array,
    'emails'  => $emails
    ]);
    }*/

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'asset_type' => 'required',
            'start_date' => 'required',
            'category_id' => 'required',
            'purchase_type' => 'required',
            'payment_cycle' => 'required',
            'amount' => 'required',
        ]);

        $othercat = $request->input('other');
        $category_id = $request->input('category_id');
        $catid = '';
        if ($othercat != '' && $category_id != '') {
            $dataCat = DB::table('assets_category')
                ->Where('cat_name', $othercat)
                ->first();

            if (! empty($dataCat) && $dataCat->id != '') {
                $catid = $dataCat->id;
            } else {
                $catid = DB::table('assets_category')->insertGetId(
                    ['cat_name' => $othercat]
                );
            }
        }

        $data = $request->except('_token');

        if ($catid != '') {
            $data['category_id'] = $catid;
        }
        if ($request->input('old_user_name') != $request->input('user_name') || $request->input('old_password') != $request->input('password')) {
            $assetLog = new AssetManamentUpdateLog();
            $assetLog->assetmenament_id = $id;
            $assetLog->user_id = \Auth::user()->id;
            $assetLog->user_name = $request->input('old_user_name');
            $assetLog->password = $request->input('old_password');
            $assetLog->ip = $request->input('old_ip');
            $assetLog->save();
        }
        if ($request->input('old_user_name') != $request->input('user_name')) {
            $this->createUserHistory($request, $id);
        }
        $data['ip_name'] = $request->ip_name;
        $data['server_password'] = $request->server_password;
        $data['folder_name'] = json_encode($request->folder_name);
        $data['website_id'] = $request->website_id;
        $data['asset_plate_form_id'] = $request->asset_plate_form_id;
        $data['email_address_id'] = $request->email_address_id;
        $data['whatsapp_config_id'] = $request->whatsapp_config_id;

        //dd($data);
        AssetsManager::find($id)->update($data);

        return redirect()->route('assets-manager.index')
            ->with('success', 'Assets updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data['archived'] = 1;
        AssetsManager::find($id)->update($data);

        return redirect()->route('assets-manager.index')
            ->with('success', 'Assets deleted successfully');
    }

    public function addNote($id, Request $request)
    {
        $assetmanager = AssetsManager::findOrFail($id);
        $notes = $assetmanager->notes;
        if (! is_array($notes)) {
            $notes = [];
        }

        $notes[] = $request->get('note');
        $assetmanager->notes = $notes;
        $assetmanager->save();

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function paymentHistory(request $request)
    {
        $asset_id = $request->input('asset_id');
        $html = '';
        $paymentData = CashFlow::where('cash_flow_able_id', $asset_id)
            ->where('cash_flow_able_type', \App\AssetsManager::class)
            ->where('type', 'paid')
            ->orderBy('date', 'DESC')
            ->get();
        $i = 1;
        if (count($paymentData) > 0) {
            foreach ($paymentData as $history) {
                $html .= '<tr>';
                $html .= '<td>'.$i.'</td>';
                $html .= '<td>'.$history->amount.'</td>';
                $html .= '<td>'.$history->date.'</td>';
                $html .= '<td>'.$history->description.'</td>';
                $html .= '</tr>';

                $i++;
            }

            return response()->json(['html' => $html, 'success' => true], 200);
        } else {
            $html .= '<tr>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '</tr>';
        }

        return response()->json(['html' => $html, 'success' => true], 200);
    }

    public function assetManamentLog(request $request)
    {
        $asset_id = $request->input('asset_id');
        //dd($asset_id);
        $html = '';
        //\DB::enableQueryLog();
        $assetLogs = AssetManamentUpdateLog::select('asset_manamentupdate_logs.*', 'users.name AS userName')
            ->leftJoin('users', 'users.id', '=', 'asset_manamentupdate_logs.user_id')
            ->where('asset_manamentupdate_logs.assetmenament_id', $asset_id)
            ->orderBy('asset_manamentupdate_logs.id', 'DESC')
            ->get();
        //dd(\DB::getQueryLog());
        $i = 1;
        //dd($assetLogs);
        if (count($assetLogs) > 0) {
            foreach ($assetLogs as $assetLog) {
                $html .= '<tr>';
                $html .= '<td>'.$assetLog->id.'</td>';
                $html .= '<td>'.$assetLog->userName.'</td>';
                $html .= '<td>'.$assetLog->user_name.'</td>';
                $html .= '<td>'.$assetLog->password.'</td>';
                $html .= '<td>'.$assetLog->created_at.'</td>';
                $html .= '</tr>';
                $i++;
            }

            return response()->json(['html' => $html, 'success' => true], 200);
        } else {
            $html .= '<tr>';
            $html .= '<td colspan="4">Record not found</td>';
            $html .= '</tr>';
        }

        return response()->json(['html' => $html, 'success' => true], 200);
    }

    public function getMagentoDevScriptUpdatesLogs(Request $request, $asset_manager_id)
    {
        try {
            $responseLog = AssetMagentoDevScripUpdateLog::where('asset_manager_id', '=', $asset_manager_id)->orderBy('id', 'desc')->get();
            if ($responseLog != null) {
                $html = '';
                foreach ($responseLog as $res) {
                    $html .= '<tr>';
                    $html .= '<td>'.$res->created_at.'</td>';
                    $html .= '<td class="expand-row-msg" data-name="ip" data-id="'.$res->id.'" style="cursor: grabbing;">
                    <span class="show-short-ip-'.$res->id.'">'.Str::limit($res->ip, 15, '...').'</span>
                    <span style="word-break:break-all;" class="show-full-ip-'.$res->id.' hidden">'.$res->website.'</span>
                    </td>';
                    $html .= '<td class="expand-row-msg" data-name="response" data-id="'.$res->id.'" style="cursor: grabbing;">
                    <span class="show-short-response-'.$res->id.'">'.Str::limit($res->response, 25, '...').'</span>
                    <span style="word-break:break-all;" class="show-full-response-'.$res->id.' hidden">'.$res->response.'</span>
                    </td>';
                    $html .= '<td class="expand-row-msg" data-name="command" data-id="'.$res->id.'" style="cursor: grabbing;">
                    <span class="show-short-command-'.$res->id.'">'.Str::limit($res->command_name, 25, '...').'</span>
                    <span style="word-break:break-all;" class="show-full-command-'.$res->id.' hidden">'.$res->command_name.'</span>
                    </td>';

                    $html .= '</tr>';
                }

                return response()->json([
                    'code' => 200,
                    'data' => $html,
                    'message' => 'Magento bash Log Listed successfully!!!',
                ]);
            }

            return response()->json(['code' => 500, 'error' => 'Wrong site id!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'data' => [], 'message' => $msg]);
        }
    }

    public function magentoDevScriptUpdate(Request $request)
    {
        try {
            $run = \Artisan::call('command:MagentoDevUpdateScriptAsset', ['id' => $request->id, 'folder_name' => $request->folder_name]);

            return response()->json(['code' => 200, 'message' => 'Magento Setting Updated successfully']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function userChangesHistoryLog(request $request)
    {
        $asset_id = $request->input('asset_id');
        $html = '';
        //\DB::enableQueryLog();
        $assetLogs = assetUserChangeHistory::select('asset_user_change_histories.*', 'users.name AS userNameChangeBy', 'u.name AS userName')
            ->leftJoin('users', 'users.id', '=', 'asset_user_change_histories.user_id')
            ->leftJoin('users AS u', 'u.id', '=', 'asset_user_change_histories.new_user_id')
            ->where('asset_user_change_histories.asset_id', $asset_id)
            ->orderBy('asset_user_change_histories.id', 'DESC')
            ->get();
        //dd(\DB::getQueryLog());
        $i = 1;
        //dd($assetLogs);
        if (count($assetLogs) > 0) {
            foreach ($assetLogs as $assetLog) {
                $html .= '<tr>';
                $html .= '<td>'.$assetLog->id.'</td>';
                $html .= '<td>'.$assetLog->userNameChangeBy.'</td>';
                $html .= '<td>'.$assetLog->userName.'</td>';
                $html .= '<td>'.$assetLog->created_at.'</td>';
                $html .= '</tr>';
                $i++;
            }

            return response()->json(['html' => $html, 'success' => true], 200);
        } else {
            $html .= '<tr>';
            $html .= '<td colspan="4">Record not found</td>';
            $html .= '</tr>';
        }

        return response()->json(['html' => $html, 'success' => true], 200);
    }

    public function createUserHistory(Request $request, $id)
    {
        try {
            $userHistory = assetUserChangeHistory::create([
                'asset_id' => $id,
                'user_id' => \Auth::user()->id,
                'new_user_id' => $request->user_name,
                'old_user_id' => $request->old_user_name,
            ]);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function plateFormStore(Request $request)
    {
        try {
            $plateform = AssetPlateForm::create([
                'name' => $request->name,
            ]);

            return response()->json(['code' => 500, 'data' => $plateform, 'message' => 'Plateform Data has been saved successfully']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Send email to given emailId
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assetsManagerSendEmail(Request $request)
    {
        try {
            $this->validate($request, [
                'user_name' => 'required',
                'from_email' => 'required',
            ]);
            $assetsmanager = AssetsManager::where('id', $request->assets_manager_id)->first();
            $usersdetails = User::where('id', $request->user_name)->first();
            $newPassword = Str::random(12);
            $message =  '';
            $message .= '<center><h4>Assets Manager Details <h4></center>'. '<br>';
            $message .= 'Name = '. $assetsmanager->name . '<br>';
            $message .= 'Capacity = '. $assetsmanager->capacity . '<br>';
            $message .= 'Password = '. $assetsmanager->password . '<br>';
            $message .= 'Provider Name = '. $assetsmanager->provider_name . '<br>';
            $message .= 'Asset Type = '. $assetsmanager->asset_type . '<br>';
            $message .= 'Category = '. $assetsmanager->category->cat_name . '<br>';
            $message .= 'Purchase Type = '. $assetsmanager->purchase_type . '<br>';
            $message .= 'Payment Cycle = '. $assetsmanager->payment_cycle . '<br>';
            $message .= 'Amount = '. $assetsmanager->amount . '<br>';
            $message .= 'Currency = '. $assetsmanager->currency . '<br>';
            $message .= 'Ip = '. $assetsmanager->ip . '<br>';
            $message .= 'Ip Name = '. $assetsmanager->ip_name . '<br>';

            //Store data in chat_message table.
            $params = [
                'number' => $usersdetails->phone,
                'user_id' => Auth::user()->id,
                'message' => $message,
            ];

            ChatMessage::create($params);

            // Store data in email table
            $from_address = isset($request->from_email) && $request->from_email != ''  ? $request->from_email : config('env.MAIL_FROM_ADDRESS');

            $email = Email::create([
                'model_id' => '',
                'model_type' => \App\AssetsManager::class,
                'from' => $from_address,
                'to' => $usersdetails->email,
                'subject' => 'Assets Manager',
                'message' => $message,
                'template' => 'reset-password',
                'status' => 'pre-send',
                'store_website_id' => null,
            ]);

            // Send email
            \App\Jobs\SendEmail::dispatch($email)->onQueue('send_email');
            \Session::flash('success', 'Assets manager email send successfully');
        } catch (\Throwable $th) {
            $emails = Email::latest('created_at')->first();
            $emails->error_message = $th->getMessage();
            $emails->save();
            \Session::flash('error', $th->getMessage());
        }
        return redirect()->back();
    }

    /**
     * Assets manager record permission
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assetsManagerRecordPermission(Request $request)
    {
        //Delete existing records
        $existsRec = AssetManagerLinkUser::select(\DB::raw('group_concat( id) as linkId'))->where("asset_manager_id", $request->assets_manager_id)->first();
        if(!empty($existsRec->linkId))
        {
            AssetManagerLinkUser::whereIn('id', explode(",",$existsRec->linkId))->delete();
        }

        //Insert new records
        $assetManagerLinkArr = [];
        if(isset($request->user_name))
        {
            foreach ($request->user_name as $key => $value) {
                $assetManagerLinkArr[] = [ 'user_id' => $value, 'asset_manager_id' => $request->assets_manager_id];
            }
            AssetManagerLinkUser::insert($assetManagerLinkArr);
            if(!empty($existsRec->linkId))
            {
                \Session::flash('success', 'Permission updated successfully');
            }else{
                \Session::flash('success', 'Permission added successfully');
            } 
        }else{
            \Session::flash('success', 'Permission removed successfully');
        }
        
        return redirect()->back();
    }

     /**
     * Assets manager link users Ids
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function linkUserList(Request $request)
    {
        $linkuser = AssetManagerLinkUser::select( \DB::raw('group_concat(DISTINCT user_id) as userids'))->distinct()->where('asset_manager_id', $request->asset_id)->first();
        return response()->json(['code' => 200, 'data' => $linkuser, 'message' => 'Assets manager data link user data fetch successfully']);
    }
    
}
