<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class InternshipController extends BaseController
{
    private function getGuruIds(array $user): array
    {
        $db = db_connect();
        $guru = $db->table('guru')->where('user_id', (int) $user['id'])->get()->getRowArray();
        // Return both guru_id from guru table and user_id for backward compatibility
        return [
            isset($guru['id']) ? (int)$guru['id'] : null, 
            (int)$user['id']
        ];
    }

    public function stats()
    {
        $user = $this->request->user ?? null;
        if (!$user) return $this->response->setStatusCode(401)->setJSON(['message'=>'Unauthorized']);
        
        try{
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
            
            // Hitung statistik berdasarkan filter yang sama dengan index()
            // Siswa yang dibimbing + siswa yang daftar mandiri
            $builder = $db->table('magang m')
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
                ->groupEnd();
            
            $total = (int) $builder->countAllResults();
            
            // Hitung per status dengan filter yang sama
            $aktifBuilder = clone $builder;
            $aktif = (int) $aktifBuilder->where('m.status','aktif')->countAllResults();
            
            $selesaiBuilder = clone $builder;
            $selesai = (int) $selesaiBuilder->where('m.status','selesai')->countAllResults();
            
            $pendingBuilder = clone $builder;
            $pending = (int) $pendingBuilder->where('m.status','pending')->countAllResults();
            
            return $this->response->setJSON([
                'total' => $total,
                'aktif' => $aktif,
                'selesai' => $selesai,
                'pending' => $pending,
            ]);
        }catch(\Throwable $e){
            log_message('error','Internship stats error: '.$e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['message'=>'Stats error']);
        }
    }

    public function index()
    {
        $user = $this->request->user ?? null;
        if (!$user) return $this->response->setStatusCode(401)->setJSON(['message'=>'Unauthorized']);
        $q = trim((string) $this->request->getGet('q'));
        $status = $this->request->getGet('status');

        [$guruIdFromHelper, $userId] = $this->getGuruIds($user);
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
        if (!$guruId && $guruIdFromHelper) { $guruId = $guruIdFromHelper; }
        
        // Query dengan join untuk mendapatkan data lengkap
        // Tampilkan: data bimbingan guru saat ini + pengajuan mandiri siswa (pending tanpa guru)
        // Exclude soft deleted records
        try {
            $builder = $db->table('magang m')
                ->select('m.id, m.siswa_id, m.guru_id, m.dudi_id, m.status, m.tanggal_mulai, m.tanggal_selesai, m.created_at, m.nilai_akhir as nilai, 
                         u.name as siswa_nama, s.nis, s.kelas, s.jurusan,
                         g.nama as guru_nama, g.nip,
                         d.nama_perusahaan, d.alamat, d.penanggung_jawab')
                ->join('users u', 'u.id = m.siswa_id', 'left')
                ->join('siswa s', 's.user_id = m.siswa_id', 'left')
                ->join('guru g', 'g.id = m.guru_id', 'left')
                ->join('dudi d', 'd.id = m.dudi_id', 'left')
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
                ->orderBy('m.tanggal_mulai','DESC');
            
            if ($status) $builder->where('m.status', $status);
            
            $builder->orderBy('m.tanggal_mulai', 'DESC');
            $rows = $builder->get()->getResultArray();
            
        } catch (\Exception $e) {
            log_message('error', 'Database query failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['message' => 'Database error: ' . $e->getMessage()]);
        }
        
        // Debug logging
        log_message('debug', 'Guru Internship Query - User ID: ' . $user['id'] . ', Guru ID: ' . $guruId . ', Rows found: ' . count($rows));
        
        return $this->response->setJSON($rows);
    }

    public function students()
    {
        $user = $this->request->user ?? null;
        if (!$user) return $this->response->setStatusCode(401)->setJSON(['message'=>'Unauthorized']);
        $db = db_connect();
        // Tampilkan hanya siswa yang belum memiliki penempatan/guru pembimbing sama sekali
        // (tidak ada record pada tabel magang), sehingga juga mengecualikan yang sudah selesai PKL
        $rows = $db->table('users u')
            ->select('u.id, u.name')
            ->where('u.role', 'siswa')
            ->where("NOT EXISTS (SELECT 1 FROM magang m WHERE m.siswa_id = u.id)", null, false)
            ->orderBy('u.name', 'ASC')
            ->get()
            ->getResultArray();
        return $this->response->setJSON($rows);
    }

    public function create()
    {
        $user = $this->request->user ?? null;
        if (!$user) return $this->response->setStatusCode(401)->setJSON(['message'=>'Unauthorized']);
        $payload = $this->request->getJSON(true) ?: $this->request->getPost();
        $siswaId = (int)($payload['siswa_id'] ?? 0);
        $dudiId = (int)($payload['dudi_id'] ?? 0);
        $tanggalMulai = $payload['tanggal_mulai'] ?? null;
        $tanggalSelesai = $payload['tanggal_selesai'] ?? null;
        if (!$siswaId || !$dudiId || !$tanggalMulai) {
            return $this->response->setStatusCode(422)->setJSON(['message'=>'Siswa, DUDI, dan Tanggal Mulai wajib diisi']);
        }
        $db = db_connect();
        // ensure no duplicate active placement for this student
        $exists = $db->table('magang')->where('siswa_id',$siswaId)->groupStart()->where('status','berlangsung')->orWhere('status','aktif')->groupEnd()->countAllResults();
        if ($exists) {
            return $this->response->setStatusCode(422)->setJSON(['message'=>'Siswa sudah memiliki penempatan aktif']);
        }
        $db->table('magang')->insert([
            'siswa_id' => $siswaId,
            'guru_id' => (int)$user['id'],
            'dudi_id' => $dudiId,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'status' => 'aktif',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return $this->response->setJSON(['message'=>'Penempatan dibuat dan diaktifkan','success'=>true]);
    }

    public function activate($id)
    {
        $user = $this->request->user ?? null;
        if (!$user) return $this->response->setStatusCode(401)->setJSON(['message'=>'Unauthorized']);
        
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
        
        $row = $db->table('magang')->where('id',(int)$id)->get()->getRowArray();
        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON(['message'=>'Data tidak ditemukan']);
        }
        
        // Izin: guru pemilik atau pengajuan pending tanpa guru (guru_id NULL/0)
        $hasPermission = (
            $row['guru_id'] == $guruId ||
            $row['guru_id'] == (int)$user['id'] ||
            ((($row['guru_id'] === null) || ((int)$row['guru_id'] === 0)) && strtolower($row['status'])==='pending')
        );
        
        if (!$hasPermission) {
            return $this->response->setStatusCode(404)->setJSON(['message'=>'Data tidak ditemukan']);
        }
        
        if (strtolower($row['status']) !== 'pending') {
            return $this->response->setStatusCode(422)->setJSON(['message'=>'Hanya data pending yang bisa diaktifkan']);
        }
        
        // saat aktivasi, set guru_id ke guru saat ini
        $db->table('magang')->where('id',(int)$id)->update([
            'status' => 'aktif',
            'guru_id' => $guruId,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        return $this->response->setJSON(['message'=>'Status diubah menjadi aktif','success'=>true]);
    }

    public function grade($id)
    {
        $user = $this->request->user ?? null;
        if (!$user) return $this->response->setStatusCode(401)->setJSON(['message'=>'Unauthorized']);
        
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
        
        $row = $db->table('magang')->where('id',(int)$id)->get()->getRowArray();
        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON(['message'=>'Data tidak ditemukan']);
        }
        
        // Izin edit: pemilik data ATAU pengajuan pending tanpa guru (guru_id=0)
        $hasPermission = ($row['guru_id'] == $guruId || $row['guru_id'] == (int)$user['id'] || ((int)$row['guru_id'] === 0 && strtolower($row['status'])==='pending'));
        if (!$hasPermission) return $this->response->setStatusCode(404)->setJSON(['message'=>'Data tidak ditemukan']);
        $payload = $this->request->getJSON(true) ?: $this->request->getPost();
        $nilaiAkhir = isset($payload['nilai']) ? (int)$payload['nilai'] : null;
        if ($nilaiAkhir === null || $nilaiAkhir < 0 || $nilaiAkhir > 100) {
            return $this->response->setStatusCode(422)->setJSON(['message'=>'Nilai harus 0-100']);
        }
        $db->table('magang')->where('id',(int)$id)->update(['status'=>'selesai','nilai'=>$nilaiAkhir]);
        return $this->response->setJSON(['message'=>'Nilai disimpan dan status selesai','success'=>true]);
    }

    public function debug($id)
    {
        $user = $this->request->user ?? null;
        if (!$user) return $this->response->setStatusCode(401)->setJSON(['message'=>'Unauthorized']);
        [$guruId, $userId] = $this->getGuruIds($user);
        $db = db_connect();
        $row = $db->table('magang')->where('id',(int)$id)->get()->getRowArray();
        
        return $this->response->setJSON([
            'user_id' => $user['id'],
            'guru_id_from_user' => $guruId,
            'user_id_from_user' => $userId,
            'magang_row' => $row,
            'magang_guru_id' => $row['guru_id'] ?? 'null',
            'permission_check' => in_array((int)$row['guru_id'], [(int)$guruId, $userId], true)
        ]);
    }

    public function debugAll()
    {
        $user = $this->request->user ?? null;
        if (!$user) return $this->response->setStatusCode(401)->setJSON(['message'=>'Unauthorized']);
        [$guruId, $userId] = $this->getGuruIds($user);
        $db = db_connect();
        
        // Get all data without filters
        $allMagang = $db->table('magang')->get()->getResultArray();
        $allDudi = $db->table('dudi')->get()->getResultArray();
        $allUsers = $db->table('users')->where('role', 'guru')->get()->getResultArray();
        $allGuru = $db->table('guru')->get()->getResultArray();
        
        return $this->response->setJSON([
            'user_id' => $user['id'],
            'guru_id_from_user' => $guruId,
            'user_id_from_user' => $userId,
            'all_magang_count' => count($allMagang),
            'all_dudi_count' => count($allDudi),
            'all_users_guru_count' => count($allUsers),
            'all_guru_count' => count($allGuru),
            'all_magang' => $allMagang,
            'all_dudi' => $allDudi,
            'all_users_guru' => $allUsers,
            'all_guru' => $allGuru
        ]);
    }

    public function update($id)
    {
        $user = $this->request->user ?? null;
        if (!$user) return $this->response->setStatusCode(401)->setJSON(['message'=>'Unauthorized']);
        
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
        
        $row = $db->table('magang')->where('id',(int)$id)->get()->getRowArray();
        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON(['message'=>'Data tidak ditemukan']);
        }
        
        // More flexible permission check - allow if guru_id matches either guru.id or user.id
        $hasPermission = false;
        if ($row['guru_id'] == $guruId || $row['guru_id'] == (int)$user['id']) {
            $hasPermission = true;
        }
        
        if (!$hasPermission) {
            return $this->response->setStatusCode(404)->setJSON(['message'=>'Data tidak ditemukan']);
        }
        $payload = $this->request->getJSON(true) ?: $this->request->getPost();
        $data = [];
        if (!empty($payload['tanggal_mulai'])) $data['tanggal_mulai'] = $payload['tanggal_mulai'];
        if (!empty($payload['tanggal_selesai'])) $data['tanggal_selesai'] = $payload['tanggal_selesai'];
        if (!empty($payload['status'])) {
            $status = strtolower($payload['status']);
            if (in_array($status, ['pending','aktif','selesai'], true)) $data['status'] = $status;
            // ketika status diubah menjadi aktif oleh guru, set guru_id menjadi guru yang menyetujui
            if ($status === 'aktif') { $data['guru_id'] = $guruId; }
        }
        if (array_key_exists('nilai', $payload)) {
            $n = $payload['nilai'];
            if ($n === null || $n === '') $data['nilai'] = null; else {
                $n = (int)$n; if ($n < 0 || $n > 100) return $this->response->setStatusCode(422)->setJSON(['message'=>'Nilai harus 0-100']);
                $data['nilai'] = $n;
            }
        }
        if (empty($data)) return $this->response->setJSON(['message'=>'Tidak ada perubahan','success'=>true]);
        $db->table('magang')->where('id',(int)$id)->update($data);
        return $this->response->setJSON(['message'=>'Data diperbarui','success'=>true]);
    }

    public function delete($id)
    {
        $user = $this->request->user ?? null;
        if (!$user) return $this->response->setStatusCode(401)->setJSON(['message'=>'Unauthorized']);
        
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
        
        // Check if magang exists
        $row = $db->table('magang')->where('id', (int)$id)->get()->getRowArray();
        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON(['message'=>'Data tidak ditemukan']);
        }
        
        // Check permission - only the assigned guru can delete
        $hasPermission = false;
        if ($row['guru_id'] == $guruId || $row['guru_id'] == (int)$user['id']) {
            $hasPermission = true;
        }
        
        if (!$hasPermission) {
            return $this->response->setStatusCode(403)->setJSON(['message'=>'Anda tidak memiliki izin untuk menghapus data ini']);
        }
        
        // Check if there are related logbook entries
        $logbookCount = $db->table('logbook')->where('magang_id', (int)$id)->countAllResults();
        if ($logbookCount > 0) {
            return $this->response->setStatusCode(422)->setJSON([
                'message'=>'Tidak dapat menghapus data magang karena masih ada jurnal terkait. Hapus jurnal terlebih dahulu.'
            ]);
        }
        
        // Hard delete - permanently remove from database
        $db->table('magang')->where('id', (int)$id)->delete();
        
        return $this->response->setJSON(['message'=>'Data magang berhasil dihapus','success'=>true]);
    }
}


