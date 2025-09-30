<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class DebugController extends BaseController
{
    public function checkDatabase()
    {
        try {
            $db = db_connect();
            
            $data = [
                'magang_count' => $db->table('magang')->countAllResults(),
                'dudi_count' => $db->table('dudi')->countAllResults(),
                'users_count' => $db->table('users')->countAllResults(),
                'guru_count' => $db->table('guru')->countAllResults(),
                'siswa_count' => $db->table('siswa')->countAllResults(),
            ];
            
            // Get sample data
            if ($data['magang_count'] > 0) {
                $data['magang_sample'] = $db->table('magang')->limit(3)->get()->getResultArray();
            }
            
            if ($data['dudi_count'] > 0) {
                $data['dudi_sample'] = $db->table('dudi')->limit(3)->get()->getResultArray();
            }
            
            if ($data['users_count'] > 0) {
                $data['users_sample'] = $db->table('users')->limit(3)->get()->getResultArray();
            }
            
            return $this->response->setJSON($data);
            
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()]);
        }
    }
    
    public function testInternships()
    {
        try {
            $db = db_connect();
            $rows = $db->table('magang')->get()->getResultArray();
            return $this->response->setJSON($rows);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()]);
        }
    }
}
