<?php

namespace App\Http\Controllers;

use App\Models\PlanAction;
use App\Plan;
use App\PlanBasisStatus;
use App\PlanCategories;
use App\PlanTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $query = Plan::whereNull('parent_id');
        $basisList = PlanBasisStatus::all();

        $typeList = PlanTypes::all();
        $categoryList = PlanCategories::all();

        if (request('status')) {
            $query->where('status', request('status'));
        }

        if (request('priority')) {
            $query->where('priority', request('priority'));
        }
        if (request('typefilter')) {
            $query->where('type', request('typefilter'));
        }
        if (request('categoryfilter')) {
            $query->where('type', request('categoryfilter'));
        }

        if (request('date')) {
            $query->whereDate('date', request('date'));
        }

        if (request('term')) {
            $query->where('subject', 'LIKE', '%'.request('term').'%');
            $query->orwhere('sub_subject', 'LIKE', '%'.request('term').'%');
            $query->orwhere('basis', 'LIKE', '%'.request('term').'%');
            $query->orwhere('implications', 'LIKE', '%'.request('term').'%');
        }

        $planList = $query->orderBy('id', 'DESC')->paginate(10);

        return view('plan-page.index', compact('planList', 'basisList', 'typeList', 'categoryList'));
    }

    public function store(Request $request)
    {
        //  dd( $request->all() );
        $rules = [
            'priority' => 'required',
            //'date' => 'required',
            'status' => 'required',
        ];

        $validation = validator(
            $request->all(),
            $rules
        );
        if (isset($request->parent_id)) {
            $plan = Plan::find($request->parent_id);
            $type = $plan->type;
            $category = $plan->category;
        } else {
            $type = PlanTypes::find($request->type);
            if (! $type) {
                $data = [
                    'type' => $request->type,
                ];

                PlanTypes::insert($data);
            }

            $category = PlanCategories::find($request->category);
            if (! $category) {
                $data = [
                    'category' => $request->category,
                ];

                PlanCategories::insert($data);
            }
        }

        $basis = PlanBasisStatus::find($request->basis);
        if (! $basis) {
            $data = [
                'status' => $request->basis,
            ];

            PlanBasisStatus::insert($data);
        }
        $typeList = PlanTypes::all();
        $categoryList = PlanCategories::all();
        //If validation fail send back the Input with errors
        if ($validation->fails()) {
            //withInput keep the users info
            return redirect()->back()->withErrors($validation)->withInput();
        } else {
            $data = [
                'subject' => $request->subject,
                'sub_subject' => $request->sub_subject,
                'description' => $request->description,
                'priority' => $request->priority,
                'date' => $request->date,
                'status' => $request->status,
                'budget' => $request->budget,
                'deadline' => $request->deadline,
                'basis' => $request->basis,
                'type' => $request->type,
                'category' => $request->category,
                'implications' => $request->implications,
            ];
            if ($request->parent_id) {
                $data['parent_id'] = $request->parent_id;
            }
            if ($request->remark) {
                $data['remark'] = $request->remark;
            }
            if ($request->id) {
                Plan::whereId($request->id)->update($data);

                return redirect()->back()->with('success', 'Plan updated successfully.');
            } else {
                Plan::insert($data);

                return redirect()->back()->with('success', 'Plan saved successfully.');
            }
        }
    }

    public function newBasis(Request $request)
    {
        $rules = [
            'name' => 'required',
        ];

        $validation = validator(
            $request->all(),
            $rules
        );
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $data = [
            'status' => $request->name,
        ];

        PlanBasisStatus::insert($data);

        return redirect()->back()->with('success', 'New status created successfully.');
    }

    public function newType(Request $request)
    {
        $rules = [
            'name' => 'required',
        ];

        $validation = validator(
            $request->all(),
            $rules
        );
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $data = [
            'type' => $request->name,
        ];

        PlanTypes::insert($data);

        return redirect()->back()->with('success', 'New type created successfully.');
    }

    public function newCategory(Request $request)
    {
        $rules = [
            'name' => 'required',
        ];

        $validation = validator(
            $request->all(),
            $rules
        );
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $data = [
            'category' => $request->name,
        ];

        PlanCategories::insert($data);

        return redirect()->back()->with('success', 'New category created successfully.');
    }

    public function edit(Request $request)
    {
        $data = Plan::where('id', $request->id)->first();
        if ($data) {
            return response()->json([
                'code' => 200,
                'object' => $data,
            ]);
        }

        return response()->json([
            'code' => 500,
            'object' => null,
        ]);
    }

    public function delete($id = null)
    {
        Plan::whereId($id)->delete();

        return redirect()->back()->with('success', 'Plan deleted successfully.');
    }

    public function report($id = null)
    {
        $reports = \App\ErpLog::where('model', \App\StoreWebsiteAnalytic::class)->orderBy('id', 'desc')->where('model_id', $id)->get();

        return view('store-website-analytics.reports', compact('reports'));
    }

    public function planAction(Request $request, $id)
    {
        $data = Plan::where('id', $id)->first();

        return $data;
        //return response()->json(["code" => 200,"message" => 'Your data saved sucessfully.']);
    }

    public function planActionAddOn(Request $request, $id)
    {
        $data = Plan::where('id', $id)
            ->with('getPlanActionStrength', 'getPlanActionWeakness', 'getPlanActionOpportunity', 'getPlanActionThreat')->first();
        $strengths = $data->getPlanActionStrength;
        $weaknesses = $data->getPlanActionWeakness;
        $opportunities = $data->getPlanActionOpportunity;
        $threats = $data->getPlanActionThreat;

        return view('modal.plan_action', compact('strengths', 'weaknesses', 'opportunities', 'threats'));
        //return response()->json(["code" => 200,"message" => 'Your data saved sucessfully.']);
    }

    public function planActionStore(Request $request)
    {
        $data = Plan::where('id', $request->id)->first();

        //old code
//        if($data){
//            $data->strength = $request->strength."\n";
//            $data->weakness = $request->weakness."\n";
//            $data->opportunity = $request->opportunity."\n";
//            $data->threat = $request->threat."\n";
//            $data->save();
//            return response()->json(["code" => 200,"message" => 'Your data saved sucessfully.']);
//        }
//        return response()->json(["code" => 500,"message" => 'Data not found!']);

        //change code by new requirement
        if ($data) {
            $created_by = \Auth::user()->id;
            $do_not_delete = [];

            //----------------- Edit Process ----------------------------
            if (isset($request->plan_action_old)) {
                $do_not_delete = $request->plan_action_old;
            }
            $plan_action_old_active = [];

            if (isset($request->plan_action_old_active_hidden)) {
                foreach ($request->plan_action_old_active_hidden as $key => $data) {
                    if (! isset($request->plan_action_old_active[$key])) {
                        $plan_action_old_active[$key] = 0;
                    } else {
                        $plan_action_old_active[$key] = $request->plan_action_old_active[$key];
                    }
                }

                $result = array_diff_assoc($plan_action_old_active, $request->plan_action_old_active_hidden);

                //get active data
                $filteredArrayByActive = Arr::where($result, function ($value, $key) {
                    return $value == 1;
                });

                //get In-active data
                $filteredArrayByInActive = Arr::where($result, function ($value, $key) {
                    return $value == 0;
                });

                //update active status
                PlanAction::whereIn('id', array_keys($filteredArrayByActive))->update(['is_active' => 1]);
                PlanAction::whereIn('id', array_keys($filteredArrayByInActive))->update(['is_active' => 0]);
            }

            //----------------- Edit Process End ----------------------------

            //----------------- Add/Edit Process ----------------------------
            if (isset($request->plan_action_strength)) {
                foreach ($request->plan_action_strength as $plan_action_strength) {
                    $plan_action_strengthData = PlanAction::firstOrCreate([
                        'plan_id' => $request->id,
                        'plan_action' => $plan_action_strength,
                        'plan_action_type' => 1,
                        'created_by' => $created_by,
                    ]);
                    array_push($do_not_delete, $plan_action_strengthData->id);
                }
            }
            if (isset($request->plan_action_weakness)) {
                foreach ($request->plan_action_weakness as $plan_action_weakness) {
                    $plan_action_weaknessData = PlanAction::firstOrCreate([
                        'plan_id' => $request->id,
                        'plan_action' => $plan_action_weakness,
                        'plan_action_type' => 2,
                        'created_by' => $created_by,
                    ]);
                    array_push($do_not_delete, $plan_action_weaknessData->id);
                }
            }
            if (isset($request->plan_action_opportunity)) {
                foreach ($request->plan_action_opportunity as $plan_action_opportunity) {
                    $plan_action_opportunityData = PlanAction::firstOrCreate([
                        'plan_id' => $request->id,
                        'plan_action' => $plan_action_opportunity,
                        'plan_action_type' => 3,
                        'created_by' => $created_by,
                    ]);
                    array_push($do_not_delete, $plan_action_opportunityData->id);
                }
            }
            if (isset($request->plan_action_threat)) {
                foreach ($request->plan_action_threat as $plan_action_threat) {
                    $plan_action_threatData = PlanAction::firstOrCreate([
                        'plan_id' => $request->id,
                        'plan_action' => $plan_action_threat,
                        'plan_action_type' => 4,
                        'created_by' => $created_by,
                    ]);
                    array_push($do_not_delete, $plan_action_threatData->id);
                }
            }
            //----------------- Add/Edit Process End ----------------------------

            //delete extra plan action
            PlanAction::whereNotIn('id', $do_not_delete)->where('plan_id', $request->id)->delete();

            return response()->json(['code' => 200, 'message' => 'Your data saved sucessfully.']);
        }

        return response()->json(['code' => 500, 'message' => 'Data not found!']);
    }

    public function planSolutionsStore(Request $request)
    {
        if ($request->solution && $request->id) {
            $data = [
                'solution' => $request->solution,
                'plan_id' => $request->id,
            ];
            DB::table('plan_solutions')->insert($data);

            return response()->json(['code' => 200, 'message' => 'Your data saved sucessfully.']);
        }

        return response()->json(['code' => 500, 'message' => 'Data not found!']);
    }

    public function planSolutionsGet(Request $request, $id)
    {
        if ($id) {
            $data = DB::table('plan_solutions')->where('plan_id', $id)->get();

            return $data;
        }

        return response()->json(['code' => 500, 'message' => 'Data not found!']);
    }
}
