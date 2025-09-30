<?php

/**
 * Script untuk membersihkan data magang yang sudah di-soft delete
 * Jalankan script ini jika Anda ingin menghapus data secara permanen dari database
 */

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Database connection
$host = $_ENV['database.default.hostname'] ?? 'localhost';
$username = $_ENV['database.default.username'] ?? 'root';
$password = $_ENV['database.default.password'] ?? '';
$database = $_ENV['database.default.database'] ?? 'simmas';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . "\n");
}

echo "=== Cleanup Soft Deleted Magang Data ===\n\n";

// Check how many soft deleted records exist
$stmt = $pdo->prepare("SELECT COUNT(*) FROM magang WHERE deleted_at IS NOT NULL");
$stmt->execute();
$softDeletedCount = $stmt->fetchColumn();

echo "Found {$softDeletedCount} soft deleted magang records.\n";

if ($softDeletedCount === 0) {
    echo "No soft deleted records found. Nothing to clean up.\n";
    exit(0);
}

// Show details of soft deleted records
echo "\nSoft deleted records:\n";
$stmt = $pdo->prepare("SELECT id, siswa_id, dudi_id, guru_id, status, deleted_at FROM magang WHERE deleted_at IS NOT NULL");
$stmt->execute();
$softDeletedRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($softDeletedRecords as $record) {
    echo "- ID: {$record['id']}, Siswa: {$record['siswa_id']}, DUDI: {$record['dudi_id']}, Guru: {$record['guru_id']}, Status: {$record['status']}, Deleted: {$record['deleted_at']}\n";
}

echo "\nDo you want to permanently delete these records? (y/N): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim(strtolower($line)) !== 'y') {
    echo "Operation cancelled.\n";
    exit(0);
}

// Permanently delete soft deleted records
$stmt = $pdo->prepare("DELETE FROM magang WHERE deleted_at IS NOT NULL");
$stmt->execute();
$deletedCount = $stmt->rowCount();

echo "\nPermanently deleted {$deletedCount} records from database.\n";
echo "Cleanup completed successfully.\n";
