<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CsvTranslator;
use App\Imports\CsvTranslatorImport;

class CsvTranslatorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = CsvTranslator::select('*');
            return datatables()
                ->eloquent($query)
                ->toJson();
        }
        return view('csv-translator.index');
    }

    public function upload(Request $request)
    {
        \Excel::import(new CsvTranslatorImport(), $request->file);
        \Session::flash('message', 'Successfully imported');
    }
}
