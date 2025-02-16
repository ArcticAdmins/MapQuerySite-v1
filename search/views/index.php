<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>主页</title>
    <!-- 引入Bootstrap的CSS文件 -->
    <link href="../static/bootstrap.min.css" rel="stylesheet">
    <script>
        console.info("%c HN CWeb %c Not Open Source ", "color: #fff; margin: 1em 0; padding: 5px 0; background: #2980b9;", "margin: 1em 0; padding: 5px 0; background: #efefef;")
    </script>
    <!-- 自定义样式 -->
    <style>
        .form-group {
            text-align: center; /* 让按钮居中 */
        }

        label {
            text-align: center; /* 让查询名称居中 */
            font-size: larger; /* 让查询名称的字体大一些 */
        }

        .card, .alert {
            margin-bottom: 20px;
        }

        td, th {
            padding: 15px;
        }

        .dropdown-menu {
            left: auto;
            right: 0;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand navbar-light bg-light">
    <a class="navbar-brand pl-3" href="index.php">熊熊地图查询</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto pr-3">
            <?php if (!isset($_SESSION['username'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">登录</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register.php">注册</a>
                </li>
            <?php else: ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        我的信息
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item disabled" href="#">用户名: <?php echo $_SESSION['username']; ?></a>
                        <a class="dropdown-item" href="changepwd.php">更改密码</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php" id="logout">退出登录</a>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<main class="container" style="margin-bottom: 100px;">
    <!-- 查询表单 -->
    <form id="userInputForm" method="post" action="search_process.php">
        <div class="form-group mt-4">
            <label for="query">查询名称</label>
            <input type="text" class="form-control" id="query" name="query" required>
        </div>
        <div class="form-group mt-4 d-flex justify-content-center">
            <?php if (!isset($_SESSION['username'])): ?>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="alert alert-warning" role="alert">
                        请先 <a href="login.php">登录</a> 或 <a href="register.php">注册</a> 后再进行查询
                    </div>
                </div>
            <?php else: ?>
                <button type="submit" class="btn btn-primary mt-0 mb-4">提交</button>
            <?php endif; ?>
        </div>
    </form>

    <!-- 查询信息卡片 -->
    <div class="card mb-3">
        <div class="card-header">
            查询信息
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item" id="userInfo">用户名: 等待查询</li>
            <li class="list-group-item" id="mapQuery">查询参数: 等待查询</li>
            <li class="list-group-item" id="queryTime">查询时间: 等待查询</li>
            <li class="list-group-item" id="queryTotals">查询总数: 等待查询</li>
        </ul>
    </div>

    <!-- 提示信息 -->
    <div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">⚠️注意!</h4>
        <p>如果没有查询到你想要的结果, 请使用/fb [地图名+问题]来反馈</p>
        <p>更多指令请查看/help</p>
    </div>
    <div class="alert alert-warning" role="alert">
        <p class="mb-0">在使用/fb指令前, 你必须要知道的一些事:</p>
        <ul>
            <li>请确保搜索过且结果没有你想要的地图</li>
            <li>请确保你反馈的地图能在kook列表里找到</li>
            <li>请确保你反馈的地图名字跟kook列表里的一致</li>
            <li>请确保你所反馈的是一张地图名字而不是一种玩法/类别</li>
            <li>恶意或不规范使用/fb指令将会被群管理加全局黑名单/禁言甚至踢出</li>
        </ul>
    </div>

    <!-- 匹配地图信息表格 -->
    <div class="table-responsive">
        <table class="table table-striped mt-4">
            <thead>
            <tr>
                <th>地图名</th>
                <th>详细信息</th>
                <th>匹配度</th>
            </tr>
            </thead>
            <tbody id="mapList">
            <!-- 表格内容将通过AJAX动态加载 -->
            </tbody>
        </table>
    </div>
    <?php if (isset($_SESSION['username']) && in_array($_SESSION['username'], ['5278626', '2383615282', '2358494761', '1956013526'])): ?>
        <!-- 一键分享按钮 -->
        <div class="form-group mt-4 d-flex justify-content-center">
            <button id="submitButton" class="btn btn-primary">一键分享</button>
        </div>
    <?php else: ?>
        <div class="alert alert-warning" role="alert">
            <h4 class="alert-heading">⚠️注意!</h4>
            <p>你没有权限使用 <em>一键分享</em> 功能</p>
        </div>
    <?php endif; ?>
</main>

<footer class="footer mt-auto py-3 bg-light">
    <!-- 页脚内容 -->
    <div class="container">
        <span class="text-muted">版权所有 &copy; HeimNad</span>
    </div>
</footer>

<!-- 引入jQuery和Bootstrap的JS文件 -->
<script src="../static/jquery.min.js"></script>
<script src="../static/bootstrap.min.js"></script>
<?php
include '../lib/watermark.php';
?>
<script>
    window.onload = function() {
        $(document).ready(function () {
            var dataCache; // 用于存储 $.ajax 请求的返回数据

            $('#userInputForm').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'post',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (data) {
                        console.log(data)
                        // 填充查询信息
                        $('#userInfo').text('用户名: ' + (data.user_id || '未登录'));
                        $('#mapQuery').text('查询参数: ' + (data.map_query));
                        $('#queryTime').text('查询时间: ' + (data.query_time));
                        $('#queryTotals').text('查到总数: ' + (data.query_totals));

                        // 清空表格并添加新的表格行
                        var tableHtml = '';
                        if (data.matched_map) {
                            $.each(data.matched_map, function (map_name, mapInfo) { // 注意这里改为 map_name
                                tableHtml += '<tr>' +
                                    '<td>' + map_name + '</td>' + // 使用 map_name
                                    '<td>' + mapInfo.details + '</td>' +
                                    '<td>' + (mapInfo.match_ratio * 1).toFixed(1) + '%</td>' +
                                    '</tr>';
                            });
                        }
                        $('#mapList').html(tableHtml);

                        // 将数据存储在 dataCache 中，以便稍后使用
                        dataCache = data;
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert('请求失败: ' + textStatus);
                    }
                });
            });

            // 当 #submitButton 被点击时，发送 $.post 请求
            $('#submitButton').on('click', function () {
                if (dataCache) {
                    // 首先，将 dataCache 中的每个 match_ratio 值小数点往前移动两位
                    if (dataCache.matched_map) {
                        $.each(dataCache.matched_map, function (index, mapInfo) {
                            // 假设 match_ratio 是要修改的属性名
                            mapInfo.match_ratio = mapInfo.match_ratio / 100;
                        });
                    }

                    var jsonData = JSON.stringify(dataCache); // 将修改后的对象转换为 JSON 字符串
                    $.ajax({
                        url: '../index.php',
                        type: 'POST',
                        data: jsonData,
                        contentType: 'application/json', // 设置请求头，告知服务器发送的是 JSON 数据
                        success: function (response) {
                            navigator.clipboard.writeText(response).then(function () {
                                alert('生成的静态网址已复制到剪贴板: ' + '\n' + response);
                            }, function (err) {
                                alert('生成的静态网址: ' + response + '\n但未能复制到剪贴板');
                            });
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            alert('请求失败: ' + textStatus);
                        }
                    });
                }
            });
        });
    }
</script>
</body>
</html>