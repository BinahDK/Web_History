<?php
session_start();
// 清除所有会话
session_unset();
session_destroy();
// 跳回首页
header("Location: p1.php");
exit;
?>