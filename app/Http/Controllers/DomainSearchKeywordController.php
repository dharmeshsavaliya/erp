<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DomainSearchKeyword;
use App\SeoKeywordIdea;
use App\StoreWebsite;
use Carbon\Carbon;

class DomainSearchKeywordController extends Controller
{
    public function searchKeyword(request $request){
		$store_website_id = 2;
		$last_record_date = DomainSearchKeyword::orderBy('id', 'desc')->pluck('created_at')->first();
		$date = Carbon::parse($last_record_date)->format('Y-m-d');
		$countries = DomainSearchKeyword::select('database')->groupBy('database')->orderBy('database','asc')->get();
		$filter = $request->all();
		$keywords = DomainSearchKeyword::select('keyword')->groupBy('keyword')->orderBy('keyword','asc')->get();			
		$data = $request->input('keywords');		
		$SearchAnalytics = DomainSearchKeyword::leftJoin('seo_tools', 'domain_search_keywords.tool_id', '=', 'seo_tools.id');		
		if( isset($request->keywords) && $request->keywords!='' ) {
			$SearchAnalytics = $SearchAnalytics->whereIn('keyword',$request->keywords);
		}		
		if( isset($request->countries) && $request->countries!='' ){
			$SearchAnalytics = $SearchAnalytics->whereIn('database',$request->countries);
		}		
		$keywordsearch = $SearchAnalytics->select('domain_search_keywords.*', 'seo_tools.tool')->where('store_website_id', $store_website_id)->where('domain_search_keywords.created_at', '>', $date." 00:00:00")->paginate(20);		
		$website = StoreWebsite::where('id', $store_website_id)->select('id', 'website')->first();
		$seoKeywordIdeas = SeoKeywordIdea::where('store_website_id', $store_website_id)->get();
		return view('seo-tools.keywords', compact('keywordsearch','countries','keywords','filter', 'website', 'seoKeywordIdeas'));
    }
	

    public function saveKeywordIdea(Request $request){
        $inputs = $request->input();		
		SeoKeywordIdea::create($inputs);
		return redirect()->back();
        //return redirect()->route('testModal')->with('success', 'Added successfully!');
    }
}