<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SiswaSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('siswa')->insert([
            'user_id' => 3, // user dengan role siswa dari UserSeeder
            'nama' => 'Ahmad Rizki',
            'nis' => '2024001',
            'kelas' => 'XII RPL 1',
            'jurusan' => 'Rekayasa Perangkat Lunak',
            'telepon' => '08123456789',
        ]);
    }
}
