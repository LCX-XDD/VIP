<?php
// 开启错误报告
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once 'config.php';

try {
    // 记录接收到的数据
    error_log("Received data: " . file_get_contents('php://input'));
    
    // 获取并验证输入数据
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        throw new Exception('无效的请求数据');
    }

    $email = trim($data['email'] ?? '');
    $newPassword = trim($data['new_password'] ?? '');
    $confirmPassword = trim($data['confirm_password'] ?? '');

    // 记录处理的数据（注意：实际生产环境不要记录密码）
    error_log("Processing: email=$email");

    // 基本验证
    if (empty($email) || empty($newPassword) || empty($confirmPassword)) {
        throw new Exception('所有字段都必须填写');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('无效的邮箱格式');
    }

    if ($newPassword !== $confirmPassword) {
        throw new Exception('两次输入的密码不一致');
    }

    // 验证邮箱是否存在并获取当前密码
    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");  // 移除 status 检查
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        throw new Exception('未找到该邮箱对应的账户');
    }
    
    // 验证新密码是否与当前密码相同
    if (password_verify($newPassword, $user['password'])) {
        throw new Exception('新密码不能与当前密码相同');
    }
    
    // 密码长度验证
    if (strlen($newPassword) < 6 || strlen($newPassword) > 20) {
        throw new Exception('密码长度必须在6-20位之间');
    }
    
    // 对新密码进行哈希处理
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    if ($hashedPassword === false) {
        throw new Exception('密码加密失败');
    }
    
    // 更新密码 - 简化SQL语句
    $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $result = $updateStmt->execute([$hashedPassword, $user['id']]);
    
    if (!$result) {
        throw new Exception('密码更新失败');
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => '密码重置成功，请使用新密码登录'
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in forgot.php: " . $e->getMessage());
    error_log("SQL State: " . $e->getCode());
    echo json_encode([
        'status' => 'error',
        'message' => '数据库错误，请稍后重试'
    ]);
} catch (Exception $e) {
    error_log("General error in forgot.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 