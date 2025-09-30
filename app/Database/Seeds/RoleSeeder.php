<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['name' => 'admin', 'description' => 'Administrator'],
            ['name' => 'teacher', 'description' => 'Guru Pembimbing'],
            ['name' => 'student', 'description' => 'Siswa'],
        ];

        $this->db->table('roles')->insertBatch($data);
    }
}
