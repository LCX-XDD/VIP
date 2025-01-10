<?php
// 禁用错误显示
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
require_once 'db_connect.php';

try {
    // 获取POST数据
    $input = file_get_contents('php://input');
    if (!$input) {
        throw new Exception('无效的请求数据');
    }
    
    $data = json_decode($input, true);
    if (!$data) {
        throw new Exception('无效的JSON格式');
    }
    
    $username = isset($data['username']) ? trim($data['username']) : '';
    $email = isset($data['email']) ? trim($data['email']) : '';
    $password = isset($data['password']) ? $data['password'] : '';

    // 验证必填字段
    if (empty($password)) {
        throw new Exception('请输入密码');
    }
    if (empty($username) && empty($email)) {
        throw new Exception('请至少输入用户名或邮箱');
    }

    // 当用户名和邮箱都不为空时的特殊验证
    if (!empty($username) && !empty($email)) {
        // 检查用户名和邮箱是否匹配同一用户
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND email = ?");
        $stmt->execute([$username, $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // 分别检查用户名和邮箱是否存在
            $stmt = $conn->prepare("SELECT username, email FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $usernameExists = false;
            $emailExists = false;
            $belongsToDifferentUsers = false;
            
            foreach ($results as $result) {
                if ($result['username'] === $username) $usernameExists = true;
                if ($result['email'] === $email) $emailExists = true;
                // 检查是否属于不同用户
                if (($result['username'] === $username && $result['email'] !== $email) ||
                    ($result['email'] === $email && $result['username'] !== $username)) {
                    $belongsToDifferentUsers = true;
                }
            }
            
            if ($belongsToDifferentUsers) {
                throw new Exception('用户名和邮箱不匹配');
            } elseif (!$usernameExists && !$emailExists) {
                throw new Exception('用户名和邮箱均不存在');
            } elseif (!$usernameExists) {
                throw new Exception('用户名不存在');
            } elseif (!$emailExists) {
                throw new Exception('邮箱不存在');
            }
        }
    } else {
        // 只有用户名或邮箱的情况
        $conditions = array();
        $params = array();
        
        if (!empty($username)) {
            $conditions[] = "username = ?";
            $params[] = $username;
        }
        if (!empty($email)) {
            $conditions[] = "email = ?";
            $params[] = $email;
        }

        $sql = "SELECT * FROM users WHERE " . implode(" OR ", $conditions);
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception('用户不存在');
        }
    }

    // 验证密码
    if (!password_verify($password, $user['password'])) {
        throw new Exception('密码错误');
    }

    // 更新最后登录时间
    $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $updateStmt->execute([$user['id']]);

    // 准备返回的用户数据
    $userData = array(
        'id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'created_at' => $user['created_at'],
        'last_login' => $user['last_login'],
        'status' => $user['status']
    );

    echo json_encode([
        'status' => 'success',
        'message' => '登录成功',
        'data' => ['user' => $userData]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

exit;
?> 