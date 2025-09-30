<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;

class DudiController extends BaseController
{
    public function index()
    {
        $user = $this->request->user ?? null;
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
        }

        $q = $this->request->getGet('q');
        $db = db_connect();

        // Get guru ID for the logged in user
        $guru = $db->table('guru')->where('user_id', $user['id'])->get()->getRowArray();
        if (!$guru) {
            // Fallback: use user_id as guru_id for backward compatibility
            $guruId = (int)$user['id'];
        } else {
            $guruId = $guru['id'];
        }

        // Get latest placement per student for this guru + students who registered independently
        // Handle both guru_id from guru table and user_id as guru_id
        $guru = $db->table('guru')->where('user_id', $user['id'])->get()->getRowArray();
        $latestSql = $db->table('magang m')
            ->select('MAX(m.id) as id, m.siswa_id, m.dudi_id')
            ->where('m.deleted_at IS NULL', null, false)
            ->groupStart()
                // Siswa yang dibimbing oleh guru ini (cek baik guru_id dari tabel guru maupun user_id)
                ->where('m.guru_id', $guruId)
                ->orWhere('m.guru_id', (int)$user['id'])
                ->orGroupStart()
                    // Siswa yang daftar mandiri (status pending tanpa guru pembimbing)
                    ->where('m.status', 'pending')
                    ->groupStart()
                        ->where('m.guru_id', 0)
                        ->orWhere('m.guru_id IS NULL', null, false)
                    ->groupEnd()
                ->groupEnd()
            ->groupEnd()
            ->groupStart()->where('m.status','aktif')->orWhere('m.status','pending')->orWhere('m.status','selesai')->groupEnd()
            ->groupBy('m.siswa_id, m.dudi_id')
            ->getCompiledSelect();

        // Count students per DUDI based on latest placements
        $countsByDudi = [];
        $latestPlacements = $db->query("($latestSql)")->getResultArray();
        foreach ($latestPlacements as $placement) {
            $dudiId = (int)$placement['dudi_id'];
            $countsByDudi[$dudiId] = ($countsByDudi[$dudiId] ?? 0) + 1;
        }

        // Only show DUDI that have students supervised by this guru
        if (empty($countsByDudi)) {
            return $this->response->setJSON([]);
        }

        // Query DUDI with student counts - only those with supervised students
        $builder = $db->table('dudi d')
            ->select('d.id, d.nama_perusahaan, d.alamat, d.telepon, d.email, d.penanggung_jawab, d.status')
            ->where('d.status', 'aktif')
            ->whereIn('d.id', array_keys($countsByDudi));

        if ($q) {
            $builder->groupStart()
                ->like('d.nama_perusahaan', $q)
                ->orLike('d.alamat', $q)
                ->orLike('d.penanggung_jawab', $q)
                ->orLike('d.email', $q)
                ->orLike('d.telepon', $q)
            ->groupEnd();
        }

        $rows = $builder->get()->getResultArray();
        
        // Add student counts to each DUDI
        foreach ($rows as &$row) {
            $row['jumlah_siswa'] = $countsByDudi[(int)$row['id']];
        }

        return $this->response->setJSON($rows);
    }

    public function all()
    {
        $user = $this->request->user ?? null;
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
        }

        $q = $this->request->getGet('q');
        $db = db_connect();

        // Query ALL active DUDI for dropdown selection
        $builder = $db->table('dudi d')
            ->select('d.id, d.nama_perusahaan, d.alamat, d.telepon, d.email, d.penanggung_jawab, d.status')
            ->where('d.status', 'aktif');

        if ($q) {
            $builder->groupStart()
                ->like('d.nama_perusahaan', $q)
                ->orLike('d.alamat', $q)
                ->orLike('d.penanggung_jawab', $q)
                ->orLike('d.email', $q)
                ->orLike('d.telepon', $q)
            ->groupEnd();
        }

        $rows = $builder->orderBy('d.nama_perusahaan', 'ASC')->get()->getResultArray();
        
        return $this->response->setJSON($rows);
    }

    public function stats()
    {
        $user = $this->request->user ?? null;
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
        }

        $db = db_connect();

        // Get guru ID for the logged in user
        $guru = $db->table('guru')->where('user_id', $user['id'])->get()->getRowArray();
        if (!$guru) {
            // Fallback: use user_id as guru_id for backward compatibility
            $guruId = (int)$user['id'];
        } else {
            $guruId = $guru['id'];
        }
        
        $latestSql = $db->table('magang m')
            ->select('MAX(m.id) as id, m.siswa_id')
            ->where('m.deleted_at IS NULL', null, false)
            ->groupStart()
                // Siswa yang dibimbing oleh guru ini (cek baik guru_id dari tabel guru maupun user_id)
                ->where('m.guru_id', $guruId)
                ->orWhere('m.guru_id', (int)$user['id'])
                ->orGroupStart()
                    // Siswa yang daftar mandiri (status pending tanpa guru pembimbing)
                    ->where('m.status', 'pending')
                    ->groupStart()
                        ->where('m.guru_id', 0)
                        ->orWhere('m.guru_id IS NULL', null, false)
                    ->groupEnd()
                ->groupEnd()
            ->groupEnd()
            ->groupStart()->where('m.status','aktif')->orWhere('m.status','pending')->groupEnd()
            ->groupBy('m.siswa_id')
            ->getCompiledSelect();

        // Total siswa magang unik (menggunakan latest per siswa)
        $totalSiswaMagang = (int) $db->table("($latestSql) lm")
            ->select('COUNT(lm.siswa_id) as cnt')
            ->get()->getRow('cnt');

        // Total DUDI dari penempatan latest
        $totalDudi = (int) $db->table('magang m')
            ->join("($latestSql) lm", 'lm.id = m.id', 'inner')
            ->join('dudi d', 'd.id = m.dudi_id', 'inner')
            ->where('d.status', 'aktif')
            ->select('COUNT(DISTINCT d.id) as cnt')
            ->get()->getRow('cnt');

        // Rata-rata siswa per perusahaan (berdasarkan latest placement)
        $avg = $totalDudi > 0 ? (int) ceil($totalSiswaMagang / $totalDudi) : 0;

        return $this->response->setJSON([
            'total_dudi' => $totalDudi,
            'total_siswa_magang' => $totalSiswaMagang,
            'rata_rata_siswa' => $avg,
        ]);
    }
}


