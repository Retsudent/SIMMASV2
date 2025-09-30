<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\LogbookModel;
use CodeIgniter\HTTP\ResponseInterface;

class LogbookController extends BaseController
{
    public function index()
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
            log_message('debug', 'Guru not found in guru table, using user_id as guru_id: ' . $guruId);
        } else {
            $guruId = $guru['id'];
        }

        // Get filter parameters
        $status = $this->request->getGet('status');
        $month = $this->request->getGet('month');
        $year = $this->request->getGet('year');
        $dateStart = $this->request->getGet('date_start');
        $dateEnd = $this->request->getGet('date_end');
        $search = $this->request->getGet('search');
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = (int)($this->request->getGet('per_page') ?? 10);
        
        // Build query with joins to get complete data
        $builder = $db->table('logbook l')
            ->select('l.*, u.name as siswa_nama, d.nama_perusahaan as dudi_nama, m.tanggal_mulai, m.tanggal_selesai')
            ->join('magang m', 'm.id = l.magang_id', 'left')
            ->join('users u', 'u.id = m.siswa_id', 'left')
            ->join('dudi d', 'd.id = m.dudi_id', 'left')
            ->where('l.deleted_at IS NULL', null, false) // Exclude soft deleted logbooks
            ->where('m.guru_id', $guruId); // Only show students supervised by this teacher

        // Apply filters
        if ($status) {
            $builder->where('l.status_verifikasi', $status);
        }
        
        if ($month) {
            $builder->where('MONTH(l.tanggal)', $month);
        }
        
        if ($year) {
            $builder->where('YEAR(l.tanggal)', $year);
        }
        
        if ($dateStart) {
            $builder->where('l.tanggal >=', $dateStart);
        }
        
        if ($dateEnd) {
            $builder->where('l.tanggal <=', $dateEnd);
        }
        
        if ($search) {
            $builder->groupStart()
                ->like('u.name', $search)
                ->orLike('l.kegiatan', $search)
                ->orLike('l.kendala', $search)
                ->orLike('l.catatan_guru', $search)
            ->groupEnd();
        }

        // Get total count for pagination
        $totalRecords = $builder->countAllResults(false);
        
        // Apply pagination
        $offset = ($page - 1) * $perPage;
        $builder->orderBy('l.tanggal', 'DESC')
                ->limit($perPage, $offset);
        
        $data = $builder->get()->getResultArray();
        
        // Calculate pagination info
        $totalPages = ceil($totalRecords / $perPage);
        $showingStart = $totalRecords > 0 ? $offset + 1 : 0;
        $showingEnd = min($offset + $perPage, $totalRecords);
        
        return $this->response->setJSON([
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_records' => $totalRecords,
                'total_pages' => $totalPages,
                'showing_start' => $showingStart,
                'showing_end' => $showingEnd
            ]
        ]);
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
            log_message('debug', 'Guru not found in guru table, using user_id as guru_id: ' . $guruId);
        } else {
            $guruId = $guru['id'];
        }

        // Get logbook statistics
        $stats = $db->table('logbook l')
            ->select('
                COUNT(*) as total,
                SUM(CASE WHEN l.status_verifikasi = \'pending\' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN l.status_verifikasi = \'disetujui\' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN l.status_verifikasi = \'ditolak\' THEN 1 ELSE 0 END) as rejected
            ')
            ->join('magang m', 'm.id = l.magang_id', 'left')
            ->where('l.deleted_at IS NULL', null, false) // Exclude soft deleted logbooks
            ->where('m.guru_id', $guruId) // Only show students supervised by this teacher
            ->get()
            ->getRowArray();

        return $this->response->setJSON([
            'total' => (int)($stats['total'] ?? 0),
            'pending' => (int)($stats['pending'] ?? 0),
            'approved' => (int)($stats['approved'] ?? 0),
            'rejected' => (int)($stats['rejected'] ?? 0)
        ]);
    }

    public function verify($id)
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
            log_message('debug', 'Guru not found in guru table, using user_id as guru_id: ' . $guruId);
        } else {
            $guruId = $guru['id'];
        }

        // Verify that this logbook belongs to a student supervised by this teacher
        $logbook = $db->table('logbook l')
            ->join('magang m', 'm.id = l.magang_id', 'left')
            ->where('l.id', $id)
            ->where('l.deleted_at IS NULL', null, false) // Exclude soft deleted logbooks
            ->where('m.guru_id', $guruId) // Only allow verification for students supervised by this teacher
            ->get()
            ->getRowArray();

        if (!$logbook) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Jurnal tidak ditemukan atau tidak ada akses']);
        }

        // Try to get JSON data first, then fallback to raw input
        $data = $this->request->getJSON(true);
        if (empty($data)) {
            $rawInput = $this->request->getRawInput();
            if (!empty($rawInput)) {
                $data = json_decode($rawInput, true) ?: [];
            } else {
                $data = $this->request->getPost();
            }
        }
        
        $status = $data['status_verifikasi'] ?? null; // pending|disetujui|ditolak
        $catatan = $data['catatan_guru'] ?? null;
        
        // Debug logging
        log_message('debug', 'LogbookController::verify - Received data: ' . json_encode($data));
        log_message('debug', 'LogbookController::verify - Status: ' . ($status ?? 'null'));
        log_message('debug', 'LogbookController::verify - Catatan: ' . ($catatan ?? 'null'));
        
        // Prepare update data
        $updateData = [
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Only update status if provided and valid
        if ($status !== null) {
            if (!in_array($status, ['pending','disetujui','ditolak'], true)) {
                return $this->response->setStatusCode(422)->setJSON(['message' => 'Status tidak valid']);
            }
            $updateData['status_verifikasi'] = $status;
        }
        
        // Always allow updating catatan_guru (even if empty string)
        if ($catatan !== null) {
            $updateData['catatan_guru'] = $catatan;
        }
        
        // Check if there's anything to update
        if (count($updateData) <= 1) { // Only updated_at
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Tidak ada data yang akan diperbarui']);
        }

        try {
            $result = $db->table('logbook')->where('id', $id)->update($updateData);
            
            if ($result === false) {
                return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal memperbarui jurnal']);
            }
            
            return $this->response->setJSON([
                'verified' => true,
                'message' => 'Jurnal berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'LogbookController::verify - Database error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['message' => 'Terjadi kesalahan server']);
        }
    }
}


