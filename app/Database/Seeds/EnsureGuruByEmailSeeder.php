<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EnsureGuruByEmailSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();
        // Target emails (include common typo variant)
        $emails = [
            'guru@simmas.tes',
            'guru@simmas.test',
        ];

        $inserted = 0;
        foreach ($emails as $email) {
            $user = $db->table('users')->where('email', $email)->get()->getRowArray();
            if (!$user) {
                continue;
            }
            // If role is not guru, still allow but mark as guru entry referencing same user
            $exists = $db->table('guru')->where('user_id', (int) $user['id'])->countAllResults();
            if (!$exists) {
                $db->table('guru')->insert([
                    'user_id' => (int) $user['id'],
                    'nama' => $user['name'] ?: 'Guru',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $inserted++;
                echo 'Inserted guru for '.$email.' (user_id '.$user['id'].')'.PHP_EOL;
            }
        }

        echo 'EnsureGuruByEmail: inserted '.$inserted.' row(s).'.PHP_EOL;
    }
}




