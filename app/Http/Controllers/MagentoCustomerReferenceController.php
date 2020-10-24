<?php

namespace App\Http\Controllers;

use App\MagentoCustomerReference;
use Illuminate\Http\Request;
use App\Setting;
use App\Customer;
use App\StoreWebsite;
use App\Helpers\InstantMessagingHelper;

class MagentoCustomerReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        ///
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        if (empty($request->name)) {
            return response()->json(['message' => 'Name is required'], 403);
        }

        // if (empty($request->phone)) {
        //     return response()->json(['error' => 'Phone is required'], 403);
        // }

        if (empty($request->email)) {
            return response()->json(['message' => 'Email is required'], 403);
        }

        if (empty($request->website)) {
            return response()->json(['message' => 'website is required'], 403);
        }
        
        // if (empty($request->social)) {
        //     return response()->json(['error' => 'Social is required'], 403);
        // }
        $name = $request->name;
        $email = $request->email;
        $website = $request->website;
        $phone = null;
        $dob = null;
        $store_website_id = null;
        $wedding_anniversery = null;
        if($request->phone) {
            $phone = $request->phone;
        }
        if($request->dob) {
            $dob = $request->dob;
        }
        if($request->wedding_anniversery) {
            $wedding_anniversery = $request->wedding_anniversery;
        }

         //getting reference
         $reference = Customer::where('email',$email)->first();
         $store_website = StoreWebsite::where('website',"like", $website)->first();
         if($store_website) {
             $store_website_id = $store_website->id;
         }
        if(empty($reference)){

            $reference = new Customer();
            $reference->name = $name;
            $reference->phone = $phone;
            $reference->email = $email;
            $reference->store_website_id = $store_website_id;
            $reference->dob = $dob;
            $reference->wedding_anniversery = $wedding_anniversery;
            $reference->save();
        
        }
        else {
            return response()->json(['message' => 'Account already exists with this email'], 403);
        }
        
        

        if($reference->phone) {
            //get welcome message
            $welcomeMessage = InstantMessagingHelper::replaceTags($reference, Setting::get('welcome_message'));
            //sending message
            app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($reference->phone, '', $welcomeMessage, '', '');
        }
        

        return response()->json(['message' => 'Saved SucessFully'], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MagentoCustomerReference  $magentoCustomerReference
     * @return \Illuminate\Http\Response
     */
    public function show(MagentoCustomerReference $magentoCustomerReference)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MagentoCustomerReference  $magentoCustomerReference
     * @return \Illuminate\Http\Response
     */
    public function edit(MagentoCustomerReference $magentoCustomerReference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MagentoCustomerReference  $magentoCustomerReference
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MagentoCustomerReference $magentoCustomerReference)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MagentoCustomerReference  $magentoCustomerReference
     * @return \Illuminate\Http\Response
     */
    public function destroy(MagentoCustomerReference $magentoCustomerReference)
    {
        //
    }
}
