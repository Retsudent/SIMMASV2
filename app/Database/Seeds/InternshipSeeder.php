<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InternshipSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('magang')->insert([
            'siswa_id' => 3,
            'guru_id' => 2,
            'dudi_id' => 1,
            'status' => 'berlangsung',
            'nilai_akhir' => null,
            'tanggal_mulai' => '2024-02-01',
            'tanggal_selesai' => '2024-05-01',
        ]);
    }
}
