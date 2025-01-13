<?php
header('Content-Type: application/json');

session_start();

// 清除管理员会话
unset($_SESSION['admin_logged_in']);
session_destroy();

echo json_encode([
    'status' => 'success',
    'message' => '退出成功'
]);
?> 