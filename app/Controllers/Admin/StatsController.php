<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class StatsController extends BaseController
{
    public function overview()
    {
        $db = db_connect();
        $totalSiswa = (int) $db->table('users')->where('role', 'siswa')->countAllResults();
        $totalDudi = (int) $db->table('dudi')->countAllResults();
        $dudiAktif = (int) $db->table('dudi')->where('status', 'aktif')->countAllResults();
        $dudiTidakAktif = (int) $db->table('dudi')->where('status', 'nonaktif')->countAllResults();
        // Siswa magang aktif: hitung siswa unik dengan status aktif/pending
        $magangAktif = (int) $db->table('magang')
            ->select('COUNT(DISTINCT siswa_id) as cnt')
            ->groupStart()
                ->where('status','aktif')
                ->orWhere('status','pending')
            ->groupEnd()
            ->get()->getRow('cnt');
        $hariIni = date('Y-m-d');
        $logbookHariIni = (int) $db->table('logbook')->where('tanggal', $hariIni)->countAllResults();
        
        // Ambil 1 penempatan aktif/pending terbaru per siswa (secara global)
        $latestMagangRows = $db->table('magang')
            ->select('MAX(id) as id')
            ->groupStart()
                ->where('status','aktif')
                ->orWhere('status','pending')
            ->groupEnd()
            ->groupBy('siswa_id')
            ->get()->getResultArray();
        $latestMagangIds = array_map(static function($r){ return (int) ($r['id'] ?? 0); }, $latestMagangRows);

        // Magang terbaru (berdasarkan 1 penempatan terakhir per siswa)
        $magangTerbaruBuilder = $db->table('magang m')
            ->select('m.id, m.tanggal_mulai, m.tanggal_selesai, m.status, u.name as siswa_name, d.nama_perusahaan')
            ->join('users u', 'u.id = m.siswa_id', 'left')
            ->join('dudi d', 'd.id = m.dudi_id', 'left');
        if (!empty($latestMagangIds)) {
            $magangTerbaruBuilder->whereIn('m.id', $latestMagangIds);
        }
        $magangTerbaru = $magangTerbaruBuilder
            ->orderBy('m.tanggal_mulai', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        // DUDI aktif list: tampilkan SEMUA dudi aktif, dengan jumlah siswa berdasarkan penempatan TERAKHIR per siswa
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
                ->groupBy('d.id, d.nama_perusahaan, d.alamat, d.telepon, x.cnt')
                ->orderBy('jumlah_siswa', 'DESC')
                ->limit(5)
                ->get()->getResultArray();
        } else {
            $dudiAktifList = $db->table('dudi d')
                ->select('d.id, d.nama_perusahaan, d.alamat, d.telepon, 0 as jumlah_siswa')
                ->where('d.status', 'aktif')
                ->orderBy('d.nama_perusahaan', 'ASC')
                ->limit(5)
                ->get()->getResultArray();
        }

        // Logbook terbaru (2 hari terakhir)
        $twoDaysAgo = date('Y-m-d', strtotime('-2 days'));
        $logbookTerbaru = $db->table('logbook l')
            ->select('l.kegiatan as deskripsi, l.tanggal, l.kendala, l.status_verifikasi as status, u.name as siswa_name')
            ->join('magang m', 'm.id = l.magang_id', 'left')
            ->join('users u', 'u.id = m.siswa_id', 'left')
            ->where('l.tanggal >=', $twoDaysAgo)
            ->orderBy('l.created_at', 'DESC')
            ->limit(3)
            ->get()->getResultArray();

        return $this->response->setJSON([
            'total_siswa' => $totalSiswa,
            'total_dudi' => $totalDudi,
            'dudi_aktif' => $dudiAktif,
            'dudi_tidak_aktif' => $dudiTidakAktif,
            'magang_aktif' => $magangAktif,
            'logbook_hari_ini' => $logbookHariIni,
            'magang_terbaru' => $magangTerbaru,
            'dudi_aktif_list' => $dudiAktifList,
            'logbook_terbaru' => $logbookTerbaru,
        ]);
    }
}


