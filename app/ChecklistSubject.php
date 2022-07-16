<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChecklistSubject extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $fillable = [
        'subject_id',
        'checklist_id',
        'is_checked',
        'user_id',
        'date'
    ];    

    public function subject(){
        return $this->belongsTo(Subject::class);
    }
}
