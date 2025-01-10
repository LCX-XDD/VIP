<?php
// 禁用错误显示
error_reporting(0);
ini_set('display_errors', 0);

try {
    // 数据库连接配置
    $host = 'localhost';
    $dbname = 'user_auth';           // 修改为你的数据库名
    $username = 'root';        // 修改为你的数据库用户名
    $password = '123456';           // 修改为你的数据库密码
    
    // 创建PDO连接
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    );
    
    $conn = new PDO($dsn, $username, $password, $options);
    
} catch(PDOException $e) {
    // 记录错误日志（可选）
    error_log("Database Connection Error: " . $e->getMessage());
    
    // 返回JSON格式的错误信息
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'error',
        'message' => '数据库连接失败，请检查配置'
    ]);
    exit;
}

// 设置时区（可选）
date_default_timezone_set('Asia/Shanghai');
?> 