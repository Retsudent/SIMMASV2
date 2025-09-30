<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DudiSeeder extends Seeder
{
    public function run()
    {
        $rows = [
            [
                'user_id' => null,
                'nama_perusahaan' => 'PT Kreatif Teknologi',
                'alamat' => 'Jl. Merdeka No. 123, Jakarta',
                'telepon' => '021-12345678',
                'email' => 'info@kreatiftek.com',
                'penanggung_jawab' => 'Andi Wijaya',
                'status' => 'aktif',
            ],
            [
                'user_id' => null,
                'nama_perusahaan' => 'CV Digital Solusi',
                'alamat' => 'Jl. Sudirman No. 45, Surabaya',
                'telepon' => '031-87654321',
                'email' => 'contact@digitalsolusi.com',
                'penanggung_jawab' => 'Sari Dewi',
                'status' => 'aktif',
            ],
        ];

        $this->db->table('dudi')->insertBatch($rows);
    }
}
