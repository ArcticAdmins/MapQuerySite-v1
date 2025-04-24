<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_map_info_list = json_decode(file_get_contents('php://input'), true);

    $old_map_info = [];

    // 设置文件路径
    $file_path = '../storage/map_info.json';

    // 检查文件夹是否存在
    if (!is_dir(dirname($file_path))) {
        // 如果文件夹不存在，创建文件夹
        mkdir(dirname($file_path), 0777, true);
    }

    // 检查文件是否存在
    if (!file_exists($file_path)) {
        // 如果文件不存在，创建一个新的空文件
        file_put_contents($file_path, '');
    } else {
        // 如果文件存在，检查文件是否为空
        if (filesize($file_path) > 0) {
            // 如果文件不为空，读取文件中的旧数据
            $old_map_info = json_decode(file_get_contents($file_path), true);
        }
    }

    // 检查新提交的数据中的 map_name 是否在旧数据中
    foreach ($new_map_info_list as $new_map_info) {
        foreach ($new_map_info as $new_map_name => $new_map_details) {
            if (array_key_exists($new_map_name, $old_map_info)) {
                // 如果有重复的数据，返回一个错误消息和重复的数据
                http_response_code(400);
                echo json_encode(['message' => '重复的地图名: ' . $new_map_name, 'duplicate' => $new_map_name]);
                exit();
            } else {
                // 将新的数据添加到 old_map_info 的后面
                $old_map_info[$new_map_name] = $new_map_details;
            }
        }
    }

    // 将 old_map_info 写入文件
    file_put_contents($file_path, json_encode($old_map_info, JSON_UNESCAPED_UNICODE));

    echo json_encode(['message' => 'Success']);
    http_response_code(200);
}
?>