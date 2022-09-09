<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CWS_TRANSAKSI_MITRA_USER extends Model
{
    protected $table = 'CWS_transaksi_mitra_user';
   
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'id_transaksi_head',
        'id_mitra_user',
        'qr_code_string',
        'booking_date',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_deleted',
        'deleted_by',
    ];
}
