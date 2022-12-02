<?php

namespace App\Http\Controllers;

use App\Library\Hubstaff\Src\Repositories\User;
use Illuminate\Http\Request;

class NegativeCouponResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $negativeCoupons = \DB::table('nagative_coupon_responses');
        $negativeCoupons->select('nagative_coupon_responses.*', 'users.name as userName');
        $negativeCoupons->leftJoin('users', function ($join) {
            $join->on('users.id', '=', 'nagative_coupon_responses.user_id');
        });

        //$negativeCoupons->lefJoin('users', 'nagative_coupon_responses.user_id', 'users.id');
        //$users = \App\User::all();
        $negativeCouponsData = $negativeCoupons->orderBy('id', 'DESC')->paginate(\App\Setting::get('pagination'));
        //dd($negativeCouponsData);
        return view('negative-coupon-response.index', compact('negativeCouponsData'));
    }

    public function search(Request $request)
    {
        $negativeCoupons = \DB::table('nagative_coupon_responses');
        $negativeCoupons->select('nagative_coupon_responses.*', 'users.name as userName');
        $negativeCoupons->leftJoin('users', function ($join) {
            $join->on('users.id', '=', 'nagative_coupon_responses.user_id');
        });
        //$negativeCoupons->lefJoin('users', 'nagative_coupon_responses.user_id', 'users.id');
        if ($request->website) {
            $negativeCoupons->where('website', $request->website);
        }
        if ($request->response_text) {
            $negativeCoupons->where('response', 'like', '%'.$request->response_text.'%');
        }
        if ($request->user) {
            $negativeCoupons->where('user_id', $request->user);
        }
        //$users = \App\User::where('id', '!=', '');
        $negativeCouponsData = $negativeCoupons->orderBy('id', 'DESC')->paginate(\App\Setting::get('pagination'));

        return view('negative-coupon-response.index', compact('negativeCouponsData'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\NagativeCouponResponse  $nagativeCouponResponse
     * @return \Illuminate\Http\Response
     */
    public function show(NagativeCouponResponse $nagativeCouponResponse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\NagativeCouponResponse  $nagativeCouponResponse
     * @return \Illuminate\Http\Response
     */
    public function edit(NagativeCouponResponse $nagativeCouponResponse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\NagativeCouponResponse  $nagativeCouponResponse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NagativeCouponResponse $nagativeCouponResponse)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\NagativeCouponResponse  $nagativeCouponResponse
     * @return \Illuminate\Http\Response
     */
    public function destroy(NagativeCouponResponse $nagativeCouponResponse)
    {
        //
    }
}
