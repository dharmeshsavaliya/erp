<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;
use App\HashTag;

class InstagramPosts extends Model
{
    use Mediable;

    protected $fillable = ['location'];

    public function send_comment()
    {
        return $this->hasMany(CommentsStats::class, 'code', 'code');
    }

    public function comments()
    {
        return $this->hasMany(InstagramPostsComments::class, 'instagram_post_id', 'id');
    }

    public function hashTags()
    {
        return $this->belongsTo(HashTag::class, 'hashtag_id');
    }

    public function userDetail()
    {
        return $this->hasOne(InstagramUsersList::class,'user_id','user_id');
    }
}
