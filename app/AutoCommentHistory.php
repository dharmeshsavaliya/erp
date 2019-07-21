<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutoCommentHistory extends Model
{
    public function account() {
        return $this->belongsTo(Account::class);
    }

    public function hashtag() {
        return $this->belongsTo(AutoReplyHashtags::class, 'auto_reply_hashtag_id', 'id');
    }

    public function user() {
        return $this->hasManyThrough(User::class, 'users_auto_comment_histories', 'auto_comment_history_id', 'user_id', 'id');
    }
}
