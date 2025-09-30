<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;

class MyController extends BaseController
{
    public function internship()
    {
        try {
            $user = $this->request->user ?? null;
            if (!$user) return $this->response->setStatusCode(401)->setJSON(['message'=>'Unauthorized']);
            
            $db = db_connect();
            
            // Get student data and their active internship
            $row = $db->table('magang m')
                ->select('m.*, 
                         u.name as siswa_nama, s.nis, s.kelas, s.jurusan,
                         d.nama_perusahaan, d.alamat as dudi_alamat, d.penanggung_jawab,
                         g.nama as guru_nama, g.nip as guru_nip')
                ->join('users u', 'u.id = m.siswa_id', 'left')
                ->join('siswa s', 's.user_id = u.id', 'left')
                ->join('dudi d', 'd.id = m.dudi_id', 'left')
                ->join('guru g', 'g.id = m.guru_id', 'left')
                ->where('m.siswa_id', (int)$user['id'])
                ->whereIn('m.status', ['aktif', 'berlangsung', 'selesai', 'pending'])
                ->where('m.deleted_at IS NULL', null, false)
                ->orderBy('m.created_at', 'DESC')
                ->get()
                ->getRowArray();
                
            // Debug logging
            log_message('debug', 'MyController::internship - User ID: ' . $user['id'] . ', Found row: ' . ($row ? 'Yes' : 'No'));
            if ($row) {
                log_message('debug', 'MyController::internship - Guru nama: ' . ($row['guru_nama'] ?? 'null') . ', Guru ID: ' . ($row['guru_id'] ?? 'null'));
            }
                
            if (!$row) {
                return $this->response->setJSON(null);
            }
            
            // Format the response with proper field names
            $result = [
                'id' => $row['id'],
                'siswa_nama' => $row['siswa_nama'],
                'nis' => $row['nis'],
                'kelas' => $row['kelas'],
                'jurusan' => $row['jurusan'],
                'nama_perusahaan' => $row['nama_perusahaan'],
                'dudi_alamat' => $row['dudi_alamat'],
                'penanggung_jawab' => $row['penanggung_jawab'],
                'tanggal_mulai' => $row['tanggal_mulai'],
                'tanggal_selesai' => $row['tanggal_selesai'],
                'status' => $row['status'],
                'nilai_akhir' => $row['nilai_akhir'],
                'guru_nama' => $row['guru_nama'],
                'guru_nip' => $row['guru_nip'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at']
            ];
            
            return $this->response->setJSON($result);
            
        } catch (\Exception $e) {
            log_message('error', 'MyController::internship error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ]);
        }
    }
}


