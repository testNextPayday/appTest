<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    protected $fillable = ['email', 'roles', 'invite_code', 'inviter', 'inviter_id', 'purpose'];
}
