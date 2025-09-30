<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JournalSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('logbook')->insert([
            'magang_id' => 1,
            'tanggal' => '2024-03-01',
            'kegiatan' => 'Membuat desain UI aplikasi kasir menggunakan Figma. Analisis UX dan wireframing.',
            'status_verifikasi' => 'disetujui',
        ]);
    }
}
