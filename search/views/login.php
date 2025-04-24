<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>登录</title>
    <!-- 引入Bootstrap的CSS文件 -->
    <!-- 引入Bootstrap的CSS文件 -->
    <link href="../static/bootstrap.min.css" rel="stylesheet">
    <script>
        console.info("%c HN CWeb %c Not Open Source ", "color: #fff; margin: 1em 0; padding: 5px 0; background: #2980b9;", "margin: 1em 0; padding: 5px 0; background: #efefef;")
    </script>
    <!-- 引入jQuery和Bootstrap的JS文件 -->
    <script src="../static/jquery.min.js"></script>
    <script src="../static/bootstrap.min.js"></script>
</head>
<body>
<!-- Bootstrap Modal for Success Messages -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">✅成功</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="successModalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Modal for Error Messages -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">❌错误</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="errorModalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand pl-3" href="index.php">熊熊地图查询</a>
</nav>

<main>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title text-center">登录</h2>
                        <form id="loginForm">
                            <div class="form-group">
                                <label for="username">用户名</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">密码</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">登录</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="footer mt-auto py-3 bg-light fixed-bottom">
    <div class="container">
        <span class="text-muted">版权所有 &copy; HeimNad</span>
    </div>
</footer>
<?php
include '../lib/watermark.php';
?>
<script>
    document.querySelector('#loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('login_process.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('网络错误, 请稍后再试.');
            }
            return response.json();
        })
        .then(data => {
            if (data.code === 200) {
                // 将用户名存储在 sessionStorage 中
                sessionStorage.setItem('username', formData.get('username'));
                document.querySelector('#successModalBody').textContent = '登录成功! 即将跳转到主页.';
                $('#successModal').modal('show');
                setTimeout(function() {
                    window.location.href = 'index.php';
                },1500);
            } else if (data.code === 400) {
                document.querySelector('#errorModalBody').textContent = data.error;
                $('#errorModal').modal('show');
            }
        })
        .catch(error => {
            console.error('fetch 操作出现问题: ', error);
            document.querySelector('#errorModalBody').textContent = '登录失败, 请查看控制台.';
            $('#errorModal').modal('show');
        });
});
</script>
</body>
</html>