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

// 获取用户ID
$id = $_GET['id'] ?? 0;

// 验证ID不为0
if ($id === 0) {
    echo json_encode(['status' => 'error', 'message' => '无效的用户ID']);
    exit;
}

// 获取用户信息
$stmt = $conn->prepare("SELECT id, username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode([
        'status' => 'success',
        'user' => $user
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => '用户不存在']);
}

$stmt->close();
$conn->close();
?> 