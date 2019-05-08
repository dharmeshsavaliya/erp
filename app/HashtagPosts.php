<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HashtagPosts extends Model
{
    public function comments() {
        return $this->hasMany(HashtagPostComment::class, 'hashtag_post_id', 'id');
    }

}
