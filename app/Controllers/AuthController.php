<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends BaseController
{
    private function getJwtSecret(): string
    {
        $key = env('encryption.key');
        if (str_starts_with($key, 'hex2bin:')) {
            $key = hex2bin(substr($key, 8));
        }
        return $key ?: 'simmas-secret';
    }

    public function login()
    {
        $payload = $this->request->getJSON(true) ?: $this->request->getPost();
        $email = $payload['email'] ?? null;
        $password = $payload['password'] ?? null;

        if (!$email || !$password) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON(['message' => 'Email dan password wajib diisi']);
        }

        $db = db_connect();
        $user = $db->table('users')->where('email', $email)->get()->getRowArray();
        if (!$user || !password_verify($password, $user['password'])) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON(['message' => 'Email atau password salah']);
        }

        $now = time();
        $payload = [
            'iss' => 'simmas-ci4',
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + 60 * 60 * 4,
            'sub' => (string)$user['id'],
            'role' => $user['role'],
            'name' => $user['name'],
            'email' => $user['email'],
        ];

        $token = JWT::encode($payload, $this->getJwtSecret(), 'HS256');

        return $this->response->setJSON([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 60 * 60 * 4,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ],
        ]);
    }

    public function me()
    {
        $user = $this->request->user ?? null;
        if (!$user) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON(['message' => 'Unauthorized']);
        }

        return $this->response->setJSON(['user' => $user]);
    }
}



