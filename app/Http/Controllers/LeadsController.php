<?php

namespace App\Http\Controllers;

use App\Category;
use App\Notification;
use App\Leads;
use App\Status;
use App\Setting;
use App\User;
use App\Brand;
use App\Product;
use App\Message;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers;


class LeadsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //

        if($request->input('orderby') == '')
            $orderby = 'desc';
        else
            $orderby = 'asc';

        switch ($request->input('sortby')) {
            case 'client_name':
                $sortby = 'client_name';
                break;
            case 'city':
                $sortby = 'city';
                break;
            case 'assigned_user':
                 $sortby = 'assigned_user';
                break;
            case 'rating':
                 $sortby = 'rating';
                break;
            case 'communication':
                 $sortby = 'communication';
                break;
            case 'status':
                 $sortby = 'status';
                break;
            case 'created_at':
                 $sortby = 'created_at';
                break;
            default :
                 $sortby = 'id';
        }

        $term = $request->input('term');
	    $leads = ((new Leads())->newQuery());
      // $leads2 = Leads::find(262)->toArray();
      // dd($leads2);
      if ($request->brand[0] != null) {
        $implode = implode(',', $request->brand);
        $leads->where('multi_brand', 'LIKE', "%$implode%");

        $brand = $request->brand;
      }

      if ($request->rating[0] != null) {
        $leads->whereIn('rating', $request->rating);

        $rating = $request->rating;
      }

      // if ($request->input('sortby') == 'communication') {
      //   $leads = Leads::with('messages')->select('messages.body')->orderBy('message:body');
      // } else
      if ( helpers::getadminorsupervisor() ) {
        if ($sortby != 'communication') {
          $leads = $leads->orderBy( $sortby, $orderby );
        }
	    } else if ( helpers::getmessagingrole() ) {
		    $leads = $leads->oldest();
	    } else {
		    $leads = $leads->oldest()->where( 'assigned_user', '=', Auth::id() );
	    }
      //
      // if ($request->brand[0] != null) {
      //   $leads->whereIn('brand', $request->brand);
      // }
      //
      // if ($request->rating[0] != null) {
      //   $leads->whereIn('rating', $request->rating);
      // }

	    if(!empty($term)){
	    	$leads = $leads->where(function ($query) use ($term){
	    		return $query
					    ->orWhere('client_name','like','%'.$term.'%')
					    ->orWhere('id','like','%'.$term.'%')
					    ->orWhere('contactno',$term)
					    ->orWhere('city','like','%'.$term.'%')
					    ->orWhere('instahandler',$term)
					    ->orWhere('assigned_user',Helpers::getUserIdByName($term))
					    ->orWhere('assigned_user',Helpers::getUserIdByName($term))
					    ->orWhere('userid',Helpers::getUserIdByName($term))
					    ->orWhere('status',(new Status())->getIDCaseInsensitive($term))
			    ;
		    });
	    }

      $leads_array = $leads->whereNull( 'deleted_at' )->paginate( Setting::get( 'pagination' ) )->toArray();
      // dd($leads_array);


      if ($sortby == 'communication') {
        if ($orderby == 'asc') {
          $leads_array['data'] = array_values(array_sort($leads_array['data'], function ($value) {
              return $value['communication']['body'];
          }));

          $leads_array['data'] = array_reverse($leads_array['data']);
        } else {
          $leads_array['data'] = array_values(array_sort($leads_array['data'], function ($value) {
              return $value['communication']['body'];
          }));
        }

      }
      // dd($leads_array);
      $leads = $leads->whereNull( 'deleted_at' )->paginate( Setting::get( 'pagination' ) );
      // dd($leads);
      return view('leads.index',compact('leads', 'leads_array','term', 'orderby', 'brand', 'rating'))
                ->with('i', (request()->input('page', 1) - 1) * 10);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $status = New status;
        $data['status'] = $status->all();
        $users = User::oldest()->get()->toArray();
        $data['users']  = $users;
        $brands = Brand::oldest()->get()->toArray();
        $data['brands']  = $brands;
        $data['products_array'] = [];

	    $data['category_select'] = Category::attr(['name' => 'multi_category[]','class' => 'form-control','id' => 'multi_category'])
	                                       ->selected()
	                                       ->renderAsMultiple();

        return view('leads.create',compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $leads = $this->validate(request(), [
          'client_name' => 'required',
//          'contactno' => 'required',
//          'city' => 'required',
          'instahandler' => '',
          'rating' => 'required',
          'status' => 'required',
          'solophone' => '',
          'comments' => '',
          'userid'=>'',
          'address'=>'',
          'multi_brand'=>'',
          'email' => '',
          'source'=>'',
          'assigned_user' => '',
          'selected_product',
          'size',
          'leadsourcetxt',
          'created_at'  => 'required|date_format:"Y-m-d H:i"'
        ]);

        $data = $request->except( '_token');
        $data['userid'] = Auth::id();
        $data['selected_product'] = json_encode( $request->input( 'selected_product' ) );


        $data['multi_brand'] = json_encode( $request->input( 'multi_brand' ) );
        $data['multi_category'] = json_encode( $request->input( 'multi_category' ) );

        $lead = Leads::create($data);


        if(!empty($request->input('assigned_user'))){

	        NotificationQueueController::createNewNotification([
		        'type' => 'button',
		        'message' => 'Client Name: '.$data['client_name'],
            // 'timestamps' => ['+0 minutes','+15 minutes','+30 minutes','+45 minutes'],
		        'timestamps' => ['+0 minutes'],
		        'model_type' => Leads::class,
		        'model_id' =>  $lead->id,
		        'user_id' => Auth::id(),
		        'sent_to' => $request->input('assigned_user'),
		        'role' => '',
	        ]);
        }
        else{

	        NotificationQueueController::createNewNotification([
		        'type' => 'button',
		        'message' => 'Client Name: '.$data['client_name'],
            // 'timestamps' => ['+0 minutes','+15 minutes','+30 minutes','+45 minutes'],
		        'timestamps' => ['+0 minutes'],
		        'model_type' => Leads::class,
		        'model_id' =>  $lead->id,
		        'user_id' => Auth::id(),
		        'sent_to' => '',
		        'role' => 'crm',
	        ]);
        }


	    NotificationQueueController::createNewNotification([
		    'message' => 'Client Name: '.$data['client_name'],
		    'timestamps' => ['+45 minutes'],
		    'model_type' => Leads::class,
		    'model_id' =>  $lead->id,
		    'user_id' => Auth::id(),
		    'sent_to' => Auth::id(),
		    'role' => '',
	    ]);

	    NotificationQueueController::createNewNotification([
		    'message' => 'Client Name: '.$data['client_name'],
		    'timestamps' => ['+0 minutes'],
		    'model_type' => Leads::class,
		    'model_id' =>  $lead->id,
		    'user_id' => Auth::id(),
		    'sent_to' => '',
		    'role' => 'Admin',
	    ]);

	    // NotificationQueueController::createNewNotification([
		  //   'message' => 'Client Name: '.$data['client_name'],
		  //   'timestamps' => ['+0 minutes'],
		  //   'model_type' => Leads::class,
		  //   'model_id' =>  $lead->id,
		  //   'user_id' => Auth::id(),
		  //   'sent_to' => '',
		  //   'role' => 'Supervisors',
	    // ]);


        return redirect()->route('leads.create')
                         ->with('success','Lead created successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $leads = Leads::find($id);
        $status = New status;
        $data = $status->all();
        $leads['statusid'] = $data;
        $users = User::all()->toArray();
        $leads['users']  = $users;
        $brands = Brand::all()->toArray();
        $leads['brands']  = $brands;
        $leads['selected_products_array'] = json_decode( $leads['selected_product'] );
        $leads['products_array'] = [];

	    $leads['multi_brand'] = is_array(json_decode($leads['multi_brand'],true) ) ? json_decode($leads['multi_brand'],true) : [];
	    $selected_categories = is_array(json_decode( $leads['multi_category'],true)) ? json_decode( $leads['multi_category'] ,true) : [] ;
	    $data['category_select'] = Category::attr(['name' => 'multi_category[]','class' => 'form-control','id' => 'multi_category', 'disabled' => ''])
	                                       ->selected($selected_categories)
	                                       ->renderAsMultiple();
	    $leads['remark'] = $leads->remark;

        $messages = Message::all()->where('moduleid','=', $leads['id'])->where('moduletype','=', 'leads')->sortByDesc("created_at")->take(2)->toArray();
        $leads['messages'] = $messages;

        if ( ! empty( $leads['selected_products_array']  ) ) {
            foreach ( $leads['selected_products_array']  as $product_id ) {
                $skuOrName                             = $this->getProductNameSkuById( $product_id );

               $data['products_array'][$product_id] = $skuOrName;
            }
        }
       // var_dump($role);
        return view('leads.show',compact('leads','id','data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $leads = Leads::find($id);
        $status = New status;
        $data = $status->all();
        $leads['statusid'] = $data;
        $users = User::all()->toArray();
        $leads['users']  = $users;
        $brands = Brand::all()->toArray();
        $leads['brands']  = $brands;
        $leads['selected_products_array'] = json_decode( $leads['selected_product'] );
        $leads['products_array']          = [];


	    $leads['multi_brand'] = is_array(json_decode($leads['multi_brand'],true) ) ? json_decode($leads['multi_brand'],true) : [];


	    $selected_categories = is_array(json_decode( $leads['multi_category'],true)) ? json_decode( $leads['multi_category'] ,true) : [] ;
	    $data['category_select'] = Category::attr(['name' => 'multi_category[]','class' => 'form-control','id' => 'multi_category'])
	                                ->selected($selected_categories)
	                                ->renderAsMultiple();

        if ( ! empty( $leads['selected_products_array']  ) ) {
            foreach ( $leads['selected_products_array']  as $product_id ) {
                $skuOrName                             = $this->getProductNameSkuById( $product_id );

               $data['products_array'][$product_id] = $skuOrName;
            }
        }
       // var_dump($leads['products_array']);
      return view('leads.edit',compact('leads','id','data'));
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
        //
        $leads = Leads::find($id);
        $this->validate(request(), [
          'client_name' => 'required',
//          'contactno' => 'required',
//          'city' => 'required',
          'instahandler' => '',
          'rating' => 'required',
          'status' => 'required',
          'solophone' => '',
          'comments' => '',
          'userid'=>'',
          'created_at'  => 'required|date_format:"Y-m-d H:i"',

        ]);

	    if (  $request->input( 'assigned_user' ) != $leads->assigned_user && !empty($request->input( 'assigned_user' ))  ) {

		    NotificationQueueController::createNewNotification([
			    'type' => 'button',
			    'message' => 'Client Name: '.$leads->client_name,
			    'timestamps' => ['+0 minutes','+15 minutes','+30 minutes','+45 minutes'],
			    'model_type' => Leads::class,
			    'model_id' =>  $id,
			    'user_id' => Auth::id(),
			    'sent_to' => $request->input('assigned_user'),
			    'role' => '',
		    ]);

		    NotificationQueueController::createNewNotification([
			    'message' => 'Client Name: '.$leads->client_name,
			    'timestamps' => ['+45 minutes'],
			    'model_type' => Leads::class,
			    'model_id' =>  $id,
			    'user_id' => Auth::id(),
			    'sent_to' => Auth::id(),
			    'role' => '',
		    ]);
	    }

        $leads->client_name = $request->get('client_name');
        $leads->contactno = $request->get('contactno');
        $leads->city= $request->get('city');
        $leads->source = $request->get('source');
        $leads->rating = $request->get('rating');
        $leads->status = $request->get('status');
        $leads->solophone = $request->get('solophone');
        $leads->comments = $request->get('comments');
        $leads->userid = $request->get('userid');
        $leads->email = $request->get('email');
        $leads->address = $request->get('address');
        $leads->assigned_user = $request->get('assigned_user');
        $leads->leadsourcetxt = $request->get('leadsourcetxt');

        $leads->multi_brand = json_encode($request->get('multi_brand'));
        $leads->multi_category = json_encode($request->get('multi_category'));

        $leads->selected_product = json_encode( $request->input( 'selected_product' ) );
        $leads->created_at = $request->created_at;
        $leads->save();



        return redirect('leads')->with('success','Lead has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         $leads = Leads::findOrFail($id);
         $leads->delete();
         return redirect('leads')->with('success','Lead has been archived');
    }

    public function permanentDelete(Leads $leads){

	    $leads->forceDelete();
	    return redirect('leads')->with('success','Lead has been  deleted');
    }

    public function getProductNameSkuById( $product_id ) {

        $product = new Product();

        $product_instance = $product->find( $product_id );

        return $product_instance->name ? $product_instance->name : $product_instance->sku;
    }
}
