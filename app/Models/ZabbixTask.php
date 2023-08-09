<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ZabbixTask extends Model
{
    use HasFactory;

    public $fillable = [
        'task_name',
        'assign_to',
    ];

    public function zabbixWebhookDatas()
    {
        return $this->hasMany(ZabbixWebhookData::class, 'zabbix_task_id');
    }
}
