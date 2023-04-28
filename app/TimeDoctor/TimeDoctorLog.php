<?php

namespace App\TimeDoctor;

use App\Task;
use App\User;
use App\DeveloperTask;
use Illuminate\Database\Eloquent\Model;

class TimeDoctorLog extends Model
{
    protected $fillable = [
        'time_doctor_account_id', 'url', 'payload', 'response', 'user_id', 'response_code', 'dev_task_id', 'task_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id', 'id');
    }

    public function devTask()
    {
        return $this->belongsTo(DeveloperTask::class, 'dev_task_id', 'id');
    }
}
