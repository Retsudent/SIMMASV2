<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GuruSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('guru')->insert([
            'user_id' => 2,
            'nip' => '19850303201003003',
            'nama' => 'Pak Suryanto',
            'telepon' => '081298765432',
        ]);
    }
}
