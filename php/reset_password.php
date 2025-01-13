<?php
header('Content-Type: application/json');

// 开启错误日志
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', 'error.log');

require_once 'db_config.php';

// 获取POST数据
$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$new_password = $data['new_password'] ?? '';

// 验证数据
if (empty($email) || empty($new_password)) {
    echo json_encode(['status' => 'error', 'message' => '请填写所有必填字段']);
    exit;
}

try {
    // 首先检查邮箱是否存在
    $stmt = $pdo->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // 检查新密码是否与旧密码相同
        if ($new_password === $row['password']) {
            echo json_encode(['status' => 'error', 'message' => '新密码不能与旧密码相同']);
            exit;
        }
        
        // 更新密码
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        
        if ($stmt->execute([$new_password, $email])) {
            echo json_encode(['status' => 'success', 'message' => '密码重置成功']);
        } else {
            error_log("Password update failed for email: $email");
            echo json_encode(['status' => 'error', 'message' => '密码更新失败，请稍后重试']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => '该邮箱未注册']);
    }
} catch(PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => '系统错误，请稍后重试']);
} catch(Exception $e) {
    error_log("General error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => '系统错误，请稍后重试']);
}
?> 