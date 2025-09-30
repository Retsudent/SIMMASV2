<?php

namespace App\Models;

use CodeIgniter\Model;

class DudiModel extends Model
{
    protected $table = 'dudi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'nama_perusahaan', 'alamat', 'telepon', 'email', 'penanggung_jawab', 'status',
        'created_at', 'updated_at',
    ];
    protected $useTimestamps = true;
}


