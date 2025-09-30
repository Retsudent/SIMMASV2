<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class FileController extends Controller
{
    public function download($filename)
    {
        $filePath = WRITEPATH . 'uploads/' . $filename;
        
        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'File not found']);
        }
        
        // Get file info
        $fileInfo = pathinfo($filePath);
        $mimeType = mime_content_type($filePath);
        
        // Set headers for download
        $this->response->setHeader('Content-Type', $mimeType);
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $this->response->setHeader('Content-Length', filesize($filePath));
        
        // Output file
        return $this->response->setBody(file_get_contents($filePath));
    }
}
