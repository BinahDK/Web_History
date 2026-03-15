<?php
session_start();
// 仅登录用户可访问
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html?error=请先登录");
    exit;
}
include 'db_config.php';

// 普通用户：仅查看自己的反馈；管理员：查看所有反馈
if ($_SESSION['is_admin'] == 1) {
    $sql = "SELECT * FROM feedback ORDER BY create_time DESC";
} else {
    $userId = $_SESSION['user_id'];
    $sql = "SELECT * FROM feedback WHERE user_id = $userId ORDER BY create_time DESC";
}

$result = mysqli_query($conn, $sql);
$feedbacks = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_close($conn);
?>
<!-- 原有HTML内容不变 -->
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查看反馈 - 中国古代建筑研究</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        :root {
            --amber: #D69E2E;
            --pine: #2D3748;
            --rice: #FAF6ED;
            --bamboo: #718096;
        }
        body { background-color: var(--rice); font-family: "Ma Shan Zheng", serif; }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Ma+Shan+Zheng&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="bg-pine text-white py-4 px-6 sticky top-0 z-10">
        <div class="container mx-auto flex justify-between items-center">
            <a href="p1.php" class="text-2xl font-bold flex items-center">
                <i class="fa fa-home mr-2 text-amber"></i>返回首页
            </a>
            <h1 class="text-xl md:text-2xl">所有用户反馈</h1>
            <a href="yijian.php" class="bg-amber hover:bg-amber/80 text-pine font-bold py-1 px-3 rounded-lg transition-colors">
                提交新反馈
            </a>
        </div>
    </nav>

    <main class="container mx-auto py-12 px-6">
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-3xl font-bold text-pine mb-6 text-center">用户反馈列表</h2>
            
            <?php if (empty($feedbacks)): ?>
                <div class="text-center text-bamboo text-xl py-10">
                    暂无用户反馈
                </div>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($feedbacks as $item): ?>
                        <div class="border border-bamboo/30 rounded-lg p-6 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-xl text-pine font-bold">
                                    反馈人：<?php echo htmlspecialchars($item['name']); ?>（<?php echo htmlspecialchars($item['username']); ?>）
                                </h3>
                                <span class="text-bamboo text-sm">
                                    <?php echo $item['create_time']; ?>
                                </span>
                            </div>
                            <div class="mb-3">
                                <i class="fa fa-envelope text-amber mr-2"></i>
                                <span class="text-bamboo">邮箱：<?php echo htmlspecialchars($item['email']); ?></span>
                            </div>
                            <div class="mt-4">
                                <p class="text-pine leading-relaxed">
                                    <?php echo nl2br(htmlspecialchars($item['content'])); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="bg-pine text-white py-6 mt-12">
        <div class="container mx-auto text-center text-bamboo">
            <p>© 2025 中国古代建筑研究项目 | 意见反馈管理</p>
        </div>
    </footer>
</body>
</html>