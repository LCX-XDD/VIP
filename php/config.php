<?php
// 禁用错误显示
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// 设置错误日志
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

// 捕获所有错误并转换为异常
set_error_handler(function($severity, $message, $file, $line) {
    error_log("PHP Error: [$severity] $message in $file on line $line");
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// 设置未捕获的异常处理器
set_exception_handler(function($e) {
    error_log("Uncaught Exception: " . $e->getMessage());
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => '系统错误，请稍后重试',
        'debug' => [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ], JSON_UNESCAPED_UNICODE);
    exit;
});

// 确保响应为 JSON
ob_start();

// 设置响应头
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 如果是 OPTIONS 请求，直接返回200
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 数据库配置
define('DB_HOST', 'localhost');
define('DB_NAME', 'user_auth');
define('DB_USER', 'root');
define('DB_PASS', '123456');
define('DB_CHARSET', 'utf8mb4');

// 通用的 JSON 响应函数
function jsonResponse($status, $message, $data = null) {
    $response = ['status' => $status, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    // 清除之前的输出缓冲
    if (ob_get_length()) ob_clean();
    
    // 设置响应码和头部
    http_response_code($status === 'error' ? 400 : 200);
    header('Content-Type: application/json; charset=utf-8');
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // 连接数据库
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    // 检查数据库是否存在
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'");
    $dbExists = $stmt->fetch();

    if (!$dbExists) {
        // 创建数据库
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    // 选择数据库
    $pdo->exec("USE `" . DB_NAME . "`");

    // 创建用户表（如果不存在）
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(20) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // 检查是否需要创建默认管理员账户
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    if ($stmt->fetchColumn() == 0) {
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute(['admin', 'admin@example.com', $adminPassword]);
    }

} catch(PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    jsonResponse('error', '数据库连接失败：' . $e->getMessage());
} catch(Exception $e) {
    error_log("General error: " . $e->getMessage());
    jsonResponse('error', '系统错误，请稍后重试');
}

// 注册关闭函数，确保总是返回 JSON
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && ($error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR))) {
        ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'error',
            'message' => '系统错误，请稍后重试',
            'debug' => $error
        ], JSON_UNESCAPED_UNICODE);
    }
});
?> 