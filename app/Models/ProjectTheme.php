<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectTheme extends Model
{
    use HasFactory;

    public $fillable = [
        'project_id',
        'name',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
