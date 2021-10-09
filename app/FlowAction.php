<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlowAction extends Model
{
    protected $table = 'flow_actions';
    protected $fillable = [
		'path_id',
		'type_id',
		'rank',
		'after_seconds',
		'message_title',
		'deleted',
	];

}
