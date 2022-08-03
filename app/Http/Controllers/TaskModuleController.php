<?php

namespace App\Http\Controllers;

use App\PushNotification;
use App\SatutoryTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Helpers;
use App\User;
use App\Task;
use App\TaskCategory;
use App\TaskStatus;
use App\TaskDueDateHistoryLog;
use App\TaskRemark;
use App\Contact;
use App\Setting;
use App\Remark;
use App\DocumentRemark;
use App\DeveloperTask;
use App\NotificationQueue;
use App\ChatMessage;
use App\DeveloperTaskHistory;
use App\ScheduledMessage;
use App\WhatsAppGroup;
use App\WhatsAppGroupNumber;
use App\PaymentReceipt;
use App\ChatMessagesQuickData;
use App\Hubstaff\HubstaffMember;
use App\Hubstaff\HubstaffTask;
use Illuminate\Pagination\LengthAwarePaginator;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Storage;
use Plank\Mediable\MediaUploaderFacade as MediaUploader;
use App\Helpers\HubstaffTrait;
use App\Helpers\MessageHelper;
use App\UserRate;
use Exception;
use Response;
use App\Hubstaff\HubstaffActivity;
use App\Sop;
use App\TaskUserHistory;
use App\LogChatMessage;
use App\Models\Tasks\TaskHistoryForStartDate;

class TaskModuleController extends Controller {
    use HubstaffTrait;
    private $githubClient;

    public function __construct() {
        // $this->init(getenv('HUBSTAFF_SEED_PERSONAL_TOKEN'));
        $this->githubClient = new Client([
            // 'auth' => [getenv('GITHUB_USERNAME'), getenv('GITHUB_TOKEN')]
            'auth' => [config('env.GITHUB_USERNAME'), config('env.GITHUB_TOKEN')],
        ]);
        $this->init(config('env.HUBSTAFF_SEED_PERSONAL_TOKEN'));
    }

