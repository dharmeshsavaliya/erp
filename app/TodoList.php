<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TodoList extends Model
{
    protected $table = 'todo_lists';
    protected $fillable = ['id', 'user_id', 'title', 'status', 'todo_date','remark', 'created_at', 'updated_at'];

    public function username(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
