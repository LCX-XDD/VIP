<?php
require_once 'config.php';

header('Content-Type: application/json');

// 启用错误报告
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 获取POST数据
$raw_data = file_get_contents('php://input');
error_log("收到的原始数据: " . $raw_data);

$data = json_decode($raw_data, true);
error_log("解析后的数据: " . print_r($data, true));

try {
    // 验证数据
    if (empty($data['username']) || empty($data['password']) || empty($data['email'])) {
        throw new Exception('请填写所有必填字段');
    }

    $conn = getDBConnection();
    error_log("数据库连接成功");
    
    // 检查用户名是否已存在
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    if (!$stmt) {
        throw new Exception('准备查询语句失败: ' . $conn->error);
    }
    
    $stmt->bind_param("s", $data['username']);
    if (!$stmt->execute()) {
        throw new Exception('执行查询失败: ' . $stmt->error);
    }
    
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception('用户名已存在');
    }
    
    // 检查邮箱是否已存在
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$stmt) {
        throw new Exception('准备查询语句失败: ' . $conn->error);
    }
    
    $stmt->bind_param("s", $data['email']);
    if (!$stmt->execute()) {
        throw new Exception('执行查询失败: ' . $stmt->error);
    }
    
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception('邮箱已存在');
    }
    
    // 添加用户
    $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    if (!$stmt) {
        throw new Exception('准备插入语句失败: ' . $conn->error);
    }
    
    $stmt->bind_param("sss", $data['username'], $data['password'], $data['email']);
    error_log("准备执行插入: username=" . $data['username'] . ", email=" . $data['email']);
    
    if ($stmt->execute()) {
        error_log("用户添加成功");
        echo json_encode([
            'status' => 'success',
            'message' => '用户添加成功'
        ]);
    } else {
        throw new Exception('添加用户失败: ' . $stmt->error);
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    error_log("添加用户时出错: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 