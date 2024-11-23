<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyDiary extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "daily_diaries";

    protected $fillable = [
        'user_id',
        'texts',
        'created_date'
    ];
}
