<?php

namespace App\Http\Controllers;

use App\VirtualminDomain;
use App\VirtualminHelper;
use Illuminate\Http\Request;
use App\Models\VirtualminDomainHistory;
use App\Models\VirtualminDomainDnsRecords;
use App\Models\VirtualminDomainDnsLogs;
use App\Models\VirtualminDomainLogs;
use App\Models\VirtualminDomainDnsRecordsHistory;
use Auth;

class VirtualminDomainController extends Controller
{
    private $virtualminHelper;

    public function __construct(VirtualminHelper $virtualminHelper)
    {
        $this->virtualminHelper = $virtualminHelper;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->get('keyword');
        $status = $request->get('status');

        // data search action
        $domains = VirtualminDomain::latest();

        if (! empty($keyword) || isset($keyword)) {
            $domains = $domains->where('name', 'LIKE', '%' . $keyword . '%');
        }
        if (! empty($status) || isset($status)) {
            $domains = $domains->where('is_enabled', $status);
        }

        $domains = $domains->paginate(10);

        return view('virtualmin-domain.index', ['domains' => $domains]);
    }

    public function syncDomains()
    {
        $this->virtualminHelper->syncDomains();

        return redirect()->back()->with('success', 'Domains synced successfully.');
    }

    public function domainCreate(Request $request)
    {
        try {

            //$url = 'https://s10.theluxuryunlimited.com:5000/api/v1/clients/' . $client_id . '/commands';
            $url = getenv('CLOUDFLARE_CREATE_DOMAIN_URL');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $account['id'] = getenv('CLOUDFLARE_ACCOUNT_ID');
            $parameters = [
                'name' => $request->name,
                'jump_start' => true,
                'account' => $account,
            ];

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));

