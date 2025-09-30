<?php
// Simple helper to ensure a user with given email has a row in 'guru' table.
// Usage: php scripts/ensure_guru.php guru@simmas.test

error_reporting(E_ALL);
ini_set('display_errors', '1');

$email = $argv[1] ?? null;
if (!$email) {
    fwrite(STDERR, "Usage: php scripts/ensure_guru.php <email>\n");
    exit(1);
}

$dsn = 'pgsql:host=127.0.0.1;port=5432;dbname=SIMMAS';
$user = 'postgres';
$pass = 'postgres';

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $stmt = $pdo->prepare('SELECT id, name, role FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$u) {
        echo "User not found for email: $email\n";
        exit(0);
    }

    $uid = (int) $u['id'];
    $stmt = $pdo->prepare('SELECT id FROM guru WHERE user_id = :uid LIMIT 1');
    $stmt->execute([':uid' => $uid]);
    $g = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($g) {
        echo "Guru row already exists for user_id $uid (email $email)\n";
        exit(0);
    }

    $ins = $pdo->prepare('INSERT INTO guru (user_id, nama, created_at, updated_at) VALUES (:uid, :nama, NOW(), NOW())');
    $ins->execute([':uid' => $uid, ':nama' => $u['name'] ?: 'Guru']);
    echo "Inserted guru for user_id $uid (email $email)\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'Error: ' . $e->getMessage() . "\n");
    exit(1);
}




