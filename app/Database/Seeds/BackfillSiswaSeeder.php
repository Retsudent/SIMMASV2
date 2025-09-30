<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BackfillSiswaSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();

        $missing = $db->query(
            "SELECT u.id, u.name FROM users u\n" .
            "LEFT JOIN siswa s ON s.user_id = u.id\n" .
            "WHERE u.role = 'siswa' AND s.user_id IS NULL"
        )->getResultArray();

        foreach ($missing as $row) {
            $db->table('siswa')->insert([
                'user_id' => (int) $row['id'],
                'nama' => $row['name'] ?: 'Siswa',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        echo 'Backfilled siswa rows: ' . count($missing) . PHP_EOL;
    }
}




