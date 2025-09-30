<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\LogbookModel;
use CodeIgniter\HTTP\ResponseInterface;

class LogbookController extends BaseController
{
    public function index()
    {
        $user = $this->request->user ?? null;
        if (!$user) return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
        
        $db = db_connect();
        
        // Get filter parameters
        $search = $this->request->getGet('q');
        $status = $this->request->getGet('status');
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = (int)($this->request->getGet('per_page') ?? 10);
        
        // Get active internship for this student
        $magang = $db->table('magang')
            ->where('siswa_id', (int)$user['id'])
            ->whereIn('status', ['aktif', 'berlangsung', 'selesai', 'pending'])
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRowArray();
            
        if (!$magang) {
            return $this->response->setJSON([
                'data' => [],
                'stats' => [
                    'total' => 0,
                    'disetujui' => 0,
                    'menunggu' => 0,
                    'ditolak' => 0
                ],
                'total_pages' => 1
            ]);
        }
        
        // Build query
        $builder = $db->table('logbook')
            ->where('magang_id', $magang['id'])
            ->where('deleted_at IS NULL');
        
        // Apply filters
        if ($search) {
            $builder->groupStart()
                ->like('kegiatan', $search)
                ->orLike('kendala', $search)
            ->groupEnd();
        }
        
        if ($status) {
            $builder->where('status_verifikasi', $status);
        }
        
        if ($bulan) {
            $builder->where('MONTH(tanggal)', $bulan);
        }
        
        if ($tahun) {
            $builder->where('YEAR(tanggal)', $tahun);
        }
        
        // Get total count for pagination
        $totalRecords = $builder->countAllResults(false);
        
        // Apply pagination
        $offset = ($page - 1) * $perPage;
        $builder->orderBy('tanggal', 'DESC')
                ->limit($perPage, $offset);
        
        $data = $builder->get()->getResultArray();
        
        // Calculate pagination info
        $totalPages = ceil($totalRecords / $perPage);
        
        // Get statistics
        $stats = $db->table('logbook')
            ->select('
                COUNT(*) as total,
                SUM(CASE WHEN status_verifikasi = \'disetujui\' THEN 1 ELSE 0 END) as disetujui,
                SUM(CASE WHEN status_verifikasi = \'pending\' THEN 1 ELSE 0 END) as menunggu,
                SUM(CASE WHEN status_verifikasi = \'ditolak\' THEN 1 ELSE 0 END) as ditolak
            ')
            ->where('magang_id', $magang['id'])
            ->where('deleted_at IS NULL')
            ->get()
            ->getRowArray();
        
        return $this->response->setJSON([
            'data' => $data,
            'stats' => [
                'total' => (int)($stats['total'] ?? 0),
                'disetujui' => (int)($stats['disetujui'] ?? 0),
                'menunggu' => (int)($stats['menunggu'] ?? 0),
                'ditolak' => (int)($stats['ditolak'] ?? 0)
            ],
            'total_pages' => $totalPages
        ]);
    }

    public function create()
    {
        $user = $this->request->user ?? null;
        if (!$user) return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
        
        // Get form data from JSON
        $jsonData = $this->request->getJSON(true);
        if (!$jsonData) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Data tidak valid']);
        }
        
        $tanggal = $jsonData['tanggal'] ?? null;
        $kegiatan = $jsonData['kegiatan'] ?? null;
        
        if (empty($tanggal) || empty($kegiatan)) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Tanggal dan kegiatan wajib diisi']);
        }
        
        // Validate minimum character length
        if (strlen(trim($kegiatan)) < 50) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Deskripsi kegiatan minimal 50 karakter']);
        }
        
        // Get active internship for this student
        $db = db_connect();
        $magang = $db->table('magang')
            ->where('siswa_id', (int)$user['id'])
            ->whereIn('status', ['aktif', 'berlangsung', 'selesai', 'pending'])
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRowArray();
            
        if (!$magang) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Anda tidak memiliki penempatan magang aktif']);
        }
        
        $data = [
            'magang_id' => $magang['id'],
            'tanggal' => $tanggal,
            'kegiatan' => trim($kegiatan),
            'status_verifikasi' => 'pending'
        ];
        
        // Handle file upload (optional) - check if file is provided in JSON
        $fileData = $jsonData['file'] ?? null;
        if ($fileData) {
            // Handle base64 file upload
            if (isset($fileData['data']) && isset($fileData['name']) && isset($fileData['type'])) {
                $fileContent = base64_decode($fileData['data']);
                $fileName = $fileData['name'];
                $fileType = $fileData['type'];
                
                // Validate file size (5MB max)
                if (strlen($fileContent) > 5 * 1024 * 1024) {
                    return $this->response->setStatusCode(422)->setJSON(['message' => 'Ukuran file maksimal 5MB']);
                }
                
                // Validate file type
                $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
                if (!in_array($fileType, $allowedTypes)) {
                    return $this->response->setStatusCode(422)->setJSON(['message' => 'Jenis file tidak didukung']);
                }
                
                // Generate new filename
                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                $newName = uniqid() . '_' . time() . '.' . $extension;
                
                // Save file
                if (file_put_contents(WRITEPATH . 'uploads/' . $newName, $fileContent)) {
                    $data['file'] = $newName;
                }
            }
        }
        
        $id = (new LogbookModel())->insert($data, true);
        return $this->response->setStatusCode(ResponseInterface::HTTP_CREATED)->setJSON(['id' => $id]);
    }

