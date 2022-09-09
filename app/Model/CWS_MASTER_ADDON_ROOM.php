<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class CWS_MASTER_ADDON_ROOM extends Model
{
    protected $table = 'CWS_master_ruang_addon';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'nama',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_deleted',
        'deleted_by',
    ];
}
