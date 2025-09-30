<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BackfillGuruSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();

        $missing = $db->query(
            "SELECT u.id, u.name FROM users u\n" .
            "LEFT JOIN guru g ON g.user_id = u.id\n" .
            "WHERE LOWER(u.role) = 'guru' AND g.user_id IS NULL"
        )->getResultArray();

        foreach ($missing as $row) {
            $db->table('guru')->insert([
                'user_id' => (int) $row['id'],
                'nama' => $row['name'] ?: 'Guru',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            echo 'inserted guru for user_id '.$row['id'].PHP_EOL;
        }

        echo 'Backfilled guru rows: ' . count($missing) . PHP_EOL;
    }
}



