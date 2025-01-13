<?php
// 设置错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // 连接到 MySQL（不指定数据库）
    $pdo = new PDO(
        "mysql:host=localhost",
        "root",
        "123456",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // 读取 SQL 文件
    $sql = file_get_contents('../setup_database.sql');

    // 执行 SQL 语句
    $pdo->exec($sql);

    echo "数据库和表创建成功！";

} catch(PDOException $e) {
    die("错误: " . $e->getMessage());
}
?> 