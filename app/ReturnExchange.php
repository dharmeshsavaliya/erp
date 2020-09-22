<?php

namespace App;

use App\ReturnExchangeHistory;
use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\SoftDeletes;
class ReturnExchange extends Model
{

    use Mediable;
    use SoftDeletes;
    protected $fillable = [
        'customer_id',
        'type',
        'reason_for_refund',
        'refund_amount',
        'status',
        'pickup_address',
        'remarks',
    ];
	
    const STATUS = [
        1 => 'Return request received from customer',
        2 => 'Return request sent to courier',
        3 => 'Return pickup',
        4 => 'Return received in warehouse',
        5 => 'Return accepted',
        6 => 'Return rejected',
    ];
	
    public function notifyToUser()
    {
        if ($this->type == "refund") {
            // notify message we need to add here
        }
    }

    public function returnExchangeProducts()
    {
        return $this->hasMany(\App\ReturnExchangeProduct::class, "return_exchange_id", "id");
    }

    public function returnExchangeHistory()
    {
        return $this->hasMany(\App\ReturnExchangeHistory::class, "return_exchange_id", "id");
    }
	
	public function returnExchangeStatus()
    {
        return $this->hasOne(\App\ReturnExchangeStatus::class, "status", "id");
    }

    /**
     * Update return exchange history
     *
     */

    public function updateHistory()
    {
        ReturnExchangeHistory::create([
            "return_exchange_id" => $this->id,
            "status_id"          => $this->status,
            "user_id"            => \Auth::user()->id,
            "comment"            => $this->remarks,
        ]);

        return true;
    }

}
