<?php

/**
 * Script untuk memeriksa data guru Pak Yanto dan data magang terkait
 */

// Database configuration - sesuaikan dengan konfigurasi Anda
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'simmas';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . "\n");
}

echo "=== Check Guru Data ===\n\n";

// Check all users with role guru
echo "1. All users with role 'guru':\n";
$stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE role = 'guru'");
$stmt->execute();
$guruUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($guruUsers as $user) {
    echo "- ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}\n";
}

// Check guru table data
echo "\n2. All data in guru table:\n";
$stmt = $pdo->prepare("SELECT id, user_id, nama, nip FROM guru");
$stmt->execute();
$guruData = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($guruData as $guru) {
    echo "- ID: {$guru['id']}, User ID: {$guru['user_id']}, Nama: {$guru['nama']}, NIP: {$guru['nip']}\n";
}

// Check magang data for each guru
echo "\n3. Magang data for each guru:\n";
foreach ($guruData as $guru) {
    echo "\nGuru: {$guru['nama']} (ID: {$guru['id']}, User ID: {$guru['user_id']})\n";
    
    // Check magang by guru_id
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM magang WHERE guru_id = ? AND deleted_at IS NULL");
    $stmt->execute([$guru['id']]);
    $countByGuruId = $stmt->fetchColumn();
    echo "  - Magang by guru_id ({$guru['id']}): {$countByGuruId} records\n";
    
    // Check magang by user_id as guru_id
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM magang WHERE guru_id = ? AND deleted_at IS NULL");
    $stmt->execute([$guru['user_id']]);
    $countByUserId = $stmt->fetchColumn();
    echo "  - Magang by user_id as guru_id ({$guru['user_id']}): {$countByUserId} records\n";
    
    // Show detailed magang data
    if ($countByGuruId > 0 || $countByUserId > 0) {
        $stmt = $pdo->prepare("
            SELECT m.id, m.siswa_id, m.dudi_id, m.status, m.tanggal_mulai, m.tanggal_selesai, 
                   u.name as siswa_nama, d.nama_perusahaan as dudi_nama
            FROM magang m
            LEFT JOIN users u ON u.id = m.siswa_id
            LEFT JOIN dudi d ON d.id = m.dudi_id
            WHERE (m.guru_id = ? OR m.guru_id = ?) AND m.deleted_at IS NULL
            ORDER BY m.created_at DESC
        ");
        $stmt->execute([$guru['id'], $guru['user_id']]);
        $magangDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($magangDetails as $magang) {
            echo "    * Magang ID: {$magang['id']}, Siswa: {$magang['siswa_nama']}, DUDI: {$magang['dudi_nama']}, Status: {$magang['status']}\n";
        }
    }
}

// Check for Pak Yanto specifically
echo "\n4. Searching for 'Pak Yanto' or similar names:\n";
$stmt = $pdo->prepare("SELECT * FROM guru WHERE nama LIKE '%Yanto%' OR nama LIKE '%yanto%'");
$stmt->execute();
$yantoData = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($yantoData)) {
    echo "No guru found with 'Yanto' in the name.\n";
} else {
    foreach ($yantoData as $yanto) {
        echo "- Found: ID: {$yanto['id']}, User ID: {$yanto['user_id']}, Nama: {$yanto['nama']}\n";
    }
}

echo "\n=== Check Complete ===\n";
