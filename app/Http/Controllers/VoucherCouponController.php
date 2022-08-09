<?php

namespace App\Http\Controllers;

use App\VoucherCoupon;
use App\Platform;
use App\EmailAddress;
use DB;
use Illuminate\Http\Request;


class VoucherCouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $voucher = new VoucherCoupon();
        
        $voucher = $voucher->select("voucher_coupons.*", "wc.number", 'em.from_address', 'vcp.name AS plateform_name', 'u.name As user_name')
        ->leftJoin("users As u", 'voucher_coupons.user_id', 'u.id')
        ->leftJoin("whatsapp_configs As wc", 'voucher_coupons.whatsapp_config_id', 'wc.id')
        ->leftJoin("email_addresses As em", 'voucher_coupons.email_address_id', 'em.id')
        ->leftJoin("voucher_coupon_platforms As vcp", 'voucher_coupons.platform_id', 'vcp.id');
        if (!empty(request('plateform_id'))) {
            $voucher = $voucher->where("platform_id",   request('plateform_id'));
        }
        if (!empty(request('email_add'))) {
            $voucher = $voucher->where("email_address_id",  request('email_add'));
        }
        if (!empty(request('whatsapp_id'))) {
            $voucher = $voucher->where("whatsapp_config_id",  request('whatsapp_id'));
        }
        $voucher = $voucher->paginate(10)->appends(request()->except('page'));    
        

        $platform = Platform::get()->pluck('name', 'id');
        $whatsapp_configs = DB::table("whatsapp_configs")->get()->pluck('number', 'id');
        $emails = DB::table("email_addresses")->get()->pluck('id', 'from_address');
        return view("voucher-coupon.index", compact('voucher', 'platform', 'whatsapp_configs', 'emails'));    
        
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            if($request->id)
                $plate = VoucherCoupon::find($request->id);
            else
                $plate = new VoucherCoupon();
            $plate->user_id = \Auth::user()->id ?? '';
            $plate->platform_id = $request->plateform_id ?? '';
            $plate->email_address_id = $request->email_id ?? '';
            $plate->whatsapp_config_id = $request->whatsapp_config_id ?? '';
            $plate->password = $request->password ?? '';
            $plate->save();
            return response()->json(['code' => 200, 'message' => 'Added successfully!!!']);
        }catch(\Exception $e){
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function plateformStore(Request $request){
        try{
            $plate = new Platform();
            $plate->name = $request->plateform_name;
            $plate->save();
            return response()->json(['code' => 200, 'message' => 'Added successfully!!!']);
        }catch(\Exception $e){
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\VoucherCoupon  $voucherCoupon
     * @return \Illuminate\Http\Response
     */
    public function show(VoucherCoupon $voucherCoupon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\VoucherCoupon  $voucherCoupon
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        try{
            $vou = VoucherCoupon::find($request->id);
            return response()->json(['code' => 200, 'data' => $vou, 'message' => 'Listed successfully!!!']);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return response()->json(['code' => 500, 'message' => $msg]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VoucherCoupon  $voucherCoupon
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $vou = VoucherCoupon::find($id);
            $vou->remark = $request->remark;
            $vou->save();
            return response()->json(['code' => 200, 'message' => 'Remark Updated successfully!!!']);
        }catch(\Exception $e){
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VoucherCoupon  $voucherCoupon
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        try{
            $vou = VoucherCoupon::find($request->id);
            $vou->delete();
            return response()->json(['code' => 200, 'message' => 'Deleted successfully!!!']);
        }catch(\Exception $e){
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }
}