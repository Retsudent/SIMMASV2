<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;

class StatsController extends BaseController
{
    public function overview()
    {
        $user = $this->request->user ?? null;
        if (!$user) return $this->response->setStatusCode(401)->setJSON(['message'=>'Unauthorized']);
        $db = db_connect();
        $guru = $db->table('guru')->where('user_id', (int)$user['id'])->get()->getRowArray();
        // Dukung data lama: beberapa seed/entri mungkin memakai users.id sebagai guru_id
        $guruId = $guru['id'] ?? null;
        $userId = (int) $user['id'];

        // Total siswa bimbingan (DISTINCT siswa) - exclude soft deleted
        $totalBimbingan = (int) $db->table('magang')
            ->select('COUNT(DISTINCT siswa_id) as cnt')
            ->groupStart()
                ->where('guru_id', (int) $guruId)
                ->orWhere('guru_id', $userId)
            ->groupEnd()
            ->where('deleted_at IS NULL', null, false)
            ->get()->getRow('cnt');

        // Siswa magang aktif (DISTINCT siswa status aktif/pending) - exclude soft deleted
        $aktif = (int) $db->table('magang')
            ->select('COUNT(DISTINCT siswa_id) as cnt')
            ->groupStart()
                ->where('guru_id', (int) $guruId)
                ->orWhere('guru_id', $userId)
            ->groupEnd()
            ->groupStart()
                ->where('status','aktif')
                ->orWhere('status','pending')
            ->groupEnd()
            ->where('deleted_at IS NULL', null, false)
            ->get()->getRow('cnt');
        $hariIni = date('Y-m-d');
        $logbookHariIni = (int) $db->table('logbook l')
            ->join('magang m','m.id=l.magang_id','left')
            ->groupStart()
                ->where('m.guru_id', (int) $guruId)
                ->orWhere('m.guru_id', $userId)
            ->groupEnd()
            ->where('m.deleted_at IS NULL', null, false)
            ->where('l.tanggal', $hariIni)
            ->countAllResults();

        // DUDI partner terhubung (distinct dudi dari siswa bimbingan) - exclude soft deleted
        // DUDI partner terhubung dari magang aktif (lebih representatif)
        $dudiPartner = (int) $db->table('magang')
            ->select('COUNT(DISTINCT dudi_id) as cnt')
            ->groupStart()
                ->where('guru_id', (int) $guruId)
                ->orWhere('guru_id', $userId)
            ->groupEnd()
            ->groupStart()
                ->where('status','aktif')
                ->orWhere('status','pending')
            ->groupEnd()
            ->where('deleted_at IS NULL', null, false)
            ->get()->getRow('cnt');

        // Ambil 1 penempatan TERBARU per siswa bimbingan dengan status AKTIF saja - exclude soft deleted
        $latestMagangRows = $db->table('magang')
            ->select('MAX(id) as id')
            ->groupStart()
                ->where('guru_id', (int) $guruId)
                ->orWhere('guru_id', $userId)
            ->groupEnd()
            ->groupStart()
                ->where('status','aktif')
            ->groupEnd()
            ->where('deleted_at IS NULL', null, false)
            ->groupBy('siswa_id')
            ->get()->getResultArray();

        $latestMagangIds = array_map(static function($r){ return (int) ($r['id'] ?? 0); }, $latestMagangRows);

        // Magang terbaru (berdasarkan 1 penempatan terakhir per siswa) - exclude soft deleted
        $magangTerbaruBuilder = $db->table('magang m')
            ->select('m.id, m.tanggal_mulai, m.tanggal_selesai, m.status, u.name as siswa_nama, d.nama_perusahaan as dudi_nama')
            ->join('users u','u.id=m.siswa_id','left')
            ->join('dudi d','d.id=m.dudi_id','left')
            ->where('m.deleted_at IS NULL', null, false);
        if (!empty($latestMagangIds)) {
            $magangTerbaruBuilder->whereIn('m.id', $latestMagangIds);
        } else {
            // fallback bila tidak ada penempatan aktif: gunakan filter guru saja
            $magangTerbaruBuilder->groupStart()
                ->where('m.guru_id', (int) $guruId)
                ->orWhere('m.guru_id', $userId)
            ->groupEnd();
        }
        $magangTerbaru = $magangTerbaruBuilder
            ->orderBy('m.tanggal_mulai','DESC')
            ->limit(5)
            ->get()->getResultArray();

        // Logbook terbaru dari siswa bimbingan
        $logbookTerbaru = $db->table('logbook l')
            ->select("l.id, l.tanggal, l.kegiatan, l.status_verifikasi, u.name as siswa_nama, d.nama_perusahaan as dudi_nama")
            ->join('magang m', 'm.id = l.magang_id', 'left')
            ->join('users u', 'u.id = m.siswa_id', 'left')
            ->join('dudi d', 'd.id = m.dudi_id', 'left')
            ->where('l.deleted_at IS NULL', null, false) // Exclude soft deleted logbooks
            ->where('m.guru_id', (int) $guruId) // Only show students supervised by this teacher
            ->orderBy('l.tanggal', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        // DUDI aktif list berdasarkan agregasi latest per siswa (hindari duplikasi join)
        if (!empty($latestMagangIds)) {
            $aggLatest = $db->table('magang')
                ->select('dudi_id, COUNT(*) as cnt')
                ->whereIn('id', $latestMagangIds)
                ->groupBy('dudi_id')
                ->getCompiledSelect();

            $dudiAktifList = $db->table('dudi d')
                ->select('d.id, d.nama_perusahaan, d.alamat, d.telepon, COALESCE(x.cnt,0) as jumlah_siswa')
                ->join("($aggLatest) x", 'x.dudi_id = d.id', 'left')
                ->where('d.status', 'aktif')
                ->orderBy('jumlah_siswa', 'DESC')
                ->limit(8)
                ->get()->getResultArray();
        } else {
            $dudiAktifList = $db->table('dudi d')
                ->select('d.id, d.nama_perusahaan, d.alamat, d.telepon, 0 as jumlah_siswa')
                ->where('d.status', 'aktif')
                ->orderBy('d.nama_perusahaan','ASC')
                ->limit(8)
                ->get()->getResultArray();
        }
        return $this->response->setJSON([
            'total_bimbingan' => $totalBimbingan,
            'dudi_partner' => (int)$dudiPartner,
            'magang_aktif' => $aktif,
            'logbook_hari_ini' => $logbookHariIni,
            'magang_terbaru' => $magangTerbaru,
            'logbook_terbaru' => $logbookTerbaru,
            'dudi_aktif_list' => $dudiAktifList,
        ]);
    }
}


