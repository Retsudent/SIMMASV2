<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function admin() 
    { 
        // Pass user data to view
        $data = [
            'user' => $this->getUserData()
        ];
        return view('dashboard/admin', $data); 
    }
    
    public function guru() 
    { 
        // Pass user data to view
        $data = [
            'user' => $this->getUserData()
        ];
        return view('dashboard/guru', $data); 
    }
    
    public function siswa() 
    { 
        // Pass user data to view
        $data = [
            'user' => $this->getUserData()
        ];
        return view('dashboard/siswa', $data); 
    }
    
    private function getUserData()
    {
        // Try to get user data from JWT token in localStorage (frontend)
        // For now, return mock data based on the route
        $uri = $this->request->getUri();
        $path = $uri->getPath();
        
        if (strpos($path, '/admin') !== false) {
            return [
                'id' => 1,
                'name' => 'Admin Sistem',
                'email' => 'admin@smk1sby.sch.id',
                'role' => 'admin'
            ];
        } elseif (strpos($path, '/guru') !== false) {
            return [
                'id' => 2,
                'name' => 'Pak Yanto', // Diperbaiki dari Pak Hendro ke Pak Yanto
                'email' => 'pak.yanto@smk1sby.sch.id',
                'role' => 'guru'
            ];
        } elseif (strpos($path, '/siswa') !== false) {
            return [
                'id' => 3,
                'name' => 'Ahmad Rizki',
                'email' => 'ahmad@smk1sby.sch.id',
                'role' => 'siswa'
            ];
        }
        
        // Return guest user as fallback
        return [
            'id' => 0,
            'name' => 'Guest',
            'email' => 'guest@example.com',
            'role' => 'guest'
        ];
    }
}


