<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScriptDocumentFiles extends Model
{
    use HasFactory;

    protected $fillable = [
        'script_document_id',
        'file_name',
        'extension',
        'remarks',
        'google_drive_file_id',
        'file_creation_date',
    ];
}