    public function update($id)
    {
        $user = $this->request->user ?? null;
        if (!$user) return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
        
        // Check if logbook exists and belongs to user
        $logbookModel = new LogbookModel();
        $logbook = $logbookModel->find($id);
        if (!$logbook) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Jurnal tidak ditemukan']);
        }
        
        // Check if user owns this logbook
        $db = db_connect();
        $magang = $db->table('magang')
            ->where('id', $logbook['magang_id'])
            ->where('siswa_id', (int)$user['id'])
            ->get()
            ->getRowArray();
            
        if (!$magang) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Anda tidak memiliki akses ke jurnal ini']);
        }
        
        // Only allow editing if status is pending or rejected
        if (!in_array($logbook['status_verifikasi'], ['pending', 'ditolak'])) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Jurnal yang sudah disetujui tidak dapat diedit']);
        }
        
        // Get form data from JSON
        $jsonData = $this->request->getJSON(true);
        if (!$jsonData) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Data tidak valid']);
        }
        
        $tanggal = $jsonData['tanggal'] ?? null;
        $kegiatan = $jsonData['kegiatan'] ?? null;
        
        // Debug logging
        log_message('debug', 'LogbookController::update - Received data:');
        log_message('debug', 'Method: ' . $this->request->getMethod());
        log_message('debug', 'Content-Type: ' . $this->request->getHeaderLine('Content-Type'));
        log_message('debug', 'Tanggal: ' . ($tanggal ?? 'null'));
        log_message('debug', 'Kegiatan: ' . ($kegiatan ?? 'null'));
        log_message('debug', 'All POST data: ' . json_encode($this->request->getPost()));
        log_message('debug', 'Raw input: ' . json_encode($this->request->getRawInput()));
        
        if (empty($tanggal) || empty($kegiatan)) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Tanggal dan kegiatan wajib diisi']);
        }
        
        // Validate minimum character length
        if (strlen(trim($kegiatan)) < 50) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Deskripsi kegiatan minimal 50 karakter']);
        }
        
        $data = [
            'tanggal' => $tanggal,
            'kegiatan' => trim($kegiatan)
        ];
        
        // Handle file upload (optional) - check if file is provided in JSON
        $fileData = $jsonData['file'] ?? null;
        if ($fileData) {
            // Handle base64 file upload
            if (isset($fileData['data']) && isset($fileData['name']) && isset($fileData['type'])) {
                $fileContent = base64_decode($fileData['data']);
                $fileName = $fileData['name'];
                $fileType = $fileData['type'];
                
                // Validate file size (5MB max)
                if (strlen($fileContent) > 5 * 1024 * 1024) {
                    return $this->response->setStatusCode(422)->setJSON(['message' => 'Ukuran file maksimal 5MB']);
                }
                
                // Validate file type
                $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
                if (!in_array($fileType, $allowedTypes)) {
                    return $this->response->setStatusCode(422)->setJSON(['message' => 'Jenis file tidak didukung']);
                }
                
                // Generate new filename
                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                $newName = uniqid() . '_' . time() . '.' . $extension;
                
                // Save file
                if (file_put_contents(WRITEPATH . 'uploads/' . $newName, $fileContent)) {
                    // Delete old file if exists
                    if ($logbook['file'] && file_exists(WRITEPATH . 'uploads/' . $logbook['file'])) {
                        unlink(WRITEPATH . 'uploads/' . $logbook['file']);
                    }
                    $data['file'] = $newName;
                }
            }
        }
        
        $logbookModel->update($id, $data);
        return $this->response->setJSON(['updated' => true]);
    }

    public function delete($id)
    {
        $user = $this->request->user ?? null;
        if (!$user) return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
        
        // Check if logbook exists and belongs to user
        $logbookModel = new LogbookModel();
        $logbook = $logbookModel->find($id);
        if (!$logbook) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Jurnal tidak ditemukan']);
        }
        
        // Check if user owns this logbook
        $db = db_connect();
        $magang = $db->table('magang')
            ->where('id', $logbook['magang_id'])
            ->where('siswa_id', (int)$user['id'])
            ->get()
            ->getRowArray();
            
        if (!$magang) {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Anda tidak memiliki akses ke jurnal ini']);
        }
        
        // Only allow deletion if status is pending or rejected
        if (!in_array($logbook['status_verifikasi'], ['pending', 'ditolak'])) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Jurnal yang sudah disetujui tidak dapat dihapus']);
        }
        
        // Use soft delete to maintain data integrity between student and teacher views
        $logbookModel->delete($id);
        return $this->response->setJSON(['deleted' => true, 'message' => 'Jurnal berhasil dihapus']);
    }
    
    public function show($id)
    {
        $user = $this->request->user ?? null;
        if (!$user) return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
        
        $db = db_connect();
        $logbook = $db->table('logbook l')
            ->select('l.*, u.name as siswa_nama, s.nis, s.kelas, s.jurusan, d.nama_perusahaan as dudi_nama, d.alamat as dudi_alamat, d.penanggung_jawab as dudi_pic')
            ->join('magang m', 'm.id = l.magang_id')
            ->join('users u', 'u.id = m.siswa_id')
            ->join('siswa s', 's.user_id = u.id', 'left')
            ->join('dudi d', 'd.id = m.dudi_id')
            ->where('l.id', $id)
            ->where('l.deleted_at IS NULL', null, false) // Exclude soft deleted logbooks
            ->where('m.siswa_id', (int)$user['id'])
            ->get()
            ->getRowArray();
            
        if (!$logbook) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Jurnal tidak ditemukan']);
        }
        
        return $this->response->setJSON($logbook);
    }
}


