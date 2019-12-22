<?php

namespace App\Http\Controllers\Logging;

use App\Loggers\LogScraper;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use App\DeveloperTask;

class LogScraperController extends Controller
{
    public function index(Request $request)
    {
        $scraperLogs = DB::table('log_scraper')->where('validated', 0);

        if (!empty($request->website)) {
            $scraperLogs = $scraperLogs->where('website', 'LIKE', '%' . $request->website . '%');
        }

        if (!empty($request->result)) {
            $scraperLogs = $scraperLogs->where('validation_result', 'LIKE', '%' . $request->result . '%');
        }

        $scraperLogs = $scraperLogs->orderBy('created_at', 'DESC')->paginate(25);

        return view('log.scraper', compact('scraperLogs'));
    }

    public function logSKU(Request $request)
    {
       
        $logScrapper = LogScraper::query();

        // Filters
        if (!empty($request->product_id)) {
            $logScrapper->where('id', $request->product_id);
        }

       if (!empty($request->sku)) {
            $logScrapper->where('sku', 'LIKE', '%' . $request->sku . '%');
        }

        if (!empty($request->brand)) {
            $logScrapper->whereIn('brand', $request->brand);
        }

        if (!empty($request->category)) {
            $logScrapper->where('category', 'LIKE', '%' . $request->category . '%');
        }

        if (!empty($request->supplier)) {
            $logScrapper->whereHas('supplier', function ($qu) use ($request) {
                    $qu->whereIn('supplier', request('supplier'));
                });
        }

        if (!empty($request->validate)) {
            if($request->validate == 2){
                $logScrapper->where('validated', 0);
            }else{
               $logScrapper->where('validated', $request->validate); 
            }
        }

        $failed = $logScrapper->where('validation_result', 'LIKE', '%SKU failed regex test%')->count();
        
        
        // Get paginated result
        $logScrappers = $logScrapper->paginate(25)->appends(request()->except(['page']));

        $existingIssues = DeveloperTask::whereNotNull('reference')->get();

        $pendingIssues = DeveloperTask::whereNotNull('reference')->where('status','Issue')->count();

        $lastCreatedIssue = DeveloperTask::whereNotNull('reference')->orderBy('created_at','desc')->first();
        
        $selected_categories = $request->category ? $request->category : 1;

        $category_selection = Category::attr(['name' => 'category[]', 'class' => 'form-control select-multiple2','id' => 'category'])
            ->selected($selected_categories)
            ->renderAsDropdown();

         $requestParam = request()->except(['page']);   
        // For ajax
        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('logging.partials.listsku_data', compact('logScrappers','category_selection','failed','existingIssues','pendingIssues','lastCreatedIssue','requestParam'))->render(),
                'links' => (string)$logScrappers->render(),
                'totalFailed' => $failed,
            ], 200);
        }

        

        // Show results
        return view('logging.product-sku', compact('logScrappers','category_selection','failed','existingIssues','lastCreatedIssue','pendingIssues','requestParam'));
    
    }
}
