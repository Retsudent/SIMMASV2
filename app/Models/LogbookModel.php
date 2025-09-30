<?php

namespace App\Models;

use CodeIgniter\Model;

class LogbookModel extends Model
{
    protected $table = 'logbook';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'magang_id','tanggal','kegiatan','kendala','file','status_verifikasi','catatan_guru','catatan_dudi','created_at','updated_at','deleted_at'
    ];
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
}


