<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DudiModel;
use CodeIgniter\HTTP\ResponseInterface;

class DudiController extends BaseController
{
    public function index()
    {
        try {
            $q = $this->request->getGet('q');
            $model = new DudiModel();
            $builder = $model->builder();
            
            if ($q) {
                $builder->groupStart()
                    ->like('nama_perusahaan', $q)
                    ->orLike('alamat', $q)
                    ->orLike('penanggung_jawab', $q)
                    ->orLike('email', $q)
                    ->orLike('telepon', $q)
                    ->groupEnd();
            }
            
            $data = $builder->get()->getResultArray();

            // Hitung jumlah siswa per DUDI berdasarkan 1 penempatan TERAKHIR per siswa - exclude soft deleted
            $db = db_connect();
            $latestRows = $db->table('magang')
                ->select('MAX(id) as id')
                ->groupStart()->where('status','aktif')->orWhere('status','pending')->groupEnd()
                ->where('deleted_at IS NULL', null, false)
                ->groupBy('siswa_id')
                ->get()->getResultArray();
            $latestIds = array_map(static function($r){ return (int) ($r['id'] ?? 0); }, $latestRows);

            $countsByDudi = [];
            if (!empty($latestIds)) {
                $rows = $db->table('magang')
                    ->select('dudi_id, COUNT(*) as cnt')
                    ->whereIn('id', $latestIds)
                    ->groupBy('dudi_id')
                    ->get()->getResultArray();
                foreach ($rows as $r) { $countsByDudi[(int)$r['dudi_id']] = (int)$r['cnt']; }
            }

            foreach ($data as &$dudi) {
                $dudi['jumlah_siswa'] = $countsByDudi[(int)$dudi['id']] ?? 0;
            }
            
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'DUDI index error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal memuat data DUDI: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $model = new DudiModel();
        $row = $model->find($id);
        if (!$row) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['message' => 'Not found']);
        }
        return $this->response->setJSON($row);
    }

    public function create()
    {
        $data = $this->request->getJSON(true) ?: $this->request->getPost();
        
        // Log the received data for debugging
        log_message('debug', 'DUDI Create - Received data: ' . json_encode($data));
        
        // Basic validation
        if (empty($data['nama_perusahaan'])) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Nama perusahaan harus diisi']);
        }
        if (empty($data['alamat'])) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Alamat harus diisi']);
        }
        if (empty($data['status'])) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Status harus dipilih']);
        }
        
        // Check if company name already exists
        $existingDudi = (new DudiModel())->where('nama_perusahaan', $data['nama_perusahaan'])->first();
        if ($existingDudi) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Nama perusahaan sudah terdaftar']);
        }
        
        $model = new DudiModel();
        $id = $model->insert($data);
        
        if (!$id) {
            log_message('error', 'DUDI Create - Failed to insert: ' . json_encode($model->errors()));
            return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal menyimpan data DUDI: ' . implode(', ', $model->errors())]);
        }
        
        log_message('debug', 'DUDI Create - Success: ID ' . $id);
        return $this->response->setStatusCode(ResponseInterface::HTTP_CREATED)->setJSON(['id' => $id, 'message' => 'Data DUDI berhasil ditambahkan', 'success' => true]);
    }

    public function update($id)
    {
        $data = $this->request->getJSON(true) ?: $this->request->getPost();
        
        log_message('debug', 'DUDI Update - Received data: ' . json_encode($data));
        
        // Basic validation
        if (empty($data['nama_perusahaan'])) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Nama perusahaan harus diisi']);
        }
        if (empty($data['alamat'])) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Alamat harus diisi']);
        }
        if (empty($data['status'])) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Status harus dipilih']);
        }
        
        // Check if company name already exists (excluding current record)
        $existingDudi = (new DudiModel())->where('nama_perusahaan', $data['nama_perusahaan'])->where('id !=', $id)->first();
        if ($existingDudi) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Nama perusahaan sudah terdaftar']);
        }
        
        $model = new DudiModel();
        if (!$model->find($id)) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['message' => 'DUDI tidak ditemukan']);
        }
        
        $updated = $model->update($id, $data);
        if (!$updated) {
            log_message('error', 'DUDI Update - Failed to update: ' . json_encode($model->errors()));
            return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal memperbarui data DUDI: ' . implode(', ', $model->errors())]);
        }
        
        log_message('debug', 'DUDI Update - Success: ID ' . $id);
        return $this->response->setStatusCode(200)->setJSON(['message' => 'Data DUDI berhasil diperbarui', 'success' => true]);
    }

    public function delete($id)
    {
        try {
            $model = new DudiModel();
            
            // Check if DUDI exists
            if (!$model->find($id)) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)->setJSON(['message' => 'DUDI tidak ditemukan']);
            }
            
            // Check if DUDI has active students
            $db = db_connect();
            $activeStudents = $db->table('magang')
                ->where('dudi_id', $id)
                ->where('status', 'aktif')
                ->countAllResults();
            
            if ($activeStudents > 0) {
                return $this->response->setStatusCode(422)->setJSON(['message' => 'Tidak dapat menghapus DUDI yang masih memiliki siswa magang aktif']);
            }
            
            // Permanently delete the DUDI
            $deleted = $model->delete($id);
            
            if (!$deleted) {
                return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal menghapus data DUDI']);
            }
            
            log_message('debug', 'DUDI Delete - Success: ID ' . $id);
            return $this->response->setJSON(['message' => 'Data DUDI berhasil dihapus secara permanen', 'success' => true]);
            
        } catch (\Exception $e) {
            log_message('error', 'DUDI Delete error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal menghapus data DUDI: ' . $e->getMessage()]);
        }
    }

    public function students($id)
    {
        $db = db_connect();
        $students = $db->table('magang m')
            ->select('u.name, u.email, m.status, m.tanggal_mulai, m.tanggal_selesai')
            ->join('users u', 'u.id = m.siswa_id', 'left')
            ->where('m.dudi_id', $id)
            ->where('m.status', 'aktif')
            ->get()
            ->getResultArray();
        
        return $this->response->setJSON($students);
    }
}


