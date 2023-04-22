<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BingClientAccount extends Model
{
    protected $guarded = [];

    public function mails()
    {
        return $this->hasMany(BingClientAccountMail::class, 'bing_client_account_id', 'id');
    }
}
