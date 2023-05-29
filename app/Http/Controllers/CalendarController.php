<?php

namespace App\Http\Controllers;

use App\Charity;
use App\Customer;
use App\Event;
use App\EventAvailability;
use App\Models\EventSchedule;
use App\Supplier;
use App\User;
use App\UserEvent\UserEvent;
use App\Vendor;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    //

    public function showUserEvent($userid, $event_slug, Request $request)
    {
        
        try {
            $userstring = base64_decode($userid);
            $userArray = explode(":", $userstring);

            if(count($userArray) < 2){
                throw new Exception("User not found");
            }
            $userid = $userArray[1];
            $user = User::where('id', $userid)->first();
            $event = Event::with('eventAvailabilities')->where('slug', $event_slug)->first();
            if($user == null) {
                throw new Exception("User not found");
            }
            if($event == null) {
                throw new Exception("Event not found");
            }
            $availableDays = [];
            if($event->eventAvailabilities) {
                $availableDays = $event->eventAvailabilities->pluck('numeric_day')->toArray();
            }
            return view('guest-event-schedule.index', compact('availableDays', 'event'));
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function getEventScheduleSlots(Request $request)
    {
        try {
            $availability = EventAvailability::where('numeric_day', $request->day)->where("event_id", $request->event_id)->first();
            $event = Event::find($request->event_id);
            
            
            $occupiedSlot = EventSchedule::where([
                "schedule_date" => $request->scheduleDate,
                "event_id" => $request->event_id
            ])->get()->pluck("start_at")->toArray();
                


            $slots = [];
            $c_startat = Carbon::parse($availability->start_at);
            $c_endat = Carbon::parse($availability->end_at);
            if($c_startat->lte($c_endat)) {
                while ($c_startat->lt($c_endat)) {
                    $slots[] = $c_startat->toTimeString();
                    $c_startat->addMinutes($event->duration_in_min ?? 30);
                }
            }

            return view('guest-event-schedule.event-slot', compact('availability', 'event', 'slots', 'occupiedSlot'));
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return "Something went wrong";
        }
    }

    public function createSchedule(Request $request)
    {
        $data = $request->all();
        try {
            $event = Event::with("user")->find($data['event']);
            if(isset($event) && $event->duration_in_min) {
                $c_end_at = Carbon::parse($data['schedule-slot'])->addMinutes($event->duration_in_min);
                $c_schedule_date = Carbon::parse($data['schedule-date']);
        
                $eventschedule = new EventSchedule();
                $eventschedule->event_id = $data['event'];
                $eventschedule->schedule_date = $c_schedule_date;
                $eventschedule->start_at = $data['schedule-slot'];
                $eventschedule->end_at = $c_end_at->toTimeString();
                $eventschedule->public_name = $data['guest-user-name'];
                $eventschedule->public_email = $data['guest-user-email'];
                $eventschedule->public_remark = $data['guest-user-reark'];
                $eventschedule->save();
                
                $userEvent = new UserEvent();
                $userEvent->user_id = $event->user->id;
                $userEvent->description = $data['schedule-slot']."-".$c_end_at->toTimeString().', '.$c_schedule_date->format('l').", ".$c_schedule_date->toDateString();
                $userEvent->subject = $event->slug." ($userEvent->description)";
                $userEvent->date = $c_schedule_date;
                
                $c_schedule_date->setTimeFromTimeString($data['schedule-slot']);
                $userEvent->start = $c_schedule_date->toDateTime();
                $c_schedule_date->setTimeFromTimeString($c_end_at->toTimeString());
                $userEvent->end = $c_schedule_date->toDateTime();
                
                $userEvent->save();
                
                return redirect()->back()->with("success_data" , [
                    "message" => "You are scheduled with ".$event->user->name,
                    "userEvent" => $userEvent,
                    "eventschedule" => $eventschedule
                ]);

            } else {
                throw new Exception("Event not found");
            }
            
        } catch (\Throwable $th) {
            return redirect()->back()->withErrors('Something went wrong');
        }
    }

    public function getEmailOftheSelectedObject(Request $request)
    {
        try {
            $objects = [
                "vendor"=> Vendor::class,
                "user"=> User::class,
                "supplier"=> Supplier::class,
                "customer"=> Customer::class,
                "charity"=> Charity::class,
            ];
            $multi_email = [];
            if(isset($request->object)){
                $multi_email = $objects[$request->object]::whereNotNull('email')->distinct()->select('email', 'id')->get()->map(function($email){
                    // dd($email);
                    return [
                        "id"=> $email->email,
                        "text"=> $email->email,
                    ];
                });
            }
            return response()->json(["status" => true, "data"=> $multi_email->toArray()]);
        } catch (\Throwable $th) {
            return response()->json(["status" => false]);
        }
    }
}