<?php

namespace App\Hubstaff;

use Illuminate\Database\Eloquent\Model;

class HubstaffActivity extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'task_id',
        'starts_at',
        'tracked',
        'keyboard',
        'mouse',
        'overall',
        'hubstaff_payment_account_id',
        'status',
        'paid',
        'is_manual',
        'user_notes'
    ];

    public static function getActivitiesForWeek($week, $year)
    {
        $result = getStartAndEndDate($week, $year);
        $start = $result['week_start'];
        $end = $result['week_end'];

        return self::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')
            ->where('starts_at', '>=', $start)
            ->where('starts_at', '<', $end)
            ->select(['hubstaff_activities.*', 'hubstaff_members.user_id as system_user_id'])
            ->get();
    }

    /**
     * get the activities between start (inclusive)
     */
    public static function getActivitiesBetween($start, $end)
    {
        return self::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')
            ->where('starts_at', '>=', $start)
            ->where('starts_at', '<', $end)
            ->select(['hubstaff_activities.*', 'hubstaff_members.user_id as system_user_id'])
            ->get();
    }

    public static function getFirstUnaccounted()
    {
        return self::whereNull('hubstaff_payment_account_id')->orderBy('starts_at')->first();
    }


    public static function getTrackedActivitiesBetween($start, $end,$user_id)
    {
        return self::leftJoin('hubstaff_members', 'hubstaff_members.hubstaff_user_id', '=', 'hubstaff_activities.user_id')->whereDate('hubstaff_activities.starts_at','>=',$start)->whereDate('hubstaff_activities.starts_at','<=',$end)->where('hubstaff_members.user_id',$user_id)->where('hubstaff_activities.status',1)->where('hubstaff_activities.paid',0)->select('hubstaff_activities.*')->get();
    }
}
