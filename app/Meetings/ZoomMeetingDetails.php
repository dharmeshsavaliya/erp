<?php

namespace App\Meetings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ZoomMeetingDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'zoom_meeting_recordings';

    protected $fillable = ['file_name', 'local_file_path', 'download_url_id', 'description', 'meeting_id', 'file_type', 'download_url', 'file_path' ,'file_size', 'file_extension'];
}
