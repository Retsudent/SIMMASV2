<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingsModel extends Model
{
    protected $table = 'school_settings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nama_sekolah',
        'website',
        'alamat',
        'telepon',
        'email',
        'kepala_sekolah',
        'npsn',
        'logo_url'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'nama_sekolah' => 'required|max_length[255]',
        'alamat' => 'required',
        'telepon' => 'required|max_length[50]',
        'email' => 'required|valid_email|max_length[255]',
        'kepala_sekolah' => 'required|max_length[255]',
    ];

    protected $validationMessages = [
        'nama_sekolah' => [
            'required' => 'Nama sekolah harus diisi',
            'max_length' => 'Nama sekolah maksimal 255 karakter'
        ],
        'alamat' => [
            'required' => 'Alamat harus diisi'
        ],
        'telepon' => [
            'required' => 'Telepon harus diisi',
            'max_length' => 'Telepon maksimal 50 karakter'
        ],
        'email' => [
            'required' => 'Email harus diisi',
            'valid_email' => 'Format email tidak valid',
            'max_length' => 'Email maksimal 255 karakter'
        ],
        'kepala_sekolah' => [
            'required' => 'Kepala sekolah harus diisi',
            'max_length' => 'Nama kepala sekolah maksimal 255 karakter'
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get the first (and only) settings record
     */
    public function getSettings()
    {
        return $this->first();
    }

    /**
     * Update settings (there should only be one record)
     */
    public function updateSettings($data)
    {
        $settings = $this->first();
        if ($settings) {
            return $this->update($settings['id'], $data);
        } else {
            return $this->insert($data);
        }
    }
}