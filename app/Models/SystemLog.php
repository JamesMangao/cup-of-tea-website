<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $table = 'system_logs';

    protected $fillable = [
        'logged_at',
        'level',
        'channel',
        'message',
        'context',
        'user_id',
        'ip',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
        'context' => 'json',
    ];
}