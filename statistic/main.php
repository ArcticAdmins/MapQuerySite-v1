<?php
// 设置文件路径
$file_path = '../storage/map_info.json';

// 检查文件是否存在
if (!file_exists($file_path)) {
    http_response_code(404);
    echo json_encode(['message' => 'File not found']);
    exit();
}

// 获取文件名
$file_name = basename($file_path);

// 获取文件大小
$file_size_bytes = filesize($file_path);

// 将文件大小转换为兆字节（MB）
$file_size_mb = round($file_size_bytes / 1048576, 2);

// 获取文件更新时间
$file_update_time = date("Y-m-d H:i:s", filemtime($file_path));

// 读取文件内容
$file_content = file_get_contents($file_path);

// 解码 JSON 文件的内容
$json_content = json_decode($file_content, true);

// 计算内容项的总数
$item_count = count($json_content);

// 获取数组的最后一个元素
$latest_map = end($json_content);

// 获取最后一个元素的键
$latest_map_name = key($json_content);

// 将地图的名字和详细信息合并为一个新的数组
$latest_map = [$latest_map_name => $latest_map];

// 初始化单人地图和多人地图的计数器
$single_map_count = 0;
$multi_map_count = 0;
$single_multi_map_count = 0;

// 遍历 JSON 文件的内容
foreach ($json_content as $item) {
    // 检查地图类型并更新计数器
    if ($item['type'] == '1') {
        $single_map_count++;
    } elseif ($item['type'] == '2') {
        $multi_map_count++;
    } elseif ($item['type'] == '3') {
        $single_multi_map_count++;
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Invalid map type']);
        exit();
    }
}

// 返回信息
echo json_encode([
    'file_name' => $file_name,
    'file_size' => $file_size_mb . ' MB',
    'file_update_time' => $file_update_time,
    'item_count' => $item_count,
    'single_map_count' => $single_map_count,
    'multi_map_count' => $multi_map_count,
    'single_multi_map_count' => $single_multi_map_count,
    'latest_map' => $latest_map
]);
?>