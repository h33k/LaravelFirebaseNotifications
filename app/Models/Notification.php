<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'device_id',
        'notification_job_id',
        'status',
    ];
}
