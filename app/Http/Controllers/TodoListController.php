<?php

namespace App\Http\Controllers;

use App\Setting;
use App\TodoCategory;
use App\TodoList;
use App\ToDoListRemarkHistoryLog;
use App\TodoStatus;
use Auth;
use Illuminate\Http\Request;

class TodoListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $search_title = request('search_title') ?? '';
            $search_status = request('search_status') ?? '';
            $search_date = request('search_date') ?? '';
            $search_todo_category_id = request('search_todo_category_id') ?? '';
            $todolists = TodoList::with('username');

            if ($s = $search_title) {
                $todolists->where('title', 'like', '%'.$s.'%');
            }

            if ($s = $search_status) {
                $todolists->where('status', 'like', '%'.$s.'%');
            }

            if ($s = $search_date) {
                $todolists->where('todo_date', $s);
            }

            if ($s = $search_todo_category_id) {
                $todolists->where('todo_category_id', $s);
            }

            //$todolists = $todolists->orderBy("todo_lists.todo_date", "desc")->paginate(Setting::get('pagination'));
            $todolists = $todolists->orderByRaw('if(isnull(todo_lists.todo_date) >= curdate() , todo_lists.todo_date, todo_lists.created_at) desc')->paginate(Setting::get('pagination'));

            $statuses = TodoStatus::all()->toArray();
            $todoCategories = TodoCategory::get();
            //dd($statuses);
            if ($request->ajax()) {
                return response()->json([
                    'tbody' => view('todolist.data', compact('todolists', 'search_title', 'statuses', 'search_todo_category_id', 'todoCategories'))->render(),
                    'links' => (string) $todolists->render(),
                ], 200);
            }

            return view('todolist.index', compact('todolists', 'search_title', 'search_status', 'search_date', 'statuses', 'search_todo_category_id', 'todoCategories'));
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $todolists = new TodoList();
            $todolists->user_id = Auth::user()->id ?? '';
            $todolists->title = $request->title;
            $todolists->subject = $request->subject;
            $todolists->status = $request->status;
            $todolists->todo_date = $request->todo_date;
            $todolists->remark = $request->remark;
            $todolists->todo_category_id = $request->todo_category_id;
            $todolists->save();
            $this->createTodolistRemarkHistory($request, $todolists->id);
            //return response()->json(["code" => 200, "data" => $todolists, "message" => "Your Todo List has been created!"]);
            return redirect()->back()->with('success', 'Your Todo List has been created!');
        } catch (\Exception $e) {
            //return response()->json(["code" => 500, "message" => $e->getMessage()]);
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TodoList  $todoList
     * @return \Illuminate\Http\Response
     */
    public function show(TodoList $todoList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TodoList  $todoList
     * @return \Illuminate\Http\Response
     */
    public function edit(TodoList $todoList)
    {
        try {
            $id = request('id') ?? '';
            $todolists = TodoList::with('username');
            if ($s = $id) {
                $todolists->where('id', $s);
            }
            $todolists = $todolists->orderBy('todo_lists.id', 'desc')->first();

            return response()->json(['code' => 200, 'data' => $todolists, 'message' => 'Your Todo List has been listed!']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TodoList  $todoList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TodoList $todoList)
    {
        try {
            $todolists = TodoList::findorfail($request->id);
            $todolists->user_id = Auth::user()->id ?? '';
            $todolists->title = $request->title;
            $todolists->subject = $request->subject;
            $todolists->status = $request->status;
            $todolists->todo_date = $request->todo_date;
            $todolists->remark = $request->remark;
            $todolists->save();
            if ($request->remark != $request->old_remark) {
                $this->createTodolistRemarkHistory($request, $todolists->id);
            }
            //return response()->json(["code" => 200, "data" => $todolists, "message" => "Your Todo List has been created!"]);
            return redirect()->back()->with('success', 'Your Todo List has been Updated!');
        } catch (\Exception $e) {
            //return response()->json(["code" => 500, "message" => $e->getMessage()]);
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TodoList  $todoList
     * @return \Illuminate\Http\Response
     */
    public function destroy(TodoList $todoList)
    {
        //
    }

    public function createTodolistRemarkHistory($request, $id)
    {
        try {
            $todoRemark = new ToDoListRemarkHistoryLog();
            $todoRemark->user_id = Auth::user()->id;
            $todoRemark->todo_list_id = $id;
            $todoRemark->remark = $request->remark;
            $todoRemark->old_remark = $request->old_remark ?? '';
            $todoRemark->save();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function getRemarkHistory(Request $request)
    {
        try {
            $todoRemark = ToDoListRemarkHistoryLog::with('username')->where('todo_list_id', $request->id)->get()->toArray();

            return response()->json(['code' => 200, 'data' => $todoRemark, 'message' => 'Your Todo remark history has been list!']);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function storeStatus(Request $request)
    {
        try {
            $todoStatus = new TodoStatus();
            $todoStatus->name = $request->status_name;
            $todoStatus->save();

            return redirect()->back()->with('success', 'Your Todo status has been Added!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function statusUpdate(Request $request)
    {
        $todolists = TodoList::findorfail($request->id);
        $todolists->status = $request->status;
        $todolists->save();

        return response()->json(['code' => 200, 'data' => $todolists, 'message' => 'Your Todo Status has been Updated!']);
    }

    public function todoCategoryUpdate(Request $request)
    {
        try {
            $todolists = TodoList::findorfail($request->id);
            $todolists->todo_category_id = $request->todo_category_id;
            $todolists->save();

            return response()->json(['code' => 200, 'data' => $todolists, 'message' => 'Your Todo Category has been Updated!']);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function storeTodoCategory(Request $request)
    {
        try {
            $todoStatus = new TodoCategory();
            $todoStatus->name = $request->todo_category_name;
            $todoStatus->save();

            return redirect()->back()->with('success', 'Your Todo status has been Added!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
