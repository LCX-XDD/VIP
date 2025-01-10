<?php
header('Content-Type: application/json');

require_once 'config.php';
require_once 'functions.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';

try {
    // 验证邮箱是否存在
    $stmt = $pdo->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo json_encode([
            'status' => 'success',
            'currentPassword' => $user['password']
        ]);
    } else {
        throw new Exception('用户不存在');
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 