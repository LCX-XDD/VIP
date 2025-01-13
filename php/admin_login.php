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
    $password = $data['password'] ?? '';

    if (empty($username) || empty($password)) {
        jsonResponse('error', '请填写管理员账号和密码');
    }

    // 查询管理员用户
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = 'admin' AND status = 'active'");
    $stmt->execute([$username]);
    
    if ($user = $stmt->fetch()) {
        // 验证密码
        if (password_verify($password, $user['password'])) {
            // 更新最后登录时间
            $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            // 返回用户信息（不包含密码）
            unset($user['password']);
            jsonResponse('success', '管理员登录成功', [
                'user' => $user
            ]);
        } else {
            jsonResponse('error', '密码错误');
        }
    } else {
        jsonResponse('error', '管理员账号不存在或已被禁用');
    }

} catch (PDOException $e) {
    error_log("Database error in admin_login.php: " . $e->getMessage());
    jsonResponse('error', '数据库错误，请稍后重试');
} catch (Exception $e) {
    error_log("General error in admin_login.php: " . $e->getMessage());
    jsonResponse('error', '系统错误，请稍后重试');
}
?> 