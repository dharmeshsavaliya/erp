<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatbotMessageLog extends Model
{
    protected $fillable = [
        'model', 'model_id','chat_message_id','message','response','status'
    ];

    public function chatbotMessageLogResponses()
    {
        return $this->hasMany("App\ChatbotMessageLogResponse","chatbot_message_log_id","id");
    }

    public static function generateLog($params)
    {
        $chat_message_log = isset($params['chat_message_log_id']) ? Self::findOrNew($params['chat_message_log_id']) : new Self;
        $chat_message_log->model = $params['model'];
        $chat_message_log->model_id = $params['model_id'];
        $chat_message_log->chat_message_id = $params['chat_message_id'];
        $chat_message_log->message = $params['message'];
        $chat_message_log->status = $params['status'];
        $chat_message_log->save();
        return $chat_message_log->id;
    }
}
