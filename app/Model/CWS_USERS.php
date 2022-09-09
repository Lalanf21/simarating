<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;

class CWS_USERS extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $table = 'CWS_users';
   
    protected $primaryKey = 'id';
   
    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $fillable = [
        'id',
        'nama',
        'email',
        'no_hp',
        'level',
        'status',
        'password',
        'is_email_verified',
        'token',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_deleted',
        'deleted_by',
    ];
}
