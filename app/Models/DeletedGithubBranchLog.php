<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedGithubBranchLog extends Model
{
    use HasFactory;

    protected $table = "deleted_github_branches_log";

    protected $fillable = [
        'repository_id',
        'branch_name',
        'deleted_by',
        'status',
        'error_message'
    ];
}
