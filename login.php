<?php
session_start();
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        header("Location: login.html?error=用户名和密码不能为空");
        exit;
    }

    // 预处理查询，防止注入
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        // 验证加密密码
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: p1.php"); // 跳回首页
            exit;
        } else {
            header("Location: login.html?error=密码错误");
            exit;
        }
    } else {
        header("Location: login.html?error=用户名不存在");
        exit;
    }
}

mysqli_close($conn);
?>