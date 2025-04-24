<?php
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!is_array($data)) {
    // Handle error here. For example, you can send a response with an error message.
    echo "Error: Failed to decode JSON.";
    exit;
}

$user_id = $data['user_id'];
$map_query = $data['map_query'];
$query_time = $data['query_time'];
$matched_map = $data['matched_map'] ?? [];
$query_totals = $data['query_totals'];

ob_start(); // 开始输出缓冲
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>熊熊地图查询 - Website</title>
    <link href="../static/bootstrap.min.css" rel="stylesheet">
    <script src=../static/jquery.min.js"></script>
    <script src="../static/bootstrap.min.js"></script>
    <style>
        .table-responsive {
            overflow-x: auto;
            min-height: 0.01%;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <!-- 查询信息卡片 -->
    <div class="card mb-3">
        <div class="card-header">
            查询信息
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">查询用户 ID: <span id="userId"><?php echo htmlspecialchars(isset($user_id) ? $user_id : ''); ?></span></li>
            <li class="list-group-item">查询地图参数: <span id="mapQuery"><?php echo htmlspecialchars(isset($map_query) ? $map_query : ''); ?></span></li>
            <li class="list-group-item">查询时间: <span id="queryTime"><?php echo htmlspecialchars(isset($query_time) ? $query_time : ''); ?></span></li>
            <li class="list-group-item">查到总数: <span id="queryTotals"><?php echo htmlspecialchars(isset($query_totals) ? $query_totals : ''); ?></span></li>
        </ul>
    </div>
    <!-- 提示信息 -->
    <div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">⚠️注意!</h4>
        <p>如果没有查询到你想要的结果, 请使用"/fb 地图名+问题"(地图名+问题不要带空格) 来反馈</p>
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
            <tbody>
            <?php if (is_array($matched_map) || is_object($matched_map)) : ?>
                <?php foreach ($matched_map as $map_name => $map_info): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($map_name); ?></td>
                        <td><?php echo htmlspecialchars($map_info['details']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($map_info['match_ratio'] * 100, 2)) . '%'; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

<?php
$html = ob_get_clean(); // 获取缓冲区内容并清除缓冲区

// 生成一个基于用户 ID 和当前微秒时间戳的 MD5 哈希值
$hash = md5($user_id . microtime());

// 将哈希值分割成几个部分，并用 "-" 连接起来
$uuid = substr($hash, 0, 8) . '-' . substr($hash, 8, 8) . '-' . substr($hash, 16, 8) . '-' . substr($hash, 24, 8);

// 生成一个基于用户 ID 和当前微秒时间戳的唯一文件名
$filename = 'result/' . $uuid . '.html';

// 将 HTML 代码保存到文件中
file_put_contents($filename, $html);

// 检查文件数量
$dir = './result/';

$files = glob("$dir/*.html");

$one_week_ago = time() - 7 * 24 * 60 * 60; // 一周前的时间戳

foreach ($files as $file) {
    if (filemtime($file) < $one_week_ago) {
        // 删除一周前的文件
        unlink($file);
    }
}

// 返回文件的 URL
echo 'https://icebear.heimnad.top/search/' . $filename;
?>