<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\User;
use App\Task;
use App\DailyActivity;
use App\Instruction;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyPlannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $userid = $request->user_id ?? Auth::id();
      $planned_at = $request->planned_at ?? Carbon::now()->format('Y-m-d');

      $tasks = Task::where('is_statutory', '=', 0)
  										->where(function ($query) use ($userid) {
  											return $query->orWhere('assign_from', '=', $userid)
  											             ->orWhere('assign_to', '=', $userid);
  										})
  		                               ->oldest()->get();

      $planned_tasks  = Task::whereNotNull('time_slot')->where('planned_at', $planned_at)->where(function ($query) use ($userid) {
        return $query->orWhere('assign_from', '=', $userid)
                     ->orWhere('assign_to', '=', $userid);
      })->orderBy('time_slot', 'ASC')->get()->groupBy('time_slot');

      $statutory  = Task::where(function ($query) use ($userid) {
        return $query->whereRaw("tasks.id IN (SELECT task_id FROM task_users WHERE user_id = $userid)")->orWhere('assign_from', '=', $userid)
                     ->orWhere('assign_to', '=', $userid);
      })->where('is_statutory', 1)->whereNull('is_verified')->count();

      $daily_activities = DailyActivity::where('user_id', $userid)->where('for_date', $planned_at)->get()->groupBy('time_slot');

      // dd($daily_activities);

      // dd($statutory);

      $time_slots = [
        '08:00am - 09:00am' => [],
        '09:00am - 10:00am' => [],
        '10:00am - 11:00am' => [],
        '11:00am - 12:00pm' => [],
        '12:00pm - 01:00pm' => [],
        '01:00pm - 02:00pm' => [],
        '02:00pm - 03:00pm' => [],
        '03:00pm - 04:00pm' => [],
        '04:00pm - 05:00pm' => [],
        '05:00pm - 06:00pm' => [],
        '06:00pm - 07:00pm' => [],
        '07:00pm - 08:00pm' => [],
        '08:00pm - 09:00pm' => [],
        '09:00pm - 10:00pm' => []
      ];

      // foreach ($statutory as $task) {
      //   $time_slots['08:00am - 10:00am'][] = $task;
      // }

      if ($statutory > 0) {
        $task = new Task;
        $task->task_subject = "Complete $statutory statutory tasks today";
        $task->is_completed = Carbon::now();
        $time_slots['08:00am - 10:00am'][] = $task;
      }

      // dd($time_slots);

      foreach ($planned_tasks as $time_slot => $data) {
        foreach ($data as $task) {
          $time_slots[$time_slot][] = $task;
        }
      }

      foreach ($daily_activities as $time_slot => $data) {
        foreach ($data as $task) {
          $time_slots[$time_slot][] = $task;
        }
      }

      $call_instructions = Instruction::select(['id', 'category_id', 'instruction', 'assigned_to', 'created_at'])->where('category_id', 10)->where('created_at', 'LIKE', "%$planned_at%")->where('assigned_to', $userid)->get();
      $users_array = Helpers::getUserArray(User::all());

      $generalCategories = \App\GeneralCategory::all()->pluck("name","id")->toArray();

      // start calulation of all time spent
      $taskCategoryWise = \App\Task::where("actual_start_date", "!=", "")
      ->where("is_completed","!=" , "")
      ->where("general_category_id","!=" , "")
      ->select([\DB::raw("sum(TIMESTAMPDIFF(MINUTE,actual_start_date, is_completed)) as spent_time"),"general_category_id","is_completed", "actual_start_date"])
      ->groupBy("general_category_id")->get()->pluck("spent_time","general_category_id")->toArray();

      $activitiesCategoryWise = \App\DailyActivity::where("actual_start_date", "!=", "")
      ->where("is_completed","!=" , "")
      ->where("general_category_id","!=" , "")
      ->select([\DB::raw("sum(TIMESTAMPDIFF(MINUTE, actual_start_date, is_completed)) as spent_time"),"general_category_id","is_completed", "actual_start_date"])
      ->groupBy("general_category_id")->get()->pluck("spent_time","general_category_id")->toArray();

      $spentTime = [];
      if(!empty($generalCategories)) {
        foreach($generalCategories as $id => $name) {
              if(!isset($spentTime[$id])){
                $spentTime[$id] = 0;
              }
              $spentTime[$id] += isset($taskCategoryWise[$id]) ? $taskCategoryWise[$id] : 0;
              $spentTime[$id] += isset($activitiesCategoryWise[$id]) ? $activitiesCategoryWise[$id] : 0;
        }
      }

      return view('dailyplanner.index', [
        'tasks'             => $tasks,
        'time_slots'        => $time_slots,
        'users_array'       => $users_array,
        'call_instructions' => $call_instructions,
        'userid'            => $userid,
        'planned_at'        => $planned_at,
        'generalCategories' => $generalCategories,
        'spentTime'         => $spentTime
      ]);
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

    public function complete(Request $request)
    {
      $user = User::find(Auth::id());
      $user->is_planner_completed = 1;
      $user->save();

      return redirect('/task')->withSuccess('You have successfully completed your daily plan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
