<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\SSP;
use Exception;
use DB;

class KeywordassignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$coupons = Coupon::orderBy('id', 'DESC')->get();
        //$keywordassign = DB::table('keywordassign')->select('*')->get();
        $keywordassign = DB::table('keywordassign')
            ->select('keywordassign.id','keywordassign.keyword','task_categories.title','keywordassign.task_description','users.name')
            ->leftJoin('users', 'keywordassign.assign_to', '=', 'users.id')
            ->leftJoin('task_categories', 'keywordassign.task_category', '=', 'task_categories.id')
            ->get();

        return view('keywordassign.index', compact('keywordassign'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $task_category = DB::table('task_categories')->select('*')->get();
        $userslist = DB::table('users')->select('*')->get();
        return view('keywordassign.create', compact('task_category','userslist'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $task = $this->validate(request(), [
            'keyword' => 'required',
            'task_category' => 'required',
            'task_description' => 'required',
            'assign_to' => 'required'
        ]);
        // Create the task
        $keyword = $request->keyword;
        $task_category = $request->task_category;
        $task_description = $request->task_description;
        $assign_to = $request->assign_to;
        $created_date = date("Y-m-d H:i:s");
        $updated_date = date("Y-m-d H:i:s");
        $insert_data = array(
                "keyword"=>$keyword,
                "task_category"=>$task_category,
                "task_description"=>$task_description,
                "assign_to"=>$assign_to,
                "created_date"=>$created_date,
                "updated_date"=>$updated_date,
            );
        DB::table('keywordassign')->insert($insert_data);
        return redirect()->route('keywordassign.index')
            ->with('success', 'Keyword assign created successfully.');
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
        $keywordassign = DB::table('keywordassign')->select('*')->where('id',$id)->get();
        $task_category = DB::table('task_categories')->select('*')->get();
        $userslist = DB::table('users')->select('*')->get();
        return view('keywordassign.edit', compact('keywordassign','task_category','userslist'));
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
        $task = $this->validate(request(), [
            'keyword' => 'required',
            'task_category' => 'required',
            'task_description' => 'required',
            'assign_to' => 'required'
        ]);
        // Create the task
        $keyword = $request->keyword;
        $task_category = $request->task_category;
        $task_description = $request->task_description;
        $assign_to = $request->assign_to;
        $updated_date = date("Y-m-d H:i:s");
        $insert_data = array(
                "keyword"=>$keyword,
                "task_category"=>$task_category,
                "task_description"=>$task_description,
                "assign_to"=>$assign_to,
                "updated_date"=>$updated_date,
            );
        $affected = DB::table('keywordassign')
              ->where('id', $id)
              ->update($insert_data);
        return redirect()->route('keywordassign.index')
            ->with('success', 'Keyword assign updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('keywordassign')->where('id', '=', $id)->delete();
        return redirect()->route('keywordassign.index')
            ->with('success', 'Keyword assign deleted successfully.');
    }
    public function taskcategory(Request $request)
    {
        $task_category_name = $request->task_category_name;
        $insert_data = array(
                "parent_id"=>0,
                "title"=>$task_category_name,
            );
        DB::table('task_categories')->insert($insert_data);
        $id = DB::getPdo()->lastInsertId();
        return response()->json(["code" => 200 , "data" => ['id'=>$id,'Category'=>$task_category_name], "message" => "Task Category Inserted"]);
    }
}
