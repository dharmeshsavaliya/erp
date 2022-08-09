<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Models\UicheckHistory;

class Uicheck extends Model {
    protected $table = 'uichecks';

    protected $fillable = [
        'id',
        'site_development_category_id',
        'site_development_id',
        'website_id',
        'issue',
        'communication_message',
        'dev_status_id',
        'admin_status_id',
        'start_time',
        'expected_completion_time',
        'actual_completion_time',
        'lock_developer',
        'lock_admin',
    ];

    public function whatsappAll($needBroadCast = false) {
        if ($needBroadCast) {
            return $this->hasMany('App\ChatMessage', 'document_id')->whereIn('status', ['7', '8', '9', '10'])->latest();
        }

        return $this->hasMany('App\ChatMessage', 'document_id')->whereNotIn('status', ['7', '8', '9', '10'])->latest();
    }


    public function updateElement($type, $newValue) {
        $oldValue = $this->$type;
        if ($oldValue != $newValue) {
            $this->$type = $newValue;
            $this->save();
            UicheckHistory::create([
                'uichecks_id' => $this->id,
                'type' => $type,
                'old_val' => $oldValue,
                'new_val' => $newValue,
                'user_id' => \Auth::id(),
            ]);
        }
    }
}