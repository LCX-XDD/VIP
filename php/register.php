<?php
require_once 'config.php';

try {
    // 检查请求方法
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse('error', '不支持的请求方法');
    }

    // 获取 POST 数据
    $input = file_get_contents('php://input');
    if (empty($input)) {
        jsonResponse('error', '未接收到数据');
    }

    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        jsonResponse('error', '无效的 JSON 数据: ' . json_last_error_msg());
    }

    // 验证必填字段
    $username = trim($data['username'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $confirmPassword = $data['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        jsonResponse('error', '请填写所有必填字段');
    }

    // 验证用户名格式
    if (!preg_match('/^[a-zA-Z0-9_]{4,20}$/', $username)) {
        jsonResponse('error', '用户名只能包含字母、数字和下划线，长度4-20位');
    }

    // 验证邮箱格式
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse('error', '请输入有效的邮箱地址');
    }

    // 验证密码长度
    if (strlen($password) < 6 || strlen($password) > 20) {
        jsonResponse('error', '密码长度必须在6-20位之间');
    }

    // 验证两次密码是否一致
    if ($password !== $confirmPassword) {
        jsonResponse('error', '两次输入的密码不一致');
    }

    // 创建用户表（如果不存在）
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(20) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 检查用户名是否已存在
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        jsonResponse('error', '用户名已被使用');
    }

    // 检查邮箱是否已存在
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        jsonResponse('error', '邮箱已被注册');
    }

    // 插入新用户
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    
    if ($stmt->execute([$username, $email, $hashedPassword])) {
        jsonResponse('success', '注册成功', [
            'username' => $username,
            'email' => $email
        ]);
    } else {
        jsonResponse('error', '注册失败，请稍后重试');
    }

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    jsonResponse('error', '数据库错误，请稍后重试');
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    jsonResponse('error', '系统错误，请稍后重试');
}
?> 