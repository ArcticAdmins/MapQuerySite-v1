<?php
session_start();
require_once '../lib/UserLogin.php';
require_once '../lib/Utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => '请求方法不被允许.']);
    exit;
}

$username = \lib\Utils::sanitizeInput($_POST['username']);
$password = \lib\Utils::sanitizeInput($_POST['password']);

$userLogin = new \lib\UserLogin();
$result = $userLogin->processLogin($username, $password);

echo json_encode($result);