            $headers = [];
            $headers[] = 'X-Auth-Email: ' . getenv('CLOUDFLARE_EMAIL');
            $headers[] = 'X-Auth-Key: ' . getenv('CLOUDFLARE_AUTH_KEY');
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);

            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            \Log::info('Virtualmin Domain API result: ' . $result);
            \Log::info('Virtualmin Domain API Error Number: ' . curl_errno($ch));
            if (curl_errno($ch)) {
                \Log::info('API Error: ' . curl_error($ch));
                //MagentoModuleLogs::create(['magento_module_id' => $magento_module_id, 'store_website_id' => $store_website_id, 'updated_by' => $updated_by, 'command' => $cmd, 'status' => 'Error', 'response' => curl_error($ch)]);
            }
            $response = json_decode($result);

            curl_close($ch);

            if(!empty($response->success)){

                $VirtualminDomain = new VirtualminDomain();
                $VirtualminDomain->name = $request->name;
                $VirtualminDomain->save();

                $virtualminDomainHistory = new VirtualminDomainHistory();
                $virtualminDomainHistory->Virtual_min_domain_id = $VirtualminDomain->id;
                $virtualminDomainHistory->user_id = Auth::user()->id;
                $virtualminDomainHistory->command = json_encode($parameters);
                $virtualminDomainHistory->error = $result['error'] ?? null;
                $virtualminDomainHistory->output = $result;
                $virtualminDomainHistory->status = $response->result->status;
                $virtualminDomainHistory->save();

                return response()->json(['code' => 200, 'message' => 'Domain Create successfully']);

            } else{

                VirtualminDomainLogs::create(['url' => $url, 'created_by' => Auth::user()->id, 'command' => json_encode($parameters), 'status' => 'Error', 'response' => $result]);

                return response()->json(['code' => 500, 'message' => $response->errors[0]->message]);
            }

            
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function adnsCreate(Request $request)
    {
        try {

            $this->validate($request, [
                'Virtual_min_domain_id' => 'required',
                'name' => 'required|alpha',
                'type' => 'required',
                'ip_address' => 'required',
            ]);

            $domain = VirtualminDomain::findOrFail($request->Virtual_min_domain_id);

            if(!empty($domain)){

                //$url = 'https://s10.theluxuryunlimited.com:5000/api/v1/clients/' . $client_id . '/commands';
                $url = getenv('CLOUDFLARE_DOMAIN_TOKEN_VERIFY_URL');

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                $headers = [];
                $headers[] = 'Authorization: Bearer ' . getenv('CLOUDFLARE_TOKEN');
                //$headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);
                $response = json_decode($result);
                curl_close($ch);

                if($response->success==1){
                    $url = getenv('CLOUDFLARE_GET_DOMAIN_ZONES_IDENTIFIER_URL');

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    $headers = [];
                    $headers[] = 'Authorization: Bearer ' . getenv('CLOUDFLARE_TOKEN');
                    //$headers[] = 'Content-Type: application/json';
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    $result = curl_exec($ch);
                    $response = json_decode($result);
                    curl_close($ch);

                    $zoneIdentifier = '';
                    if(!empty($response->result)){
                        foreach ($response->result as $key => $value) {
                            if($domain->name==$value->name){
                                $zoneIdentifier = $value->id;
                                break;
                            }
                        }
                    }

                    if(!empty($zoneIdentifier)){

                        $url = getenv('CLOUDFLARE_GET_DOMAIN_ZONES_IDENTIFIER_URL').'/'.$zoneIdentifier.'/dns_records';

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 1);

                        $parameters = [
                            'content' => $request->ip_address,
                            'name' => $request->name.'.'.$domain->name,
                            'proxied' => ($request->proxied==1) ? true : false,
                            'type' => $request->type,
                        ];

                        if($request->dns_type=='MX'){
                            $parameters['priority'] = intval($request->priority);
                        }

                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));

                        $headers = [];
                        $headers[] = 'Authorization: Bearer ' . getenv('CLOUDFLARE_TOKEN');
                        $headers[] = 'Content-Type: application/json';
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        $result = curl_exec($ch);

                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        //\Log::info('Virtualmin Domain CREATE A DNS API result: ' . $result);
                        //\Log::info('Virtualmin Domain CREATE A DNS API Error Number: ' . curl_errno($ch));
                        if (curl_errno($ch)) {
                            \Log::info('API Error: ' . curl_error($ch));
                        }
                        $response = json_decode($result);

                        curl_close($ch);

                        if(!empty($response->success)){

                            $VirtualminDomainDnsRecords = new VirtualminDomainDnsRecords();
                            $VirtualminDomainDnsRecords->Virtual_min_domain_id = $request->Virtual_min_domain_id;
                            $VirtualminDomainDnsRecords->identifier_id = $response->result->id;
                            $VirtualminDomainDnsRecords->dns_type = $request->dns_type;
                            $VirtualminDomainDnsRecords->type = $request->type;
                            $VirtualminDomainDnsRecords->priority = !empty($request->priority) ? $request->priority : NULL;
                            $VirtualminDomainDnsRecords->content = $request->ip_address;
                            $VirtualminDomainDnsRecords->name = $request->name;
                            $VirtualminDomainDnsRecords->domain_with_dns_name = $request->name.'.'.$domain->name;
                            $VirtualminDomainDnsRecords->proxied = ($request->proxied==1) ? true : false;
                            $VirtualminDomainDnsRecords->save();

                            $VirtualminDomainDnsRecordsHistory = new VirtualminDomainDnsRecordsHistory();
                            $VirtualminDomainDnsRecordsHistory->Virtual_min_domain_id = $VirtualminDomainDnsRecords->id;
                            $VirtualminDomainDnsRecordsHistory->user_id = Auth::user()->id;
                            $VirtualminDomainDnsRecordsHistory->command = json_encode($parameters);
                            $VirtualminDomainDnsRecordsHistory->error = $result['error'] ?? null;
                            $VirtualminDomainDnsRecordsHistory->output = $result;
                            $VirtualminDomainDnsRecordsHistory->save();

                            return response()->json(['code' => 200, 'message' => 'Domain DNS Create successfully']);

                        } else{

                            VirtualminDomainDnsLogs::create(['url' => $url, 'dns_type' => $request->dns_type, 'created_by' => Auth::user()->id, 'command' => json_encode($parameters), 'status' => 'Error', 'response' => $result]);

                            return response()->json(['code' => 500, 'message' => $response->errors[0]->message]);
                        }

                    } else {
                        return response()->json(['code' => 500, 'message' => 'Invalid zone identifier']);
                    }
                } else {
                    return response()->json(['code' => 500, 'message' => 'Invalid API Token']);
                }
            } else{
                return response()->json(['code' => 500, 'message' => 'Domain is not exists.']);
            }
            
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function enableDomain(Request $request, $id)
    {
        try {
            // Find the domain in the local database
            $domain = VirtualminDomain::findOrFail($id);

            // Enable the domain using Virtualmin API
            $response = $this->virtualminHelper->enableDomain($domain);

            // Maintain Log depends on the response in new Table

            // Update the domain status in your local database if needed
            $domain->update(['is_enabled' => true]);

            return redirect()->back()->with('success', 'Domain enabled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    public function disableDomain(Request $request, $id)
    {
        try {
            // Find the domain in the local database
            $domain = VirtualminDomain::findOrFail($id);

            // Disable the domain using Virtualmin API
            $response = $this->virtualminHelper->disableDomain($domain);

            // Maintain Log depends on the response in new Table

            // Update the domain status in your local database if needed
            $domain->update(['is_enabled' => false]);

            return redirect()->back()->with('success', 'Domain disabled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    public function deleteDomain(Request $request, $id)
    {
        try {
            // Find the domain in the local database
            $domain = VirtualminDomain::findOrFail($id);

            // Disable the domain using Virtualmin API
            $response = $this->virtualminHelper->deleteDomain($domain->name);

            // Maintain Log depends on the response in new Table

            // Update the domain status in your local database if needed
            $domain->delete();

            return redirect()->back()->with('success', 'Domain deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    public function updateDates()
    {
        if ($new = request('value')) {
            try {
                if ($virtualminDomain = VirtualminDomain::find(request('domain_id'))) {
                    if (request('column_name') == 'start_date') {
                        $virtualminDomain->start_date = $new;
                    }
                    if (request('column_name') == 'expiry_date') {
                        $virtualminDomain->expiry_date = $new;
                    }

                    $virtualminDomain->save();

                    return respJson(200, 'Successfully updated.');
                }
            } catch (\Exception $e) {
                return respJson(404, $e->getMessage());
            }

            return respJson(404, 'No data found.');
        }

        return respJson(400, 'Value is required.');
    }

    public function domainShow(Request $request)
    {
        $perPage = 5;

        $histories = VirtualminDomainHistory::with(['user'])
        ->where('Virtual_min_domain_id', $request->id)
        ->latest()
        ->paginate($perPage);

        $html = view('virtualmin-domain.domain-history-modal-html')->with('domainHistories', $histories)->render();

        return response()->json(['code' => 200, 'data' => $histories, 'html' => $html, 'message' => 'Content render']);
    }

    public function managecloudDomain(Request $request, $id)
    {   
        try {
            // Find the domain in the local database
            $domain = VirtualminDomain::findOrFail($id);

            $keyword = $request->get('keyword');
            $dns_type = $request->get('dns_type');

            // data search action
            $domainsDnsRecords = VirtualminDomainDnsRecords::with('VirtualminDomain')->where('Virtual_min_domain_id', $id)->whereNull('deleted_at')->orderBy('id', 'DESC');

            if (! empty($keyword) || isset($keyword)) {
                $domainsDnsRecords = $domainsDnsRecords->where('domain_with_dns_name', 'LIKE', '%' . $keyword . '%')->orWhere('content', 'LIKE', '%' . $keyword . '%');
            }

            if (! empty($dns_type) || isset($dns_type)) {
                $domainsDnsRecords = $domainsDnsRecords->where('dns_type', $dns_type);
            }

            if (! empty($proxied) || isset($proxied)) {
                $domainsDnsRecords = $domainsDnsRecords->where('proxied', $proxied);
            }

            $domainsDnsRecords = $domainsDnsRecords->paginate(10);

            if(!empty($domain)){

                //$url = 'https://s10.theluxuryunlimited.com:5000/api/v1/clients/' . $client_id . '/commands';
                $url = getenv('CLOUDFLARE_DOMAIN_TOKEN_VERIFY_URL');

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                $headers = [];
                $headers[] = 'Authorization: Bearer ' . getenv('CLOUDFLARE_TOKEN');
                //$headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);
                $response = json_decode($result);
                curl_close($ch);

                if($response->success==1){
                    $url = getenv('CLOUDFLARE_GET_DOMAIN_ZONES_IDENTIFIER_URL');

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    $headers = [];
                    $headers[] = 'Authorization: Bearer ' . getenv('CLOUDFLARE_TOKEN');
                    //$headers[] = 'Content-Type: application/json';
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    $result = curl_exec($ch);
                    $response = json_decode($result);
                    curl_close($ch);

                    $zoneIdentifier = '';
                    if(!empty($response->result)){
                        foreach ($response->result as $key => $value) {
                            if($domain->name==$value->name){
                                $zoneIdentifier = $value->id;
                                break;
                            }
                        }
                    }

                    if(!empty($zoneIdentifier)){

                        $url = getenv('CLOUDFLARE_GET_DOMAIN_ZONES_IDENTIFIER_URL').'/'.$zoneIdentifier.'/settings/rocket_loader';

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

                        $headers = [];
                        $headers[] = 'X-Auth-Email: ' . getenv('CLOUDFLARE_EMAIL');
                        $headers[] = 'X-Auth-Key: ' . getenv('CLOUDFLARE_AUTH_KEY');
                        $headers[] = 'Content-Type: application/json';
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        $result = curl_exec($ch);

                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        if (curl_errno($ch)) {
                            \Log::info('API Error: ' . curl_error($ch));
                        }
                        $response = json_decode($result);

                        curl_close($ch);

                        if(!empty($response->success)){ 
                            $domain->rocket_loader = $response->result->value;
                            $domain->save();  
                        }
                    }
                }
            }

            return view('virtualmin-domain.managecloud', ['domainsDnsRecords' => $domainsDnsRecords, 'domain' => $domain]);

        } catch (\Exception $e) {
            return Redirect::route('virtualmin-domain.index')->with('error', $e->getMessage());
        }
    }

    public function deletednsDomain(Request $request)
    {   

        try {

            $domainsDnsRecords = VirtualminDomainDnsRecords::with('VirtualminDomain')->where('id', $request->id)->first();

            if(!empty($domainsDnsRecords)){

                //$url = 'https://s10.theluxuryunlimited.com:5000/api/v1/clients/' . $client_id . '/commands';
                $url = getenv('CLOUDFLARE_DOMAIN_TOKEN_VERIFY_URL');

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                $headers = [];
                $headers[] = 'Authorization: Bearer ' . getenv('CLOUDFLARE_TOKEN');
                //$headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);
                $response = json_decode($result);
                curl_close($ch);

                if($response->success==1){
                    $url = getenv('CLOUDFLARE_GET_DOMAIN_ZONES_IDENTIFIER_URL');

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    $headers = [];
                    $headers[] = 'Authorization: Bearer ' . getenv('CLOUDFLARE_TOKEN');
                    //$headers[] = 'Content-Type: application/json';
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    $result = curl_exec($ch);
                    $response = json_decode($result);
                    curl_close($ch);

                    $zoneIdentifier = '';
                    if(!empty($response->result)){
                        foreach ($response->result as $key => $value) {
                            if($domainsDnsRecords->VirtualminDomain->name==$value->name){
                                $zoneIdentifier = $value->id;
                                break;
                            }
                        }
                    }

                    if(!empty($zoneIdentifier)){

                        $url = getenv('CLOUDFLARE_GET_DOMAIN_ZONES_IDENTIFIER_URL').'/'.$zoneIdentifier.'/dns_records/'.$domainsDnsRecords->identifier_id;

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

                        $headers = [];
                        $headers[] = 'Authorization: Bearer ' . getenv('CLOUDFLARE_TOKEN');
                        $headers[] = 'Content-Type: application/json';
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        $result = curl_exec($ch);

                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        //\Log::info('Virtualmin Domain CREATE A DNS API result: ' . $result);
                        //\Log::info('Virtualmin Domain CREATE A DNS API Error Number: ' . curl_errno($ch));
                        if (curl_errno($ch)) {
                            \Log::info('API Error: ' . curl_error($ch));
                        }
                        $response = json_decode($result);

                        curl_close($ch);

                        if(!empty($response->success)){

                            $dnsdomain = VirtualminDomainDnsRecords::findOrFail($request->id);

                            // Update the domain status in your local database if needed
                            $dnsdomain->delete();

                            return response()->json(['code' => 200, 'message' => 'Domain DNS Create successfully']);

                        } else{

                            VirtualminDomainDnsLogs::create(['url' => $url, 'created_by' => Auth::user()->id, 'status' => 'Error', 'response' => $result]);

                            return response()->json(['code' => 500, 'message' => $response->errors[0]->message]);
                        }

                    } else {
                        return response()->json(['code' => 500, 'message' => 'Invalid zone identifier']);
                    }
                } else {
                    return response()->json(['code' => 500, 'message' => 'Invalid API Token']);
                }
            } else{
                return response()->json(['code' => 500, 'message' => 'Domain is not exists.']);
            }
            
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    public function domainShowDns(Request $request)
    {
        $perPage = 5;

        $histories = VirtualminDomainDnsRecordsHistory::with(['user'])
        ->where('Virtual_min_domain_id', $request->id)
        ->latest()
        ->paginate($perPage);

        $html = view('virtualmin-domain.dnsdomain-history-modal-html')->with('domainHistories', $histories)->render();

        return response()->json(['code' => 200, 'data' => $histories, 'html' => $html, 'message' => 'Content render']);
    }

    public function dnsedit(Request $request)
    {

        $id = $request->get('id', 0);
        $VirtualminDomainDnsRecords = VirtualminDomainDnsRecords::where('id', $id)->first();
        if ($VirtualminDomainDnsRecords) {
            if ($VirtualminDomainDnsRecords->dns_type=='A') {
                return view('virtualmin-domain.edit', compact('VirtualminDomainDnsRecords'));
            } else if($VirtualminDomainDnsRecords->dns_type=='MX'){
                return view('virtualmin-domain.editmx', compact('VirtualminDomainDnsRecords'));
            } else if($VirtualminDomainDnsRecords->dns_type=='TXT'){
                return view('virtualmin-domain.edittxt', compact('VirtualminDomainDnsRecords'));
            }
        }

        return 'Page Note Not Found';
    }

    public function dnsupdate(Request $request)
    {   

        try {

            $this->validate($request, [
                'Virtual_min_domain_id' => 'required',
                'name' => 'required|alpha',
                'type' => 'required',
                'ip_address' => 'required',
                'id' => 'required',
            ]);

            $domain = VirtualminDomain::findOrFail($request->Virtual_min_domain_id);

            if(!empty($domain)){

                $domainsDnsRecords = VirtualminDomainDnsRecords::findOrFail($request->id);

                if(!empty($domainsDnsRecords)){

                    //$url = 'https://s10.theluxuryunlimited.com:5000/api/v1/clients/' . $client_id . '/commands';
                    $url = getenv('CLOUDFLARE_DOMAIN_TOKEN_VERIFY_URL');

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    $headers = [];
                    $headers[] = 'Authorization: Bearer ' . getenv('CLOUDFLARE_TOKEN');
                    //$headers[] = 'Content-Type: application/json';
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    $result = curl_exec($ch);
                    $response = json_decode($result);
                    curl_close($ch);

                    if($response->success==1){
                        $url = getenv('CLOUDFLARE_GET_DOMAIN_ZONES_IDENTIFIER_URL');

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 0);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                        $headers = [];
                        $headers[] = 'Authorization: Bearer ' . getenv('CLOUDFLARE_TOKEN');
                        //$headers[] = 'Content-Type: application/json';
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                        $result = curl_exec($ch);
                        $response = json_decode($result);
                        curl_close($ch);

                        $zoneIdentifier = '';
                        if(!empty($response->result)){
                            foreach ($response->result as $key => $value) {
                                if($domain->name==$value->name){
                                    $zoneIdentifier = $value->id;
                                    break;
                                }
                            }
                        }

                        if(!empty($zoneIdentifier)){

                            $url = getenv('CLOUDFLARE_GET_DOMAIN_ZONES_IDENTIFIER_URL').'/'.$zoneIdentifier.'/dns_records/'.$domainsDnsRecords->identifier_id;

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

                            $parameters = [
                                'content' => $request->ip_address,
                                'name' => $request->name.'.'.$domain->name,
                                'proxied' => ($request->proxied==1) ? true : false,
                                'type' => $request->type,
                            ];

                            if($request->dns_type=='MX'){
                                $parameters['priority'] = intval($request->priority);
                            }

                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));

                            $headers = [];
                            $headers[] = 'Authorization: Bearer ' . getenv('CLOUDFLARE_TOKEN');
                            $headers[] = 'Content-Type: application/json';
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            $result = curl_exec($ch);

                            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                            //\Log::info('Virtualmin Domain CREATE A DNS API result: ' . $result);
                            //\Log::info('Virtualmin Domain CREATE A DNS API Error Number: ' . curl_errno($ch));
                            if (curl_errno($ch)) {
                                \Log::info('API Error: ' . curl_error($ch));
                            }
                            $response = json_decode($result);

                            curl_close($ch);

                            if(!empty($response->success)){

                                $domainsDnsRecords->Virtual_min_domain_id = $request->Virtual_min_domain_id;
                                $domainsDnsRecords->identifier_id = $response->result->id;
                                $domainsDnsRecords->dns_type = $request->dns_type;
                                $domainsDnsRecords->type = $request->type;
                                $domainsDnsRecords->priority = !empty($request->priority) ? $request->priority : NULL;
                                $domainsDnsRecords->content = $request->ip_address;
                                $domainsDnsRecords->name = $request->name;
                                $domainsDnsRecords->domain_with_dns_name = $request->name.'.'.$domain->name;
                                $domainsDnsRecords->proxied = ($request->proxied==1) ? true : false;
                                $domainsDnsRecords->save();

                                $VirtualminDomainDnsRecordsHistory = new VirtualminDomainDnsRecordsHistory();
                                $VirtualminDomainDnsRecordsHistory->Virtual_min_domain_id = $request->id;
                                $VirtualminDomainDnsRecordsHistory->user_id = Auth::user()->id;
                                $VirtualminDomainDnsRecordsHistory->command = json_encode($parameters);
                                $VirtualminDomainDnsRecordsHistory->error = $result['error'] ?? null;
                                $VirtualminDomainDnsRecordsHistory->output = $result;
                                $VirtualminDomainDnsRecordsHistory->save();

                                return response()->json(['code' => 200, 'message' => 'Domain DNS updated successfully']);

                            } else{

                                VirtualminDomainDnsLogs::create(['url' => $url, 'dns_type' => $request->dns_type, 'created_by' => Auth::user()->id, 'command' => json_encode($parameters), 'status' => 'Error', 'response' => $result]);

                                return response()->json(['code' => 500, 'message' => $response->errors[0]->message]);
                            }

                        } else {
                            return response()->json(['code' => 500, 'message' => 'Invalid zone identifier']);
                        }
                    } else {
                        return response()->json(['code' => 500, 'message' => 'Invalid API Token']);
                    }
                } else{
                    return response()->json(['code' => 500, 'message' => 'Domain DNS is not exists.']);
                }
            } else{
                return response()->json(['code' => 500, 'message' => 'Domain is not exists.']);
            }
            
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
        $id = $request->get('id', 0);
        $pageNotes = \App\PageNotes::where('id', $id)->first();
        if ($pageNotes) {
            $pageNotes->user_id = \Auth::user()->id;
            $pageNotes->category_id = $request->get('category_id', null);
            $pageNotes->note = $request->get('note', '');

            if ($pageNotes->save()) {
                $list = $pageNotes->getAttributes();
                $list['name'] = $pageNotes->user->name;
                $list['category_name'] = ! empty($pageNotes->pageNotesCategories->name) ? $pageNotes->pageNotesCategories->name : '';

                return response()->json(['code' => 1, 'notes' => $list]);
            }
        }

        return response()->json(['code' => -1, 'message' => 'oops, something went wrong!!']);
    }

    public function domainstatusupdate(Request $request)
    {   
        try {

            $this->validate($request, [
                'id' => 'required',
                'value' => 'required',
            ]);

            $domain = VirtualminDomain::findOrFail($request->id);

            if(!empty($domain)){

                //$url = 'https://s10.theluxuryunlimited.com:5000/api/v1/clients/' . $client_id . '/commands';
                $url = getenv('CLOUDFLARE_DOMAIN_TOKEN_VERIFY_URL');

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                $headers = [];
                $headers[] = 'Authorization: Bearer ' . getenv('CLOUDFLARE_TOKEN');
                //$headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);
                $response = json_decode($result);
                curl_close($ch);

                if($response->success==1){
                    $url = getenv('CLOUDFLARE_GET_DOMAIN_ZONES_IDENTIFIER_URL');

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    $headers = [];
                    $headers[] = 'Authorization: Bearer ' . getenv('CLOUDFLARE_TOKEN');
                    //$headers[] = 'Content-Type: application/json';
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    $result = curl_exec($ch);
                    $response = json_decode($result);
                    curl_close($ch);

                    $zoneIdentifier = '';
                    if(!empty($response->result)){
                        foreach ($response->result as $key => $value) {
                            if($domain->name==$value->name){
                                $zoneIdentifier = $value->id;
                                break;
                            }
                        }
                    }

                    if(!empty($zoneIdentifier)){

                        $url = getenv('CLOUDFLARE_GET_DOMAIN_ZONES_IDENTIFIER_URL').'/'.$zoneIdentifier.'/settings/rocket_loader';

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 1);

                        $parameters = [
                            'value' => $request->value,
                        ];

                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));

                        $headers = [];
                        $headers[] = 'X-Auth-Email: ' . getenv('CLOUDFLARE_EMAIL');
                        $headers[] = 'X-Auth-Key: ' . getenv('CLOUDFLARE_AUTH_KEY');
                        $headers[] = 'Content-Type: application/json';
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        $result = curl_exec($ch);

                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        if (curl_errno($ch)) {
                            \Log::info('API Error: ' . curl_error($ch));
                        }
                        return $response = json_decode($result);

                        curl_close($ch);

                        if(!empty($response->success)){

                            $domain->rocket_loader = $response->result->value;
                            $domain->save(); 

                            return response()->json(['code' => 200, 'message' => 'Domain DNS updated successfully']);

                        } else{
                            return response()->json(['code' => 500, 'message' => $response->errors[0]->message]);
                        }

                    } else {
                        return response()->json(['code' => 500, 'message' => 'Invalid zone identifier']);
                    }
                } else {
                    return response()->json(['code' => 500, 'message' => 'Invalid API Token']);
                }
            } else{
                return response()->json(['code' => 500, 'message' => 'Domain is not exists.']);
            }
            
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return response()->json(['code' => 500, 'message' => $msg]);
        }
        $id = $request->get('id', 0);
        $pageNotes = \App\PageNotes::where('id', $id)->first();
        if ($pageNotes) {
            $pageNotes->user_id = \Auth::user()->id;
            $pageNotes->category_id = $request->get('category_id', null);
            $pageNotes->note = $request->get('note', '');

            if ($pageNotes->save()) {
                $list = $pageNotes->getAttributes();
                $list['name'] = $pageNotes->user->name;
                $list['category_name'] = ! empty($pageNotes->pageNotesCategories->name) ? $pageNotes->pageNotesCategories->name : '';

                return response()->json(['code' => 1, 'notes' => $list]);
            }
        }

        return response()->json(['code' => -1, 'message' => 'oops, something went wrong!!']);
    }
}
