<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserFeedbackCategory;
use App\UserFeedbackStatus;
use App\User;
use App\UserFeedbackCategorySopHistory;
use App\UserFeedbackCategorySopHistoryComment;
use App\Sop;
use App\DeveloperTask;
use App\Task;


class UserManagementController extends Controller
{
    public function addFeedbackTableData(Request $request)
    {
        $status = UserFeedbackStatus::get();
        $user_id = '';
        if(\Auth::user()->isAdmin() == true) {
            $category = UserFeedbackCategory::select('id', 'user_id', 'sop_id', 'category', 'sop')->groupBy('category');
            //if($request->user_id)
            //    $category = $category->where('user_id', $request->user_id)->groupBy('category');
        } else {
            $category = UserFeedbackCategory::select('id', 'user_id', 'sop_id', 'category', 'sop')->where('user_id', \Auth::user()->id)->groupBy('category');
            $category = $category->get();
            if(empty($category[0]->id)) {
                $category = UserFeedbackCategory::select('id', 'user_id', 'sop_id', 'category', 'sop')->groupBy('category');
            }
        }
            
        //\Auth::user()->isAdmin()
        if($request->user_id){
            if(\Auth::user()->isAdmin() == true) {
                $user_id = $request->user_id;
            } else {
                $user_id = \Auth::user()->id;
            }
        }
        $users = User::all();
        $sops = Sop::all();
        //dd($sops);
        $category = $category->paginate(25);
        return view('user-management.get-user-feedback-table',compact('category', 'status','user_id', 'users', 'request', 'sops'));
    }

    public function taskCount($user_feedback_cat_id)
    {
        $taskStatistics['Devtask'] = DeveloperTask::where('user_feedback_cat_id', $user_feedback_cat_id)->where('status', '!=', 'Done')->select();

        $query = DeveloperTask::join('users', 'users.id', 'developer_tasks.assigned_to')->where('user_feedback_cat_id', $user_feedback_cat_id)->where('status', '!=', 'Done')->select('developer_tasks.id', 'developer_tasks.task as subject', 'developer_tasks.status', 'users.name as assigned_to_name');
        $query = $query->addSelect(\DB::raw("'Devtask' as task_type,'developer_task' as message_type"));
        $taskStatistics = $query->get();
        //print_r($taskStatistics);
        $othertask = Task::where('user_feedback_cat_id', $user_feedback_cat_id)->whereNull('is_completed')->select();
        $query1 = Task::join('users', 'users.id', 'tasks.assign_to')->where('user_feedback_cat_id', $user_feedback_cat_id)->whereNull('is_completed')->select('tasks.id', 'tasks.task_subject as subject', 'tasks.assign_status', 'users.name as assigned_to_name');
        $query1 = $query1->addSelect(\DB::raw("'Othertask' as task_type,'task' as message_type"));
        $othertaskStatistics = $query1->get();
        $merged = $othertaskStatistics->merge($taskStatistics);

        return response()->json(["code" => 200, "taskStatistics" => $merged]);

    }

    public function sopHistory(Request $request)
    {
        try{
            if($request->sop_text == '')
                return response()->json(['code'=>'500',  'message' => 'Please enter sop name']);
            $sop = new UserFeedbackCategorySopHistory();
            $sop->category_id = $request->cat_id;
            $sop->user_id = \Auth::user()->id;
            $sop->sop = $request->sop_text;
            $sop->save();
            UserFeedbackCategory::where('id', $request->cat_id)->update(['sop_id' => $sop->id, 'sop' => $request->sop_text, 'sops_id' => $request->sops_id]);
            return response()->json(['code'=>'200', 'data' => $sop, 'message' => 'Data saved successfully']);
        } catch(\Exception $e){
            return response()->json(['code'=>'500',  'message' => $e->getMessage()]);
        }
    }
    
    public function getSopHistory(Request $request)
    {
        try{
            if($request->cat_id == '')
                return response()->json(['code'=>'500',  'message' => 'History not found']);
            $sop = UserFeedbackCategorySopHistory::where('category_id', $request->cat_id)->get();
            return response()->json(['code'=>'200', 'data' => $sop, 'message' => 'Data listed successfully']);
        } catch(\Exception $e){
            return response()->json(['code'=>'500',  'message' => $e->getMessage()]);
        }
    }

    public function getSopCommentHistory(Request $request)
    {
        try{
            if($request->sop_history_id == '')
                return response()->json(['code'=>'500',  'message' => 'History not found']);
            $sopComment = UserFeedbackCategorySopHistoryComment::where('sop_history_id', $request->sop_history_id)->get();
            
            return response()->json(['code'=>'200', 'data' => $sopComment, 'message' => 'Data listed successfully']);
        } catch(\Exception $e){
            return response()->json(['code'=>'500',  'message' => $e->getMessage()]);
        }
    }

    public function sopHistoryComment(Request $request)
    {
        try{
            $sopComment = new UserFeedbackCategorySopHistoryComment();
            $sopComment->sop_history_id = $request->sop_history_id;
            $sopComment->user_id = \Auth::user()->id;
            $sopComment->comment = $request->comment;
            $sopComment->accept_reject = $request->accept_reject;
            $sopComment->save();
            return response()->json(['code'=>'200', 'data' => $sopComment, 'message' => 'Comment saved successfully']);
        } catch(\Exception $e){
            return response()->json(['code'=>'500',  'message' => $e->getMessage()]);
        }
    }
}
