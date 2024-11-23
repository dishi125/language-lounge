<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TempUser extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'temp_users';
    protected $fillable = [
        'social_id',
        'social_type',
        'email',
        'username',
        'auth_code',
        'apple_refresh_token',
        'apple_access_token'
    ];

}
