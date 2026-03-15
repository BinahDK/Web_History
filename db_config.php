<?php
// WAMP专属配置（默认无密码）
$db_host = "localhost";
$db_user = "root";
$db_pass = ""; // WAMP默认密码为空，不要改
$db_name = "ancient_architecture";

// 连接数据库
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// 检查连接
if (!$conn) {
    die("数据库连接失败: " . mysqli_connect_error() . "<br>请检查WAMP的MySQL是否启动，或数据库配置是否正确");
}

// 设置中文编码
mysqli_set_charset($conn, "utf8");
?>