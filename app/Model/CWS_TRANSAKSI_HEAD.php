<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CWS_TRANSAKSI_HEAD extends Model
{
    protected $table = 'CWS_transaksi_head';
   
    protected $primaryKey = 'id';
   
    protected $fillable = [
        'id',
        'transaksi_no',
        'transaksi_date',
        'booking_date',
        'id_mitra',
        'start_time',
        'end_time',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_deleted',
        'deleted_by',
    ];

    public function mitra(){
        return $this->belongsTo('\App\Model\CWS_MASTER_MITRA','id_mitra','id');
    }
}
