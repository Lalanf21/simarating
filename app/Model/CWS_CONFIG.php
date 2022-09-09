<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CWS_CONFIG extends Model
{
    protected $table = 'CWS_master_config';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'capacity',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];
}
