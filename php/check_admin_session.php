<?php
header('Content-Type: application/json');

session_start();

// 检查管理员会话
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    echo json_encode([
        'status' => 'success',
        'message' => '管理员已登录',
        'admin' => [
            'username' => $_SESSION['admin_username']
        ]
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => '管理员未登录'
    ]);
}
?> 