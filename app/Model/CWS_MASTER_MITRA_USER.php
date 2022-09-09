<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CWS_MASTER_MITRA_USER extends Model
{
    protected $table = 'CWS_master_mitra_user';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'id_mitra',
        'nama',
        'email',
        'no_hp',
        'password',
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
