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

// 验证ID不为0
if ($id === 0) {
    echo json_encode(['status' => 'error', 'message' => '无效的用户ID']);
    exit;
}

// 删除用户
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => '删除用户成功'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => '用户不存在']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => '删除用户失败：' . $stmt->error]);
}

$stmt->close();
$conn->close();
?> 