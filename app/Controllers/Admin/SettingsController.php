<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingsModel;
use CodeIgniter\HTTP\ResponseInterface;

class SettingsController extends BaseController
{
    protected $settingsModel;

    public function __construct()
    {
        $this->settingsModel = new SettingsModel();
    }

    public function index()
    {
        $settings = $this->settingsModel->getSettings();
        
        if (!$settings) {
            // Return default settings if none exist
            $settings = [
                'nama_sekolah' => 'SMK Negeri 1 Surabaya',
                'website' => 'www.smkn1surabaya.sch.id',
                'alamat' => 'Jl. SMEA No.4, Sawahan, Kec. Sawahan, Kota Surabaya, Jawa Timur 60252',
                'telepon' => '031-5678910',
                'email' => 'info@smkn1surabaya.sch.id',
                'kepala_sekolah' => 'Drs. H. Sutrisno, M.Pd.',
                'npsn' => '20567890',
                'logo_url' => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiBmaWxsPSIjMDA3QkZGIi8+Cjx0ZXh0IHg9IjUwIiB5PSI1NSIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXNpemU9IjE0IiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+TE9HTzwvdGV4dD4KPC9zdmc+',
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        
        return $this->response->setJSON($settings);
    }
    
    /**
     * Get school settings for public display (no authentication required)
     */
    public function getSchoolInfo()
    {
        $settings = $this->settingsModel->getSettings();
        
        if (!$settings) {
            // Return default settings if none exist
            $settings = [
                'nama_sekolah' => 'SMK Negeri 1 Surabaya',
                'website' => 'www.smkn1surabaya.sch.id',
                'alamat' => 'Jl. SMEA No.4, Sawahan, Kec. Sawahan, Kota Surabaya, Jawa Timur 60252',
                'telepon' => '031-5678910',
                'email' => 'info@smkn1surabaya.sch.id',
                'kepala_sekolah' => 'Drs. H. Sutrisno, M.Pd.',
                'npsn' => '20567890',
                'logo_url' => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIiBmaWxsPSIjMDA3QkZGIi8+Cjx0ZXh0IHg9IjUwIiB5PSI1NSIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXNpemU9IjE0IiBmaWxsPSJ3aGl0ZSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+TE9HTzwvdGV4dD4KPC9zdmc+',
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        
        return $this->response->setJSON($settings);
    }
    
    public function update()
    {
        $data = $this->request->getJSON(true) ?: $this->request->getPost();
        
        // Validate required fields
        if (empty($data['nama_sekolah'])) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Nama sekolah harus diisi']);
        }
        if (empty($data['alamat'])) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Alamat harus diisi']);
        }
        if (empty($data['telepon'])) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Telepon harus diisi']);
        }
        if (empty($data['email'])) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Email harus diisi']);
        }
        if (empty($data['kepala_sekolah'])) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Kepala sekolah harus diisi']);
        }
        
        try {
            // Save to database
            $result = $this->settingsModel->updateSettings($data);
            
            if ($result) {
                return $this->response->setJSON([
                    'message' => 'Pengaturan berhasil disimpan',
                    'success' => true
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'message' => 'Gagal menyimpan pengaturan',
                    'success' => false
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Settings update failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Terjadi kesalahan saat menyimpan pengaturan: ' . $e->getMessage(),
                'success' => false
            ]);
        }
    }
    
    public function uploadLogo()
    {
        $file = $this->request->getFile('logo');
        
        if (!$file || !$file->isValid()) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'File logo tidak valid']);
        }
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Tipe file tidak didukung. Gunakan JPG, PNG, GIF, atau WebP']);
        }
        
        // Validate file size (max 2MB)
        if ($file->getSize() > 2 * 1024 * 1024) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Ukuran file maksimal 2MB']);
        }
        
        try {
            // Read file content and convert to base64
            $fileContent = file_get_contents($file->getTempName());
            $logoData = base64_encode($fileContent);
            $mimeType = $file->getMimeType();
            $logoUrl = 'data:' . $mimeType . ';base64,' . $logoData;
            
            // Save logo URL to database
            $result = $this->settingsModel->updateSettings(['logo_url' => $logoUrl]);
            
            if (!$result) {
                return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal menyimpan logo ke database']);
            }
            
            // Log for debugging
            log_message('debug', 'Logo uploaded successfully, size: ' . strlen($logoData) . ' bytes');
            
            return $this->response->setJSON([
                'message' => 'Logo berhasil diupload',
                'logo_url' => $logoUrl,
                'success' => true
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Logo upload failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal mengupload logo: ' . $e->getMessage()]);
        }
    }
}
