<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;

class DudiController extends BaseController
{
    // List semua DUDI aktif untuk siswa (persis dari tabel dudi)
    public function all()
    {
        $user = $this->request->user ?? null;
        if (!$user) return $this->response->setStatusCode(401)->setJSON(['message'=>'Unauthorized']);

        $q = trim((string)$this->request->getGet('q'));
        $db = db_connect();
        // agregasi jumlah siswa (aktif+pending) per DUDI - exclude soft deleted
        $agg = $db->table('magang')
            ->select('dudi_id, COUNT(*) as cnt')
            ->groupStart()->where('status','aktif')->orWhere('status','pending')->groupEnd()
            ->where('deleted_at IS NULL', null, false)
            ->groupBy('dudi_id')
            ->getCompiledSelect();

        $builder = $db->table('dudi d')
            ->select('d.id, d.nama_perusahaan, d.alamat, d.telepon, d.email, d.penanggung_jawab, d.status, COALESCE(x.cnt,0) as jumlah_siswa')
            ->join("($agg) x", 'x.dudi_id = d.id', 'left')
            ->where('d.status','aktif');
        if ($q !== '') {
            $builder->groupStart()
                ->like('d.nama_perusahaan', $q)
                ->orLike('d.alamat', $q)
                ->orLike('d.penanggung_jawab', $q)
                ->orLike('d.email', $q)
                ->orLike('d.telepon', $q)
            ->groupEnd();
        }
        $rows = $builder->orderBy('d.nama_perusahaan','ASC')->get()->getResultArray();
        return $this->response->setJSON($rows);
    }
}


