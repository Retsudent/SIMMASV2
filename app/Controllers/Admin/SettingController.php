<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class SettingController extends BaseController
{
    public function show()
    {
        $row = db_connect()->table('school_settings')->get()->getRowArray();
        return $this->response->setJSON($row ?: []);
    }

    public function update()
    {
        $data = $this->request->getJSON(true) ?: $this->request->getRawInput();
        $db = db_connect();
        $exists = $db->table('school_settings')->countAll() > 0;
        if ($exists) {
            $db->table('school_settings')->update($data);
        } else {
            $db->table('school_settings')->insert($data);
        }
        return $this->response->setStatusCode(ResponseInterface::HTTP_OK)->setJSON(['saved' => true]);
    }
}


