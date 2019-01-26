<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DeveloperTask;
use App\DeveloperModule;
use App\DeveloperComment;
use App\DeveloperCost;
use App\User;
use App\Helpers;
use App\Issue;
use Auth;
use Plank\Mediable\Media;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;

class DevelopmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct() {
     		$this->middleware('permission:developer-tasks', ['except' => ['issueCreate', 'issueStore', 'moduleStore']]);
     	}

    public function index(Request $request)
    {
      // $tasks = DeveloperTask::where('user_id', $user)
      $user = $request->user ? $request->user : Auth::id();
      $tasks = DeveloperTask::where('user_id', $user)->where('module', 0)->where('status', '!=', 'Done')->orderBy('priority')->get()->groupBy('module_id');
      $completed_tasks = DeveloperTask::where('user_id', $user)->where('module', 0)->where('status', 'Done')->orderBy('priority')->get()->groupBy('module_id');
      $modules = DeveloperModule::all();
      $users = Helpers::getUserArray(User::all());
      $comments = DeveloperComment::where('send_to', $user)->latest()->get();
      $amounts = DeveloperCost::where('user_id', $user)->orderBy('paid_date')->get();
      $module_names = [];

      foreach ($modules as $module) {
        $module_names[$module->id] = $module->name;
      }

      return view('development.index', [
        'tasks' => $tasks,
        'completed_tasks' => $completed_tasks,
        'users' => $users,
        'modules' => $modules,
        'user'  => $user,
        'module_names'  => $module_names,
        'comments'  => $comments,
        'amounts'  => $amounts
      ]);
    }

    public function issueIndex()
    {
      $issues = Issue::all();
      $users = Helpers::getUserArray(User::all());

      return view('development.issue', [
        'issues'  => $issues,
        'users'   => $users
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

    public function issueCreate()
    {
      return view('development.issue-create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
        'priority'  => 'required|integer',
        'task'      => 'required|string|min:3',
        'cost'      => 'sometimes||nullable|integer',
        'status'    => 'required'
      ]);

      $data = $request->except('_token');
      $data['user_id'] = $request->user_id ? $request->user_id : Auth::id();

      // if ($request->module_id) {
      //   $module = DeveloperTask::find($request->module_id);
      //   $module->user_id = $data['user_id'];
      //   $module->module = 0;
      //   $module->save();
      // }

      $task = DeveloperTask::create($data);

      if ($request->hasfile('images')) {
        foreach ($request->file('images') as $image) {
          $media = MediaUploader::fromSource($image)->upload();
          $task->attachMedia($media,config('constants.media_tags'));
        }
      }

      return redirect()->route('development.index')->with('success', 'You have successfully added task!');
    }

    public function issueStore(Request $request)
    {
      $this->validate($request, [
        'priority'  => 'required|integer',
        'issue'     => 'required|min:3'
      ]);

      $data = $request->except('_token');

      $issue = Issue::create($data);

      if ($request->hasfile('images')) {
        foreach ($request->file('images') as $image) {
          $media = MediaUploader::fromSource($image)->upload();
          $issue->attachMedia($media,config('constants.media_tags'));
        }
      }

      return redirect()->back()->with('success', 'You have successfully submitted an issue!');
    }

    public function moduleStore(Request $request)
    {
      $this->validate($request, [
        'name'  => 'required|string|min:3'
      ]);

      $data = $request->except('_token');

      DeveloperModule::create($data);

      return redirect()->back()->with('success', 'You have successfully submitted an issue!');
    }

    public function commentStore(Request $request)
    {
      $this->validate($request, [
        'message'  => 'required|string|min:3'
      ]);

      $data = $request->except('_token');
      $data['user_id'] = Auth::id();

      DeveloperComment::create($data);

      return redirect()->back()->with('success', 'You have successfully wrote a comment!');
    }

    public function costStore(Request $request)
    {
      $this->validate($request, [
        'amount'      => 'required|numeric',
        'paid_date'   => 'required'
      ]);

      $data = $request->except('_token');

      DeveloperCost::create($data);

      return redirect()->back()->with('success', 'You have successfully added payment!');
    }

    public function awaitingResponse(Request $request, $id)
    {
      $comment = DeveloperComment::find($id);
      $comment->status = 1;
      $comment->save();

      return response('success');
    }

    public function issueAssign(Request $request, $id)
    {
      $this->validate($request, [
        'user_id' => 'required|integer'
      ]);

      $issue = Issue::find($id);
      $task = new DeveloperTask;

      $task->priority = $issue->priority;
      $task->task = $issue->issue;
      $task->user_id = $request->user_id;
      $task->status = 'Planned';

      $task->save();

      foreach ($issue->getMedia(config('constants.media_tags')) as $image) {
        $task->attachMedia($image, config('constants.media_tags'));
      }

      $issue->user_id = $request->user_id;
      $issue->save();
      $issue->delete();

      return redirect()->back()->with('success', 'You have successfully assigned the issue!');
    }

    public function moduleAssign(Request $request, $id)
    {
      $this->validate($request, [
        'user_id' => 'required|integer'
      ]);

      $module = DeveloperTask::find($id);

      $module->user_id = $request->user_id;
      $module->module = 0;

      $module->save();

      return redirect()->route('development.index')->with('success', 'You have successfully assigned the module!');
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
      $this->validate($request, [
        'priority'  => 'required|integer',
        'task'      => 'required|string|min:3',
        'cost'      => 'sometimes||nullable|integer',
        'status'    => 'required'
      ]);

      $data = $request->except('_token');
      $data['user_id'] = $request->user_id ? $request->user_id : Auth::id();

      $task = DeveloperTask::find($id);
      $task->update($data);

      if ($request->hasfile('images')) {
        foreach ($request->file('images') as $image) {
          $media = MediaUploader::fromSource($image)->upload();
          $task->attachMedia($media,config('constants.media_tags'));
        }
      }

      return redirect()->route('development.index')->with('success', 'You have successfully updated task!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      DeveloperTask::find($id)->delete();

      return redirect()->route('development.index')->with('success', 'You have successfully archived the task!');
    }

    public function issueDestroy($id)
    {
      Issue::find($id)->delete();

      return redirect()->route('development.issue.index')->with('success', 'You have successfully archived the issue!');
    }

    public function moduleDestroy($id)
    {
      DeveloperModule::find($id)->delete();

      return redirect()->route('development.index')->with('success', 'You have successfully archived the module!');
    }
}
