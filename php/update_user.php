<?php
header('Content-Type: application/json');
require_once 'admin_session.php';

// 验证管理员会话
checkAdminSession();

// 数据库连接配置
$db_host = 'localhost';
$db_user = 'root';
$db_password = '123456';
$db_name = 'user_auth';

// 创建数据库连接
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// 检查连接
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => '数据库连接失败：' . $conn->connect_error]));
}

// 设置编码
$conn->set_charset("utf8mb4");

// 获取POST数据
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? 0;
$username = $data['username'] ?? '';
$email = $data['email'] ?? '';

// 验证数据
if ($id === 0 || empty($username) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => '所有字段都必须填写']);
    exit;
}

// 检查用户名是否已被其他用户使用
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
$stmt->bind_param("si", $username, $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => '用户名已存在']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// 检查邮箱是否已被其他用户使用
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
$stmt->bind_param("si", $email, $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => '邮箱已被注册']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// 更新用户信息
$stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
$stmt->bind_param("ssi", $username, $email, $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => '更新用户成功',
            'user' => [
                'id' => $id,
                'username' => $username,
                'email' => $email
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => '用户不存在']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => '更新用户失败：' . $stmt->error]);
}

$stmt->close();
$conn->close();
?> 