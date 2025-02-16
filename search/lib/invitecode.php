<?php
header('Content-Type: application/json');

$inviteCodeFile = '../storage/ic.json';

// 处理 GET 请求
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($inviteCodeFile)) {
        $inviteCodes = json_decode(file_get_contents($inviteCodeFile), true);
        echo json_encode($inviteCodes);
    } else {
        http_response_code(404);
        echo json_encode(['error' => '邀请码文件不存在.']);
    }
    exit;
}

// 处理 POST 请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newInviteCode = json_decode(file_get_contents('php://input'), true);

    if (file_exists($inviteCodeFile)) {
        $inviteCodes = json_decode(file_get_contents($inviteCodeFile), true);
    } else {
        $inviteCodes = [];
    }

    $inviteCodes = array_replace_recursive($inviteCodes, $newInviteCode);
    file_put_contents($inviteCodeFile, json_encode($inviteCodes));

    echo json_encode(['message' => '邀请码添加成功.']);
    exit;
}

http_response_code(405);
echo json_encode(['error' => '请求方法不被允许.']);