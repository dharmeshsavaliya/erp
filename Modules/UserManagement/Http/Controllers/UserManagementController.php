<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\ColdLeads;
use App\User;
use App\UserRate;
use App\Role;
use App\Permission;
use App\ApiKey;
use App\Customer;
use Auth;
use App\Helpers;
use App\Task;
use PragmaRX\Tracker\Vendor\Laravel\Models\Session;
use \Carbon\Carbon;
use DateTime;
use App\Hubstaff\HubstaffActivity;
use App\Hubstaff\HubstaffPaymentAccount;
use App\Payment;
use App\PaymentMethod;
use App\Team;
use App\DeveloperTask;
use App\UserAvaibility;
use DB;
use Hash;
use Illuminate\Support\Arr;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $title = "User management";
        return view('usermanagement::index', compact('title'));
    }

    public function records(Request $request)
    {
        $user = new User;
        if(!Auth::user()->isAdmin()) {
            $user = $user->where('id',Auth::user()->id);
        }
       
        if($request->is_active == 1) {
            $user = $user->where('is_active',1);
        }
        if($request->is_active == 2) {
            $user = $user->where('is_active',0);
        }
        if($request->keyword != null) {
            $user = $user->where(function($q) use ($request) {
                $q->where("email","like","%".$request->keyword."%")
                ->orWhere("name","like","%".$request->keyword."%")
                ->orWhere("phone","like","%".$request->keyword."%");
            });
        }
    

        $user = $user->select(["users.*"])->orderBy('is_active','DESC')->paginate(12);
        $limitchacter = 50;

        $items = [];
        $replies = null;
        if (!$user->isEmpty()) {
            foreach ($user as $u) {
                $currentRate = $u->latestRate;
                $team = Team::where('user_id', $u->id)->first();
                $user_in_team = 0;
                if($team) {
                    $u["team_leads"] = $team->users->count();
                    $u["team_members"] = $team->users->toArray();
                    $u["team"] = $team;
                    $user_in_team = 1;
                }
                $isMember = $u->teams()->first();
                if($isMember) {
                    $user_in_team = 1;
                }

                $u["user_in_team"] = $user_in_team;
                $u["hourly_rate"] = ($currentRate) ? $currentRate->hourly_rate : 0;
                $u["currency"]    = ($currentRate) ? $currentRate->currency : "USD";


                $u["yesterday_hrs"] = $u->yesterdayHrs();
                $u["isAdmin"] = $u->isAdmin();

                $online_now = $u->lastOnline();
                if($online_now) {
                    $u["online_now"] =   \Carbon\Carbon::parse($online_now)->format('d-m H:i');
                }
                else {
                    $u["online_now"] = null;
                }

                $lastPaid = $u->payments()->orderBy('id','desc')->first();
                if($lastPaid) {
                    $lastPaidOn = $lastPaid->paid_upto;
                }
                else {
                    $query = HubstaffPaymentAccount::where('user_id',$u->id)->first();
                    if(!$query) {
                        $lastPaidOn = date("Y-m-d"); 
                    }
                    else {
                        $lastPaidOn =  date('Y-m-d',strtotime($query->billing_start . "-1 days"));
                    }
                }
                if($lastPaidOn) {
                    $u["previousDue"] = $u->previousDue($lastPaidOn);
                }
                else {
                    $u["previousDue"] = '';
                }
                
                $u["lastPaidOn"] = $lastPaidOn;
                
                if($u->payment_frequency == 'fornightly') {
                    $u["nextDue"] =  date('Y-m-d',strtotime($lastPaidOn . "+1 days"));
                }
                if($u->payment_frequency == 'weekly') {
                    $u["nextDue"] = date('Y-m-d',strtotime($lastPaidOn . "+7 days"));
                }
                if($u->payment_frequency == 'biweekly') {
                    $u["nextDue"] = date('Y-m-d',strtotime($lastPaidOn . "+14 days"));
                }
                if($u->payment_frequency == 'monthly') {
                    $u["nextDue"] = date('Y-m-d',strtotime($lastPaidOn . "+30 days"));
                }
                $items[] = $u;
            }

            $replies = \App\Reply::where("model", "User")->whereNull("deleted_at")->pluck("reply", "id")->toArray();
        }

        $isAdmin = Auth::user()->isAdmin();
        return response()->json([
            "code"       => 200,
            "data"       => $items,
            "replies" => $replies,
            'isAdmin' => $isAdmin,
            "pagination" => (string) $user->links(),
            "total"      => $user->total(),
            "page"       => $user->currentPage(),
        ]);

    }

    public function edit($id) {
        $user = User::find($id);
		$roles = Role::orderBy('name', 'asc')->pluck('name', 'id')->all();
		$permission = Permission::orderBy('name', 'asc')->pluck('name', 'id')->all();

		$users = User::all();
		$userRole = $user->roles->pluck('name', 'id')->all();
		$userPermission = $user->permissions->pluck('name', 'id')->all();
		$agent_roles  = array('sales' => 'Sales', 'support' => 'Support', 'queries' => 'Others');
		$user_agent_roles = explode(',', $user->agent_role);
		$api_keys = ApiKey::select('number')->get();
		$customers_all = Customer::select(['id', 'name', 'email', 'phone', 'instahandler'])->whereRaw("customers.id NOT IN (SELECT customer_id FROM user_customers WHERE user_id != $id)")->get()->toArray();

        $userRate = UserRate::getRateForUser($user->id);
        // return response()->json([
        //     "code"       => 200,
        //     "user"       => $user,
        //     "users"       => $users,
        //     "agent_roles" => $agent_roles,
        //     "api_keys"    => $api_keys,
        //     "customers_all" => $customers_all,
        //     "userRate" => $userRate,
        // ]);

        return view('usermanagement::templates.edit-user', compact('user','userRole', 'users', 'roles', 'edit', 'agent_roles', 'user_agent_roles', 'api_keys', 'customers_all', 'permission', 'userPermission', 'userRate'));
    }


    public function update(Request $request, $id)
	{
		$this->validate($request, [
			'name' => 'required',
			'email' => 'required|email|unique:users,email,' . $id,
			'phone' => 'sometimes|nullable|integer|unique:users,phone,' . $id,
			'password' => 'same:confirm-password'
        ]);
		$input = $request->all();
		$hourly_rate = $input['hourly_rate'];
		$currency = $input['currency'];

		unset($input['hourly_rate']);
		unset($input['currency']);
		$input['name'] = str_replace(' ', '_', $input['name']);
		if (isset($input['agent_role'])) {
			$input['agent_role'] = implode(',', $input['agent_role']);
		} else {
			$input['agent_role'] = '';
		}


		if (!empty($input['password'])) {
			$input['password'] = Hash::make($input['password']);
		} else {
			$input = array_except($input, array('password'));
		}

		$user = User::find($id);
		$user->update($input);

		if ($request->customer[0] != '') {
			$user->customers()->sync($request->customer);
		}


		$user->listing_approval_rate = $request->get('listing_approval_rate') ?? '0';
		$user->listing_rejection_rate = $request->get('listing_rejection_rate') ?? '0';
		$user->save();


		$userRate = new UserRate();
		$userRate->start_date = Carbon::now();
		$userRate->hourly_rate = $hourly_rate;
		$userRate->currency = $currency;
		$userRate->user_id = $user->id;
		$userRate->save();
		return redirect()->back()
			->with('success', 'User updated successfully');
    }
    


	public function activate(Request $request, $id)
	{
		$user = User::find($id);
		if ($user->is_active == 1) {
			$user->is_active = 0;
		} else {
			$user->is_active = 1;
		}

		$user->save();

        return response()->json([
            "code"       => 200,
            "message"       => 'User sucessfully updated',
            "page"       => $request->get('page')
        ]);

    }
    public function show($id)
	{
		$user = User::find($id);

		if (Auth::id() != $id) {
			return redirect()->route('user-management.index')->withWarning("You don't have access to this page!");
		}

		$users_array = Helpers::getUserArray(User::all());
		$roles = Role::pluck('name', 'name')->all();
		$users = User::all();
		$userRole = $user->roles->pluck('name', 'name')->all();
		$agent_roles  = array('sales' => 'Sales', 'support' => 'Support', 'queries' => 'Others');
		$user_agent_roles = explode(',', $user->agent_role);
		$api_keys = ApiKey::select('number')->get();

		$pending_tasks = Task::where('is_statutory', 0)
			->whereNull('is_completed')
			->where(function ($query) use ($id) {
				return $query->orWhere('assign_from', $id)
					->orWhere('assign_to', $id);
			})->get();

		// dd($pending_tasks);
        // return response()->json(["code" => 200, "user" => $user]);

        return view('usermanagement::templates.show', 
        [
			'user'	=> $user,
			'users_array'	=> $users_array,
			'roles'	=> $roles,
			'users'	=> $users,
			'userRole'	=> $userRole,
			'agent_roles'	=> $agent_roles,
			'user_agent_roles'	=> $user_agent_roles,
			'api_keys'	=> $api_keys,
			'pending_tasks'	=> $pending_tasks,
		]);

    }
    
    public function usertrack($id)
    {
        $user = User::find($id);
        $actions = $user->actions()->orderBy('created_at', 'DESC')->get();

        $tracks = Session::where('user_id', $id)->orderBy('created_at', 'DESC')->get();

        $routeActions = [
            'users.index' => 'Viewed Users Page',
            'users.show' => 'Viewed A User',
            'customer.index' => 'Viewed Customer Page',
            'customer.show' => 'Viewed A Customer Page',
            'cold-leads.index' => 'Viewed Cold Leads Page',
            'home' => 'Landed Homepage',
            'purchase.index' => 'Viewed Purchase Page'
        ];

        $models = [
            'users.show' => new User(),
            'customer.show' => new Customer(),
            'cold-leads.show' => new ColdLeads(),
        ];

        return view('usermanagement::templates.track', 
        [
			'actions'	=> $actions,
			'tracks'	=> $tracks,
			'routeActions'	=> $routeActions,
			'models'	=> $models
		]);
    }

    
    public function userPayments($id, Request $request)
	{
		$params = $request->all();

		$date = new DateTime();

		if (isset($params['year']) && isset($params['week'])) {
			$year = $params['year'];
			$week = $params['week'];
		} else {
			$week = $date->format("W");
			$year = $date->format("Y");
		}
		$result = getStartAndEndDate($week, $year);
		$start = $result['week_start'];
		$end = $result['week_end'];

		$user = User::join('hubstaff_payment_accounts as hpa',"hpa.user_id","users.id")->where('users.id', $id)->with(['currentRate'])->first();
		$usersRatesThisWeek = UserRate::ratesForWeek($week, $year);

		$usersRatesPreviousWeek = UserRate::latestRatesForWeek($week - 1, $year);

		$activitiesForWeek = HubstaffActivity::getActivitiesForWeek($week, $year);

		$paymentsDone = Payment::getConsidatedUserPayments();

		$amountToBePaid = HubstaffPaymentAccount::getConsidatedUserAmountToBePaid();

		

        $now = now();
        $paymentMethods = array();
            if($user) {
                $user->secondsTracked = 0;
			$user->currency = '-';
			$user->total = 0;

			

            $userPaymentsDone = 0;
            
			$userPaymentsDoneModel = $paymentsDone->first(function ($value) use($user) {
				return $value->user_id == $user->id;
			});

			if($userPaymentsDoneModel){
				$userPaymentsDone = $userPaymentsDoneModel->paid;
			}

			$userPaymentsToBeDone = 0;
			$userAmountToBePaidModel = $amountToBePaid->first(function ($value) use($user){
				return $value->user_id == $user->id;
			});

			if($userAmountToBePaidModel){
				$userPaymentsToBeDone = $userAmountToBePaidModel->amount;
			}

			$user->balance = $userPaymentsToBeDone - $userPaymentsDone;

			

			//echo $user->id. ' '.$userPaymentsToBeDone. ' '. $userPaymentsDone. PHP_EOL;


			$invidualRatesPreviousWeek  = $usersRatesPreviousWeek->first(function ($value, $key) use ($user) {
				return $value->user_id == $user->id;
			});




			$weekRates = [];

			if ($invidualRatesPreviousWeek) {
				$weekRates[] = array(
					'start_date' => $start,
					'rate' => $invidualRatesPreviousWeek->hourly_rate,
					'currency' => $invidualRatesPreviousWeek->currency
				);
			}

			$rates = $usersRatesThisWeek->filter(function ($value, $key) use ($user) {
				return $value->user_id == $user->id;
			});

			if ($rates) {

				foreach ($rates as $rate) {
					$weekRates[] = array(
						'start_date' => $rate->start_date,
						'rate' => $rate->hourly_rate,
						'currency' => $rate->currency
					);
				}
			}


			usort($weekRates, function ($a, $b) {
				return strtotime($a['start_date']) - strtotime($b['start_date']);
			});


			if (sizeof($weekRates) > 0) {
				$lastEntry = $weekRates[sizeof($weekRates) - 1];

				$weekRates[] = array(
					'start_date' => $end,
					'rate' => $lastEntry['rate'],
					'currency' => $lastEntry['currency']
				);

				$user->currency = $lastEntry['currency'];
			}

			$activities = $activitiesForWeek->filter(function ($value, $key) use ($user) {
				return $value->system_user_id === $user->id;
			});

			$user->trackedActivitiesForWeek = $activities;

			foreach ($activities as $activity) {
				$user->secondsTracked += $activity->tracked;
				$i = 0;
				while ($i < sizeof($weekRates) - 1) {

					$start = $weekRates[$i];
					$end = $weekRates[$i + 1];

					if ($activity->starts_at >= $start['start_date'] && $activity->start_time < $end['start_date']) {
						// the activity needs calculation for the start rate and hence do it
						$earnings = $activity->tracked * ($start['rate'] / 60 / 60);
						$activity->rate = $start['rate'];
						$activity->earnings = $earnings;
						$user->total += $earnings;
						break;
					}
					$i++;
				}
			}
		

		//exit;
            
            foreach (PaymentMethod::all() as $paymentMethod) {
                $paymentMethods[$paymentMethod->id] = $paymentMethod->name;
            }
        }
			
        
        return view('usermanagement::templates.payments', 
        [
			'user' => $user,
			'id' => $id,
			'selectedYear' => $year,
			'selectedWeek' => $week,
			'paymentMethods' => $paymentMethods
		]);
    }
    

    public function getRoles($id) {
        $user = User::find($id);
		$roles = Role::orderBy('name', 'asc')->pluck('name', 'id')->all();
		$userRole = $user->roles->pluck('name', 'id')->all();
        
        return response()->json([
            "code"       => 200,
            "user"       => $user,
            "userRole"       => $userRole,
            "roles"       => $roles
        ]);
    }

        public function submitRoles($id, Request $request) {
            $user = User::find($id);
            if(Auth::user()->hasRole('Admin')) {
                $user->roles()->sync($request->input('roles'));
                return response()->json([
                    "code"       => 200,
                    "message"       => 'Role updated successfully'
                ]);
            }
            return response()->json([
                "code"       => 200,
                "message"       => 'Unauthorized access'
            ]);
    }

    public function getPermission($id) {
        $user = User::find($id);
		$permission = Permission::orderBy('name', 'asc')->pluck('name', 'id')->all();
        $userPermission = $user->permissions->pluck('name', 'id')->all();
        
        return response()->json([
            "code"       => 200,
            "user"       => $user,
            "userPermission"       => $userPermission,
            "permissions"       => $permission
        ]);
    }

    public function submitPermission($id, Request $request) {
        $user = User::find($id);
        if(Auth::user()->hasRole('Admin')) {
            $user->permissions()->sync($request->input('permissions'));
            return response()->json([
                "code"       => 200,
                "message"       => 'Permission updated successfully'
            ]);
        }
        return response()->json([
            "code"       => 200,
            "message"       => 'Unauthorized access'
        ]);
    }

    public function addNewPermission(Request $request) {
        
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'route' => 'required|unique:roles,name',
            
        ]);
        $permission = new Permission();
        $permission->name = $request->name;
        $permission->route = $request->route;
        $permission->save();
        return response()->json([
            "code"       => 200,
            "permission"       => $permission
        ]);    
    }

    public function paymentInfo($id) {
        $user = User::find($id);
        $lastPaid = $user->payments()->orderBy('id','desc')->first();
        if($lastPaid) {
            $lastPaidOn = $lastPaid->paid_upto;
        }
        else {
            $query = HubstaffPaymentAccount::where('user_id',$id)->first();
            if(!$query) {
                return response()->json([
                    "code"       => 500,
                    "message"       => 'No data found'
                ]); 
            }
            $lastPaidOn = date('Y-m-d',strtotime($query->billing_start . "-1 days"));
        }
        $pendingPyments = HubstaffPaymentAccount::where('user_id',$id)->where('billing_start','>',$lastPaidOn)->get();
        if(!count($pendingPyments)) {
            return response()->json([
                "code"       => 500,
                "message"       => 'No data found'
            ]); 
        }
        $pendingTerms = [];
        if($user->payment_frequency == 'fornightly') {
            $totalPendingTerms = count($pendingPyments);
            $perPacket = 1;
        }
        if($user->payment_frequency == 'weekly') {
            $totalPendingTerms = floor(count($pendingPyments)/7);
            $perPacket = 7;
        }
        if($user->payment_frequency == 'biweekly') {
            $totalPendingTerms = floor(count($pendingPyments)/14);
            $perPacket = 14;
        }
        if($user->payment_frequency == 'monthly') {
            $totalPendingTerms = floor(count($pendingPyments)/30);
            $perPacket = 30;
        }
        $count = 0;
        $packetCount = 0;
        $totalAmount = 0;
        $totalAmountTobePaid = 0;
        foreach($pendingPyments as $pending) {
            if($count < $totalPendingTerms) {
                $totalAmount = $totalAmount + ($pending->hrs * $pending->rate);
                $totalAmountTobePaid = $totalAmountTobePaid + ($pending->hrs * $pending->rate * $pending->ex_rate);
                $packetCount = $packetCount + 1;
                if($packetCount == $perPacket) {
                    $packetCount = 0;
                    $count = $count + 1;
                    $array = array(
                        'totalAmount' => $totalAmount,
                        'lastPaidOn' => $pending->billing_start,
                        'currency' => $pending->currency,
                        'totalAmountTobePaid' => $totalAmountTobePaid,
                        'payment_currency' => $pending->payment_currency
                    );

                    $pendingTerms[] = $array;
                    $totalAmount = 0;
                    $totalAmountTobePaid = 0;
                }
            }
            else {
            break;
            }
        }
        $paymentMethods = PaymentMethod::all();
        return view('usermanagement::templates.add-payment', compact('user','pendingTerms','paymentMethods'));


    }

    public function addPaymentMethod(Request $request) {
        
        $paymentMethods = new PaymentMethod;
        $paymentMethods->name = $request->name;
        $paymentMethods->save();
        $paymentMethods = PaymentMethod::all();
        return view('usermanagement::templates.new-payment-methods', compact('paymentMethods'));
    }

    public function savePayments($id, Request $request) {
        
        $this->validate($request, [
			'currency' => 'required',
			'amounts' => 'required',
			'payment_method_id' => 'required',
        ]);
        

        $user = User::find($id);
        $lastPaid = $user->payments()->orderBy('id','desc')->first();
        if($lastPaid) {
            $lastPaidOn = $lastPaid->paid_upto;
        }
        else {
            $query = HubstaffPaymentAccount::where('user_id',$id)->first();
            $lastPaidOn = date('Y-m-d',strtotime($query->billing_start . "-1 days"));
        }
        $pendingPyments = HubstaffPaymentAccount::where('user_id',$id)->where('billing_start','>',$lastPaidOn)->get();
        $pendingTerms = [];
        if($user->payment_frequency == 'fornightly') {
            $perPacket = 1;
        }
        if($user->payment_frequency == 'weekly') {
            $perPacket = 7;
        }
        if($user->payment_frequency == 'biweekly') {
            $perPacket = 14;
        }
        if($user->payment_frequency == 'monthly') {
            $perPacket = 30;
        }
        $count = 1;
        $totalPendingRaws = $perPacket * count($request->amounts);
        foreach($pendingPyments as $pending) {
            if($count == $totalPendingRaws) {
                $resetLastPaidOn = $pending->billing_start;
            break;
            }
            $count++;
        }
        $totalAmount = 0;
        foreach($request->amounts as $amount) {
            $totalAmount = $totalAmount + $amount;
        }
        $payment = new Payment;
        $payment->payment_method_id = $request->payment_method_id;
        $payment->user_id = $id;
        $payment->note = $request->note;
        $payment->amount = $totalAmount;
        $payment->currency = $request->currency;
        $payment->paid_upto = $resetLastPaidOn;
        $payment->save();
        return redirect()->back()->with('success','Payment done successfully');
    }


    public function addReply(Request $request)
    {
      $reply = $request->get("reply");
      $autoReply = [];
      // add reply from here 
      if (!empty($reply)) {
  
        $autoReply = \App\Reply::updateOrCreate(
          ['reply' => $reply, 'model' => 'User', "category_id" => 1],
          ['reply' => $reply]
        );
      }
  
      return response()->json(["code" => 200, 'data' => $autoReply]);
    }

    public function deleteReply(Request $request)
    {
      $id = $request->get("id");
  
      if ($id > 0) {
        $autoReply = \App\Reply::where("id", $id)->first();
        if ($autoReply) {
          $autoReply->delete();
        }
      }
  
      return response()->json([
        "code" => 200, "data" => \App\Reply::where("model", "User")
          ->whereNull("deleted_at")
          ->pluck("reply", "id")
          ->toArray()
      ]);
    }


    public function userTasks($id) {
        $user = User::find($id);
         $tasks = Task::where('assign_to',$id)->where('is_completed',NULL)->select('id as task_id','task_subject as subject','task_details as details','approximate as approximate_time','due_date');
         $tasks = $tasks->addSelect(DB::raw("'TASK' as type"));
         $devTasks = DeveloperTask::where('assigned_to',$id)->where('status','!=','Done')->select('id as task_id','subject','task as details','estimate_minutes as approximate_time','end_time as due_date');
         $devTasks = $devTasks->addSelect(DB::raw("'DEVTASK' as type"));

         $taskList = $devTasks->union($tasks)->get();
       
            return response()->json([
                "code"       => 200,
                "user"       => $user,
                "taskList"       => $taskList
            ]);
    }


    public function createTeam($id) {
        $user = User::find($id);
        $users = User::where('id','!=',$id)->where('is_active',1)->get()->pluck('name', 'id');
        
        return response()->json([
            "code"       => 200,
            "user"       => $user,
            "users"       => $users
        ]);
    }

    public function submitTeam($id, Request $request) {
        $user = User::find($id);
        $isLeader = Team::where('user_id',$id)->first();
        if($isLeader) {
            return response()->json([
                "code"       => 500,
                "message"       => 'This user is already a team leader'
            ]);
        }

        $isMember = $user->teams()->first();
        if($isMember) {
            return response()->json([
                "code"       => 500,
                "message"       => 'This user is already a team member'
            ]);
        }
        $team = new Team;
        $team->name = $request->name;
        $team->user_id = $id;
        $team->save();
        if(Auth::user()->hasRole('Admin')) {
            $members = $request->input('members');
            if($members) {
                foreach($members as $mem) {
                    $u = User::find($mem);
                    if($u) {
                        $isMember = $u->teams()->first();
                        $isLeader = Team::where('user_id',$mem)->first();
                        if(!$isMember && !$isLeader) {
                            $team->users()->attach($mem); 
                        }
                    }
                }
            }
            return response()->json([
                "code"       => 200,
                "message"       => 'Team created successfully'
            ]);
        }
        return response()->json([
            "code"       => 200,
            "message"       => 'Unauthorized access'
        ]);
    }


    public function getTeam($id) {
        $team = Team::where('user_id',$id)->first();
        $team->user;
        $team->members = $team->users()->pluck('name','id');
        $totalMembers =  $team->users()->count();
       
        $users = User::where('id','!=',$id)->where('is_active',1)->get()->pluck('name', 'id');
        return response()->json([
            "code"       => 200,
            "team"       => $team,
            "users"       => $users,
            "totalMembers"       => $totalMembers
        ]);
    }


    public function editTeam($id, Request $request) {
        $team = Team::find($id);

        if(Auth::user()->hasRole('Admin')) {
            $team->update(['name' => $request->name]);
            $members = $request->input('members');
            if($members) {
                $team->users()->detach();
                foreach($members as $mem) {
                    $u = User::find($mem);
                    if($u) {
                        $isMember = $u->teams()->first();
                        $isLeader = Team::where('user_id',$mem)->first();
                        if(!$isMember && !$isLeader) {
                            $team->users()->attach($mem); 
                        }
                    }
                }
            }
            return response()->json([
                "code"       => 200,
                "message"       => 'Team updated successfully'
            ]);
        }
        return response()->json([
            "code"       => 200,
            "message"       => 'Unauthorized access'
        ]);
    }

    public function saveUserAvaibility(Request $request) {
        // $this->validate($request, [
		// 	'user_id' => 'required',
		// 	'from' => 'required',
		// 	'to' => 'required',
		// 	'day' => 'required',
		// 	'status' => 'required',
        // ]);
        if(!$request->user_id || $request->user_id == "" || !$request->day || $request->day == "") {
            return response()->json([
                "code"       => 500,
                "error"       => 'User name and day is required'
            ]);
        }
        if($request->status == 1) {
            if(!$request->from || $request->from == "" || !$request->to || $request->to == "") {
                return response()->json([
                    "code"       => 500,
                    "error"       => 'From and To is required'
                ]);
            }
            if($request->to <= $request->from) {
                return response()->json([
                    "code"       => 500,
                    "error"       => 'Put time in 24 hours format'
                ]);
            }
        }

        $note = trim($request->note);
        if(!$request->status) {
            
            if(!$note || $note == "") {
                return response()->json([
                    "code"       => 500,
                    "error"       => 'Please provide reason for absence'
                ]);
            }
        }
        $nextDay = 'next '.$request->day;
        $day = date('Y-m-d', strtotime($nextDay));
        $user_avaibility = new UserAvaibility;
        $user_avaibility->date = $day;
        $user_avaibility->from = $request->from;
        $user_avaibility->user_id = $request->user_id;
        $user_avaibility->to = $request->to;
        $user_avaibility->status = $request->status;
        $user_avaibility->note = $note;
        $user_avaibility->save();
        return response()->json([
            "code"       => 200,
            "message"       => 'Successful'
        ]);
        
    }

    public function userAvaibility($id) {
        $user = User::find($id);
        $today = date('Y-m-d');
        $avaibility = UserAvaibility::where('user_id',$id)->where('date','>=',$today)->get();
        foreach($avaibility as $av) {
            $av->day = date('D', strtotime($av['date']));
        }
        $avaibility = $avaibility->toArray();
        return response()->json([
            "code"       => 200,
            "user"       => $user,
            "avaibility" => $avaibility
        ]);
    }
    public function userAvaibilityUpdate($id, Request $request) {
        UserAvaibility::find($id)->update(['status' => $request->status, 'note' => $request->note]);
        
        return response()->json([
            "code"       => 200,
            "user"       => 'Success'
        ]);
    }


}
