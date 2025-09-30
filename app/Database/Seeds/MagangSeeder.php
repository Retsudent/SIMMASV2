<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MagangSeeder extends Seeder
{
    public function run()
    {
        // Get user IDs
        $db = db_connect();
        $admin = $db->table('users')->where('email', 'admin@simmas.test')->get()->getRowArray();
        $guru = $db->table('users')->where('email', 'guru@simmas.test')->get()->getRowArray();
        $siswa = $db->table('users')->where('email', 'siswa@simmas.test')->get()->getRowArray();
        
        // Get DUDI IDs
        $dudi1 = $db->table('dudi')->where('nama_perusahaan', 'PT Kreatif Teknologi')->get()->getRowArray();
        $dudi2 = $db->table('dudi')->where('nama_perusahaan', 'CV Digital Solusi')->get()->getRowArray();
        
        if (!$dudi1 || !$dudi2) {
            echo "DUDI data not found. Please run DudiSeeder first.\n";
            return;
        }
        
        // Insert guru data
        if ($guru) {
            $guruData = [
                'user_id' => $guru['id'],
                'nip' => '123456789012345678',
                'nama_lengkap' => 'Guru Pembimbing',
                'alamat' => 'Jl. Guru No. 1, Surabaya',
                'telepon' => '081234567890',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $existingGuru = $db->table('guru')->where('user_id', $guru['id'])->get()->getRowArray();
            if (!$existingGuru) {
                $db->table('guru')->insert($guruData);
            }
        }
        
        // Insert magang data
        $magangData = [
            [
                'siswa_id' => $siswa['id'],
                'guru_id' => $guru['id'],
                'dudi_id' => $dudi1['id'],
                'tanggal_mulai' => '2024-01-15',
                'tanggal_selesai' => '2024-04-15',
                'status' => 'berlangsung',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'siswa_id' => $siswa['id'],
                'guru_id' => $guru['id'],
                'dudi_id' => $dudi2['id'],
                'tanggal_mulai' => '2024-02-01',
                'tanggal_selesai' => '2024-05-01',
                'status' => 'berlangsung',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ]
        ];
        
        $db->table('magang')->insertBatch($magangData);
        
        // Insert logbook data
        $logbookData = [
            [
                'magang_id' => 1,
                'tanggal' => date('Y-m-d'),
                'kegiatan' => 'Mempelajari sistem database dan melakukan backup data harian',
                'kendala' => 'Kendala: Tidak ada kendala berarti',
                'status_verifikasi' => 'disetujui',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'magang_id' => 1,
                'tanggal' => date('Y-m-d', strtotime('-1 day')),
                'kegiatan' => 'Membuat design mockup untuk website perusahaan',
                'kendala' => 'Kendala: Software design masih belum familiar',
                'status_verifikasi' => 'pending',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ],
            [
                'magang_id' => 1,
                'tanggal' => date('Y-m-d', strtotime('-2 days')),
                'kegiatan' => 'Mengikuti training keamanan sistem informasi',
                'kendala' => 'Kendala: Materi cukup kompleks untuk dipahami',
                'status_verifikasi' => 'ditolak',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ],
            [
                'magang_id' => 2,
                'tanggal' => date('Y-m-d', strtotime('-3 days')),
                'kegiatan' => 'Membuat desain UI aplikasi kasir menggunakan Figma. Analisis UX dan wireframing',
                'kendala' => 'Kendala: Belum familiar dengan tools Figma',
                'status_verifikasi' => 'disetujui',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
            ]
        ];
        
        $db->table('logbook')->insertBatch($logbookData);
        
        echo "Magang and Logbook data seeded successfully!\n";
    }
}
