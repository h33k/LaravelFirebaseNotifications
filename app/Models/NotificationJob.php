<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationJob extends Model
{
    protected $table = 'notifications_jobs';
    protected $fillable = [
        'date_sent',
        'text',
        'status',
    ];
}
