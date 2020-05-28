<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use App\UserEvent\UserEvent;
use App\UserEvent\UserEventAttendee;
use App\UserEvent\UserEventParticipant;
use Auth;
use Illuminate\Http\Request;

class UserEventController extends Controller
{

    function index()
    {
        $userId = Auth::user()->id;
        $link = base64_encode('soloerp:' . $userId);
        return view(
            'user-event.index',
            [
                'link' => $link
            ]
        );
    }



    /**
     * list of user events as json
     */
    function list(Request $request)
    {

        $userId = Auth::user()->id;

        if (!$userId) {
            return response()->json(
                [
                    'message' => 'Not allowed'
                ],
                401
            );
        }

        $start = explode('T', $request->get('start'))[0];
        $end = explode('T', $request->get('end'))[0];

        $events = UserEvent::with(['attendees'])
            ->where('start', '>=', $start)
            ->where('end', '<', $end)
            ->where('user_id', $userId)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'subject' => $event->subject,
                    'title' => $event->subject,
                    'description' => $event->description,
                    'date' => $event->date,
                    'start' => $event->start,
                    'end' => $event->end,
                    'attendees' => $event->attendees
                ];
            });


        return response()->json($events);
    }

    /**
     * edit event
     */
    function editEvent(Request $request, int $id)
    {
        $userId = Auth::user()->id;

        if (!$userId) {
            return response()->json(
                [
                    'message' => 'Not allowed'
                ],
                401
            );
        }

        $start = $request->get('start');
        $end = $request->get('end');

        $userEvent = UserEvent::find($id);

        if (!$userEvent) {
            return response()->json(
                [
                    'message' => 'Event not found'
                ],
                404
            );
        }

        if ($userEvent->user_id != $userId) {
            return response()->json(
                [
                    'message' => 'Not allowed to edit event'
                ],
                401
            );
        }

        $userEvent->start = $start;
        $userEvent->end = $end;
        $userEvent->save();

        // once user event has been stored create the event in daily planner
        $dailyActivities = new \App\DailyActivity; 
        if($userEvent->daily_activity_id > 0) {
            $dailyActivities  = \App\DailyActivity::find($userEvent->daily_activity_id);
            if(empty($dailyActivities)) {
                $dailyActivities = new \App\DailyActivity;
            }
        }

        $dailyActivities->time_slot = date("h:00 a",strtotime($userEvent->start)) . " - " .date("h:00 a",strtotime($userEvent->end));
        $dailyActivities->activity  = $userEvent->subject;
        $dailyActivities->user_id   = $userId;
        $dailyActivities->for_date  = $date;
        
        if($dailyActivities->save()) {
           $userEvent->daily_activity_id =  $dailyActivities->id;
           $userEvent->save();
        }

        // check first and vendors
        $vendors = $request->get("vendors",[]);
        UserEventParticipant::where("user_event_id",$userEvent->id)->delete();
        if(!empty($vendors) && is_array($vendors)) {
            foreach($vendors as $vendor) {
                $userEventParticipant = new UserEventParticipant;
                $userEventParticipant->user_event_id = $userEvent->id;
                $userEventParticipant->object = \App\Vendor::class;
                $userEventParticipant->object_id = $vendor;
                $userEventParticipant->save();
            }
        }

        return response()->json([
            'message' => 'Event updated',
            'event' => [
                'id' =>  $userEvent->id,
                'title' =>  $userEvent->title,
                'start' =>  $userEvent->start,
                'end' => $userEvent->end
            ]
        ]);
    }

    /**
     * Create a new event
     */
    function createEvent(Request $request)
    {
        $userId = Auth::user()->id;

        if (!$userId) {
            return response()->json(
                [
                    'message' => 'Not allowed'
                ],
                401
            );
        }



        $date = $request->get('date');
        $time = $request->get('time');
        $subject = $request->get('subject');
        $description = $request->get('description');
        $contactsString = $request->get('contacts');

        $errors = array();

        // date validations
        if (!$date) {
            $errors['date'][] = 'Date is missing';
        } else if (!preg_match('/^[0-9]{4}-((0[1-9])|(1[0|1|2]))-(0|1|2|3)[0-9]$/', $date)) {
            $errors['date'][] = 'Invalid date format';
        } else if (!validateDate($date)) {
            $errors['date'][] = 'Invalid date';
        }

        if (isset($time)) {
            if (!preg_match('/^(([0-1][0-9])|(2[0-3])):[0-5][0-9]$/', $time)) {
                $errors['time'] = 'Invalid time format';
            }
        }

        if (empty(trim($subject))) {
            $errors['subject'][] = 'Subject is required';
        }

        if (!empty($errors)) {
            return response()->json($errors, 400);
        }


        $start = $date . ' ' . $time;
        $end = strtotime($start . ' + 1 hour');
        $start = strtotime($start);


        $userEvent = new UserEvent;
        $userEvent->user_id = $userId;
        $userEvent->subject = $subject;
        $userEvent->description = $description;
        $userEvent->date = $date;

        if (isset($time)) {
            $start = strtotime($date . ' ' . $time);
            $end = strtotime($date . ' ' . $time . ' + 1 hour');
            $userEvent->start = date('Y-m-d H:i:s', $start);
            $userEvent->end = date('Y-m-d H:i:s', $end);
        }



        $userEvent->save();

        // once user event has been stored create the event in daily planner
        $dailyActivities = new \App\DailyActivity;
        $dailyActivities->time_slot = date("h:00 a",strtotime($userEvent->start)) . " - " .date("h:00 a",strtotime($userEvent->end));
        $dailyActivities->activity  = $userEvent->subject;
        $dailyActivities->user_id   = $userId;
        $dailyActivities->for_date  = $date;
        
        if($dailyActivities->save()) {
           $userEvent->daily_activity_id =  $dailyActivities->id;
           $userEvent->save();
        }

        // save the attendees
        $attendees = explode(',', $contactsString);

        $attendeesResponse = [];

        foreach ($attendees as $attendee) {
            $attendeeDb = new UserEventAttendee;
            $attendeeDb->user_event_id = $userEvent->id;
            $attendeeDb->contact = $attendee;
            $attendeeDb->save();

            $attendeesResponse[] = $attendeeDb->toArray();
        }

        $vendors = $request->get("vendors",[]);
        if(!empty($vendors) && is_array($vendors)) {
            foreach($vendors as $vendor) {
                $userEventParticipant = new UserEventParticipant;
                $userEventParticipant->user_event_id = $userEvent->id;
                $userEventParticipant->object = \App\Vendor::class;
                $userEventParticipant->object_id = $vendor;
                $userEventParticipant->save();
            }
        }

        return response()->json([
            "code"    => 200, 
            'message' => 'Event added successfully',
            'event' => $userEvent->toArray(),
            'attendees' => $attendeesResponse
        ]);
    }

    function removeEvent(Request $request, $id)
    {
        $userId = Auth::user()->id;

        if (!$userId) {
            return response()->json(
                [
                    'message' => 'Not allowed'
                ],
                401
            );
        }

        $result = UserEvent::where('id', $id)->where('user_id', $userId)->first();
        if($result) {
            $result->attendees()->delete();
            $result->delete();
            return response()->json([
                'message' => 'Event deleted:' . $result
            ]);
        }

        return response()->json([
            'message' => 'Failed to deleted',
            404
        ]);
    }

    /*
             ____    _   _   ____    _       ___    ____ 
            |  _ \  | | | | | __ )  | |     |_ _|  / ___|
            | |_) | | | | | |  _ \  | |      | |  | |    
            |  __/  | |_| | | |_) | | |___   | |  | |___ 
            |_|      \___/  |____/  |_____| |___|  \____|
                                                            
    */

    /**
     * show public calendar
     */
    function publicCalendar($id)
    {
        $calendarId = base64_decode($id);
        $calendarUserId = explode(':', $calendarId)[1];

        $user = User::find($calendarUserId, ['name']);

        return view(
            'user-event.public-calendar',
            [
                'calendarId' => $id,
                'user' => $user
            ]
        );
    }

    /**
     * events of the user without auth
     */
    function publicEvents(Request $request, $id)
    {
        $text = base64_decode($id);
        $calendarUserId = explode(':', $text)[1];

        $start = explode('T', $request->get('start'))[0];
        $end = explode('T', $request->get('end'))[0];

        $events = UserEvent::with(['attendees'])
            ->where('start', '>=', $start)
            ->where('end', '<', $end)
            ->where('user_id', $calendarUserId)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'subject' => $event->subject,
                    'title' => $event->subject,
                    'description' => $event->description,
                    'date' => $event->date,
                    'start' => $event->start,
                    'end' => $event->end,
                    'attendees' => $event->attendees
                ];
            });
        return response()->json($events);
    }

    /**
     * suggest timing for the invitation view
     */
    function suggestInvitationTiming($invitationId)
    {

        $attendee = UserEventAttendee::with('event')->find($invitationId);

        return view(
            'user-event.public-calendar-time-suggestion',
            [
                'attendee' => $attendee,
                'invitationId' => $invitationId
            ]
        );
    }

    /**
     * save suggested timing
     */
    function saveSuggestedInvitationTiming(Request $request, $invitationId)
    {
        UserEventAttendee::where('id', '=', $invitationId)
        ->update([
            'suggested_time' => $request->get('time')
        ]);

        return redirect('/calendar/public/event/suggest-time/'.$invitationId)->with([
            'message' => 'Saved data'
        ]);
    }
}
