<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SshLogin extends Model
{
    use HasFactory;

    protected $table = 'ssh_logins';
}
