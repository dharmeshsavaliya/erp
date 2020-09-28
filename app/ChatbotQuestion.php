<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatbotQuestion extends Model
{
    protected $fillable = [
        'value', 'workspace_id', 'created_at', 'updated_at', 'keyword_or_question', 'category_id',
        'sending_time','repeat','is_active','erp_or_watson','suggested_reply','auto_approve','chat_message_id'
    ];

    public function chatbotQuestionExamples()
    {
    	return $this->hasMany("App\ChatbotQuestionExample","chatbot_question_id","id");
    }

    public function chatbotKeywordValues()
    {
        return $this->hasMany("App\ChatbotKeywordValue", "chatbot_keyword_id", "id");
    }
}
