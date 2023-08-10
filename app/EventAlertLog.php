<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */
use Illuminate\Database\Eloquent\Model;

class EventAlertLog extends Model
{
    protected $fillable = [
        'eventalertloggable_id',
        'eventalertloggable_type',
        'user_id',
        'is_read',
        'event_alert_date',
        'event_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all of the owning eventalertloggable models.
     */
    public function eventalertloggable()
    {
        return $this->morphTo();
    }
}
