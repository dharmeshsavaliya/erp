<?php

namespace App\Http\Controllers;

use App\CashFlow;
use App\File;
use App\Helpers;
use App\MonetaryAccount;
use App\Purchase;
use App\ReadOnly\CashFlowCategories;
use App\Setting;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Storage;

class CashFlowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cash_flow = CashFlow::with(['user', 'files']);
        if ($request->type!='')
            $cash_flow->where('type',$request->type);
        if ($request->module_type!='')
           {
               if ($request->module_type=='order')
                  {
                    $cash_flow->where('cash_flow_able_type','\App\Order');
                    if ($request->b_name!='')
                        {
                            $cash_flow->join('orders','cash_flows.cash_flow_able_id','orders.id');
                            $cash_flow->join('customers','orders.customer_id','customers.id');
                            $cash_flow->where('name','like',"%$request->b_name%");
                        } 

                  } 
               if ($request->module_type=='payment_receipt')
                  {
                            $cash_flow->where('cash_flow_able_type','\App\PaymentReceipt'); 
                            $cash_flow->join('payment_receipts','cash_flows.cash_flow_able_id','assets_manager.id');
                            $cash_flow->where('remarks','like',"%$request->b_name%"); 
                           
                  }  
               if ($request->module_type=='assent_manager')
                  {
                        $cash_flow->where('cash_flow_able_type','\App\AssetsManager'); 
                        $cash_flow->join('assets_manager','cash_flows.cash_flow_able_id','assets_manager.id');
                        $cash_flow->where('name','like',"%$request->b_name%");
                  
                         

                  }      
           }
        
        if ($request->daterange!='')
        {
            $date=explode("-", $request->daterange);
            $datefrom=date('Y-m-d',strtotime($date[0]));
            $dateto=date('Y-m-d',strtotime($date[1]));
            $cash_flow->whereRaw("date(date) between date('$datefrom') and date('$dateto')");
        }
        $cash_flows = $cash_flow->orderBy('date', 'desc')->orderBy('cash_flows.id', 'desc')->paginate(Setting::get('pagination'));
        $users      = User::select(['id', 'name', 'email'])->get();
        $categories = (new CashFlowCategories)->all();
        //$orders = Order::with('order_product')->select(['id', 'order_date', 'balance_amount'])->orderBy('order_date', 'DESC')->paginate(Setting::get('pagination'), ['*'], 'order-page');
        $purchases = Purchase::with('products')->select(['id', 'created_at'])->orderBy('created_at', 'DESC')->paginate(Setting::get('pagination'), ['*'], 'purchase-page');
        //$vouchers = Voucher::orderBy('date', 'DESC')->paginate(Setting::get('pagination'), ['*'], 'voucher-page');
        if ($request->ajax()) 
        {
            return view('cashflows.index_page', [
                'cash_flows' => $cash_flows,
                'users'      => $users,
                'categories' => $categories,
                'purchases'  => $purchases,
            ]);
        }
        else
        {
        return view('cashflows.index', [
            'cash_flows' => $cash_flows,
            'users'      => $users,
            'categories' => $categories,
            'purchases'  => $purchases,
        ]);
        }
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            // 'user_id'               => 'required|integer',
            'cash_flow_category_id' => 'sometimes|nullable|integer',
            'description'           => 'sometimes|nullable|string',
            'date'                  => 'required',
            'amount'                => 'required|integer',
            'type'                  => 'required|string',
        ]);

        $data            = $request->except(['_token', 'file']);
        $data['user_id'] = Auth::id();

        $cash_flow = CashFlow::create($data);

        if ($request->hasFile('file')) {
            foreach ($request->file('file') as $file) {
                $filename = $file->getClientOriginalName();

                $file->storeAs("files", $filename, 'uploads');

                $new_file             = new File;
                $new_file->filename   = $filename;
                $new_file->model_id   = $cash_flow->id;
                $new_file->model_type = CashFlow::class;
                $new_file->save();
            }
        }

        return redirect()->route('cashflow.index')->withSuccess('You have successfully added a record!');
    }

    public function download($id)
    {
        $file = File::find($id);

        return Storage::disk('uploads')->download('files/' . $file->filename);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cash_flow = CashFlow::find($id);

        if ($cash_flow->files) {
            foreach ($cash_flow->files as $file) {
                Storage::disk('uploads')->delete("files/$file->filename");
                $file->delete();
            }
        }

        $cash_flow->delete();

        return redirect()->route('cashflow.index')->withSuccess('You have successfully deleted a record!');
    }

    public function masterCashFlow(CashFlow $cashFlow, MonetaryAccount $account, Request $request)
    {
        $cash_flows         = $cashFlow;
        $capitals           = $account;
        $data['start_date'] = date('Y-m-d');
        $data['end_date']   = date('Y-m-d');
        $range_start        = $request->get('range_start');
        $range_end          = $request->get('range_end');

        $dates = [date('Y-m-d')];
        if ($range_start != '' && $range_end != '') {
            $cash_flows                = $cash_flows->whereBetween('date', [$range_start . ' 00:00', $range_end . ' 23:59']);
            $added_capitals_in_between = MonetaryAccount::whereBetween('date', [$range_start . ' 00:00', $range_end . ' 23:59'])->get();
            $data['start_date']        = $range_start;
            $data['end_date']          = $range_end;
        }

        if (!$range_start || !$range_end) {
            $cash_flows                = $cash_flows->where('date', date('Y-m-d'));
            $added_capitals_in_between = MonetaryAccount::where('date', date('Y-m-d'))->get();
        }
        $capitals        = $capitals->where('date', '<', $data['start_date'] . ' 00:00')->get();
        $currencies      = Helpers::currencies();
        $opening_balance = [
            'total' => 0,
        ];
        foreach ($currencies as $currency_id => $currency) {
            $opening_balance[$currency] = 0;
        }
        foreach ($capitals as $capital) {
            $opening_balance['total'] += $capital->amount;
            foreach ($currencies as $currency_id => $currency) {
                if (array_key_exists($currency, $opening_balance)) {
                    $opening_balance[$currency] += $capital->currency == $currency_id ? $capital->amount : 0;
                } else {
                    $opening_balance[$currency] = $capital->currency == $currency_id ? $capital->amount : 0;
                }

            }
        }
        $data['currencies']                = $currencies;
        $data['opening_balance']           = $opening_balance;
        $data['added_capitals_in_between'] = $added_capitals_in_between;
        $data['transactions']              = collect($cash_flows->orderBy('date')->orderBy('type', 'desc')->orderBy('cash_flow_able_type')->get()->toArray());
        return view('cashflows.master', $data);
    }

    public function doPayment(Request $request)
    {
        $id = $request->get("cash_flow_id", 0);

        $validator = Validator::make($request->all(), [
            'cash_flow_id' => 'required',
            'description'  => 'required',
            'date'         => 'required',
            'amount'       => 'required',
            'type'         => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(["code" => 401, "data" => $validator->errors(), "message" => "Please fix validation errors"]);
        }

        if ($id > 0) {
            $cashflow = \App\CashFlow::find($id);
            if ($cashflow) {
                $cashflow->erp_amount          = $request->amount;
                $cashflow->type                = $request->type;
                $cashflow->monetary_account_id = $request->monetary_account_id;
                $cashflow->updated_by          = auth()->user()->id;
                $cashflow->status              = 1;
                if ($cashflow->erp_amount > 0) {
                    $cashflow->erp_eur_amount = \App\Currency::convert($cashflow->erp_amount, 'EUR', $cashflow->currency);
                }
                
                $cashflow->save();
            }

            return response()->json(["code" => 200, "data" => [], "message" => "Receipt Created successfully"]);
        }

        return response()->json(["code" => 500, "data" => [], "message" => "Cashflow requested id is not found"]);
    }


    public function getBnameList(Request $request)
    {
         
         $model_type=$request->model_type;
         if ($model_type=='order')
         {
                $model_type="\App\Customer";
                $rs=$model_type::get();
                $data='';
                foreach($rs as $r)
                {
                   
                    $arr['name']=$r->name;


                    $data=$arr;

                }
                
                return response()->json($data);
         } 
         if ($model_type=='assent_manager')
         {
                $model_type="\App\AssetsManager";
                $rs=$model_type::get();
                $data='';
                foreach($rs as $r)
                {
                    $arr['name']=$r->name;


                    $data=$arr;

                }
                
                return response()->json($data);
         } 
         
         if ($model_type=='payment_receipt')
         {
                $model_type="\App\PaymentReceipt";
                $rs=$model_type::get();
                $data='';
                foreach($rs as $r)
                {
                    
                    $arr['name']=$r->remarks;


                    $data=$arr;

                }
                
                return response()->json($data);
         }  
    }    

}
    