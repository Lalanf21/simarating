<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CWS_MASTER_MITRA extends Model
{
    protected $table = 'CWS_master_mitra';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'nama_perusahaan',
        'nama_brand',
        'corporation_code',
        'kuota',
        'pic',
        'logo',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'is_deleted',
        'deleted_by',
    ];

    public function user_mitra()
    {
        return $this->hasOne('\App\Model\CWS_MASTER_MITRA_USER','id_mitra','id');
    }

    public function pic()
    {
        return $this->belongsTo('\App\Model\CWS_MASTER_MITRA_USER','pic','id');
    }

    public function transaksi_head()
    {
        return $this->hasOne('\App\Model\CWS_TRANSAKSI_HEAD','id_mitra','id');
    }
}
