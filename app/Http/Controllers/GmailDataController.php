<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Setting;
use App\GmailDataList;
use Illuminate\Http\Request;

class GmailDataController extends Controller
{
    public function index(Request $request)
    {
        if ($request->sender) {
            $data = GmailDataList::where('sender', $request->sender)->groupBy('sender')->orderBy('created_at', 'desc')->paginate(Setting::get('pagination'));
        } else {
            $data = GmailDataList::orderBy('created_at', 'desc')->groupBy('sender')->paginate(Setting::get('pagination'));
        }
        $senders = GmailDataList::select('sender')->groupBy('sender')->get();
        $brands = Brand::get()->pluck('name')->toArray();
        // For ajax
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('scrap.partials.list-gmail', compact('brands', 'data', 'senders'))->render(),
                'links' => (string) $data->appends($request->all())->render(),
                'total' => $data->total(),
            ], 200);
        }

        return view('scrap.gmail', compact('brands', 'data', 'senders'));
    }

    public function show($sender)
    {
        $datas = GmailDataList::where('sender', 'LIKE', '%' . $sender . '%')->get();
        $brands = Brand::get()->pluck('name')->toArray();

        return view('scrap.show-gmail', compact('brands', 'datas', 'sender'));
    }
}
