<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $conn = getDBConnection();
    
    // 获取用户总数
    $countQuery = "SELECT COUNT(*) as total FROM users";
    $countResult = $conn->query($countQuery);
    if (!$countResult) {
        throw new Exception('查询用户总数失败: ' . $conn->error);
    }
    $totalUsers = $countResult->fetch_assoc()['total'];
    
    // 获取分页参数
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;
    
    // 获取用户列表
    $query = "SELECT id, username, email, created_at FROM users ORDER BY id DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('准备查询语句失败: ' . $conn->error);
    }
    
    $stmt->bind_param('ii', $limit, $offset);
    if (!$stmt->execute()) {
        throw new Exception('执行查询失败: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'id' => $row['id'],
            'username' => $row['username'],
            'email' => $row['email'],
            'created_at' => $row['created_at']
        ];
    }
    
    // 返回结果
    echo json_encode([
        'status' => 'success',
        'data' => [
            'users' => $users,
            'total' => $totalUsers,
            'page' => $page,
            'limit' => $limit
        ]
    ]);
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    error_log('Error in get_users.php: ' . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => '获取用户列表失败: ' . $e->getMessage()
    ]);
}
?> 