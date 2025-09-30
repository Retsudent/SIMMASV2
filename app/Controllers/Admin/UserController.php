<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class UserController extends BaseController
{
    public function index()
    {
        $q = $this->request->getGet('q');
        $role = $this->request->getGet('role');
        $model = new UserModel();
        $builder = $model->builder();
        
        if ($q) {
            $builder->groupStart()
                ->like('name', $q)
                ->orLike('email', $q)
                ->orLike('role', $q)
                ->groupEnd();
        }
        
        if ($role) {
            $builder->where('role', $role);
        }
        
        return $this->response->setJSON($builder->orderBy('created_at', 'DESC')->get()->getResultArray());
    }

    public function create()
    {
        $data = $this->request->getJSON(true) ?: $this->request->getPost();
        if (empty($data['name']) || empty($data['email']) || empty($data['password']) || empty($data['role'])) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Data tidak lengkap']);
        }
        
        // Check if email already exists
        $existingUser = (new UserModel())->where('email', $data['email'])->first();
        if ($existingUser) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Email sudah terdaftar']);
        }
        
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['email_verified_at'] = !empty($data['email_verified_at']) ? $data['email_verified_at'] : null;
        $id = (new UserModel())->insert($data, true);

        // Sinkronisasi user ke tabel entitas peran
        $db = db_connect();
        if ($data['role'] === 'guru') {
            $exists = $db->table('guru')->where('user_id', $id)->countAllResults();
            if (!$exists) {
                $db->table('guru')->insert([
                    'user_id' => $id,
                    'nama' => $data['name'] ?? 'Guru',
                    'telepon' => $data['telepon'] ?? null,
                ]);
            }
        } elseif ($data['role'] === 'siswa') {
            $exists = $db->table('siswa')->where('user_id', $id)->countAllResults();
            if (!$exists) {
                $db->table('siswa')->insert([
                    'user_id' => $id,
                    'nama' => $data['name'] ?? 'Siswa',
                    'kelas' => $data['kelas'] ?? null,
                    'jurusan' => $data['jurusan'] ?? null,
                ]);
            }
        }

        return $this->response->setStatusCode(ResponseInterface::HTTP_CREATED)->setJSON([
            'id' => $id, 
            'message' => 'Data user berhasil ditambahkan', 
            'success' => true
        ]);
    }

    public function update($id)
    {
        $data = $this->request->getJSON(true) ?: $this->request->getRawInput();
        unset($data['password']); // edit tidak mengubah password
        
        // Check if email already exists for other users
        if (isset($data['email'])) {
            $existingUser = (new UserModel())->where('email', $data['email'])->where('id !=', $id)->first();
            if ($existingUser) {
                return $this->response->setStatusCode(422)->setJSON(['message' => 'Email sudah terdaftar']);
            }
        }
        
        (new UserModel())->update($id, $data);

        // Sinkronisasi nama/role ke tabel entitas
        $db = db_connect();
        $user = (new UserModel())->find($id);
        if ($user) {
            if ($user['role'] === 'guru') {
                $row = $db->table('guru')->where('user_id', $id)->get()->getRowArray();
                if ($row) { $db->table('guru')->where('user_id',$id)->update(['nama'=>$user['name']]); }
                else { $db->table('guru')->insert(['user_id'=>$id,'nama'=>$user['name']]); }
            } elseif ($user['role'] === 'siswa') {
                $row = $db->table('siswa')->where('user_id', $id)->get()->getRowArray();
                if ($row) { $db->table('siswa')->where('user_id',$id)->update(['nama'=>$user['name']]); }
                else { $db->table('siswa')->insert(['user_id'=>$id,'nama'=>$user['name']]); }
            }
        }

        return $this->response->setStatusCode(200)->setJSON([
            'message' => 'Data user berhasil diperbarui', 
            'success' => true
        ]);
    }

    public function delete($id)
    {
        $db = db_connect();
        $user = (new UserModel())->find($id);
        
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'User tidak ditemukan']);
        }
        
        // Check if user has active internship placements
        if ($user['role'] === 'siswa') {
            $activePlacements = $db->table('magang')
                ->where('siswa_id', $id)
                ->whereIn('status', ['aktif', 'pending'])
                ->countAllResults();
            
            if ($activePlacements > 0) {
                return $this->response->setStatusCode(422)->setJSON([
                    'message' => 'Tidak dapat menghapus siswa yang masih memiliki penempatan magang aktif'
                ]);
            }
        }
        
        // Delete related data first
        if ($user['role'] === 'siswa') {
            // Delete from siswa table
            $db->table('siswa')->where('user_id', $id)->delete();
            
            // Delete related logbook entries
            $magangIds = $db->table('magang')
                ->select('id')
                ->where('siswa_id', $id)
                ->get()->getResultArray();
            
            if (!empty($magangIds)) {
                $magangIdList = array_column($magangIds, 'id');
                $db->table('logbook')->whereIn('magang_id', $magangIdList)->delete();
            }
            
            // Delete magang records
            $db->table('magang')->where('siswa_id', $id)->delete();
            
        } elseif ($user['role'] === 'guru') {
            // Delete from guru table
            $db->table('guru')->where('user_id', $id)->delete();
            
            // Check if guru has active students
            $activeStudents = $db->table('magang')
                ->where('guru_id', $id)
                ->whereIn('status', ['aktif', 'pending'])
                ->countAllResults();
            
            if ($activeStudents > 0) {
                return $this->response->setStatusCode(422)->setJSON([
                    'message' => 'Tidak dapat menghapus guru yang masih memiliki siswa bimbingan aktif'
                ]);
            }
            
            // Delete related logbook entries
            $magangIds = $db->table('magang')
                ->select('id')
                ->where('guru_id', $id)
                ->get()->getResultArray();
            
            if (!empty($magangIds)) {
                $magangIdList = array_column($magangIds, 'id');
                $db->table('logbook')->whereIn('magang_id', $magangIdList)->delete();
            }
            
            // Delete magang records
            $db->table('magang')->where('guru_id', $id)->delete();
        }
        
        // Finally delete the user
        (new UserModel())->delete($id);
        
        return $this->response->setStatusCode(200)->setJSON([
            'message' => 'Data user dan data terkait berhasil dihapus', 
            'success' => true
        ]);
    }
}


