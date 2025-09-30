<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtAuth implements FilterInterface
{
    private function getJwtSecret(): string
    {
        $key = env('encryption.key');
        if (str_starts_with($key, 'hex2bin:')) {
            $key = hex2bin(substr($key, 8));
        }
        return $key ?: 'simmas-secret';
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');
        if (!str_starts_with($header, 'Bearer ')) {
            return service('response')->setStatusCode(401)->setJSON(['message' => 'Token tidak ditemukan']);
        }
        $jwt = substr($header, 7);
        try {
            $decoded = JWT::decode($jwt, new Key($this->getJwtSecret(), 'HS256'));
            // role check if provided
            if (!empty($arguments)) {
                $roles = (array) $arguments;
                if (!in_array($decoded->role, $roles, true)) {
                    return service('response')->setStatusCode(403)->setJSON(['message' => 'Akses ditolak']);
                }
            }
            // attach to request
            $request->user = [
                'id' => (int)$decoded->sub,
                'name' => $decoded->name ?? '',
                'email' => $decoded->email ?? '',
                'role' => $decoded->role ?? '',
            ];
        } catch (\Throwable $e) {
            return service('response')->setStatusCode(401)->setJSON(['message' => 'Token tidak valid']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}



