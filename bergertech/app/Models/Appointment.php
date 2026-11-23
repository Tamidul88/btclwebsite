<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'topic',
        'preferred_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'preferred_at' => 'datetime',
        ];
    }
}
