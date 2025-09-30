<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $password = password_hash('password', PASSWORD_DEFAULT);
        $users = [
            [
                'name' => 'Admin Sistem',
                'email' => 'admin@simmas.test',
                'email_verified_at' => date('Y-m-d H:i:s'),
                'password' => $password,
                'role' => 'admin',
            ],
            [
                'name' => 'Guru Pembimbing',
                'email' => 'guru@simmas.test',
                'email_verified_at' => date('Y-m-d H:i:s'),
                'password' => $password,
                'role' => 'guru',
            ],
            [
                'name' => 'Ahmad Rizki',
                'email' => 'siswa@simmas.test',
                'email_verified_at' => date('Y-m-d H:i:s'),
                'password' => $password,
                'role' => 'siswa',
            ],
        ];

        $this->db->table('users')->insertBatch($users);
    }
}
