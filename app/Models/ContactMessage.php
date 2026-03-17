<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'is_read',
        'admin_note',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];
}
