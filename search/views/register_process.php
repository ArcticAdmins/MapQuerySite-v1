<?php
session_start();
require_once '../lib/UserRegistration.php';
require_once '../lib/Utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => '请求方法不被允许.']);
    exit;
}

// CSRF Token 验证失败的处理
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    error_log('CSRF Token 验证失败.');
    http_response_code(403);
    echo json_encode(['error' => 'CSRF Token 验证失败.']);
    exit;
}

$username = \lib\Utils::sanitizeInput($_POST['username']);
$password = \lib\Utils::sanitizeInput($_POST['password']);
$invitationCode = isset($_POST['invitationCode']) ? \lib\Utils::sanitizeInput($_POST['invitationCode']) : null;

$userRegistration = new \lib\UserRegistration();
$result = $userRegistration->processRegistration($username, $password, $invitationCode);

if ($result['code'] !== 200) {
    error_log('注册失败, 错误码: ' . $result['code']);
}

// 处理注册结果
if ($result['code'] === 200) {
    $_SESSION['username'] = $username;
    http_response_code(200); // 成功注册
} else {
    http_response_code($result['code']); // 根据错误类型设置状态码
}

echo json_encode($result);