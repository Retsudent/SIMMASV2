<?php

/**
 * Script untuk memperbaiki data guru Pak Yanto
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

echo "=== Fix Guru Data ===\n\n";

// 1. Check if Pak Yanto exists in users table
echo "1. Checking for Pak Yanto in users table...\n";
$stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE name LIKE '%Yanto%' OR name LIKE '%yanto%'");
$stmt->execute();
$yantoUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($yantoUsers)) {
    echo "No user found with 'Yanto' in the name.\n";
    echo "Creating a test user for Pak Yanto...\n";
    
    // Create test user for Pak Yanto
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, email_verified_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $password = password_hash('password', PASSWORD_DEFAULT);
    $stmt->execute([
        'Pak Yanto',
        'pak.yanto@simmas.test',
        $password,
        'guru',
        date('Y-m-d H:i:s'),
        date('Y-m-d H:i:s'),
        date('Y-m-d H:i:s')
    ]);
    
    $yantoUserId = $pdo->lastInsertId();
    echo "Created user Pak Yanto with ID: {$yantoUserId}\n";
} else {
    $yantoUserId = $yantoUsers[0]['id'];
    echo "Found Pak Yanto with ID: {$yantoUserId}\n";
}

// 2. Check if Pak Yanto exists in guru table
echo "\n2. Checking for Pak Yanto in guru table...\n";
$stmt = $pdo->prepare("SELECT id, user_id, nama FROM guru WHERE user_id = ?");
$stmt->execute([$yantoUserId]);
$yantoGuru = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$yantoGuru) {
    echo "Pak Yanto not found in guru table. Creating...\n";
    $stmt = $pdo->prepare("INSERT INTO guru (user_id, nama, nip, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $yantoUserId,
        'Pak Yanto',
        '19850303201003003',
        date('Y-m-d H:i:s'),
        date('Y-m-d H:i:s')
    ]);
    
    $yantoGuruId = $pdo->lastInsertId();
    echo "Created guru record with ID: {$yantoGuruId}\n";
} else {
    $yantoGuruId = $yantoGuru['id'];
    echo "Found Pak Yanto in guru table with ID: {$yantoGuruId}\n";
}

// 3. Check for existing magang data
echo "\n3. Checking for existing magang data...\n";
$stmt = $pdo->prepare("SELECT COUNT(*) FROM magang WHERE (guru_id = ? OR guru_id = ?) AND deleted_at IS NULL");
$stmt->execute([$yantoGuruId, $yantoUserId]);
$magangCount = $stmt->fetchColumn();

echo "Found {$magangCount} magang records for Pak Yanto.\n";

if ($magangCount == 0) {
    echo "No magang data found. Creating sample data...\n";
    
    // Get a student and DUDI to create sample data
    $stmt = $pdo->prepare("SELECT id FROM users WHERE role = 'siswa' LIMIT 1");
    $stmt->execute();
    $siswa = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("SELECT id FROM dudi LIMIT 1");
    $stmt->execute();
    $dudi = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($siswa && $dudi) {
        $stmt = $pdo->prepare("INSERT INTO magang (siswa_id, guru_id, dudi_id, status, tanggal_mulai, tanggal_selesai, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $siswa['id'],
            $yantoGuruId, // Use guru_id from guru table
            $dudi['id'],
            'aktif',
            date('Y-m-d'),
            date('Y-m-d', strtotime('+3 months')),
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s')
        ]);
        
        echo "Created sample magang data.\n";
    } else {
        echo "No student or DUDI found to create sample data.\n";
    }
}

// 4. Final check
echo "\n4. Final check - Data for Pak Yanto:\n";
$stmt = $pdo->prepare("
    SELECT m.id, m.siswa_id, m.dudi_id, m.status, 
           u.name as siswa_nama, d.nama_perusahaan as dudi_nama
    FROM magang m
    LEFT JOIN users u ON u.id = m.siswa_id
    LEFT JOIN dudi d ON d.id = m.dudi_id
    WHERE (m.guru_id = ? OR m.guru_id = ?) AND m.deleted_at IS NULL
    ORDER BY m.created_at DESC
");
$stmt->execute([$yantoGuruId, $yantoUserId]);
$finalData = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($finalData as $data) {
    echo "- Magang ID: {$data['id']}, Siswa: {$data['siswa_nama']}, DUDI: {$data['dudi_nama']}, Status: {$data['status']}\n";
}

echo "\n=== Fix Complete ===\n";
