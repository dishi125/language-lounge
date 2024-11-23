<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportantSetting extends Model
{
    use HasFactory;

    protected $table = "important_settings";

    protected $fillable = [
        'name',
        'value',
    ];
}
