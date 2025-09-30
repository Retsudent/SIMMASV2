<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('school_settings')->insert([
            'logo_url' => null,
            'nama_sekolah' => 'SMK Negeri 1 Surabaya',
            'alamat' => 'Jl. SMEA No.4, Surabaya',
            'telepon' => '031-5678910',
            'email' => 'info@smkn1surabaya.sch.id',
            'website' => 'www.smkn1surabaya.sch.id',
            'kepala_sekolah' => 'Drs. H. Sutrisno, M.Pd.',
            'npsn' => '20567890',
        ]);
    }
}
