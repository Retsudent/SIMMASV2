<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;

class InternshipController extends BaseController
{
    // Siswa mendaftar magang sendiri → status pending, tanpa guru_id
    public function apply()
    {
        $user = $this->request->user ?? null;
        if (!$user) return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);

        $payload = $this->request->getJSON(true) ?: $this->request->getPost();
        $dudiId = (int)($payload['dudi_id'] ?? 0);
        $tanggalMulai = $payload['tanggal_mulai'] ?? null;
        $tanggalSelesai = $payload['tanggal_selesai'] ?? null;
        if (!$dudiId) return $this->response->setStatusCode(422)->setJSON(['message' => 'DUDI wajib dipilih']);

        $db = db_connect();
        // Batasi maksimal 3 pendaftaran (pending/aktif) per siswa
        $activeCount = $db->table('magang')
            ->where('siswa_id', (int)$user['id'])
            ->whereIn('status', ['pending', 'aktif'])
            ->countAllResults();
        if ($activeCount >= 3) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Batas maksimal pendaftaran adalah 3 DUDI']);
        }
        // Cegah duplikasi pengajuan ke DUDI yang sama (selama masih pending/aktif)
        $dup = $db->table('magang')
            ->where('siswa_id', (int)$user['id'])
            ->where('dudi_id', $dudiId)
            ->whereIn('status', ['pending', 'aktif'])
            ->countAllResults();
        if ($dup) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Anda sudah mendaftar ke DUDI ini']);
        }

        $db->table('magang')->insert([
            'siswa_id' => (int)$user['id'],
            'guru_id' => 0, // belum ada pembimbing (tabel NOT NULL)
            'dudi_id' => $dudiId,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'message' => 'Pendaftaran magang berhasil diajukan, menunggu verifikasi dari pihak guru',
            'success' => true,
        ]);
    }

    // Ambil daftar pengajuan/penempatan siswa (pending/aktif)
    public function myList()
    {
        $user = $this->request->user ?? null;
        if (!$user) return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
        $db = db_connect();
        $rows = $db->table('magang m')
            ->select('m.id, m.dudi_id, m.status, m.created_at, d.nama_perusahaan')
            ->join('dudi d','d.id=m.dudi_id','left')
            ->where('m.siswa_id', (int)$user['id'])
            ->whereIn('m.status', ['pending','aktif'])
            ->where('m.deleted_at IS NULL', null, false)
            ->orderBy('m.created_at','DESC')
            ->get()->getResultArray();
        return $this->response->setJSON($rows);
    }
}


