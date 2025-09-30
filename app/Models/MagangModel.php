<?php

namespace App\Models;

use CodeIgniter\Model;

class MagangModel extends Model
{
    protected $table = 'magang';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'siswa_id','dudi_id','guru_id','status','nilai_akhir','tanggal_mulai','tanggal_selesai','created_at','updated_at'
    ];
    protected $useTimestamps = true;
}


