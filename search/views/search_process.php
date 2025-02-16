<?php
session_start();

require_once __DIR__ . '/../lib/Utils.php';

// 如果用户未登录，返回错误信息并退出
if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => '请先登录后再进行查询']);
    exit;
}

// 配置文件路径
$configPath = __DIR__ . '/../storage/config.php';
$config = require_once $configPath;

$query = $_POST['query'] ?? '';
$userInputSplit = \lib\Utils::split_str($query);

// 从 JSON 文件中获取数据，添加错误处理
$db = $config['storage']['json_db'];
$json = @file_get_contents($db);
if ($json === false) {
    // 处理错误，例如返回错误信息
    echo json_encode(['error' => '无法读取数据库文件']);
    exit;
}
$maps = json_decode($json, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    // 处理JSON解码错误
    echo json_encode(['error' => 'JSON解码错误']);
    exit;
}

// 使用 implode 函数将用户输入的字符数组组合成一个字符串
$userInputCombined = implode('', $userInputSplit);
// 将数据库中的键名按照单个字符分割
$keysSplit = array_map('\lib\Utils::split_str', array_keys($maps));

// 对用户输入和数据库数据进行关键字匹配
$matches = [];
foreach ($keysSplit as $keySplit) {
    $keyCombined = implode('', $keySplit);
    $ratio = \lib\Utils::similar_text_cn($userInputCombined, $keyCombined);
    if ($ratio > 0) { // 只接受匹配比例大于0的匹配
        $matches[$keyCombined] = $ratio;
    }
}

// 按照匹配程度从高到低排序
arsort($matches);

// 遍历 matches 列表
$matchedMaps = [];
$queryTotals = 0;
foreach ($matches as $key => $ratio) {
    // 获取地图的详细信息和参数
    if (isset($maps[$key])) {
        $details = $maps[$key];

        // 将匹配比例转换为百分比并保留小数
        $matchRatioPercent = number_format($ratio * 100, 3);

        // 将地图添加到 matched_maps 数组中
        $matchedMaps[$key] = [
            "details" => $details,
            "match_ratio" => $matchRatioPercent
        ];

        $queryTotals += 1;
    }
}

$data = [
    "user_id" => $_SESSION['username'] ?? null,
    "map_query" => $userInputCombined,
    "query_time" => date('Y-m-d H:i:s'),
    "matched_map" => $matchedMaps,
    "query_totals" => $queryTotals
];

echo json_encode($data);