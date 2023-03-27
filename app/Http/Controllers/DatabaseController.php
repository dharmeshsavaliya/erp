<?php

namespace App\Http\Controllers;

use App\DatabaseHistoricalRecord;
use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $databaseHis = DatabaseHistoricalRecord::latest();

        $customRange = $request->get('customrange');

        if (! empty($customRange)) {
            $range = explode(' - ', $customRange);
            if (! empty($range[0])) {
                $startDate = $range[0];
            }
            if (! empty($range[1])) {
                $endDate = $range[1];
            }
        }

        if (! empty($startDate)) {
            $databaseHis = $databaseHis->whereDate('created_at', '>=', $startDate);
        }

        if (! empty($endDate)) {
            $databaseHis = $databaseHis->whereDate('created_at', '<=', $endDate);
        }

        $databaseHis = $databaseHis->paginate(20);

        $page = $databaseHis->currentPage();

        if ($request->ajax()) {
            $tml = (string) view('database.partial.list', compact('databaseHis', 'page'));

            return response()->json(['code' => 200, 'tpl' => $tml, 'page' => $page]);
        }

        return view('database.index', compact('databaseHis', 'page'));
    }

    public function states(Request $request)
    {
        return view('database.states');
    }

    public function processList()
    {
        return response()->json(['code' => 200, 'records' => \DB::select('show processlist')]);
    }

    public function processKill(Request $request)
    {
        $id = $request->get('id');

        return response()->json(['code' => 200, 'records' => \DB::statement("KILL $id")]);
    }

    public function export(Request $request)
    {
        $dbName = $request->input('db_name');
        \Log::info('Database name:'.$dbName);
        $dumpName = str_replace(' ', '_', $dbName).'_schema.sql';
        \Log::info('Dump name:'.$dumpName);
        $cmd = 'mysqldump -h erpdb -u erplive -p  --no-data '.$dbName.' > '.$dumpName;
        \Log::info('Executing:'.$cmd);
        $allOutput = [];
        $allOutput[] = $cmd;
        $result = exec($cmd, $allOutput);
        chmod($dumpName, 0755);

        header('Content-Type: application/octet-stream');
        header('Content-Transfer-Encoding: Binary');
        header('Content-disposition: attachment; filename=erp_live_schema.sql');
        $dumpUrl = env('APP_URL').'/'.$dumpName;

        return response()->json(['code' => 200, 'data' => $dumpUrl, 'message' => 'Database exported successfully']);
    }
}
