<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html?error=请先登录后提交反馈");
    exit;
}

// 引入数据库配置
include 'db_config.php';

// 处理反馈提交
$submitMsg = '';
$msgType = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $content = trim($_POST['feedback']);
    $userId = $_SESSION['user_id'];
    $username = $_SESSION['username'];

    // 验证输入
    if (empty($name) || empty($email) || empty($content)) {
        $submitMsg = '所有字段不能为空！';
        $msgType = 'error';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $submitMsg = '请输入有效的邮箱地址！';
        $msgType = 'error';
    } else {
        // 插入反馈到数据库
        $sql = "INSERT INTO feedback (user_id, username, name, email, content) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "issss", $userId, $username, $name, $email, $content);
        
        if (mysqli_stmt_execute($stmt)) {
            $submitMsg = '反馈提交成功！我们会尽快处理您的意见，感谢支持～';
            $msgType = 'success';
            // 清空表单
            $_POST = [];
        } else {
            $submitMsg = '提交失败，请重试！';
            $msgType = 'error';
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>意见反馈 - 中国古代建筑研究</title>
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
        .nav-link:hover { color: var(--amber)!important; }
        .msg-success {
            background-color: #d4edda;
            color: #155724;
        }
        .msg-error {
            background-color: #f8d7da;
            color: #721c24;
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
            <h1 class="text-xl md:text-2xl">意见反馈</h1>
            <!-- 新增：登录状态显示 + 查看反馈入口 -->
            <div class="flex items-center gap-4">
                <span class="text-amber">欢迎，<?php echo $_SESSION['username']; ?></span>
                <a href="view_feedback.php" class="bg-amber/20 hover:bg-amber/30 py-1 px-3 rounded transition-colors">
                    查看所有反馈
                </a>
            </div>
        </div>
    </nav>

    <!-- 核心内容 -->
    <main class="container mx-auto py-12 px-6">
        <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-3xl font-bold text-pine mb-6 text-center">欢迎提出宝贵意见</h2>
            
            <!-- 提交结果提示 -->
            <?php if ($submitMsg): ?>
                <div class="mb-6 p-3 rounded-lg text-center <?php echo $msgType === 'success' ? 'msg-success' : 'msg-error'; ?>">
                    <?php echo $submitMsg; ?>
                </div>
            <?php endif; ?>
            
            <!-- 改造表单：提交到自身，POST方法 -->
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-pine font-bold mb-2" for="name">
                        <i class="fa fa-user text-amber mr-2"></i>姓名
                    </label>
                    <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" class="w-full px-4 py-2 border border-bamboo rounded-lg focus:outline-none focus:border-amber" placeholder="请输入您的姓名" required>
                </div>
                <div>
                    <label class="block text-pine font-bold mb-2" for="email">
                        <i class="fa fa-envelope text-amber mr-2"></i>联系邮箱
                    </label>
                    <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" class="w-full px-4 py-2 border border-bamboo rounded-lg focus:outline-none focus:border-amber" placeholder="请输入您的邮箱" required>
                </div>
                <div>
                    <label class="block text-pine font-bold mb-2" for="feedback">
                        <i class="fa fa-comment text-amber mr-2"></i>反馈内容
                    </label>
                    <textarea id="feedback" name="feedback" rows="6" class="w-full px-4 py-2 border border-bamboo rounded-lg focus:outline-none focus:border-amber" placeholder="请描述您的意见/建议/问题..." required><?php echo isset($_POST['feedback']) ? htmlspecialchars($_POST['feedback']) : ''; ?></textarea>
                </div>
                <div class="text-center">
                    <button type="submit" class="bg-amber hover:bg-amber/80 text-pine font-bold py-3 px-8 rounded-lg transition-colors">
                        提交反馈
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center text-bamboo">
                <p><i class="fa fa-info-circle text-amber mr-2"></i>反馈处理周期：1-3个工作日</p>
                <p class="mt-2">我们会通过邮箱回复您的反馈结果，感谢支持！</p>
            </div>
        </div>
    </main>

    <!-- 页脚 -->
    <footer class="bg-pine text-white py-6 mt-12">
        <div class="container mx-auto text-center text-bamboo">
            <p>© 2025 中国古代建筑研究项目 | 意见反馈专线</p>
        </div>
    </footer>
</body>
</html>