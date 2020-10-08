<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstagramThread extends Model
{
    public function conversation() {
        return $this->hasMany(ChatMessage::class, 'unique_id', 'thread_id');
    }

    public function lead() {
        return $this->belongsTo(ColdLeads::class, 'cold_lead_id', 'id');
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'id', 'account_id')->whereNotNull('proxy');
    }

    public function instagramUser()
    {
        return $this->hasOne(InstagramUsersList::class, 'id', 'instagram_user_id');
    
    }

    public function erpUser()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    
    }

    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class, 'unique_id', 'thread_id')->orderBy('id','desc')->whereNotNull('message');
    }
}
