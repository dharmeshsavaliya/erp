<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Auth;
use DB;
use App\CustomerCharity;
use App\VendorCharity;
use App\Setting;
use App\User;
use App\Category;
use App\Brand;
use App\Product;
use App\VendorCategory;

class CustomerCharityController extends Controller
{
    CONST DEFAULT_FOR = 2; //For Vendor

    public function index(Request $request){ 
     	
        $term = $request->term ?? '';
        $sortByClause = '';
        $orderby = 'DESC';

        if ($request->orderby == '') {
        $orderby = 'ASC';
        } 
        if ($request->sortby == 'id') {
        $sortByClause = "id $orderby,";
        }
        $whereArchived = ' `deleted_at` IS NULL ';

        if ($request->get('with_archived') == 'on') {
        $whereArchived = '  `deleted_at` IS NOT NULL  ';
        }

        $isAdmin = Auth::user()->isAdmin();
        if($isAdmin) {
        $permittedCategories = [];
        }else {
        $permittedCategories = Auth::user()->vendorCategoryPermission->pluck('id')->all() + [0];
        }
        //getting request 
        if ($request->term || $request->name || $request->id || $request->category || $request->email || $request->phone ||
            $request->address || $request->email || $request->communication_history || $request->status != null || $request->updated_by != null
        ) {
        //Query Initiate
        if($isAdmin) {
            $query  = CustomerCharity::query();
        }else{
            $imp_permi = implode(",", $permittedCategories);
            if($imp_permi != 0)
            {
            $query  = CustomerCharity::whereIn('category_id',$permittedCategories);  
            }
            else
            {
            $query  = CustomerCharity::query();
            }
            
        }

        if (request('term') != null) {
            $query->where('name', 'LIKE', "%{$request->term}%");
        }

        //if Id is not null 
        if (request('id') != null) {
            $query->where('id', request('id', 0));
        }

        //If name is not null 
        if (request('name') != null) {
            $query->where('name', 'LIKE', '%' . request('name') . '%');
        }


        //if addess is not null
        if (request('address') != null) {
            $query->where('address', 'LIKE', '%' . request('address') . '%');
        }

        //if email is not null 
        if (request('email') != null) {
            $query->where('email', 'LIKE', '%' . request('email') . '%');
        }


        //if phone is not null
        if (request('phone') != null) {
            $query->where('phone', 'LIKE', '%' . request('phone') . '%');
        }
        $status = request('status');
        if ($status != null && !request('with_archived')) {
            $query = $query->where(function ($q) use ($status) {
                $q->orWhere('status', $status);
            });
            // $query->orWhere('status', $status);
        }

        if (request('updated_by') != null && !request('with_archived')) {
            $query = $query->where(function ($q) use ($status) {
            $q->orWhere('updated_by', request('updated_by'));
            });
            // $query->orWhere('updated_by', request('updated_by'));
        }

        //if category is not nyll
        if (request('category') != null) {
            $query->whereHas('category', function ($qu) use ($request) {
            $qu->where('category_id', '=', request('category'));
            });
        }
    //if email is not nyll
        if (request('email') != null) {
            $query->where('email', 'like', '%'.request('email').'%');

        }



        if (request('communication_history') != null && !request('with_archived')) {
            $communication_history = request('communication_history');
            $query->orWhereRaw("customer_charities.id in (select vendor_id from chat_messages where vendor_id is not null and message like '%" . $communication_history . "%')");
        }

    

        if ($request->with_archived != null && $request->with_archived != '') {
            $pagination = Setting::get('pagination');
            if (request()->get('select_all') == 'true') {
            $pagination = $customer_charities->count();
        }
        
        $totalVendor = $query->orderby('name', 'asc')->whereNotNull('deleted_at')->count();
        $customer_charities = $query->orderby('name', 'asc')->whereNotNull('deleted_at')->paginate($pagination);
        } else {
            $pagination = Setting::get('pagination');
            if (request()->get('select_all') == 'true') {
            $pagination = $customer_charities->count();
            }
                $totalVendor = $query->orderby('name', 'asc')->count();
            $customer_charities = $query->orderby('name', 'asc')->paginate($pagination);
        }
        } else {
        if($isAdmin) {
            $permittedCategories = "";
        }else{
            if(empty($permittedCategories)) {
            $permittedCategories = [0];
            }
            $permittedCategories_all = implode(',',$permittedCategories);
            if($permittedCategories_all == 0)
            {
            $permittedCategories = ''; 
            }
            else
            {
            $permittedCategories = 'and customer_charities.category_id in (' .implode(',',$permittedCategories). ')';  
            }

            
        }
        $customer_charities = DB::select('
                    SELECT *,
                    (SELECT mm1.message FROM chat_messages mm1 WHERE mm1.id = message_id) as message,
                    (SELECT mm2.status FROM chat_messages mm2 WHERE mm2.id = message_id) as message_status,
                    (SELECT mm3.created_at FROM chat_messages mm3 WHERE mm3.id = message_id) as message_created_at 
                    FROM (SELECT customer_charities.id,customer_charities.product_id, customer_charities.frequency, customer_charities.is_blocked ,customer_charities.reminder_message, customer_charities.category_id, customer_charities.name, customer_charities.phone, customer_charities.email, customer_charities.address, customer_charities.social_handle, customer_charities.website, customer_charities.login, customer_charities.password, customer_charities.gst, customer_charities.account_name, customer_charities.account_iban, customer_charities.account_swift,
                        customer_charities.created_at,customer_charities.updated_at,
                        customer_charities.updated_by,
                        customer_charities.reminder_from,
                        customer_charities.reminder_last_reply,
                        customer_charities.status,
                        category_name,
                    chat_messages.message_id 
                    FROM customer_charities 

                    LEFT JOIN (SELECT MAX(id) as message_id, vendor_id FROM chat_messages GROUP BY vendor_id ORDER BY created_at DESC) AS chat_messages
                    ON customer_charities.id = chat_messages.vendor_id

                    LEFT JOIN (SELECT id, title AS category_name FROM vendor_categories) AS vendor_categories
                    ON customer_charities.category_id = vendor_categories.id WHERE ' . $whereArchived . '
                    )
                    AS customer_charities
 
                    WHERE (name LIKE "%' . $term . '%" OR
                    phone LIKE "%' . $term . '%" OR
                    email LIKE "%' . $term . '%" OR
                    address LIKE "%' . $term . '%" OR
                    social_handle LIKE "%' . $term . '%" OR
                    category_id IN (SELECT id FROM vendor_categories WHERE title LIKE "%' . $term . '%") OR
                    id IN (SELECT model_id FROM agents WHERE model_type LIKE "%Vendor%" AND (name LIKE "%' . $term . '%" OR phone LIKE "%' . $term . '%" OR email LIKE "%' . $term . '%"))) ' .$permittedCategories. '
                    ORDER BY ' . $sortByClause . ' message_created_at DESC;
                ');

        // dd($customer_charities);

            $totalVendor = count($customer_charities);

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = Setting::get('pagination');
        if (request()->get('select_all') == 'true') {
            $perPage = count($customer_charities);
            $currentPage = 1;
        }

        if (!is_numeric($perPage)) {
            $perPage = 2;
        }


        $currentItems = array_slice($customer_charities, $perPage * ($currentPage - 1), $perPage);

        $customer_charities = new LengthAwarePaginator($currentItems, count($customer_charities), $perPage, $currentPage, [
            'path'  => LengthAwarePaginator::resolveCurrentPath()
        ]);
        }


        $vendor_categories = VendorCategory::all();


        $users = User::all();

        $replies = \App\Reply::where("model", "Vendor")->whereNull("deleted_at")->pluck("reply", "id")->toArray();

        /* if ($request->ajax()) {
        return response()->json([
            'tbody' => view('customer_charities.partials.data', compact('customer_charities', 'replies'))->render(),
            'links' => (string) $customer_charities->render()
        ], 200);
        } */

        $updatedProducts = CustomerCharity::join("users as u","u.id","customer_charities.updated_by")
        ->groupBy("customer_charities.updated_by")
        ->select([\DB::raw("count(u.id) as total_records"),"u.name"])
        ->get();

        return view('vendors.charity', [
        'vendors' => $customer_charities,
        'vendor_categories' => $vendor_categories,
        'term'    => $term,
        'orderby'    => $orderby,
        'users' => $users,
        'replies' => $replies,
        'updatedProducts' => $updatedProducts,
        'totalVendor' => $totalVendor,
        ]);

    }

    public function store(Request $request, $id = null){

        $this->validate($request, [
            'category_id'   => 'sometimes|nullable|numeric',
            'name'          => 'required|string|max:255',
            'address'       => 'sometimes|nullable|string',
            'phone'         => 'required|nullable|numeric',
            'email'         => 'sometimes|nullable|email',
            'social_handle' => 'sometimes|nullable',
            'website'       => 'sometimes|nullable',
            'login'         => 'sometimes|nullable',
            'password'      => 'sometimes|nullable',
            'gst'           => 'sometimes|nullable|max:255',
            'account_name'  => 'sometimes|nullable|max:255',
            'account_iban'  => 'sometimes|nullable|max:255',
            'account_swift' => 'sometimes|nullable|max:255',
            'frequency_of_payment'   => 'sometimes|nullable|max:255',
            'bank_name'   => 'sometimes|nullable|max:255',
            'bank_address'   => 'sometimes|nullable|max:255',
            'city'   => 'sometimes|nullable|max:255',
            'country'   => 'sometimes|nullable|max:255',
            'ifsc_code'   => 'sometimes|nullable|max:255',
            'remark'   => 'sometimes|nullable|max:255',
      ]);
  
      $source = $request->get("source","");
  
      $data = $request->except(['_token', 'create_user']);
      if(empty($data["whatsapp_number"]))  {
          //$data["whatsapp_number"] = config("apiwha.instances")[0]['number'];
          //get default whatsapp number for vendor from whatsapp config
          $task_info = DB::table('whatsapp_configs')
                      ->select('*')
                      ->whereRaw("find_in_set(".self::DEFAULT_FOR.",default_for)")
                      ->first();
      if(isset($task_info->number) && $task_info->number!=null){
      $data["whatsapp_number"] = $task_info->number;
      }
      }
  
      if(empty($data["default_phone"]))  {
        $data["default_phone"] = $data["phone"];
      }
  
      if(!empty($source)) {
         $data["status"] = 0;
      }  
  
  
      if($id == null){
          $charity = CustomerCharity::create($data); 
          $charity_category = Category::where('title', 'charity')->first();
          $charity_brand = Brand::where('name', 'charity')->first();
          $product = new Product(); 
          $product->sku = '';
          $product->name = $charity->name;
          $product->brand = $charity_brand->id;
          $product->category = $charity_category->id;
          $product->save(); 
          CustomerCharity::where('id', $charity->id)->update([
              'product_id' => $product->id
          ]);
      }else{
        CustomerCharity::where('id', $id)->update($data);
      }
  
      if ($request->create_user == 'on') {
        if ($request->email != null) {
          $userEmail = User::where('email', $request->email)->first();
        } else {
          $userEmail = null;
        }
        $userPhone = User::where('phone', $request->phone)->first();
        if ($userEmail == null && $userPhone == null) {
          $user = new User;
          $user->name = str_replace(' ', '_', $request->name);
          if ($request->email == null) {
            $email = str_replace(' ', '_', $request->name) . '@solo.com';
          } else {
            // $email = explode('@', $request->email);
            // $email = $email[0] . '@solo.com';
            $email = $request->email;
          }
          $password = str_random(10);
          $user->email = $email;
          $user->password = Hash::make($password);
          $user->phone = $request->phone;
  
          // check the default whatsapp no and store it
          $whpno = \DB::table('whatsapp_configs')
              ->select('*')
              ->whereRaw("find_in_set(4,default_for)")
              ->first();
          if($whpno)     {
            $user->whatsapp_number = $whpno->number;
          }
  
          $user->save();
          $role = Role::where('name', 'Developer')->first();
          $user->roles()->sync($role->id);
          $message = 'We have created an account for you on our ERP. You can login using the following details: url: https://erp.theluxuryunlimited.com/ username: ' . $email . ' password:  ' . $password . '';
          app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($request->phone,$user->whatsapp_number, $message);
        } else {
          if(!empty($source)) {
             return redirect()->back()->withErrors('Charity Created , couldnt create User, Email or Phone Already Exist');
          }
          return redirect()->route('customer.charity')->withErrors('Charity Created , couldnt create User, Email or Phone Already Exist');
        }
      }
  
      $isInvitedOnGithub = false;
      if ($request->create_user_github == 'on' && isset($request->email)) {
        //has requested for github invitation
        $isInvitedOnGithub = $this->sendGithubInvitaion($request->email);
      }
  
      $isInvitedOnHubstaff = false;
      if ($request->create_user_hubstaff == 'on' && isset($request->email)) {
        //has requested hubstaff invitation
        $isInvitedOnHubstaff = $this->sendHubstaffInvitation($request->email);
      }
  
      if(!empty($source)) {
         return redirect()->back()->withSuccess('You have successfully saved a charity!');
      }
  
      return redirect()->route('customer.charity')->withSuccess('You have successfully saved a charity!');
    }

    public function delete($id){
        $customer_charity = CustomerCharity::find($id);
        $customer_charity->delete();

    return redirect()->route('customer.charity')->withSuccess('You have successfully deleted a charity');

    }
}