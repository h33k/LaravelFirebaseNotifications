<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'name',
        'user_id',
        'device_uid',
        'fcm_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
