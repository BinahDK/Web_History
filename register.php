<?php
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // 验证输入
    if (empty($username) || empty($email) || empty($password)) {
        header("Location: register.html?error=所有字段不能为空");
        exit;
    }
    if (strlen($password) < 6) {
        header("Location: register.html?error=密码长度不能少于6位");
        exit;
    }

    // 检查用户名/邮箱是否重复
    $sql_check = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "ss", $username, $email);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        header("Location: register.html?error=用户名或邮箱已存在");
        exit;
    }

    // 密码加密
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 插入用户
    $sql_insert = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "sss", $username, $email, $hashed_password);

    if (mysqli_stmt_execute($stmt_insert)) {
        header("Location: register.html?success=1");
        exit;
    } else {
        header("Location: register.html?error=注册失败，请重试");
        exit;
    }
}

mysqli_close($conn);
?>