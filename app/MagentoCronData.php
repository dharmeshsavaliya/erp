<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoCronData extends Model
{
    protected $fillable = [
        'store_website_id',
        'cron_id',
        'job_code',
        'cron_message',
        'website',
        'cronstatus',
        'cron_created_at',
        'cron_scheduled_at',
        'cron_executed_at',
        'cron_finished_at',
    ];
}
