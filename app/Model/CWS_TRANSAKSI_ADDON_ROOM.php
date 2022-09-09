<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CWS_TRANSAKSI_ADDON_ROOM extends Model
{
    protected $table = 'CWS_transaksi_ruang_addon';
   
    protected $primaryKey = 'id';
   
    protected $fillable = [
        'id',
        'id_transaksi_head',
        'id_ruang_addon',
        'start_time',
        'end_time',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_deleted',
        'deleted_by',
    ];
}
