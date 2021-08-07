<?php

namespace App\Http\Controllers;

use App\Leads;
use App\Order;
use Illuminate\Http\Request;
use App\Setting;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Helpers;
use Illuminate\Support\Facades\DB;
use App\ErpLeads;

class LeadOrderController extends Controller
{

    public function __construct()
    {

    }

   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $term             = $request->input('term');
        $date             = $request->date ?? '';
        $brandList        = \App\Brand::all()->pluck("name", "id")->toArray();
        $brandIds         = $request->get("brand_id");

        if ($request->input('orderby') == '') {
            $orderby = 'DESC';
        } else {
            $orderby = 'ASC';
        }
       // $Order = Order::select('id','customer_id','order_date','product_id','deleted_at');
        $Order = Order::select('orders.id','orders.customer_id','product_id','order_date','products.name','brands.name as brand_name','products.price_inr','products.price_inr_discounted','users.name as customer_name','brands.id as brand_id')
                        ->join('order_products','order_products.order_id','=','orders.id')
                        ->join('products','order_products.product_id','=','products.id')
                        ->leftJoin('users','orders.customer_id','=','users.id')
                        ->join('brands','products.brand','=','brands.id');
        if (empty($term)) {
            $orders = $Order;
        } 
        else { 
            $orders = $Order->orWhere('orders.id', '=', $term)
                            //->orWhere('erp_leads.id', '=', $term)
                            ->orWhere('products.name', 'like', '%' . $term . '%')
                            ->orWhere('brands.id', '=', $brandIds)
                            ->orWhere('users.name', 'like', '%' . $term . '%');
        }                 
        
        //$leads = ErpLeads::select('id','customer_id','created_at as order_date','product_id','deleted_at')->union($Order)->orderBy('customer_id')->take(50)->get()->toArray();
        $leads = ErpLeads::select('erp_leads.id','erp_leads.customer_id','product_id','erp_leads.created_at as order_date','products.name','brands.name as brand_name','products.price_inr','products.price_inr_discounted','users.name as customer_name','brands.id as brand_id')
                            ->join('products','erp_leads.product_id','=','products.id')
                            ->leftJoin('users','erp_leads.customer_id','=','users.id')
                            ->join('brands','erp_leads.brand_id','=','brands.id')
                            ->union($orders);

        if (empty($term)) {
            $orders = $leads;
        } 
        else { 
            $orders = $leads->orWhere('erp_leads.id', '=', $term)
                            ->orWhere('products.name', 'like', '%' . $term . '%')
                            ->orWhere('users.name', 'like', '%' . $term . '%');
        }    
                                               
        // $perPage = Setting::get('pagination');
        // $currentPage = LengthAwarePaginator::resolveCurrentPage();
        
        if (!empty($brandIds)) {
            $orders = $orders->orWhere("brand_id", $brandIds);
        }
        $orders = $orders->orderBy('id')
                    ->get()->toArray();
        $leadOrder_array = $orders;
        
        //$currentItems = array_slice($leadOrder_array, $perPage * ($currentPage - 1), $perPage);

        //$leadOrder_array = new LengthAwarePaginator($currentItems, count($leadOrder_array), $perPage, $currentPage);
        //$leads = $leadOrder_array->whereNull('deleted_at')->paginate(Setting::get('pagination'));

        // if ($request->ajax()) {
        //     $html = view('lead-order.lead-order-item', ['leadOrder_array' => $leadOrder_array])->render();

        //     return response()->json(['html' => $html]);
        // }

        return view('lead-order.index', compact('leadOrder_array','leads','brandList'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }
}