    public function index(Request $request) {
        if ($request->input('selected_user') == '') {
            $userid = Auth::id();

            $searchMasterUserId = $userid;
            if ($request->search_master_user_id != '')
                $searchMasterUserId = $request->search_master_user_id;

            $searchSecondMasterUserId = $userid;
            if ($request->search_second_master_user_id != '')
                $searchSecondMasterUserId = $request->search_second_master_user_id;

            $userquery = ' AND (assign_from = ' . $userid . ' OR  second_master_user_id = ' . $searchSecondMasterUserId . ' OR  master_user_id = ' . $searchMasterUserId . ' OR  id IN (SELECT task_id FROM task_users WHERE user_id = ' . $userid . ' AND type LIKE "%User%")) ';
        } else {
            $userid = $request->input('selected_user');

            $searchMasterUserId = $userid;
            if ($request->search_master_user_id != '')
                $searchMasterUserId = $request->search_master_user_id;

            $searchSecondMasterUserId = $userid;
            if ($request->search_second_master_user_id != '')
                $searchSecondMasterUserId = $request->search_second_master_user_id;

            $userquery = ' AND (master_user_id = ' . $searchMasterUserId . ' OR  second_master_user_id = ' . $searchSecondMasterUserId . ' OR  id IN (SELECT task_id FROM task_users WHERE user_id = ' . $userid . ' AND type LIKE "%User%")) ';
        }

        if (!$request->input('type') || $request->input('type') == '') {
            $type = 'pending';
        } else {
            $type = $request->input('type');
        }
        $activeCategories = TaskCategory::where('is_active', 1)->pluck('id')->all();

        $categoryWhereClause = '';
        $category = '';
        // $request->category = $request->category ? $request->category : 1;
        if ($request->category != '') {
            $categoryWhereClause = "AND category = $request->category";
            $category = $request->category;
            /*if ($request->category != 1) {
               $categoryWhereClause = "AND category = $request->category";
               $category = $request->category;
            } else {
                $category_condition  = implode(',', $activeCategories);
                if ($category_condition != '' || $category_condition != null) {
                    $category_condition = '( ' . $category_condition . ' )';
                    $categoryWhereClause = "AND category in " . $category_condition;
                } else {
                    $categoryWhereClause = "";
                }
            }*/
        }

        $term = $request->term ?? "";
        $searchWhereClause = '';

        if ($request->term != '') {
            $searchWhereClause = ' AND (id LIKE "%' . $term . '%" OR category IN (SELECT id FROM task_categories WHERE title LIKE "%' . $term . '%") OR task_subject LIKE "%' . $term . '%" OR task_details LIKE "%' . $term . '%" OR assign_from IN (SELECT id FROM users WHERE name LIKE "%' . $term . '%") OR id IN (SELECT task_id FROM task_users WHERE user_id IN (SELECT id FROM users WHERE name LIKE "%' . $term . '%")))';
        }
        if ($request->get('is_statutory_query') != '' && $request->get('is_statutory_query') != null) {
            $searchWhereClause .= ' AND is_statutory = ' . $request->get('is_statutory_query');
        } else {
            $searchWhereClause .= ' AND is_statutory != 3';
        }
        $orderByClause = ' ORDER BY';
        if ($request->sort_by == 1) {
            $orderByClause .= ' tasks.created_at desc,';
        } elseif ($request->sort_by == 2) {
            $orderByClause .= ' tasks.created_at asc,';
        }
        $data['task'] = [];

        $search_term_suggestions = [];
        $search_suggestions = [];
        $assign_from_arr = array(0);
        $special_task_arr = array(0);
        $assign_to_arr = array(0);
        $data['task']['pending'] = [];
        $data['task']['statutory_not_completed'] = [];
        $data['task']['completed'] = [];
        $status_filter = '';
        if ($request->filter_status) {
            $status_filter = " AND status IN ('" . implode("','", $request->filter_status) . "')";
        } else {
            //1 => for "done" lable status
            $status_filter = " AND status NOT IN ('1')";
        }


        $flag_filter = ' ';

        if ($request->ajax()) {
            $flag_filter = ' AND is_flagged = 0 ';
            if ($request->flag_filter) {
                $flag_filter = ' ';
            }
        }

        if ($type == 'pending') {
            $paginate = 50;
            $page = $request->get('page', 1);
            $offSet = ($page * $paginate) - $paginate;

            $orderByClause .= ' is_flagged DESC, message_created_at DESC';
            $isCompleteWhereClose = ' AND is_verified IS NULL ';

            if (!Auth::user()->isAdmin()) {
                $isCompleteWhereClose = ' AND is_verified IS NULL ';
            }
            if ($request->filter_by == 1) {
                $isCompleteWhereClose = ' AND is_completed IS NULL ';
            }
            if ($request->filter_by == 2) {
                $isCompleteWhereClose = ' AND is_completed IS NOT NULL AND is_verified IS NULL ';
            }

            $data['task']['pending'] = DB::select('
			SELECT tasks.*

			FROM (
			  SELECT * FROM tasks
			  LEFT JOIN (
				  SELECT 
				  chat_messages.id as message_id, 
				  chat_messages.task_id, 
				  chat_messages.message, 
				  chat_messages.status as message_status, 
				  chat_messages.sent as message_type, 
				  chat_messages.created_at as message_created_at, 
				  chat_messages.is_reminder AS message_is_reminder,
				  chat_messages.user_id AS message_user_id
				  FROM chat_messages join chat_messages_quick_datas on chat_messages_quick_datas.last_communicated_message_id = chat_messages.id WHERE chat_messages.status not in(7,8,9) and chat_messages_quick_datas.model="App\\\\Task"
			  ) as chat_messages  ON chat_messages.task_id = tasks.id
			) AS tasks
			WHERE (deleted_at IS NULL) AND (id IS NOT NULL) AND is_statutory != 1 ' . $isCompleteWhereClose . $userquery . $status_filter . $flag_filter . $categoryWhereClause . $searchWhereClause . $orderByClause . ' limit ' . $paginate . ' offset ' . $offSet . '; ');


            foreach ($data['task']['pending'] as $task) {
                array_push($assign_to_arr, $task->assign_to);
                array_push($assign_from_arr, $task->assign_from);
                array_push($special_task_arr, $task->id);
            }

            $user_ids_from = array_unique($assign_from_arr);
            $user_ids_to = array_unique($assign_to_arr);

            foreach ($data['task']['pending'] as $task) {
                $search_suggestions[] = "#" . $task->id . " " . $task->task_subject . ' ' . $task->task_details;
                $from_exist = in_array($task->assign_from, $user_ids_from);
                if ($from_exist) {
                    $from_user = User::find($task->assign_from);
                    if ($from_user) {
                        $search_term_suggestions[] = $from_user->name;
                    }
                }

                $to_exist = in_array($task->assign_to, $user_ids_to);
                if ($to_exist) {
                    $to_user = User::find($task->assign_to);
                    if ($to_user) {
                        $search_term_suggestions[] = $to_user->name;
                    }
                }
                $search_term_suggestions[] = "$task->id";
                $search_term_suggestions[] = $task->task_subject;
                $search_term_suggestions[] = $task->task_details;
            }
        } elseif ($type == 'completed') {
            $paginate = 50;
            $page = $request->get('page', 1);
            $offSet = ($page * $paginate) - $paginate;
            $orderByClause .= ' last_communicated_at DESC';

            $data['task']['completed'] = DB::select('
                SELECT *,
 				message_id,
                message,
                message_status,
                message_type,
                message_created_At as last_communicated_at
                FROM (
                  SELECT * FROM tasks
                 LEFT JOIN (
					SELECT 
					chat_messages.id as message_id, 
					chat_messages.task_id, 
					chat_messages.message, 
					chat_messages.status as message_status, 
					chat_messages.sent as message_type, 
					chat_messages.created_at as message_created_at, 
					chat_messages.is_reminder AS message_is_reminder,
					chat_messages.user_id AS message_user_id
					FROM chat_messages join chat_messages_quick_datas on chat_messages_quick_datas.last_communicated_message_id = chat_messages.id WHERE chat_messages.status not in(7,8,9) and chat_messages_quick_datas.model="App\\\\Task"
                 ) AS chat_messages ON chat_messages.task_id = tasks.id
                ) AS tasks
                WHERE (deleted_at IS NULL) AND (id IS NOT NULL) AND is_statutory != 1 AND is_verified IS NOT NULL ' . $userquery . $categoryWhereClause . $status_filter . $flag_filter . $searchWhereClause . $orderByClause . ' limit ' . $paginate . ' offset ' . $offSet . ';');


            foreach ($data['task']['completed'] as $task) {
                array_push($assign_to_arr, $task->assign_to);
                array_push($assign_from_arr, $task->assign_from);
                array_push($special_task_arr, $task->id);
            }

            $user_ids_from = array_unique($assign_from_arr);
            $user_ids_to = array_unique($assign_to_arr);

            foreach ($data['task']['completed'] as $task) {
                $search_suggestions[] = "#" . $task->id . " " . $task->task_subject . ' ' . $task->task_details;
                $from_exist = in_array($task->assign_from, $user_ids_from);
                if ($from_exist) {
                    $from_user = User::find($task->assign_from);
                    if ($from_user) {
                        $search_term_suggestions[] = $from_user->name;
                    }
                }

                $to_exist = in_array($task->assign_to, $user_ids_to);
                if ($to_exist) {
                    $to_user = User::find($task->assign_to);
                    if ($to_user) {
                        $search_term_suggestions[] = $to_user->name;
                    }
                }
                $search_term_suggestions[] = "$task->id";
                $search_term_suggestions[] = $task->task_subject;
                $search_term_suggestions[] = $task->task_details;
            }
        } elseif ($type == 'statutory_not_completed') {
            $paginate = 50;
            $page = $request->get('page', 1);
            $offSet = ($page * $paginate) - $paginate;
            $orderByClause .= ' last_communicated_at DESC';

            $data['task']['statutory_not_completed'] = DB::select('
	               SELECT *,
				   message_id,
	               message,
	               message_status,
	               message_type,
	               message_created_At as last_communicated_at

	               FROM (
	                 SELECT * FROM tasks
	                 LEFT JOIN (
							SELECT 
							chat_messages.id as message_id, 
							chat_messages.task_id, 
							chat_messages.message, 
							chat_messages.status as message_status, 
							chat_messages.sent as message_type, 
							chat_messages.created_at as message_created_at, 
							chat_messages.is_reminder AS message_is_reminder,
							chat_messages.user_id AS message_user_id
							FROM chat_messages join chat_messages_quick_datas on chat_messages_quick_datas.last_communicated_message_id = chat_messages.id WHERE chat_messages.status not in(7,8,9) and chat_messages_quick_datas.model="App\\\\Task"
	                 ) AS chat_messages ON chat_messages.task_id = tasks.id

	               ) AS tasks
				   WHERE (deleted_at IS NULL) AND (id IS NOT NULL) AND is_statutory = 1 AND is_verified IS NULL ' . $userquery . $categoryWhereClause . $status_filter . $flag_filter . $orderByClause . ' limit ' . $paginate . ' offset ' . $offSet . ';');

            foreach ($data['task']['statutory_not_completed'] as $task) {
                array_push($assign_to_arr, $task->assign_to);
                array_push($assign_from_arr, $task->assign_from);
                array_push($special_task_arr, $task->id);
            }

            $user_ids_from = array_unique($assign_from_arr);
            $user_ids_to = array_unique($assign_to_arr);

            foreach ($data['task']['statutory_not_completed'] as $task) {
                $search_suggestions[] = "#" . $task->id . " " . $task->task_subject . ' ' . $task->task_details;
                $from_exist = in_array($task->assign_from, $user_ids_from);
                if ($from_exist) {
                    $from_user = User::find($task->assign_from);
                    if ($from_user) {
                        $search_term_suggestions[] = $from_user->name;
                    }
                }

                $to_exist = in_array($task->assign_to, $user_ids_to);
                if ($to_exist) {
                    $to_user = User::find($task->assign_to);
                    if ($to_user) {
                        $search_term_suggestions[] = $to_user->name;
                    }
                }
                $search_term_suggestions[] = "$task->id";
                $search_term_suggestions[] = $task->task_subject;
                $search_term_suggestions[] = $task->task_details;
            }
        } else {
            return;
        }


        //task pending backup

        // $data['task']['pending'] = DB::select('
        // SELECT tasks.*

        // FROM (
        //   SELECT * FROM tasks
        //   LEFT JOIN (
        // 	  SELECT
        // 		  MAX(id) as max_id,
        // 		  task_id as tk
        // 	  FROM chat_messages
        // 	  WHERE chat_messages.status not in(7,8,9)
        // 	  GROUP BY task_id
        // 	  ORDER BY chat_messages.created_at DESC
        //    ) AS chat_messages_max ON chat_messages_max.tk = tasks.id
        //   LEFT JOIN (
        // 	  SELECT
        // 		  id as message_id,
        // 		  task_id,
        // 		  message,
        // 		  status as message_status,
        // 		  sent as message_type,
        // 		  created_at as message_created_at,
        // 		  is_reminder AS message_is_reminder,
        // 		  user_id AS message_user_id
        // 	  FROM chat_messages
        //   ) AS chat_messages ON chat_messages.message_id = chat_messages_max.max_id
        // ) AS tasks
        // WHERE (deleted_at IS NULL) AND (id IS NOT NULL) AND is_statutory != 1 AND is_verified IS NULL AND (assign_from = ' . $userid . ' OR id IN (SELECT task_id FROM task_users WHERE user_id = ' . $userid . ' AND type LIKE "%User%")) ' . $categoryWhereClause . $searchWhereClause . '
        // 		   AND (message_id = (
        // 			 SELECT MAX(id) FROM chat_messages WHERE task_id = tasks.id
        // 			 ) OR message_id IS NULL)
        // ORDER BY is_flagged DESC, message_created_at DESC;
        // 		 ');
        //end pending backup



        // dd($data['task']['pending']);

        // $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // $perPage = Setting::get('pagination');
        // $currentItems = array_slice($data['task']['pending'], $perPage * ($currentPage - 1), $perPage);
        //
        // $data['task']['pending'] = new LengthAwarePaginator($currentItems, count($data['task']['pending']), $perPage, $currentPage, [
        // 	'path'	=> LengthAwarePaginator::resolveCurrentPath()
        // ]);

        // dd($data['task']['pending']);

        // $tasks = Task::all();
        //
        // foreach($tasks as $task) {
        // 	if ($task->assign_to != 0) {
        // 		$user = $task->assign_to;
        // 		$task->users()->syncWithoutDetaching($user);
        // 	}
        // }

        // $data['task']['completed']  = Task::where( 'is_statutory', '=', 0 )
        //                                     ->whereNotNull( 'is_completed'  )
        // 									->where( function ($query ) use ($userid) {
        // 										return $query->orWhere( 'assign_from', '=', $userid )
        // 										             ->orWhere( 'assign_to', '=', $userid );
        // 									});
        // if ($request->category != '') {
        // 	$data['task']['completed'] = $data['task']['completed']->where('category', $request->category);
        // }
        //
        // if ($request->term != '') {
        // 	$data['task']['completed'] = $data['task']['completed']->where(function ($query) use ($term) {
        // 		$query->whereRaw('category IN (SELECT id FROM task_categories WHERE name LIKE "%' . $term . '%") OR assign_from IN (SELECT id FROM users WHERE name LIKE "%' . $term . '%")')
        // 					->orWhere('id', 'LIKE', "%$term%")
        // 					->orWhere('task_details', 'LIKE', "%$term%")->orWhere('task_subject', 'LIKE', "%$term%");
        // 	});
        // }
        //
        // $data['task']['completed'] = $data['task']['completed']->get()->toArray();

        //completed task backup

        // $data['task']['completed'] = DB::select('
        // SELECT *,
        //  message_id,
        // message,
        // message_status,
        // message_type,
        // message_created_At as last_communicated_at
        // FROM (
        //   SELECT * FROM tasks
        //   LEFT JOIN (
        // 	 SELECT
        // 		 MAX(id) as max_id,
        // 		 task_id as tk
        // 	 FROM chat_messages
        // 	 WHERE chat_messages.status not in(7,8,9)
        // 	 GROUP BY task_id
        // 	 ORDER BY chat_messages.created_at DESC
        //   ) AS chat_messages_max ON chat_messages_max.tk = tasks.id
        //  LEFT JOIN (
        // 	 SELECT
        // 		 id as message_id,
        // 		 task_id,
        // 		 message,
        // 		 status as message_status,
        // 		 sent as message_type,
        // 		 created_at as message_created_At,
        // 		 is_reminder AS message_is_reminder,
        // 		 user_id AS message_user_id
        // 	 FROM chat_messages
        //  ) AS chat_messages ON chat_messages.message_id = chat_messages_max.max_id
        // ) AS tasks
        // WHERE (deleted_at IS NULL) AND (id IS NOT NULL) AND is_statutory != 1 AND is_verified IS NOT NULL AND (assign_from = ' . $userid . ' OR id IN (SELECT task_id FROM task_users WHERE user_id = ' . $userid . ' AND type LIKE "%User%")) ' . $categoryWhereClause . $searchWhereClause . '
        // ORDER BY last_communicated_at DESC;
        // 		 ');

        //completed task backup end

        // $satutory_tasks = SatutoryTask::latest()
        //                                          ->orWhere( 'assign_from', '=', $userid )
        // 										 ->orWhere( 'assign_to', '=', $userid )->whereNotNull('completion_date')
        //                                          ->get();
        //
        // foreach ($satutory_tasks as $task) {
        // 	switch ($task->recurring_type) {
        // 		case 'EveryDay':
        // 			if (Carbon::parse($task->completion_date)->format('Y-m-d') < date('Y-m-d')) {
        // 				$task->completion_date = null;
        // 				$task->save();
        // 			}
        // 			break;
        // 		case 'EveryWeek':
        // 			if (Carbon::parse($task->completion_date)->addWeek()->format('Y-m-d') < date('Y-m-d')) {
        // 				$task->completion_date = null;
        // 				$task->save();
        // 			}
        // 			break;
        // 		case 'EveryMonth':
        // 			if (Carbon::parse($task->completion_date)->addMonth()->format('Y-m-d') < date('Y-m-d')) {
        // 				$task->completion_date = null;
        // 				$task->save();
        // 			}
        // 			break;
        // 		case 'EveryYear':
        // 			if (Carbon::parse($task->completion_date)->addYear()->format('Y-m-d') < date('Y-m-d')) {
        // 				$task->completion_date = null;
        // 				$task->save();
        // 			}
        // 			break;
        // 		default:
        //
        // 	}
        // }

        // $data['task']['statutory'] = SatutoryTask::latest()->where(function ($query) use ($userid) {
        // 	$query->where('assign_from', $userid)
        //  				->orWhere('assign_to', $userid);
        // });
        //
        //
        // if ($request->category != '') {
        // 	$data['task']['statutory'] = $data['task']['statutory']->where('category', $request->category);
        // }
        //
        // if ($request->term != '') {
        // 	$data['task']['statutory'] = $data['task']['statutory']->where(function ($query) use ($term) {
        // 		$query->whereRaw('category IN (SELECT id FROM task_categories WHERE name LIKE "%' . $term . '%") OR assign_from IN (SELECT id FROM users WHERE name LIKE "%' . $term . '%")')
        // 					->orWhere('id', 'LIKE', "%$term%")
        // 					->orWhere('task_details', 'LIKE', "%$term%")->orWhere('task_subject', 'LIKE', "%$term%");
        // 	});
        // }
        //
        // $data['task']['statutory'] = $data['task']['statutory']->get()->toArray();

        // $data['task']['statutory_completed'] = Task::latest()->where( 'is_statutory', '=', 1 )
        //                                    ->whereNotNull( 'is_completed'  )
        //                                    ->where( function ($query ) use ($userid) {
        // 	                                   return $query->orWhere('assign_from', '=', $userid)
        // 	                                                ->orWhere('assign_to', '=', $userid);
        //                                    })
        //                                    ->get()->toArray();


        // dd($data['task']['statutory_completed']);

        // foreach ($data['task']['statutory_completed'] as $task) {
        // 	dump($task->id);
        // }
        //
        // dd('stap');

        // $data['task']['statutory_today'] = Task::latest()->where( 'is_statutory', '=', 1 )
        //                                            ->where( 'is_completed', '=',  null  )
        //                                            ->where( function ($query ) use ($userid) {
        // 	                                           return $query->orWhere( 'assign_from', '=', $userid )
        // 	                                                        ->orWhere( 'assign_to', '=', $userid );
        //                                            });
        //
        // if ($request->category != '') {
        // 	$data['task']['statutory_today'] = $data['task']['statutory_today']->where('category', $request->category);
        // }
        //
        // if ($request->term != '') {
        // 	$data['task']['statutory_today'] = $data['task']['statutory_today']->where(function ($query) use ($term) {
        // 		$query->whereRaw('category IN (SELECT id FROM task_categories WHERE name LIKE "%' . $term . '%") OR assign_from IN (SELECT id FROM users WHERE name LIKE "%' . $term . '%")')
        // 					->orWhere('id', 'LIKE', "%$term%")
        // 					->orWhere('task_details', 'LIKE', "%$term%")->orWhere('task_subject', 'LIKE', "%$term%");
        // 	});
        // }
        //
        //  $data['task']['statutory_today'] = $data['task']['statutory_today']->get()->toArray();

        //		$data['task']['statutory_completed_ids'] = [];
        //		foreach ($data['task']['statutory_completed'] as $item)
        //			$data['task']['statutory_completed_ids'][] =  $item['statutory_id'];


        // $data['task']['deleted']   = Task::onlyTrashed()
        //                                 ->where( 'is_statutory', '=', 0 )
        // 								->where( function ($query ) use ($userid) {
        // 									return $query->orWhere( 'assign_from', '=', $userid )
        // 									             ->orWhere( 'assign_to', '=', $userid );
        // 								});
        //
        // if ($request->category != '') {
        // 	$data['task']['deleted'] = $data['task']['deleted']->where('category', $request->category);
        // }
        //
        // if ($request->term != '') {
        // 	$data['task']['deleted'] = $data['task']['deleted']->where(function ($query) use ($term) {
        // 		$query->whereRaw('category IN (SELECT id FROM task_categories WHERE name LIKE "%' . $term . '%") OR assign_from IN (SELECT id FROM users WHERE name LIKE "%' . $term . '%")')
        // 					->orWhere('id', 'LIKE', "%$term%")
        // 					->orWhere('task_details', 'LIKE', "%$term%")->orWhere('task_subject', 'LIKE', "%$term%");
        // 	});
        // }
        //
        // $data['task']['deleted'] = $data['task']['deleted']->get()->toArray();

        //  $tasks_query = Task::where('is_statutory', 0)
        // 												->where('assign_to', Auth::id());
        //
        // $pending_tasks_count = Task::where('is_statutory', 0)->where('assign_to', Auth::id())->whereNull('is_completed')->get();
        // $completed_tasks_count = $tasks_query->whereNull('is_completed')->count();
        // dd($pending_tasks_count);

        // $tasks_query = Task::where('is_statutory', 0)->where('assign_to', Auth::id())->whereNull('is_completed')->count();
        //
        // dd($tasks_query);
        // $users = Helpers::getUserArray(User::all());

        $data['users'] =  User::orderBy('name')->where('is_active', 1)->get()->toArray();
        $data['daily_activity_date'] = $request->daily_activity_date ? $request->daily_activity_date : date('Y-m-d');

        // foreach ($data['task']['pending'] as $task) {
        // }


        // $category = '';
        //My code start
        $selected_user = $request->input('selected_user');
        $usrlst = User::orderby('name')->where('is_active', 1)->get();
        $users  = Helpers::getUserArray($usrlst);
        $task_categories = TaskCategory::where('parent_id', 0)->get();
        $task_categories_dropdown = nestable(TaskCategory::where('is_approved', 1)->get()->toArray())->attr(['name' => 'category', 'class' => 'form-control input-sm'])
            ->selected($request->category)
            ->renderAsDropdown();

        $categories = [];
        foreach (TaskCategory::all() as $category) {
            $categories[$category->id] = $category->title;
        }

        if (!empty($selected_user) && !Helpers::getadminorsupervisor()) {
            return response()->json(['user not allowed'], 405);
        }
        //My code end
        $tasks_view = [];
        $priority  = \App\ErpPriority::where('model_type', '=', Task::class)->pluck('model_id')->toArray();

        $openTask = \App\Task::join("users as u", "u.id", "tasks.assign_to")
            ->whereNull("tasks.is_completed")
            ->groupBy("tasks.assign_to")
            ->select(\DB::raw("count(u.id) as total"), "u.name as person")
            ->pluck("total", "person");

        if ($request->is_statutory_query == 3) {
            $title = 'Discussion tasks';
        } else {
            $title = 'Task & Activity';
        }

        $task_statuses = TaskStatus::all();


        if ($request->ajax()) {
            if ($type == 'pending') {
                return view('task-module.partials.pending-row-ajax', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses'));
            } elseif ($type == 'statutory_not_completed') {
                return view('task-module.partials.statutory-row-ajax', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses'));
            } elseif ($type == 'completed') {
                return view('task-module.partials.completed-row-ajax', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses'));
            } else {
                return view('task-module.partials.pending-row-ajax', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses'));
            }
        }

        if ($request->is_statutory_query == 3) {
            return view('task-module.discussion-tasks', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses'));
        } else {
            $statuseslist = TaskStatus::pluck('name', 'id')->toArray();
            $selectStatusList = TaskStatus::pluck('id')->toArray();
            return view('task-module.show', compact('data', 'users', 'selected_user', 'category', 'term', 'search_suggestions', 'search_term_suggestions', 'tasks_view', 'categories', 'task_categories', 'task_categories_dropdown', 'priority', 'openTask', 'type', 'title', 'task_statuses', 'statuseslist', 'selectStatusList'));
        }
    }


    // public function createTask() {
    // 	$users                     = User::oldest()->get()->toArray();
    // 	$data['users']             = $users;
    // 	$task_categories = TaskCategory::where('parent_id', 0)->get();
    // 	$task_categories_dropdown = nestable(TaskCategory::where('is_approved', 1)->get()->toArray())->attr(['name' => 'category','class' => 'form-control input-sm'])
    // 	                                        ->renderAsDropdown();

    // 	$categories = [];
    // 	foreach (TaskCategory::all() as $category) {
    // 		$categories[$category->id] = $category->title;
    // 	}
    // 	return view( 'task-module.create-task',compact('data','task_categories','task_categories_dropdown','categories'));
    // }

    public function updateCost(Request $request) {
        $task = Task::find($request->task_id);

        // if($task && $request->approximate) {
        //     DeveloperTaskHistory::create([
        // 		'developer_task_id' => $task->id,
        // 		'model' => 'App\Task',
        //         'attribute' => "estimation_minute",
        //         'old_value' => $task->approximate,
        //         'new_value' => $request->approximate,
        //         'user_id' => auth()->id(),
        //     ]);
        // }
        if (Auth::user()->isAdmin()) {
            $task->cost = $request->cost;
            $task->save();
            return response()->json(['msg' => 'success']);
        } else {
            return response()->json(['msg' => 'Not authorized user to update'], 500);
        }
    }




    public function saveMilestone(Request $request) {
        $task = Task::find($request->task_id);
        if (!$task->is_milestone) {
            return;
        }
        $total = $request->total;
        if ($task->milestone_completed) {
            if ($total <= $task->milestone_completed) {
                return response()->json([
                    'message' => 'Milestone no can\'t be reduced'
                ], 500);
            }
        }

        if ($total > $task->no_of_milestone) {
            return response()->json([
                'message' => 'Estimated milestone exceeded'
            ], 500);
        }
        if (!$task->cost || $task->cost == '') {
            return response()->json([
                'message' => 'Please provide cost first'
            ], 500);
        }

        $newCompleted = $total - $task->milestone_completed;
        $individualPrice = $task->cost / $task->no_of_milestone;
        $totalCost = $individualPrice * $newCompleted;

        $task->milestone_completed = $total;
        $task->save();
        $payment_receipt = new PaymentReceipt;
        $payment_receipt->date = date('Y-m-d');
        $payment_receipt->worked_minutes = $task->approximate;
        $payment_receipt->rate_estimated = $totalCost;
        $payment_receipt->status = 'Pending';
        $payment_receipt->task_id = $task->id;
        $payment_receipt->user_id = $task->assign_to;
        $payment_receipt->save();



        return response()->json([
            'status' => 'success'
        ]);
    }

    public function updateApproximate(Request $request) {
        $task = Task::find($request->task_id);

        if (Auth::user()->id == $task->assign_to || Auth::user()->isAdmin()) {
            if ($task && $request->approximate) {
                DeveloperTaskHistory::create([
                    'developer_task_id' => $task->id,
                    'model' => 'App\Task',
                    'attribute' => "estimation_minute",
                    'old_value' => $task->approximate,
                    'remark' => isset($request->remark) ? $request->remark : null,
                    'new_value' => $request->approximate,
                    'user_id' => auth()->id(),
                ]);
            }

            $task->approximate = $request->approximate;
            $task->save();

            if (Auth::user()->isAdmin()) {
                $user = User::find($task->assign_to);
                $msg = 'TIME ESTIMATED BY ADMIN FOR TASK ' . '#DEVTASK-' . $task->id . '-' . $task->subject . ' ' .  $request->approximate . ' MINS';
            } else {
                $user = User::find($task->master_user_id);
                $msg = 'TIME ESTIMATED BY USER FOR TASK ' . '#DEVTASK-' . $task->id . '-' . $task->subject . ' ' .  $request->approximate . ' MINS';
            }
            if ($user) {
                $receiver_user_phone = $user->phone;
                if ($receiver_user_phone) {
                    $chat = ChatMessage::create([
                        'number' => $receiver_user_phone,
                        'user_id' => $user->id,
                        'customer_id' => $user->id,
                        'message' => $msg,
                        'status' => 0,
                        'developer_task_id' => $request->task_id
                    ]);
                    app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($receiver_user_phone, $user->whatsapp_number, $msg, false, $chat->id);
                }
            }

            return response()->json(['msg' => 'success']);
        } else {
            return response()->json(['msg' => 'Unauthorized access'], 500);
        }
    }

    public function updatePriorityNo(Request $request) {
        $task = Task::find($request->task_id);

        if (Auth::user()->id == $task->assign_to || Auth::user()->isAdmin()) {
            $task->priority_no = $request->priority;
            $task->save();
            return response()->json(['msg' => 'success']);
        } else {
            return response()->json(['msg' => 'Unauthorized access'], 500);
        }
    }

    public function taskListByUserId(Request $request) {
        $user_id = $request->get('user_id', 0);
        $selected_issue = $request->get('selected_issue', []);
        $issues = Task::select('tasks.*')
            ->leftJoin('erp_priorities', function ($query) {
                $query->on('erp_priorities.model_id', '=', 'tasks.id');
                $query->where('erp_priorities.model_type', '=', Task::class);
            })->whereNull('is_verified');

        if (auth()->user()->isAdmin()) {
            $issues = $issues->where(function ($q) use ($selected_issue, $user_id) {

                if ((count($selected_issue) != 0 && count($selected_issue) != 1)) {
                    $q->whereIn('tasks.id', $selected_issue);
                }

                $user_id = is_null($user_id) ? 0 : $user_id;

                if ($user_id != 0) {
                    $q->where('tasks.assign_to', $user_id)->orWhere("tasks.master_user_id", $user_id);
                }
            });
        } else {
            $issues = $issues->whereNotNull('erp_priorities.id');
        }

        $issues = $issues->groupBy('tasks.id')->orderBy('erp_priorities.id')->get();

        foreach ($issues as &$value) {
            $value->created_by = User::where('id', $value->assign_from)->value('name');
            $value->created_at = \Carbon\Carbon::parse($value->created_at)->format('d-m-y H:i:s');
        }
        unset($value);
        $viewData = view('task-module.taskpriority', compact('issues'))->render();

        return response()->json([
            'html' => $viewData,

        ], 200);

        //  return response()->json($issues);
    }

    public function setTaskPriority(Request $request) {
        //dd($request->get);
        //   dd($request->all());
        $priority = $request->get('priority', null);
        $user_id = $request->get('user_id', 0);

        //get all user task
        //$developerTask = Task::where('assign_to', $user_id)->pluck('id')->toArray();

        //delete old priority
        \App\ErpPriority::where('user_id', $user_id)->where('model_type', '=', Task::class)->delete();

        if (!empty($priority)) {
            foreach ((array)$priority as $model_id) {
                \App\ErpPriority::create([
                    'model_id' => $model_id,
                    'model_type' => Task::class,
                    'user_id' => $user_id
                ]);
            }

            $developerTask = Task::select('tasks.id', 'tasks.task_subject', 'tasks.task_details', 'tasks.assign_from')
                ->join('erp_priorities', function ($query) use ($user_id) {
                    $user_id = is_null($user_id) ? 0 : $user_id;
                    $query->on('erp_priorities.model_id', '=', 'tasks.id');
                    $query->where('erp_priorities.model_type', '=', Task::class);
                    $query->where('user_id', $user_id);
                })
                ->whereNull('is_verified')
                ->orderBy('erp_priorities.id')
                ->get();

            $message = "";
            $i = 1;

            foreach ($developerTask as $value) {
                $message .= $i . " : #Task-" . $value->id . "-" . $value->task_subject . "\n";
                $i++;
            }

            if (!empty($message)) {
                $requestData = new Request();
                $requestData->setMethod('POST');
                $params = [];
                $params['user_id'] = $user_id;

                $string = "";

                if (!empty($request->get('global_remarkes', null))) {
                    $string .= $request->get('global_remarkes') . "\n";
                }

                $string .= "Task Priority is : \n" . $message;

                $params['message'] = $string;
                $params['status'] = 2;
                $requestData->request->add($params);
                app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'priority');
            }
        }
        return response()->json([
            'status' => 'success'
        ]);
    }

    public function store(Request $request) {
        dd("We are not using this function anymore, If you reach here, that means that we have to change this.");
        $this->validate($request, [
            'task_subject'    => 'required',
            'task_details'    => 'required',
            'assign_to' => 'required_without:assign_to_contacts'
        ]);
        $data = $request->except('_token');
        $data['assign_from'] = Auth::id();

        if ($request->task_type == 'quick_task') {
            $data['is_statutory'] = 0;
            $data['category'] = 6;
            $data['model_type'] = $request->model_type;
            $data['model_id'] = $request->model_id;
        }

        if ($request->task_type == 'note-task') {
            $main_task = Task::find($request->task_id);
        } else {
            if ($request->assign_to) {
                $data['assign_to'] = $request->assign_to[0];
            } else {
                $data['assign_to'] = $request->assign_to_contacts[0];
            }
        }

        if (!empty($data['status'])) {
            $data['status'] = 3;
        }


        $task = Task::create($data);

        // dd($request->all());
        if ($request->is_statutory == 3) {
            foreach ($request->note as $note) {
                if ($note != null) {
                    Remark::create([
                        'taskid'    => $task->id,
                        'remark'    => $note,
                        'module_type'    => 'task-note'
                    ]);
                }
            }
        }

        if ($request->task_type != 'note-task') {
            if ($request->assign_to) {
                foreach ($request->assign_to as $user_id) {
                    $task->users()->attach([$user_id => ['type' => User::class]]);
                }
            }

            if ($request->assign_to_contacts) {
                foreach ($request->assign_to_contacts as $contact_id) {
                    $task->users()->attach([$contact_id => ['type' => Contact::class]]);
                }
            }
        }

        if ($task->is_statutory != 1) {
            $message = "#" . $task->id . ". " . $task->task_subject . ". " . $task->task_details;
        } else {
            $message = $task->task_subject . ". " . $task->task_details;
        }

        $params = [
            'number'       => null,
            'user_id'      => Auth::id(),
            'approved'     => 1,
            'status'       => 2,
            'task_id'            => $task->id,
            'message'      => $message
        ];
        if (count($task->users) > 0) {
            if ($task->assign_from == Auth::id()) {
                foreach ($task->users as $key => $user) {
                    if ($key == 0) {
                        $params['erp_user'] = $user->id;
                    } else {
                        app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message']);
                    }
                }
            } else {
                foreach ($task->users as $key => $user) {
                    if ($key == 0) {
                        $params['erp_user'] = $task->assign_from;
                    } else {
                        if ($user->id != Auth::id()) {
                            app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message']);
                        }
                    }
                }
            }
        }

        if (count($task->contacts) > 0) {
            foreach ($task->contacts as $key => $contact) {
                if ($key == 0) {
                    $params['contact_id'] = $task->assign_to;
                } else {
                    app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($contact->phone, null, $params['message']);
                }
            }
        }

        $chat_message = ChatMessage::create($params);
        ChatMessagesQuickData::updateOrCreate([
            'model' => \App\Task::class,
            'model_id' => $params['task_id']
        ], [
            'last_communicated_message' => @$params['message'],
            'last_communicated_message_at' => $chat_message->created_at,
            'last_communicated_message_id' => ($chat_message) ? $chat_message->id : null,
        ]);


        $myRequest = new Request();
        $myRequest->setMethod('POST');
        $myRequest->request->add(['messageId' => $chat_message->id]);

        app('App\Http\Controllers\WhatsAppController')->approveMessage('task', $myRequest);

        //   $hubstaff_project_id = getenv('HUBSTAFF_BULK_IMPORT_PROJECT_ID');
        $hubstaff_project_id = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID');

        $assignedUser = HubstaffMember::where('user_id', $request->input('assign_to'))->first();
        // $hubstaffProject = HubstaffProject::find($request->input('hubstaff_project'));

        $hubstaffUserId = null;
        if ($assignedUser) {
            $hubstaffUserId = $assignedUser->hubstaff_user_id;
        }
        $taskSummery = substr($message, 0, 200);


        $hubstaffTaskId = $this->createHubstaffTask(
            $taskSummery,
            $hubstaffUserId,
            $hubstaff_project_id
        );

        if ($hubstaffTaskId) {
            $task->hubstaff_task_id = $hubstaffTaskId;
            $task->save();
        }
        if ($hubstaffUserId) {
            $task = new HubstaffTask();
            $task->hubstaff_task_id = $hubstaffTaskId;
            $task->project_id = $hubstaff_project_id;
            $task->hubstaff_project_id = $hubstaff_project_id;
            $task->summary = $message;
            $task->save();
        }

        $task_statuses = TaskStatus::all();

        if ($request->ajax()) {
            $hasRender = request("has_render", false);

            if (!empty($hasRender)) {
                $users      = Helpers::getUserArray(User::all());
                $priority      = \App\ErpPriority::where('model_type', '=', Task::class)->pluck('model_id')->toArray();


                if ($task->is_statutory == 1) {
                    $mode = "task-module.partials.statutory-row";
                } elseif ($task->is_statutory == 3) {
                    $mode = "task-module.partials.discussion-pending-raw";
                } else {
                    $mode = "task-module.partials.pending-row";
                }

                $view = (string)view($mode, compact('task', 'priority', 'users', 'task_statuses'));
                return response()->json(["code" => 200, "statutory" => $task->is_statutory, "raw" => $view]);
            }

            return response('success');
        }

        return redirect()->back()->with('success', 'Task created successfully.');
    }


    public function createHubstaffTask(string $taskSummary, ?int $hubstaffUserId, int $projectId, bool $shouldRetry = true) {
        $tokens = $this->getTokens();
        // echo '<pre>';print_r($tokens);





        $url = 'https://api.hubstaff.com/v2/projects/' . $projectId . '/tasks';


        $httpClient = new Client();
        try {
            $body = array(
                'summary' => $taskSummary
            );

            if ($hubstaffUserId) {
                $body['assignee_id'] = $hubstaffUserId;
            } else {
                // $body['assignee_id'] = getenv('HUBSTAFF_DEFAULT_ASSIGNEE_ID');
                $body['assignee_id'] = config('env.HUBSTAFF_DEFAULT_ASSIGNEE_ID');
            }

            $response = $httpClient->post(
                $url,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer ' . $tokens->access_token,
                        'Content-Type' => 'application/json'
                    ],

                    RequestOptions::BODY => json_encode($body)
                ]
            );
            $parsedResponse = json_decode($response->getBody()->getContents());


            return $parsedResponse->task->id;
        } catch (ClientException $e) {
            if ($e->getCode() == 401) {
                $this->refreshTokens();
                if ($shouldRetry) {
                    return $this->createHubstaffTask(
                        $taskSummary,
                        $hubstaffUserId,
                        $projectId,
                        false
                    );
                } else {
                }
            }
        }

        return false;
    }


    public function flag(Request $request) {
        if ($request->task_type == 'DEVTASK') {
            $task = DeveloperTask::find($request->task_id);
        } else {
            $task = Task::find($request->task_id);
        }

        if ($task->is_flagged == 0) {
            $task->is_flagged = 1;
        } else {
            $task->is_flagged = 0;
        }

        $task->save();

        return response()->json(['is_flagged' => $task->is_flagged]);
    }

    public function remarkFlag(Request $request) {
        $remark = Remark::find($request->remark_id);

        if ($remark->is_flagged == 0) {
            $remark->is_flagged = 1;
        } else {
            $remark->is_flagged = 0;
        }

        $remark->save();

        return response()->json(['is_flagged' => $remark->is_flagged]);
    }

    public function plan(Request $request, $id) {
        $task = Task::find($id);
        $task->time_slot = $request->time_slot;
        $task->planned_at = $request->planned_at;
        $task->general_category_id = $request->get("general_category_id", null);
        $task->save();

        return response()->json([
            'task'    => $task
        ]);
    }

    public function loadView(Request $request) {
        $tasks = Task::whereIn('id', $request->selected_tasks)->get();
        $users = Helpers::getUserArray(User::all());
        $view = view('task-module.partials.task-view', [
            'tasks_view' => $tasks,
            'users'            => $users
        ])->render();

        return response()->json([
            'view'    => $view
        ]);
    }

    public function assignMessages(Request $request) {
        $messages_ids = json_decode($request->selected_messages, true);

        foreach ($messages_ids as $message_id) {
            $message = ChatMessage::find($message_id);
            $message->task_id = $request->task_id;
            $message->save();
        }

        return redirect()->back()->withSuccess('You have successfully assign messages');
    }

    public function messageReminder(Request $request) {
        $this->validate($request, [
            'message_id'        => 'required|numeric',
            'reminder_date'    => 'required'
        ]);

        $message = ChatMessage::find($request->message_id);

        $additional_params = [
            'user_id'    => $message->user_id,
            'task_id'    => $message->task_id,
            'erp_user'    => $message->erp_user,
            'contact_id'    => $message->contact_id,
        ];

        $params = [
            'user_id'       => Auth::id(),
            'message'       => "Reminder - " . $message->message,
            'type'                    => 'task',
            'data'                    => json_encode($additional_params),
            'sending_time'  => $request->reminder_date
        ];

        ScheduledMessage::create($params);

        return redirect()->back()->withSuccess('You have successfully set a reminder!');
    }

    public function convertTask(Request $request, $id) {
        $task = Task::find($id);

        $task->is_statutory = 3;
        $task->save();

        return response('success', 200);
    }

    public function updateSubject(Request $request, $id) {
        $task = Task::find($id);
        $task->task_subject = $request->subject;
        $task->save();

        return response('success', 200);
    }

    public function addNote(Request $request, $id) {
        Remark::create([
            'taskid'    => $id,
            'remark'    => $request->note,
            'module_type'    => 'task-note'
        ]);

        return response('success', 200);
    }

    public function addSubnote(Request $request, $id) {
        $remark = Remark::create([
            'taskid'    => $id,
            'remark'    => $request->note,
            'module_type'    => 'task-note-subnote'
        ]);

        $id = $remark->id;

        return response(['success' => $id], 200);
    }

    public function updateCategory(Request $request, $id) {
        $task = Task::find($id);
        $task->category = $request->category;
        $task->save();

        return response('success', 200);
    }

    public function show($id) {
        $task = Task::find($id);

        if (!$task) {
            abort(404, "Task is not exist");
        }


        $chatMessages = ChatMessage::where('task_id', $id)->get();
        if ((!$task->users->contains(Auth::id()) && $task->is_private == 1) || ($task->assign_from != Auth::id() && $task->contacts()->count() > 0) || (!$task->users->contains(Auth::id()) && $task->assign_from != Auth::id() && Auth::id() != 6)) {
            return redirect()->back()->withErrors("This task is private!");
        }

        $users = User::all();
        $users_array = Helpers::getUserArray(User::all());
        $categories = TaskCategory::attr(['title' => 'category', 'class' => 'form-control input-sm', 'placeholder' => 'Select a Category', 'id' => 'task_category'])
            ->selected($task->category)
            ->renderAsDropdown();

        if (request()->has('keyword')) {
            $taskNotes = $task->notes()->orderBy('is_flagged')->where('is_hide', 0)->where('remark', 'like', '%' . request()->keyword . '%')->paginate(20);
        } else {
            $taskNotes = $task->notes()->orderBy('is_flagged')->where('is_hide', 0)->paginate(20);
        }

        $hiddenRemarks = $task->notes()->where('is_hide', 1)->get();
        return view('task-module.task-show', [
            'task'    => $task,
            'users'    => $users,
            'users_array'    => $users_array,
            'categories'    => $categories,
            'taskNotes'    => $taskNotes,
            'hiddenRemarks'    => $hiddenRemarks,
            'chatMessages'    => $chatMessages,
        ]);
    }

    public function update(Request $request, $id) {
        $this->validate($request, [
            'assign_to.*'        => 'required_without:assign_to_contacts',
            'sending_time'    => 'sometimes|nullable|date'
        ]);

        $task = Task::find($id);
        $task->users()->detach();
        $task->contacts()->detach();

        if ($request->assign_to) {
            foreach ($request->assign_to as $user_id) {
                $task->users()->attach([$user_id => ['type' => User::class]]);
            }

            $task->assign_to = $request->assign_to[0];
        }

        if ($request->assign_to_contacts) {
            foreach ($request->assign_to_contacts as $contact_id) {
                $task->users()->attach([$contact_id => ['type' => Contact::class]]);
            }

            $task->assign_to = $request->assign_to_contacts[0];
        }

        if ($request->sending_time) {
            $task->sending_time = $request->sending_time;
        }

        $task->save();

        return redirect()->route('task.show', $id)->withSuccess('You have successfully reassigned users!');
    }

    public function makePrivate(Request $request, $id) {
        $task = Task::find($id);

        if ($task->is_private == 1) {
            $task->is_private = 0;
        } else {
            $task->is_private = 1;
        }

        $task->save();

        return response()->json([
            'task'    => $task
        ]);
    }

    public function isWatched(Request $request, $id) {
        $task = Task::find($id);

        if ($task->is_watched == 1) {
            $task->is_watched = 0;
        } else {
            $task->is_watched = 1;
        }

        $task->save();

        return response()->json([
            'task'    => $task
        ]);
    }

    public function complete(Request $request, $taskid) {
        $task  = Task::find($taskid);
        // $task->is_completed = date( 'Y-m-d H:i:s' );
        //		$task->deleted_at = null;

        // if ( $task->assign_to == Auth::id() ) {
        // 	$task->save();
        // }

        // $tasks = Task::where('category', $task->category)->where('assign_from', $task->assign_from)->where('is_statutory', $task->is_statutory)->where('task_details', $task->task_details)->where('task_subject', $task->task_subject)->get();
        //
        // foreach ($tasks as $item) {
        // 	if ($request->type == 'complete') {
        // 		if ($item->is_completed == '') {
        // 			$item->is_completed = date( 'Y-m-d H:i:s' );
        // 		} else if ($item->is_verified == '') {
        // 			$item->is_verified = date( 'Y-m-d H:i:s' );
        // 		}
        // 	} else if ($request->type == 'clear') {
        // 		$item->is_completed = NULL;
        // 		$item->is_verified = NULL;
        // 	}
        //
        // 	$item->save();
        // }
        if ($request->type == 'complete') {
            if (is_null($task->is_completed)) {
                $task->is_completed = date('Y-m-d H:i:s');
            } elseif (is_null($task->is_verified)) {
                if ($task->assignedTo) {
                    if ($task->assignedTo->fixed_price_user_or_job == 1) {
                        // Fixed price task.
                        if ($task->cost == null) {
                            if ($request->ajax()) {
                                return response()->json([
                                    'message'    => 'Please provide cost for fixed price task.'
                                ], 500);
                            }

                            return redirect()->back()
                                ->with('error', 'Please provide cost for fixed price task.');
                        }
                        if (!$task->is_milestone) {
                            $payment_receipt = new PaymentReceipt;
                            $payment_receipt->date = date('Y-m-d');
                            $payment_receipt->worked_minutes = $task->approximate;
                            $payment_receipt->rate_estimated = $task->cost;
                            $payment_receipt->status = 'Pending';
                            $payment_receipt->task_id = $task->id;
                            $payment_receipt->user_id = $task->assign_to;
                            $payment_receipt->save();
                        }
                    }
                }
                $task->is_verified = date('Y-m-d H:i:s');
            }
        } elseif ($request->type == 'clear') {
            $task->is_completed = null;
            $task->is_verified = null;
        }
        $task->save();

        // if($task->is_statutory == 0)
        // 	$message = 'Task Completed: ' . $task->task_details;
        // else
        // 	$message = 'Recurring Task Completed: ' . $task->task_details;

        // PushNotification::create( [
        // 	'message'    => $message,
        // 	'model_type' => Task::class,
        // 	'model_id'   => $task->id,
        // 	'user_id'    => Auth::id(),
        // 	'sent_to'    => $task->assign_from,
        // 	'role'       => '',
        // ] );
        //
        // PushNotification::create( [
        // 	'message'    => $message,
        // 	'model_type' => Task::class,
        // 	'model_id'   => $task->id,
        // 	'user_id'    => Auth::id(),
        // 	'sent_to'    => '',
        // 	'role'       => 'Admin',
        // ] );

        // $notification_queues = NotificationQueue::where('model_id', $task->id)->where('model_type', 'App\Task')->delete();

        if ($request->ajax()) {
            return response()->json([
                'task'    => $task
            ]);
        }

        return redirect()->back()
            ->with('success', 'Task marked as completed.');
    }

    public function start(Request $request, $taskid) {
        $task               = Task::find($taskid);

        $task->actual_start_date = date('Y-m-d H:i:s');
        $task->save();

        if ($request->ajax()) {
            return response()->json([
                'task'    => $task
            ]);
        }

        return redirect()->back()->with('success', 'Task started.');
    }

    public function statutoryComplete($taskid) {
        $task               = SatutoryTask::find($taskid);
        $task->completion_date = date('Y-m-d H:i:s');
        //		$task->deleted_at = null;

        if ($task->assign_to == Auth::id()) {
            $task->save();
        }

        $message = 'Statutory Task Completed: ' . $task->task_details;

        // $notification_queues = NotificationQueue::where('model_id', $task->id)->where('model_type', 'App\StatutoryTask')->delete();

        // PushNotification::create( [
        // 	'message'    => $message,
        // 	'model_type' => SatutoryTask::class,
        // 	'model_id'   => $task->id,
        // 	'user_id'    => Auth::id(),
        // 	'sent_to'    => $task->assign_from,
        // 	'role'       => '',
        // ] );

        return redirect()->back()
            ->with('success', 'Statutory Task marked as completed.');
    }

    public function addRemark(Request $request) {
        $remark       = $request->input('remark');
        $id           = $request->input('id');
        $created_at = date('Y-m-d H:i:s');
        $update_at = date('Y-m-d H:i:s');
        if ($request->module_type == "document") {
            $remark_entry = DocumentRemark::create([
                'document_id'    => $id,
                'remark'    => $remark,
                'module_type'    => $request->module_type,
                'user_name'    => $request->user_name ? $request->user_name : Auth::user()->name
            ]);
        } else {
            $remark_entry = Remark::create([
                'taskid'    => $id,
                'remark'    => $remark,
                'module_type'    => $request->module_type,
                'user_name'    => $request->user_name ? $request->user_name : Auth::user()->name
            ]);
        }

        if ($request->module_type == 'task-discussion') {
            // NotificationQueueController::createNewNotification([
            // 	'message' => 'Remark for Developer Task',
            // 	'timestamps' => ['+0 minutes'],
            // 	'model_type' => DeveloperTask::class,
            // 	'model_id' =>  $id,
            // 	'user_id' => Auth::id(),
            // 	'sent_to' => $request->user == Auth::id() ? 6 : $request->user,
            // 	'role' => '',
            // ]);

            // NotificationQueueController::createNewNotification([
            // 	'message' => 'Remark for Developer Task',
            // 	'timestamps' => ['+0 minutes'],
            // 	'model_type' => DeveloperTask::class,
            // 	'model_id' =>  $id,
            // 	'user_id' => Auth::id(),
            // 	'sent_to' => 56,
            // 	'role' => '',
            // ]);
        }

        // if ($request->module_type == 'developer') {
        // 	$task = DeveloperTask::find($id);
        //
        // 	if ($task->user->id == Auth::id()) {
        // 		NotificationQueueController::createNewNotification([
        // 			'message' => 'New Task Remark',
        // 			'timestamps' => ['+0 minutes'],
        // 			'model_type' => DeveloperTask::class,
        // 			'model_id' =>  $task->id,
        // 			'user_id' => Auth::id(),
        // 			'sent_to' => 6,
        // 			'role' => '',
        // 		]);
        //
        // 		NotificationQueueController::createNewNotification([
        // 			'message' => 'New Task Remark',
        // 			'timestamps' => ['+0 minutes'],
        // 			'model_type' => DeveloperTask::class,
        // 			'model_id' =>  $task->id,
        // 			'user_id' => Auth::id(),
        // 			'sent_to' => 56,
        // 			'role' => '',
        // 		]);
        // 	} else {
        // 		NotificationQueueController::createNewNotification([
        // 			'message' => 'New Task Remark',
        // 			'timestamps' => ['+0 minutes'],
        // 			'model_type' => DeveloperTask::class,
        // 			'model_id' =>  $task->id,
        // 			'user_id' => Auth::id(),
        // 			'sent_to' => $task->user_id,
        // 			'role' => '',
        // 		]);
        // 	}
        // }
        // $remark_entry = DB::insert('insert into remarks (taskid, remark, created_at, updated_at) values (?, ?, ?, ?)', [$id  ,$remark , $created_at, $update_at]);

        // if (is_null($request->module_type)) {
        // 	$task = Task::find($remark_entry->taskid);
        //
        // 	PushNotification::create( [
        // 		'message'    => 'Remark added: ' . $remark,
        // 		'model_type' => Task::class,
        // 		'model_id'   => $task->id,
        // 		'user_id'    => Auth::id(),
        // 		'sent_to'    => $task->assign_from,
        // 		'role'       => '',
        // 	] );
        //
        // 	PushNotification::create( [
        // 		'message'    => 'Remark added: ' . $remark,
        // 		'model_type' => Task::class,
        // 		'model_id'   => $task->id,
        // 		'user_id'    => Auth::id(),
        // 		'sent_to'    => '',
        // 		'role'       => 'Admin',
        // 	] );
        // }


        return response()->json(['remark' => $remark], 200);
    }

    public function list(Request $request) {
        $pending_tasks = Task::where('is_statutory', 0)->whereNull('is_completed')->where('assign_from', Auth::id());
        $completed_tasks = Task::where('is_statutory', 0)->whereNotNull('is_completed')->where('assign_from', Auth::id());

        if (is_array($request->user) && $request->user[0] != null) {
            $pending_tasks = $pending_tasks->whereIn('assign_to', $request->user);
            $completed_tasks = $completed_tasks->whereIn('assign_to', $request->user);
        }

        if ($request->date != null) {
            $pending_tasks = $pending_tasks->where('created_at', 'LIKE', "%$request->date%");
            $completed_tasks = $completed_tasks->where('created_at', 'LIKE', "%$request->date%");
        }

        $pending_tasks = $pending_tasks->oldest()->paginate(Setting::get('pagination'));
        $completed_tasks = $completed_tasks->orderBy('is_completed', 'DESC')->paginate(Setting::get('pagination'), ['*'], 'completed-page');

        $users = Helpers::getUserArray(User::all());
        $user = $request->user ?? [];
        $date = $request->date ?? '';

        return view('task-module.list', [
            'pending_tasks'        => $pending_tasks,
            'completed_tasks'    => $completed_tasks,
            'users'                        => $users,
            'user'                        => $user,
            'date'                        => $date
        ]);
    }

    public function getremark(Request $request) {
        $id   = $request->input('id');

        $task = Task::find($id);

        echo $task->remark;
    }


    public function deleteTask(Request $request) {
        $id   = $request->input('id');
        $task = Task::find($id);

        if ($task) {
            $task->remark = $request->input('comment');
            $task->save();

            $task->delete();
        }


        if ($request->ajax()) {
            return response()->json(["code" => 200]);
        }
    }

    public function archiveTask($id) {
        $task = Task::find($id);

        $task->delete();

        if ($request->ajax()) {
            return response('success');
        }
        return redirect('/');
    }

    public function archiveTaskRemark($id) {
        $task = Remark::find($id);
        $remark  = $task->remark;
        $task->delete_at = now();
        $task->update();

        return response(['success' => $remark], 200);
    }

    public function deleteStatutoryTask(Request $request) {
        $id   = $request->input('id');
        $task = SatutoryTask::find($id);
        $task->delete();

        return redirect()->back();
    }

    public function exportTask(Request $request) {
        $users = $request->input('selected_user');
        $from = $request->input('range_start') . " 00:00:00.000000";
        $to   = $request->input('range_end') . " 23:59:59.000000";

        $tasks = (new Task())->newQuery()->withTrashed()->whereBetween('created_at', [$from, $to])->where('assign_from', '!=', 0)->where('assign_to', '!=', 0);

        if (!empty($users)) {
            $tasks = $tasks->whereIn('assign_to', $users);
        }

        $tasks_list =  $tasks->get()->toArray();
        $tasks_csv = [];
        $userList = Helpers::getUserArray(User::all());

        for ($i = 0; $i < sizeof($tasks_list); $i++) {
            $task_csv = [];
            $task_csv['id'] = $tasks_list[$i]['id'];
            $task_csv['SrNo'] = $i + 1;
            $task_csv['assign_from'] = $userList[$tasks_list[$i]['assign_from']];
            $task_csv['assign_to'] = $userList[$tasks_list[$i]['assign_to']];
            $task_csv['type'] = $tasks_list[$i]['is_statutory'] == 1 ? 'Statutory' : 'Other';
            $task_csv['task_subject'] = $tasks_list[$i]['task_subject'];
            $task_csv['task_details'] = $tasks_list[$i]['task_details'];
            $task_csv['completion_date'] = $tasks_list[$i]['completion_date'];
            $task_csv['remark'] = $tasks_list[$i]['remark'];
            $task_csv['completed_on'] = $tasks_list[$i]['is_completed'];
            $task_csv['created_on'] = $tasks_list[$i]['created_at'];

            array_push($tasks_csv, $task_csv);
        }

        // $this->outputCsv('tasks.csv', $tasks_csv);
        return view('task-module.export')->withTasks($tasks_csv);
    }

    public function outputCsv($fileName, $assocDataArray) {
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . $fileName);
        if (isset($assocDataArray['0'])) {
            $fp = fopen('php://output', 'w');
            fputcsv($fp, array_keys($assocDataArray['0']));
            foreach ($assocDataArray as $values) {
                fputcsv($fp, $values);
            }
            fclose($fp);
        }
    }


    public static function getClasses($task) {
        $classes = ' ';
        // dump($task);
        $classes .= ' ' . ((empty($task) && $task->assign_from == Auth::user()->id) ? 'mytask' : '') . ' ';
        $classes .= ' ' . ((empty($task) && time() > strtotime($task->completion_date . ' 23:59:59'))  ? 'isOverdue' : '') . ' ';


        $task_status = empty($task) ? Helpers::statusClass($task->assign_status) : '';

        $classes .= $task_status;

        return $classes;
    }

    public function recurringTask() {
        $statutory_tasks = SatutoryTask::all()->toArray();

        foreach ($statutory_tasks as $statutory_task) {
            switch ($statutory_task['recurring_type']) {

                case 'EveryDay':
                    self::createTasksFromSatutary($statutory_task);
                    break;

                case 'EveryWeek':
                    if ($statutory_task['recurring_day'] == date('D')) {
                        self::createTasksFromSatutary($statutory_task);
                    }
                    break;

                case 'EveryMonth':
                    if ($statutory_task['recurring_day'] == date('d')) {
                        self::createTasksFromSatutary($statutory_task);
                    }
                    break;

                case 'EveryYear':
                    $dayNdate  = date('d-n', strtotime($statutory_task['recurring_day']));
                    if ($dayNdate == date('d-n')) {
                        self::createTasksFromSatutary($statutory_task);
                    }
                    break;
            }
        }
    }

    public static function createTasksFromSatutary($statutory_task) {
        $statutory_task['is_statutory'] = 1;
        $statutory_task['statutory_id'] = $statutory_task['id'];
        $task = Task::create($statutory_task);

        // PushNotification::create([
        // 	'message'    => 'Recurring Task: ' . $statutory_task['task_details'],
        // 	'role'       => '',
        // 	'model_type' => Task::class,
        // 	'model_id'   => $task->id,
        // 	'user_id'    => Auth::id(),
        // 	'sent_to'    => $statutory_task['assign_to'],
        // ]);
    }

    public function getTaskRemark(Request $request) {
        $id   = $request->input('id');

        if (is_null($request->module_type)) {
            $remark = \App\Task::getremarks($id);
        } else {
            $remark = Remark::where('module_type', $request->module_type)->where('taskid', $id)->get();
        }

        return response()->json($remark, 200);
    }

    public function addWhatsAppGroup(Request $request) {
        $whatsapp_number = '971562744570';
        $task = Task::findorfail($request->id);

        // Yogesh Sir Number
        $admin_number = User::findorfail(6);
        $assigned_from = Helpers::getUserArray(User::where('id', $task->assign_from)->get());
        $assigned_to = Helpers::getUserArray(User::where('id', $task->assign_to)->get());
        $task_id = $task->id;

        //Check if task id is present in Whats App Group
        $group = WhatsAppGroup::where('task_id', $task_id)->first();

        if ($group == null) {
            //First Create Group Using Admin id
            $phone = $admin_number->phone;
            $result = app('App\Http\Controllers\WhatsAppController')->createGroup($task_id, '', $phone, '', $whatsapp_number);
            if (isset($result['chatId']) && $result['chatId'] != null) {
                $task_id = $task_id;
                $chatId = $result['chatId'];
                //Create Group
                $group = new WhatsAppGroup;
                $group->task_id = $task_id;
                $group->group_id = $chatId;
                $group->save();
                //Save Whats App Group With Reference To Group ID
                $group_number = new WhatsAppGroupNumber;
                $group_number->group_id = $group->id;
                $group_number->user_id = $admin_number->id;
                $group_number->save();
                //Chat Message
                $params['task_id'] = $task_id;
                $params['group_id'] = $group->id;
                ChatMessage::create($params);
            } else {
                $group = new WhatsAppGroup;
                $group->task_id = $task_id;
                $group->group_id = null;
                $group->save();

                $group_number = new WhatsAppGroupNumber;
                $group_number->group_id = $group->id;
                $group_number->user_id = $admin_number->id;
                $group_number->save();

                $params['task_id'] = $task_id;
                $params['group_id'] = $group->id;
                $params['error_status'] = 1;
                ChatMessage::create($params);
            }
        }

        //iF assigned from is different from Yogesh Sir
        if ($admin_number->id != array_keys($assigned_from)[0]) {
            $request->request->add(['group_id' => $group->id, 'user_id' => array_keys($assigned_from), 'task_id' => $task->id, 'whatsapp_number' => $whatsapp_number]);

            $this->addGroupParticipant(request());
        }

        //Add Assigned To Into Whats App Group
        if (array_keys($assigned_to)[0] != null) {
            $request->request->add(['group_id' => $group->id, 'user_id' => array_keys($assigned_to), 'task_id' => $task->id, 'whatsapp_number' => $whatsapp_number]);

            $this->addGroupParticipant(request());
        }
        return response()->json(['group_id' => $group->id]);
    }

    public function addGroupParticipant(Request $request) {
        $whatsapp_number = '971562744570';
        //Now Add Participant In the Group

        foreach ($request->user_id as $key => $value) {
            $check = WhatsAppGroupNumber::where('group_id', $request->group_id)->where('user_id', $value)->first();
            if ($check == null) {
                $user = User::findorfail($value);
                $group = WhatsAppGroup::where('task_id', $request->task_id)->first();
                $phone = $user->phone;
                $result = app('App\Http\Controllers\WhatsAppController')->createGroup('', $group->group_id, $phone, '', $whatsapp_number);
                if (isset($result['add']) && $result['add'] != null) {
                    $task_id = $request->task_id;

                    $group_number = new WhatsAppGroupNumber;
                    $group_number->group_id = $request->group_id;
                    $group_number->user_id = $user->id;
                    $group_number->save();
                    $params['user_id'] = $user->id;
                    $params['task_id'] = $task_id;
                    $params['group_id'] = $request->group_id;
                    ChatMessage::create($params);
                } else {
                    $task_id = $request->task_id;

                    $group_number = new WhatsAppGroupNumber;
                    $group_number->group_id = $request->group_id;
                    $group_number->user_id = $user->id;
                    $group_number->save();
                    $params['user_id'] = $user->id;
                    $params['task_id'] = $task_id;
                    $params['group_id'] = $request->group_id;
                    $params['error_status'] = 1;
                    ChatMessage::create($params);
                }
            }
        }

        return redirect()->back()->with('message', 'Participants Added To Group');
    }

    public function getDetails(Request $request) {
        $task = \App\Task::where("id", $request->get("task_id", 0))->first();

        if ($task) {
            return response()->json(["code" => 200, "data" => $task]);
        }

        return response()->json(["code" => 500, "message" => "Sorry, no task found"]);
    }

    public function saveNotes(Request $request) {
        $task = \App\Task::where("id", $request->get("task_id", 0))->first();

        if ($task) {
            if ($task->is_statutory == 3) {
                foreach ($request->note as $note) {
                    if ($note != null) {
                        Remark::create([
                            'taskid'    => $task->id,
                            'remark'    => $note,
                            'module_type'    => 'task-note'
                        ]);
                    }
                }
            }

            return response()->json(["code" => 200, "data" => $task, "message" => "Note added!"]);
        }

        return response()->json(["code" => 500, "message" => "Sorry, no task found"]);
    }

    public function createTaskFromSortcut(Request $request) {
        $created = 0;
        $message = '';
        $assignedUserId = 0;
        $data = $request->except('_token');
        //print_r($data); die;
        $this->validate($request, [
            'task_subject'    => 'required',
            'task_detail'    => 'required',
            'task_asssigned_to' => 'required_without:assign_to_contacts',
            //'cost'=>'sometimes|integer'
        ]);
        $data['assign_from'] = Auth::id();
        $data['status'] = 3;

        $taskType = $request->get("task_type");



        if ($taskType == "4" || $taskType == "5" || $taskType == "6") {
            $data = [];

            if (is_array($request->task_asssigned_to)) {
                $data["assigned_to"] = $request->task_asssigned_to[0];
            } else {
                $data["assigned_to"] = $request->task_asssigned_to;
            }

            $data["subject"]         = $request->get("task_subject");
            $data["task"]             = $request->get("task_detail");
            $data["task_type_id"]    = 1;
            $data["site_developement_id"]    = $request->get("site_id");
            $data["cost"]    = $request->get("cost", 0);
            $data["status"]    = 'In Progress';
            $data["created_by"]    = Auth::id();
            //echo $data["site_developement_id"]; die;

            if ($taskType == 5 || $taskType == 6) {
                $data["task_type_id"]    = 3;
            }
            $task = DeveloperTask::create($data);
            $created = 1;
            $message = '#DEVTASK-' . $task->id . ' => ' . $task->subject;
            $assignedUserId = $task->assigned_to;

            $newBranchName = null;
            if (!empty($request->get('repository_id')) && $request->get('repository_id') > 0) {
                $newBranchName = $this->createBranchOnGithub(
                    $request->get('repository_id'),
                    $task->id,
                    $task->subject
                );
                if ($newBranchName) {
                    $task->github_branch_name = $newBranchName;
                    $task->save();
                }
            }

            if (is_string($newBranchName) && !empty($newBranchName)) {
                $message = $request->get("task_detail") . PHP_EOL . "A new branch " . $newBranchName . " has been created. Please pull the current code and run 'git checkout " . $newBranchName . "' to work in that branch.";
            } else {
                $message = $request->get("task_detail");
            }

            $requestData = new Request();
            $requestData->setMethod('POST');
            $requestData->request->add(['issue_id' => $task->id, 'message' => $message, 'status' => 1]);
            app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'issue');
        } else {
            if ($request->task_type == 'quick_task') {
                $data['is_statutory'] = 0;
                $data['category'] = 6;
                $data['model_type'] = $request->model_type;
                $data['model_id'] = $request->model_id;
            }

            if ($request->task_type == 'note-task') {
                $main_task = Task::find($request->task_id);
                if (is_array($request->task_asssigned_to)) {
                    $data["assign_to"] = $request->task_asssigned_to[0];
                } else {
                    $data["assign_to"] = $request->task_asssigned_to;
                }
            } else {
                if ($request->task_asssigned_to) {
                    if (is_array($request->task_asssigned_to)) {
                        $data["assign_to"] = $request->task_asssigned_to[0];
                    } else {
                        $data["assign_to"] = $request->task_asssigned_to;
                    }
                } else {
                    $data['assign_to'] = $request->assign_to_contacts[0];
                }
            }
            //discussion task
            if ($request->get("task_type") == 3) {
                $task = Task::find($request->get("task_subject"));
                $data['is_statutory'] = $request->get("task_type");
                $data['task_details'] = $request->get("task_detail");
                $data['task_subject'] = $request->get("task_subject");
                $data["customer_id"]    = $request->get("customer_id");
                if ($request->category_id != null) {
                    $data['category']       = $request->category_id;
                }
                if (!$task) {
                    $task = Task::create($data);
                    $remarks = $request->get("task_subject");
                    $created = 1;
                    $assignedUserId = $task->assign_to;
                    $message = '#TASK-' . $task->id . ' => ' . $task->task_subject . ". " . $task->task_details;
                } else {
                    $remarks = $task->task_subject;
                }
                $exist = Remark::where('taskid', $task->id)->where('remark', $remarks)->where('module_type', 'task-note')->first();
                if (!$exist) {
                    Remark::create([
                        'taskid'    => $task->id,
                        'remark'    => $remarks,
                        'module_type'    => 'task-note'
                    ]);
                }
                if ($request->note) {
                    foreach ($request->note as $note) {
                        if ($note != null) {
                            Remark::create([
                                'taskid'    => $task->id,
                                'remark'    => $note,
                                'module_type'    => 'task-note'
                            ]);
                        }
                    }
                }
            } else {
                $data['is_statutory'] = $request->get("task_type");
                $data['task_details'] = $request->get("task_detail");
                $data['task_subject'] = $request->get("task_subject");
                $data["customer_id"]    = $request->get("customer_id");
                $data["site_developement_id"]    = $request->site_id;
                $data["cost"]    = $request->get("cost");
                if ($request->category_id != null) {
                    $data['category']       = $request->category_id;
                }
                $task = Task::create($data);
                $created = 1;
                $assignedUserId = $task->assign_to;
                if ($task->is_statutory != 1) {
                    $message = "#" . $task->id . ". " . $task->task_subject . ". " . $task->task_details;
                } else {
                    $message = $task->task_subject . ". " . $task->task_details;
                }
            }


            if ($request->task_type != 'note-task') {
                if ($request->task_asssigned_to) {
                    if (is_array($request->task_asssigned_to)) {
                        foreach ($request->task_asssigned_to as $user_id) {
                            $task->users()->attach([$user_id => ['type' => User::class]]);
                        }
                    } else {
                        $task->users()->attach([$request->task_asssigned_to => ['type' => User::class]]);
                    }
                }



                if ($request->assign_to_contacts) {
                    foreach ($request->assign_to_contacts as $contact_id) {
                        $task->users()->attach([$contact_id => ['type' => Contact::class]]);
                    }
                }
            }



            $params = [
                'number'       => null,
                'user_id'      => Auth::id(),
                'approved'     => 1,
                'status'       => 2,
                'task_id'       => $task->id,
                'message'      => $message
            ];

            if (count($task->users) > 0) {
                if ($task->assign_from == Auth::id()) {
                    foreach ($task->users as $key => $user) {
                        if ($key == 0) {
                            $params['erp_user'] = $user->id;
                        } else {
                            app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message']);
                        }
                    }
                } else {
                    foreach ($task->users as $key => $user) {
                        if ($key == 0) {
                            $params['erp_user'] = $task->assign_from;
                        } else {
                            if ($user->id != Auth::id()) {
                                app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone, $user->whatsapp_number, $params['message']);
                            }
                        }
                    }
                }
            }

            if (count($task->contacts) > 0) {
                foreach ($task->contacts as $key => $contact) {
                    if ($key == 0) {
                        $params['contact_id'] = $task->assign_to;
                    } else {
                        app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($contact->phone, null, $params['message']);
                    }
                }
            }

            $chat_message = ChatMessage::create($params);
            ChatMessagesQuickData::updateOrCreate([
                'model' => \App\Task::class,
                'model_id' => $params['task_id']
            ], [
                'last_communicated_message' => @$params['message'],
                'last_communicated_message_at' => $chat_message->created_at,
                'last_communicated_message_id' => ($chat_message) ? $chat_message->id : null,
            ]);

            $myRequest = new Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add(['messageId' => $chat_message->id]);

            app('App\Http\Controllers\WhatsAppController')->approveMessage('task', $myRequest);
        }


        if ($created) {
            // $hubstaff_project_id = getenv('HUBSTAFF_BULK_IMPORT_PROJECT_ID');
            $hubstaff_project_id = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID');

            $assignedUser = HubstaffMember::where('user_id', $assignedUserId)->first();

            $hubstaffUserId = null;
            $hubstaffTaskId = null;
            if ($assignedUser) {
                $hubstaffUserId = $assignedUser->hubstaff_user_id;
            }
            $taskSummery = substr($message, 0, 200);
            if ($hubstaffUserId) {
                $hubstaffTaskId = $this->createHubstaffTask(
                    $taskSummery,
                    $hubstaffUserId,
                    $hubstaff_project_id
                );
            }


            if ($hubstaffTaskId) {
                $task->hubstaff_task_id = $hubstaffTaskId;
                $task->save();
            }
            if ($hubstaffTaskId) {
                $hubtask = new HubstaffTask();
                $hubtask->hubstaff_task_id = $hubstaffTaskId;
                $hubtask->project_id = $hubstaff_project_id;
                $hubtask->hubstaff_project_id = $hubstaff_project_id;
                $hubtask->summary = $message;
                $hubtask->save();
            }
        }

        if ($request->ajax() && $request->from == 'task-page') {
            $hasRender = request("has_render", false);

            $task_statuses = TaskStatus::all();

            if (!empty($hasRender)) {
                $users      = Helpers::getUserArray(User::all());
                $priority      = \App\ErpPriority::where('model_type', '=', Task::class)->pluck('model_id')->toArray();

                if ($task->is_statutory == 1) {
                    $mode = "task-module.partials.statutory-row";
                }
                // else if($task->is_statutory == 3) {
                // 	$mode = "task-module.partials.discussion-pending-raw";
                // }
                else {
                    $mode = "task-module.partials.pending-row";
                }

                $view = (string)view($mode, compact('task', 'priority', 'users', 'task_statuses'));
                return response()->json(["code" => 200, "statutory" => $task->is_statutory, "raw" => $view]);
            }
            return response('success');
        }

        return response()->json(["code" => 200, "data" => [], "message" => "Your quick task has been created!"]);
    }

    //START - Purpose : Set Remined , Revise - DEVTASK-4354
    public function sendRemindMessage(Request $request) {
        $user = User::find($request->user_id);
        if ($user) {
            $receiver_user_phone = $user->phone;
            if ($receiver_user_phone) {
                $task = Task::find($request->id);
                $msg = 'PLS ADD ESTIMATED TIME FOR TASK  ' . '#TASK-' . $task->id . '-' . $task->subject;
                $chat = ChatMessage::create([
                    'number' => $receiver_user_phone,
                    'user_id' => $user->id,
                    'customer_id' => $user->id,
                    'message' => $msg,
                    'status' => 0,
                    'developer_task_id' => $request->id
                ]);

                app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($receiver_user_phone, $user->whatsapp_number, $msg, false, $chat->id);

                MessageHelper::sendEmailOrWebhookNotification([$task->user_id], $msg);
            }
        }
        return response()->json([
            'message' => 'Remind message sent successfully',
        ]);
    }

    public function sendReviseMessage(Request $request) {
        $user = User::find($request->user_id);
        if ($user) {
            $receiver_user_phone = $user->phone;
            if ($receiver_user_phone) {
                $task = Task::find($request->id);
                $msg = 'TIME NOT APPROVED REVISE THE ESTIMATED TIME FOR TASK ' . '#TASK-' . $task->id . '-' . $task->subject;
                $chat = ChatMessage::create([
                    'number' => $receiver_user_phone,
                    'user_id' => $user->id,
                    'customer_id' => $user->id,
                    'message' => $msg,
                    'status' => 0,
                    'developer_task_id' => $request->id
                ]);
                app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($receiver_user_phone, $user->whatsapp_number, $msg, false, $chat->id);

                MessageHelper::sendEmailOrWebhookNotification([$task->assigned_to], $msg);
            }
        }
        return response()->json([
            'message' => 'Revise message sent successfully',
        ]);
    }
    //END - DEVTASK-4354

    public function getDiscussionSubjects() {
        $discussion_subjects = Task::where('is_statutory', 3)->where('is_verified', null)->pluck('task_subject', 'id')->toArray();
        return response()->json(["code" => 200, "discussion_subjects" => $discussion_subjects]);
    }

    /***
     * Delete task note
     */
    public function deleteTaskNote(Request $request) {
        $task = Remark::whereId($request->note_id)->delete();
        session()->flash('success', 'Deleted successfully.');
        return response(['success' => "Deleted"], 200);
    }

    /**
     * Hide task note from list
     */
    public function hideTaskRemark(Request $request) {
        $task = Remark::whereId($request->note_id)->update(['is_hide' => 1]);
        session()->flash('success', 'Hide successfully.');
        return response(['success' => "Hidden"], 200);
    }

    public function assignMasterUser(Request $request) {
        $masterUserId = $request->get("master_user_id");
        $issue = Task::find($request->get('issue_id'));

        $user = User::find($masterUserId);

        if (!$user) {
            return response()->json([
                'status' => 'success', 'message' => 'user not found'
            ], 500);
        }

        if ($request->get("lead") == '1') {
            $old_id = $issue->master_user_id;
            if (!$old_id) {
                $old_id = 0;
            }
            $issue->master_user_id = $masterUserId;
            $task_type = "leaddeveloper";
        } else {
            $old_id = $issue->second_master_user_id;
            if (!$old_id) {
                $old_id = 0;
            }
            $issue->second_master_user_id = $masterUserId;
            $task_type = "second_leaddeveloper";
        }

        $issue->save();

        // $hubstaff_project_id = getenv('HUBSTAFF_BULK_IMPORT_PROJECT_ID');
        $hubstaff_project_id = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID');

        $assignedUser = HubstaffMember::where('user_id', $masterUserId)->first();

        $hubstaffUserId = null;
        if ($assignedUser) {
            $hubstaffUserId = $assignedUser->hubstaff_user_id;
        }
        $message = "#" . $issue->id . ". " . $issue->task_subject . ". " . $issue->task_details;
        $summary = substr($message, 0, 200);
        $hubstaffTaskId = $this->createHubstaffTask(
            $summary,
            $hubstaffUserId,
            $hubstaff_project_id
        );
        if ($hubstaffTaskId) {
            $issue->lead_hubstaff_task_id = $hubstaffTaskId;
            $issue->save();
        }
        if ($hubstaffTaskId) {
            $task = new HubstaffTask();
            $task->hubstaff_task_id = $hubstaffTaskId;
            $task->project_id = $hubstaff_project_id;
            $task->hubstaff_project_id = $hubstaff_project_id;
            $task->summary = $message;
            $task->save();
        }
        $taskUser = new TaskUserHistory;
        $taskUser->model = 'App\Task';
        $taskUser->model_id = $issue->id;
        $taskUser->old_id = ($old_id == "") ? 0 : $old_id;
        $taskUser->new_id = $masterUserId;
        $taskUser->user_type = $task_type;
        $taskUser->updated_by = Auth::user()->name;
        $taskUser->save();
        return response()->json([
            'status' => 'success'
        ]);
    }

    public function uploadDocuments(Request $request) {
        $path = storage_path('tmp/uploads');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }


    public function saveDocuments(Request $request) {
        $loggedUser = $request->user();

        if (!$request->task_id || $request->task_id == '') {
            return response()->json(["code" => 500, "data" => [], "message" => "Select one task"]);
        }
        $documents = $request->input('document', []);
        $task = Task::find($request->task_id);

        if (!empty($documents)) {
            $count = count($documents);

            $message = '[' . $loggedUser->name . '] - #ISSUE-' . $task->id . ' - ' . $task->task_subject . "\n\n " . $count . ' new attchment' . ($count > 1 ? 's' : '');

            foreach ($documents as $file) {
                $path  = storage_path('tmp/uploads/' . $file);
                $media = MediaUploader::fromSource($path)
                    ->toDirectory('task-files/' . floor($task->id / config('constants.image_per_folder')))
                    ->upload();
                $task->attachMedia($media, config('constants.media_tags'));

                if (!empty($media->filename)) {
                    DB::table('media')->where('filename', $media->filename)->update(['user_id' => Auth::id()]);
                }

                $message .= "\n" . $file;
            }

            $message . "\nhas been added. \n Please check it and add your comment if any.";

            MessageHelper::sendEmailOrWebhookNotification($task->users->pluck('id')->toArray(), $message);

            return response()->json(["code" => 200, "data" => [], "message" => "Done!"]);
        } else {
            return response()->json(["code" => 500, "data" => [], "message" => "No documents for upload"]);
        }
    }


    public function previewTaskImage($id) {
        $task = Task::find($id);

        $records = [];
        if ($task) {
            $userList = User::pluck('name', 'id')->all();
            $task = Task::find($id);
            $userName = '';
            $mediaDetail = array();
            // $usrSelectBox = "";
            // if (!empty($userList)) {
            // 	$usrSelectBox = (string) \Form::select("send_message_to", $userList, null, ["class" => "form-control send-message-to-id"]);
            // }
            if ($task->hasMedia(config('constants.attach_image_tag'))) {
                foreach ($task->getMedia(config('constants.attach_image_tag')) as $media) {
                    $imageExtensions = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg', 'svgz', 'cgm', 'djv', 'djvu', 'ico', 'ief', 'jpe', 'pbm', 'pgm', 'pnm', 'ppm', 'ras', 'rgb', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd'];
                    $explodeImage = explode('.', $media->getUrl());
                    $extension = end($explodeImage);

                    if (in_array($extension, $imageExtensions)) {
                        $isImage = true;
                    } else {
                        $isImage = false;
                    }

                    $mediaDetail = DB::table('media')->where('id', $media->id)->first();
                    if ($mediaDetail) {
                        $userName = User::where('id', $mediaDetail->user_id)->pluck('name')->first();
                    } else {
                        $userName = '';
                    }

                    $records[] = [
                        "media_id"  => $id,
                        "id"        => $media->id,
                        'url'       => $media->getUrl(),
                        'task_id'   => $task->id,
                        'isImage'   => $isImage,
                        'userList'  => $userList,
                        'userName'  => $userName,
                        'created_at'  => $media->created_at
                    ];
                }
            }
        }

        $records = array_reverse($records);
        $title = 'Preview images';
        return view('task-module.partials.preview-task-images', compact('title', 'records'));
    }

    public function SendTask(Request $request) {
        $id = $request->id;

        // $user_id = Auth::id();
        if ($request->type == 'TASK') {
            $task = Task::find($request->taskdata);
            $user = User::find($task->assign_to);
        } else {
            $task = DeveloperTask::find($request->taskdata);
            $user = User::find($task->user_id);
        }
        $taskdata = $request->taskdata;

        $media        = \Plank\Mediable\Media::find($request->id);

        $admin = Auth::user();

        $userid = Auth::id();
        $msg = $media->getUrl();
        if ($user && $user->phone) {
            if ($request->type == 'TASK') {

                $params = ChatMessage::create([
                    'id'      => $id,
                    'user_id' => $userid,
                    'task_id' => $request->task_id,

                    'sent_to_user_id' => $user->id,

                    'erp_user' => $task->assign_to,
                    'contact_id' => $task->assign_to,
                    'message' => $media->getUrl(),

                ]);
                $params = ChatMessage::create([
                    'id'      => $id,
                    'user_id' => $user->id,
                    'task_id' => $taskdata,

                    'sent_to_user_id' => $userid,

                    'erp_user' => $task->assign_to,
                    'contact_id' => $task->assign_to,
                    'message' => $media->getUrl(),

                ]);
            } else {
                $params = ChatMessage::create([
                    'id'      => $id,
                    'user_id' => $userid,
                    'task_id' => $request->task_id,
                    'developer_task_id' => $task->id,
                    'sent_to_user_id' => $user->id,
                    'issue_id' => $task->id,
                    'erp_user' => $task->assign_to,
                    'contact_id' => $task->assign_to,
                    // 'approved' => '1',
                    // 'status' => '2',
                    'message' => $media->getUrl(),

                ]);
            }


            if ($params) {
                app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone, $user->whatsapp_number, $msg);

                return response()->json([
                    'message' => 'Successfully Send File'
                ], 200);
            }
            return response()->json([
                'message' => 'Something Was Wrong'
            ], 500);


            return response()->json(["message" => "Sorry required fields is missing like id , userid"], 500);
        }
    }
    public function sendDocument(Request $request) {
        if ($request->id != null && $request->user_id != null) {
            $media        = \Plank\Mediable\Media::find($request->id);
            $user         = \App\User::find($request->user_id);
            $id = $request->id;
            $task = Task::find($request->user_id);
            $task_id = $request->doc_id;
            $userid = Auth::id();
            $msg = $media->getUrl();
            if ($user && $user->phone) {

                $params = ChatMessage::create([
                    'id'      => $id,
                    // 'user_id' => $user->id,
                    'user_id' => $userid,
                    'task_id' => $task_id,
                    'erp_user' => $user->id,
                    'sent_to_user_id' => $userid,
                    'message' => $media->getUrl(),

                ]);

                if ($params) {
                    app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone, $user->whatsapp_number, $msg);

                    return response()->json([
                        'message' => 'Successfully Send Document'
                    ], 200);
                }
                return response()->json([
                    'message' => 'Something Was Wrong'
                ], 500);
            }
        }
        return response()->json(["message" => "Sorry required fields is missing like User, Phone"], 500);
    }

    public function SendTaskSOP(Request $request) {

        $media        = \Plank\Mediable\Media::find($request->id);
        $user         = \App\User::find($request->user_id);

        $task = Task::find($request->task_id);
        $username = User::find($task->assign_to);

        // dd($username->name);
        $userid = Auth::id();

        $params = Sop::create([
            'name' => $username->name,
            'content' => $media->getUrl(),

        ]);

        return response()->json(["message" => "Data Added Successfully"]);
    }

    public function approveTimeHistory(Request $request) {
        if (Auth::user()->isAdmin) {
            if (!$request->approve_time || $request->approve_time == "" || !$request->developer_task_id || $request->developer_task_id == '') {
                return response()->json([
                    'message' => 'Select one time first'
                ], 500);
            }
            DeveloperTaskHistory::where('developer_task_id', $request->developer_task_id)->where('attribute', 'estimation_minute')->where('model', 'App\Task')->update(['is_approved' => 0]);
            $history = DeveloperTaskHistory::find($request->approve_time);
            $history->is_approved = 1;
            $history->save();

            $task = Task::find($request->developer_task_id);
            $time = $history->new_value !== null ? $history->new_value : $history->old_value;
            $msg = 'TIME APPROVED FOR TASK ' . '#DEVTASK-' . $task->id . '-' . $task->subject . ' - ' .  $time . ' MINS';

            $user = User::find($request->user_id);
            $admin = Auth::user();
            $master_user = User::find($task->master_user_id);

            if ($user) {
                if ($admin->phone) {
                    $chat = ChatMessage::create([
                        'number' => $admin->phone,
                        'user_id' => $user->id,
                        'customer_id' => $user->id,
                        'message' => $msg,
                        'status' => 0,
                        'developer_task_id' => $request->developer_task_id
                    ]);
                } else if ($user->phone) {
                    $chat = ChatMessage::create([
                        'number' => $user->phone,
                        'user_id' => $user->id,
                        'customer_id' => $user->id,
                        'message' => $msg,
                        'status' => 0,
                        'developer_task_id' => $request->developer_task_id
                    ]);
                } else if ($master_user && $master_user->phone) {
                    $chat = ChatMessage::create([
                        'number' => $master_user->phone,
                        'user_id' => $user->id,
                        'customer_id' => $user->id,
                        'message' => $msg,
                        'status' => 0,
                        'developer_task_id' => $request->developer_task_id
                    ]);
                }
                if (isset($chat)) {
                    if ($admin->phone) {
                        app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($admin->phone, $admin->whatsapp_number, $msg, false, $chat->id);
                    }
                    if ($user->phone) {
                        app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($user->phone, $user->whatsapp_number, $msg, false, $chat->id);
                    }
                    if ($master_user && $master_user->phone) {
                        app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($master_user->phone, $master_user->whatsapp_number, $msg, false, $chat->id);
                    }
                }
            }

            return response()->json([
                'message' => 'Success'
            ], 200);
        }
        return response()->json([
            'message' => 'Only admin can approve'
        ], 500);
    }

    public function getTrackedHistory(Request $request) {
        $id = $request->id;
        $type = $request->type;
        if ($type == 'lead') {
            $task_histories = DB::select(DB::raw("SELECT hubstaff_activities.task_id,cast(hubstaff_activities.starts_at as date) as starts_at_date,sum(hubstaff_activities.tracked) as total_tracked,tasks.master_user_id,users.name FROM `hubstaff_activities`  join tasks on tasks.lead_hubstaff_task_id = hubstaff_activities.task_id join users on users.id = tasks.master_user_id where tasks.id = " . $id . " group by starts_at_date"));
        } else {
            $task_histories = DB::select(DB::raw("SELECT hubstaff_activities.task_id,cast(hubstaff_activities.starts_at as date) as starts_at_date,sum(hubstaff_activities.tracked) as total_tracked,tasks.assign_to,users.name FROM `hubstaff_activities`  join tasks on tasks.hubstaff_task_id = hubstaff_activities.task_id join users on users.id = tasks.assign_to where tasks.id = " . $id . " group by starts_at_date"));
        }

        return response()->json(['histories' => $task_histories]);
    }

    public function updateTaskDueDate(Request $request) {
        if ($new = request('value')) {
            if ($task = Task::find(request('task_id'))) {
                $oldValue = $task->due_date;

                $task->update(['due_date' => $new]);
                TaskDueDateHistoryLog::create([
                    'task_id' => $task->id,
                    'task_type' => 'TASK',
                    'updated_by' => Auth::id(),
                    'old_due_date' => $oldValue,
                    'new_due_date' => $task->due_date,
                ]);
                return respJson(200, 'Successfully updated.');
            }
            return respJson(404, 'No task found.');
        }
        return respJson(400, 'Due date is required.');
    }

    public function taskCreateGetRemark(Request $request) {
        try {
            $msg = "";
            if ($request->remark != '') {
                TaskRemark::create(
                    [
                        'task_id' => $request->task_id,
                        'task_type' => $request->type,
                        'updated_by' => Auth::id(),
                        'remark' => $request->remark,
                    ]
                );
                $msg = " Created and ";
            }
            $taskRemarkData = TaskRemark::where([['task_id', '=', $request->task_id], ['task_type', '=', $request->type]])->get();
            $html = '';
            foreach ($taskRemarkData as $taskRemark) {
                $html .= "<tr>";
                $html .= "<td>" . $taskRemark->id . "</td>";
                $html .= "<td>" . $taskRemark->users->name . "</td>";
                $html .= "<td>" . $taskRemark->remark . "</td>";
                $html .= "<td>" . $taskRemark->created_at . "</td>";
                $html .= "<td><i class='fa fa-copy copy_remark' data-remark_text='" . $taskRemark->remark . "'></i></td>";
            }
            return response()->json(['code' => 200, 'data' => $html, 'message' => 'Remark ' . $msg . ' listed Successfully']);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'data' => '', 'message' => $e->getMessage()]);
        }
    }

    public function getTaskDueDateHistoryLog(Request $request) {
        $taskHistory = TaskDueDateHistoryLog::where([['task_id', '=', $request->task_id]])->get();

        if (count($taskHistory) > 0) {
            $html = "";
            foreach ($taskHistory as $taskHistoryData) {
                $html .= "<tr>";
                $html .= "<td>" . $taskHistoryData->id . "</td>";
                $html .= "<td>" . $taskHistoryData->users->name . "</td>";
                $html .= "<td>" . $taskHistoryData->old_due_date . "</td>";
                $html .= "<td>" . $taskHistoryData->new_due_date . "</td>";
                $html .= "<td>" . $taskHistoryData->created_at . "</td>";
                $html .= "</tr>";
            }
            return response()->json(['code' => 200, 'data' => $html, 'msg' => 'Task Due Date History successfully loaded']);
        } else {
            return response()->json(['code' => 500, 'msg' => 'Task Due Date history long not found'], 200);
        }
    }

    public function createHubstaffManualTask(Request $request) {
        $task = Task::find($request->id);
        if ($task) {
            if ($request->type == 'developer') {
                $user_id = $task->assign_to;
            } else {
                $user_id = $task->master_user_id;
            }
            // $hubstaff_project_id = getenv('HUBSTAFF_BULK_IMPORT_PROJECT_ID');
            $hubstaff_project_id = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID');

            $assignedUser = HubstaffMember::where('user_id', $user_id)->first();

            $hubstaffUserId = null;
            if ($assignedUser) {
                $hubstaffUserId = $assignedUser->hubstaff_user_id;
            }
            $taskSummery = "#" . $task->id . ". " . $task->task_subject;
            // $hubstaffUserId = 901839;
            if ($hubstaffUserId) {
                $hubstaffTaskId = $this->createHubstaffTask(
                    $taskSummery,
                    $hubstaffUserId,
                    $hubstaff_project_id
                );
            } else {
                return response()->json([
                    'message' => 'Hubstaff member not found'
                ], 500);
            }
            if ($hubstaffTaskId) {
                if ($request->type == 'developer') {
                    $task->hubstaff_task_id = $hubstaffTaskId;
                } else {
                    $task->lead_hubstaff_task_id = $hubstaffTaskId;
                }
                $task->save();
            } else {
                return response()->json([
                    'message' => 'Hubstaff task not created'
                ], 500);
            }
            if ($hubstaffTaskId) {



                $task = new HubstaffTask();
                $task->hubstaff_task_id = $hubstaffTaskId;
                $task->project_id = $hubstaff_project_id;
                $task->hubstaff_project_id = $hubstaff_project_id;
                $task->summary = $taskSummery;
                $task->save();
            }
            return response()->json([
                'message' => 'Successful'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Task not found'
            ], 500);
        }
    }

    public function getTaskCategories() {
        $categories = TaskCategory::where('is_approved', 1)->get();
        return view('task-module.partials.all-task-category', compact('categories'));
    }

    public function completeBulkTasks(Request $request) {
        if (count($request->selected_tasks) > 0) {
            foreach ($request->selected_tasks as $t) {
                $task = Task::find($t);
                $task->is_completed = date('Y-m-d H:i:s');
                $task->is_verified = date('Y-m-d H:i:s');
                if ($task->assignedTo) {
                    if ($task->assignedTo->fixed_price_user_or_job == 1) {
                        // Fixed price task.
                        continue;
                    }
                }
                $task->save();
            }
        }
        return response()->json(['message' => 'Successful']);
    }


    public function deleteBulkTasks(Request $request) {
        if (count($request->selected_tasks) > 0) {
            foreach ($request->selected_tasks as $t) {
                $task = Task::where('id', $t)->delete();
            }
        }
        return response()->json(['message' => 'Successful']);
    }

    public function getTimeHistory(Request $request) {
        $id = $request->id;
        $task_module = DeveloperTaskHistory::join('users', 'users.id', 'developer_tasks_history.user_id')
            ->where('developer_task_id', $id)
            ->where('model', 'App\Task')
            ->where('attribute', 'estimation_minute')
            ->select('developer_tasks_history.*', 'users.name')
            ->get();
        if ($task_module) {
            return $task_module;
        }
        return 'error';
    }


    /* update task status 
    */

    public function updateStatus(Request $request) {
        try {

            $task = Task::find($request->task_id);
            $old_status = $task->status;

            $task->status = $request->status;

            $task->save();
            DeveloperTaskHistory::create([
                'developer_task_id' => $request->task_id,
                'model' => 'App\Task',
                'attribute' => "task_status",
                'old_value' => $old_status,
                'new_value' => $task->status,
                'user_id' => Auth::id(),
            ]);

            if ($task->status == 1) {

                $task_user = User::find($task->assign_to);
                if (!$task_user) {
                    return response()->json([
                        'message'    => 'Please assign the task.'
                    ], 500);
                }
                $team_user = \DB::table('team_user')->where('user_id', $task->assign_to)->first();
                if ($team_user) {
                    $team_lead = \DB::table('teams')->where('id', $team_user->team_id)->first();
                    if ($team_lead) {
                        $task_user_for_payment = User::find($team_lead->user_id);
                    }
                }
                if (empty($task_user_for_payment)) {
                    $task_user_for_payment = $task_user;
                }
                // dd($task_user_for_payment);
                if ($task_user_for_payment->fixed_price_user_or_job == 0) {
                    return response()->json([
                        'message'    => 'Please provide salary payment method for user.'
                    ], 500);
                }
                if (!empty($task_user_for_payment)) {
                    if ($task_user_for_payment->fixed_price_user_or_job == 1) {
                        if ($task->cost == null) {
                            return response()->json([
                                'message'    => 'Please provide cost for fixed price task.'
                            ], 500);
                        }
                        $rate_estimated = $task->cost ?? 0;
                    } else if ($task_user_for_payment->fixed_price_user_or_job == 2) {
                        $userRate = UserRate::getRateForUser($task_user_for_payment->id);
                        if ($userRate && $userRate->hourly_rate !== null) {
                            $rate_estimated = $task->approximate * ($userRate->hourly_rate ?? 0) / 60;
                        } else {
                            return response()->json([
                                'message'    => 'Please provide hourly rate of user.'
                            ], 500);
                        }
                    }
                    $receipt_id = PaymentReceipt::create([
                        'status'            => 'Pending',
                        'rate_estimated'    => $rate_estimated,
                        'date'              => date('Y-m-d'),
                        'currency'          => '',
                        'user_id'           => $task_user_for_payment->id,
                        'by_command'        => 4,
                        'task_id'           => $task->id,
                    ]);
                }
            }

            return response()->json([
                'status' => 'success', 'message' => 'The task status updated.'
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error', 'message' => 'The task status not updated.'
            ], 500);
        }
    }

    /* create new task status */

    public function createStatus(Request $request) {
        $this->validate($request, ['task_status' => 'required']);

        try {

            TaskStatus::create(['name' => $request->task_status]);

            return redirect()->back()->with('success', 'The task status created successfully.');
        } catch (Exception $e) {

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function updateTaskReminder(Request $request) {
        $task = Task::find($request->get('task_id'));
        $task->frequency            = $request->get('frequency');
        $task->reminder_message     = $request->get('message');
        $task->reminder_from        = $request->get('reminder_from', "0000-00-00 00:00");
        $task->reminder_last_reply  = $request->get('reminder_last_reply', 0);
        $task->last_send_reminder   = date("Y-m-d H:i:s");
        $task->save();

        $message = $request->get('message');
        if (optional($task->assignedTo)->phone) {
            $requestData = new Request();
            $requestData->setMethod('POST');
            $requestData->request->add(['task_id' => $task->id, 'message' => $message, 'status' => 1]);
            app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'task');
            //app('App\Http\Controllers\WhatsAppController')->sendWithThirdApi($task->assignedTo->phone, '', $message);
        }

        return response()->json([
            'success'
        ]);
    }

    public function sendBrodCast(Request $request) {
        $taskIds = $request->selected_tasks;

        if (!empty($taskIds)) {
            foreach ($taskIds as $tid) {
                // started to send message
                $requestData = new Request();
                $requestData->setMethod('POST');
                $requestData->request->add(['task_id' => $tid, 'message' => $request->message, 'status' => 1]);
                app('App\Http\Controllers\WhatsAppController')->sendMessage($requestData, 'task');
            }

            return response()->json(["code" => 200, "message" => "Message has been sent to all selected task"]);
        }

        return response()->json(["code" => 500, "message" => "Please select atleast one task"]);
    }
    public function CommunicationTaskStatus(Request $request) {
        $task = Task::find($request->get('task_id'));

        if ($task->communication_status == 0) {
            $status = 1;
        }
        if ($task->communication_status == 1) {
            $status = 0;
        }

        $updatetask = Task::find($request->get('task_id'));
        $updatetask->communication_status = $status;
        $updatetask->update();
        return response()->json(['status' => 'success', 'communication_status' => $status]);
    }
    public function recurringHistory(request $request) {
        $task_id = $request->input('task_id');
        $html = '';
        $chatData = LogChatMessage::where('task_id', $task_id)->where('task_time_reminder', 0)
            ->orderBy('id', 'DESC')
            ->get();
        $i = 1;
        if (count($chatData) > 0) {
            foreach ($chatData as $history) {
                $html .= '<tr>';
                $html .= '<td>' . $i . '</td>';
                $html .= '<td>' . $history->log_case_id . '</td>';
                $html .= '<td>' . $history->message . '</td>';
                $html .= '<td>' . $history->log_msg . '</td>';
                $html .= '<td>' . $history->created_at . '</td>';
                $html .= '</tr>';

                $i++;
            }
            return response()->json(['html' => $html, 'success' => true], 200);
        } else {
            $html .= '<tr>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '<td></td>';
            $html .= '</tr>';
        }
        return response()->json(['html' => $html, 'success' => true], 200);
    }
    public function AssignTaskToUser(Request $request) {
        $task = Task::find($request->get('issue_id'));
        $old_id = $task->assign_to;
        if (!$old_id) {
            $old_id = 0;
        } else {
            DB::delete('delete from task_users where task_id = ? AND user_id = ? AND type = ?', array($task->id, $old_id, User::class));
        }
        $task->assign_to = $request->get('user_id');
        $task->save();
        //    $task->users()->attach([$request->input('assign_to') => ['type' => User::class]]);



        //   $hubstaff_project_id = getenv('HUBSTAFF_BULK_IMPORT_PROJECT_ID');
        $hubstaff_project_id = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID');

        $assignedUser = HubstaffMember::where('user_id', $request->input('user_id'))->first();
        // $hubstaffProject = HubstaffProject::find($request->input('hubstaff_project'));

        $hubstaffUserId = null;
        if ($assignedUser) {
            $hubstaffUserId = $assignedUser->hubstaff_user_id;
        }
        if ($task->is_statutory != 1) {
            $message = "#" . $task->id . ". " . $task->task_subject . ". " . $task->task_details;
        } else {
            $message = $task->task_subject . ". " . $task->task_details;
        }

        $taskSummery = substr($message, 0, 200);


        $hubstaffTaskId = $this->createHubstaffTask(
            $taskSummery,
            $hubstaffUserId,
            $hubstaff_project_id
        );

        if ($hubstaffTaskId) {
            $task->hubstaff_task_id = $hubstaffTaskId;
            $task->save();
        }
        if ($hubstaffUserId) {
            $task = new HubstaffTask();
            $task->hubstaff_task_id = $hubstaffTaskId;
            $task->project_id = $hubstaff_project_id;
            $task->hubstaff_project_id = $hubstaff_project_id;
            $task->summary = $message;
            $task->save();
        }

        $taskUser = new TaskUserHistory;
        $taskUser->model = 'App\Task';
        $taskUser->model_id = $task->id;
        $taskUser->old_id = $old_id;
        $taskUser->new_id = $request->get('user_id');
        $taskUser->user_type = 'developer';
        $taskUser->updated_by = Auth::user()->name;
        $taskUser->save();


        $values = array('task_id' => $request->get('issue_id'), 'user_id' => $request->get('user_id'), 'type' => 'App\User');
        DB::table('task_users')->insert($values);

        return response()->json(['status' => 'success']);
    }

    /**
     * return branch name or false
     */
    private function createBranchOnGithub($repositoryId, $taskId, $taskTitle,  $branchName = 'master') {
        $newBranchName = 'DEVTASK-' . $taskId;

        // get the master branch SHA
        // https://api.github.com/repositories/:repoId/branches/master
        $url = 'https://api.github.com/repositories/' . $repositoryId . '/branches/' . $branchName;
        try {
            $response = $this->githubClient->get($url);
            $masterSha = json_decode($response->getBody()->getContents())->commit->sha;
        } catch (Exception $e) {
            return false;
        }

        // create a branch
        // https://api.github.com/repositories/:repo/git/refs
        $url = 'https://api.github.com/repositories/' . $repositoryId . '/git/refs';
        try {
            $this->githubClient->post(
                $url,
                [
                    RequestOptions::BODY => json_encode([
                        "ref" => "refs/heads/" . $newBranchName,
                        "sha" => $masterSha
                    ])
                ]
            );
            return $newBranchName;
        } catch (Exception $e) {

            if ($e instanceof ClientException && $e->getResponse()->getStatusCode() == 422) {
                // branch already exists
                return $newBranchName;
            }
            return false;
        }
    }
    public function getUserHistory(Request $request) {
        if (isset($request->type)) {
            if ($request->type == "developer") {
                $users = TaskUserHistory::where('model', 'App\DeveloperTask')->where('model_id', $request->id)->get();
            } else {
                $users = TaskUserHistory::where('model', 'App\Task')->where('model_id', $request->id)->get();
            }
        } else {
            $users = TaskUserHistory::where('model', 'App\Task')->where('model_id', $request->id)->get();
        }

        foreach ($users as $u) {
            $old_name = null;
            $new_name = null;
            if ($u->old_id) {
                $old_name = User::find($u->old_id)->name;
            }
            if ($u->new_id) {
                $new_name = User::find($u->new_id)->name;
            }
            $u->new_name = $new_name;
            $u->old_name = $old_name;
        }
        return response()->json([
            'users' => $users
        ], 200);
    }
    public function getSiteDevelopmentTask(Request $request) {

        $site_developement_id = \App\SiteDevelopment::where("website_id", $request->site_id)->pluck("id");
        //    dd($site_developement_id);
        $merged = [];
        if (!empty($site_developement_id)) {
            $taskStatistics['Devtask'] = DeveloperTask::whereIn('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select();

            $query = DeveloperTask::join('users', 'users.id', 'developer_tasks.assigned_to')->whereIn('site_developement_id', $site_developement_id)->where('status', '!=', 'Done')->select('developer_tasks.id', 'developer_tasks.task as subject', 'developer_tasks.status', 'users.name as assigned_to_name');
            $query = $query->addSelect(DB::raw("'Devtask' as task_type,'developer_task' as message_type"));
            $taskStatistics = $query->get();
            //  print_r($taskStatistics);
            $othertask = Task::whereIn('site_developement_id', $site_developement_id)->whereNull('is_completed')->select();
            $query1 = Task::join('users', 'users.id', 'tasks.assign_to')->whereIn('site_developement_id', $site_developement_id)->whereNull('is_completed')->select('tasks.id', 'tasks.task_subject as subject', 'tasks.assign_status', 'users.name as assigned_to_name');
            $query1 = $query1->addSelect(DB::raw("'Othertask' as task_type,'task' as message_type"));
            $othertaskStatistics = $query1->get();
            $merged = $othertaskStatistics->merge($taskStatistics);
        }
        //   return view('task-module.partials.site-development-task', compact('merged'));

        return response()->json(["code" => 200, "taskStatistics" => $merged]);
    }


    /*
    * AssignMultipleTaskToUser : Assign multiple task to user
    * DEVTASK-21672
    */
    public function AssignMultipleTaskToUser(Request $request) {
        $tasks = $request->get('taskIDs');
        if (count($tasks) > 0) {
            foreach ($tasks as $tsk) {
                $task = Task::find($tsk);
                $old_id = $task->assign_to;
                if (!$old_id) {
                    $old_id = 0;
                } else {
                    DB::delete('delete from task_users where task_id = ? AND user_id = ? AND type = ?', array($task->id, $old_id, User::class));
                }
                $task->assign_to = $request->get('user_assigned_to');
                $task->save();
                //    $task->users()->attach([$request->input('assign_to') => ['type' => User::class]]);



                //   $hubstaff_project_id = getenv('HUBSTAFF_BULK_IMPORT_PROJECT_ID');
                $hubstaff_project_id = config('env.HUBSTAFF_BULK_IMPORT_PROJECT_ID');

                $assignedUser = HubstaffMember::where('user_id', $request->input('user_assigned_to'))->first();
                // $hubstaffProject = HubstaffProject::find($request->input('hubstaff_project'));

                $hubstaffUserId = null;
                if ($assignedUser) {
                    $hubstaffUserId = $assignedUser->hubstaff_user_id;
                }
                if ($task->is_statutory != 1) {
                    $message = "#" . $task->id . ". " . $task->task_subject . ". " . $task->task_details;
                } else {
                    $message = $task->task_subject . ". " . $task->task_details;
                }

                $taskSummery = substr($message, 0, 200);


                $hubstaffTaskId = $this->createHubstaffTask(
                    $taskSummery,
                    $hubstaffUserId,
                    $hubstaff_project_id
                );

                if ($hubstaffTaskId) {
                    $task->hubstaff_task_id = $hubstaffTaskId;
                    $task->save();
                }
                if ($hubstaffUserId) {
                    $task = new HubstaffTask();
                    $task->hubstaff_task_id = $hubstaffTaskId;
                    $task->project_id = $hubstaff_project_id;
                    $task->hubstaff_project_id = $hubstaff_project_id;
                    $task->summary = $message;
                    $task->save();
                }

                $taskUser = new TaskUserHistory;
                $taskUser->model = 'App\Task';
                $taskUser->model_id = $task->id;
                $taskUser->old_id = $old_id;
                $taskUser->new_id = $request->get('user_assigned_to');
                $taskUser->user_type = 'developer';
                $taskUser->updated_by = Auth::user()->name;
                $taskUser->save();


                $values = array('task_id' => $task->id, 'user_id' => $request->get('user_assigned_to'), 'type' => 'App\User');
                DB::table('task_users')->insert($values);
            }
        }


        return redirect('/development/automatic/tasks')->withSuccess('You have successfully assigned task!');
    }

    public function taskStartDateUpdate() {
        if ($new = request('start_date')) {
            if ($task = Task::find(request('task_id'))) {
                $task->updateStartDate($new);
                return response()->json([
                    'message' => 'Successfully updated'
                ], 200);
            }
            return response()->json([
                'message' => 'No task found.'
            ], 404);
        }
        return response()->json([
            'message' => 'Start date is required.'
        ], 400);
    }
    public function taskStartDateHistory() {
        $list = TaskHistoryForStartDate::with('updatedBy')->where([['task_id', '=', request('task_id')]])->orderBy('id')->get();
        if ($list->count()) {
            $html = "";
            foreach ($list as $single) {
                $html .= "<tr>";
                $html .= "<td>" . $single->id . "</td>";
                $html .= "<td>" . ($single->updatedBy ? $single->updatedBy->name : '-') . "</td>";
                $html .= "<td>" . $single->old_value . "</td>";
                $html .= "<td>" . $single->new_value . "</td>";
                $html .= "<td>" . $single->created_at . "</td>";
                $html .= "</tr>";
            }
            return response()->json(['data' => $html]);
        } else {
            return response()->json(['message' => 'No records found.'], 404);
        }
    }

    public function dropdownUserWise() {
        try {
            $dataArr = [];
            if ($userId = request('userId')) {
                $dTasks = DeveloperTask::where('assigned_to', $userId)
                    ->whereNotIn('status', ['Done', 'User Complete'])
                    ->orderBy('id', 'DESC')->limit(10)->get();
                foreach ($dTasks as $key => $dTask) {
                    $dataArr['Developer Tasks']['DT-' . $dTask->id] = '(DT-' . $dTask->id . ') - ' . $dTask->subject;
                }

                $tasks = Task::where('assign_to', $userId)->whereNotIn('status', [1, 15])->orderBy('id', 'DESC')->limit(10)->get();
                foreach ($tasks as $key => $task) {
                    $dataArr['Tasks']['T-' . $task->id] = '(T-' . $dTask->id . ') - ' . $task->task_subject;
                }
            }
            return response()->json([
                'list' => $dataArr ? makeDropdown($dataArr) : NULL
            ]);
        } catch (\Throwable $th) {
            return respException($th);
        }
    }
    public function slotAssign() {
        try {
            $newValue = request('date') . ' ' . substr(request('slot'), 0, 2) . ':00:00';
            if ($id = isDeveloperTaskId(request('taskId'))) {
                if ($single = DeveloperTask::find($id)) {
                    $oldValue = $single->start_date;
                    if ($oldValue == $newValue) {
                        return respJson(400, 'No change in time slot.');
                    }
                    $single->start_date = $newValue;
                    $single->save();
                    $single->updateHistory('start_date', $oldValue, $newValue);
                    return respJson(200, 'Time slot updated successfully.');
                }
            } else if ($id = isRegularTaskId(request('taskId'))) {
                if ($single = Task::find($id)) {
                    $single->updateStartDate($newValue);
                    return respJson(200, 'Time slot updated successfully.');
                }
            }
            return respJson(404, 'No task found.');
        } catch (\Throwable $th) {
            return respException($th);
        }
    }
}
