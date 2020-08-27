<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\ImQueue;

class Account extends Model
{
    use SoftDeletes;
  protected $fillable = [
    'first_name', 'last_name', 'email', 'password', 'dob', 'platform', 'followers_count', 'posts_count', 'dp_count', 'broadcast', 'country', 'gender','proxy'
  ];

  public function reviews()
  {
    return $this->hasMany('App\Review');
  }

  public function thread()
  {
    return $this->hasMany('App\InstagramThread','account_id','id')->whereNotNull('instagram_user_id');
  }

  public function has_posted_reviews()
  {
    $count = $this->hasMany('App\Review')->where('status', 'posted')->count();

    return $count > 0;
  }

  public function imQueueBroadcast()
  {
    return $this->hasMany(ImQueue::class,'number_from','last_name');
  }

  public function storeWebsite()
  {
      return $this->hasOne('\App\StoreWebsite','id','store_website_id');
  }
}
