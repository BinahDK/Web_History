<?php
session_start();
// 验证：必须是管理员才能访问
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: p1.php?error=无管理员权限");
    exit;
}
include 'db_config.php';

// 处理反馈删除
if (isset($_GET['del_feedback'])) {
    $feedbackId = intval($_GET['del_feedback']);
    $delSql = "DELETE FROM feedback WHERE id = $feedbackId";
    mysqli_query($conn, $delSql);
    header("Location: admin_dashboard.php");
    exit;
}

// 查询所有用户
$userSql = "SELECT id, username, email, is_admin, create_time FROM users ORDER BY id DESC";
$userResult = mysqli_query($conn, $userSql);
$users = mysqli_fetch_all($userResult, MYSQLI_ASSOC);

// 查询所有反馈
$feedbackSql = "SELECT f.*, u.username AS user_name FROM feedback f LEFT JOIN users u ON f.user_id = u.id ORDER BY f.create_time DESC";
$feedbackResult = mysqli_query($conn, $feedbackSql);
$feedbacks = mysqli_fetch_all($feedbackResult, MYSQLI_ASSOC);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员后台 - 中国古代建筑研究</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        :root {
            --amber: #D69E2E;
            --pine: #2D3748;
            --rice: #71613cff;
            --bamboo: #718096;
        }
        body { background-color: var(--rice); font-family: "Ma Shan Zheng", serif; }
        .admin-tag {
            background-color: #e53e3e;
            color: white;
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .user-tag {
            background-color: #38a169;
            color: white;
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 3px;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Ma+Shan+Zheng&display=swap" rel="stylesheet">
</head>
<body>
    <!-- 导航栏 -->
    <nav class="bg-pine text-white py-4 px-6 sticky top-0 z-10">
        <div class="container mx-auto flex justify-between items-center">
            <a href="p1.php" class="text-2xl font-bold flex items-center">
                <i class="fa fa-home mr-2 text-amber"></i>返回首页
            </a>
            <h1 class="text-xl md:text-2xl">管理员后台</h1>
            <div class="flex items-center gap-4">
                <span class="text-amber">管理员：<?php echo $_SESSION['username']; ?></span>
                <a href="logout.php" class="bg-amber/20 hover:bg-amber/30 py-1 px-3 rounded transition-colors">
                    退出登录
                </a>
            </div>
        </div>
    </nav>

    <!-- 核心内容 -->
    <main class="container mx-auto py-12 px-6">
        <!-- 标签页切换 -->
        <div class="max-w-5xl mx-auto mb-8">
            <div class="flex border-b border-bamboo/30">
                <button class="py-2 px-6 text-amber border-b-2 border-amber font-bold" onclick="showTab('user-tab')">用户管理</button>
                <button class="py-2 px-6 text-bamboo hover:text-amber" onclick="showTab('feedback-tab')">反馈管理</button>
            </div>
        </div>

        <!-- 用户管理标签页 -->
        <div id="user-tab" class="max-w-5xl mx-auto bg-white rounded-xl shadow-lg p-8 mb-8">
            <h2 class="text-3xl font-bold text-pine mb-6 text-center">所有用户列表</h2>
            <?php if (empty($users)): ?>
                <div class="text-center text-bamboo text-xl py-10">暂无用户</div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-pine/5">
                                <th class="py-3 px-4 border-b border-bamboo/30 text-left">用户ID</th>
                                <th class="py-3 px-4 border-b border-bamboo/30 text-left">用户名</th>
                                <th class="py-3 px-4 border-b border-bamboo/30 text-left">邮箱</th>
                                <th class="py-3 px-4 border-b border-bamboo/30 text-left">身份</th>
                                <th class="py-3 px-4 border-b border-bamboo/30 text-left">注册时间</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr class="hover:bg-rice">
                                    <td class="py-3 px-4 border-b border-bamboo/30"><?php echo $user['id']; ?></td>
                                    <td class="py-3 px-4 border-b border-bamboo/30"><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td class="py-3 px-4 border-b border-bamboo/30"><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td class="py-3 px-4 border-b border-bamboo/30">
                                        <?php if ($user['is_admin'] == 1): ?>
                                            <span class="admin-tag">管理员</span>
                                        <?php else: ?>
                                            <span class="user-tag">普通用户</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4 border-b border-bamboo/30"><?php echo $user['create_time']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- 反馈管理标签页 -->
        <div id="feedback-tab" class="max-w-5xl mx-auto bg-white rounded-xl shadow-lg p-8 hidden">
            <h2 class="text-3xl font-bold text-pine mb-6 text-center">所有反馈列表</h2>
            <?php if (empty($feedbacks)): ?>
                <div class="text-center text-bamboo text-xl py-10">暂无反馈</div>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($feedbacks as $item): ?>
                        <div class="border border-bamboo/30 rounded-lg p-6 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-xl text-pine font-bold">
                                    反馈人：<?php echo htmlspecialchars($item['name']); ?>（<?php echo htmlspecialchars($item['user_name']); ?>）
                                </h3>
                                <div class="flex gap-3">
                                    <span class="text-bamboo text-sm"><?php echo $item['create_time']; ?></span>
                                    <a href="?del_feedback=<?php echo $item['id']; ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('确定删除该反馈吗？')">
                                        <i class="fa fa-trash"></i> 删除
                                    </a>
                                </div>
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

    <!-- 页脚 -->
    <footer class="bg-pine text-white py-6 mt-12">
        <div class="container mx-auto text-center text-bamboo">
            <p>© 2025 中国古代建筑研究项目 | 管理员后台</p>
        </div>
    </footer>

    <script>
        // 标签页切换逻辑
        function showTab(tabId) {
            // 隐藏所有标签页
            document.getElementById('user-tab').classList.add('hidden');
            document.getElementById('feedback-tab').classList.add('hidden');
            // 重置按钮样式
            document.querySelectorAll('.flex button').forEach(btn => {
                btn.classList.remove('text-amber', 'border-b-2', 'border-amber', 'font-bold');
                btn.classList.add('text-bamboo', 'hover:text-amber');
            });
            // 显示目标标签页
            document.getElementById(tabId).classList.remove('hidden');
            // 激活当前按钮
            event.target.classList.add('text-amber', 'border-b-2', 'border-amber', 'font-bold');
            event.target.classList.remove('text-bamboo', 'hover:text-amber');
        }
    </script>
</body>
</html>