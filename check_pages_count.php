<?php
$host = '145.223.98.97';
$db   = 'bms_v2';
$user = 'bms_v2';
$pass = 'bmsv2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM pages');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "إجمالي الصفحات في قاعدة البيانات: " . number_format($result['total']) . "\n";
    
    // Get max ID
    $stmt = $pdo->query('SELECT MAX(id) as max_id FROM pages');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "أعلى ID: " . number_format($result['max_id']) . "\n";
    
} catch(PDOException $e) {
    echo "خطأ: " . $e->getMessage() . "\n";
}
