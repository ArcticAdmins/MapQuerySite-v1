<?php

namespace lib;

require_once 'Database.php';

class UserLogin
{
    public function processLogin($username, $password): array
    {
        $db = Database::getInstance('../storage/config.php');
        $conn = $db->getConnection();

        $stmt = $conn->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);

        $user = $stmt->fetch();

        if ($user === false || !password_verify($password, $user['password'])) {
            return ['code' => 400, 'error' => '用户名或密码错误!'];
        }

        $_SESSION['username'] = $username;

        return ['code' => 200, 'message' => '用户登录成功!'];
    }
}