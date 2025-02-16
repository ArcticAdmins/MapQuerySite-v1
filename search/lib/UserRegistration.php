<?php

namespace lib;

require_once 'Database.php';

class UserRegistration
{
    public function processRegistration($username, $password, $invitationCode = null): array
    {
        $config = require '../storage/config.php';

        if (!$config['registration']['open']) {
            return ['code' => 403, 'error' => '注册当前已关闭.'];
        }

        // 检查邀请码
        $inviteCodeFile = '../storage/ic.json';
        $validInviteCodeFound = false;
        $inviter = null;
        $code = null;
        if (file_exists($inviteCodeFile)) {
            $inviteCodes = json_decode(file_get_contents($inviteCodeFile), true);
            foreach ($inviteCodes as $inviter => $codes) {
                foreach ($codes as $code => $details) {
                    // 先检查邀请码是否有效且未被使用
                    if ($code === $invitationCode && !$details['used']) {
                        $validInviteCodeFound = true;
                        break 2;
                    }
                }
            }
            if (!$validInviteCodeFound) {
                return ['code' => 400, 'error' => '邀请码无效或已被使用.'];
            }
        } else {
            return ['code' => 500, 'error' => '邀请码文件不存在.'];
        }

        if ($config['registration']['require_invitation_code']) {
            if (!$this->checkInvitationCode($invitationCode)) {
                return ['code' => 403, 'error' => '邀请码无效.'];
            }
        }

        if (preg_match('/\s/', $username) || preg_match('/<.*?script.*?>/i', $username)) {
            return ['code' => 400, 'error' => '用户名不能包含空格或脚本标签!'];
        }

        $db = Database::getInstance('../storage/config.php');
        $conn = $db->getConnection();

        $stmt = $conn->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);

        if ($stmt->fetch() !== false) {
            return ['code' => 400, 'error' => '用户名已被使用!'];
        }

        $hashedPassword = Utils::hashPassword($password);

        $stmt = $conn->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
        $stmt->execute(['username' => $username, 'password' => $hashedPassword]);

        // 用户注册成功后，再将邀请码标记为已使用
        if ($validInviteCodeFound) {
            $inviteCodes[$inviter][$code]['used'] = true; // 标记为已使用
            file_put_contents($inviteCodeFile, json_encode($inviteCodes)); // 保存修改后的邀请码状态
        }

        return ['code' => 200, 'message' => '用户注册成功!'];
    }

    private function checkInvitationCode($invitationCode): bool
    {
        $inviteCodeFile = '../storage/ic.json';
        if (file_exists($inviteCodeFile)) {
            $inviteCodes = json_decode(file_get_contents($inviteCodeFile), true);
            foreach ($inviteCodes as $inviter => $codes) {
                foreach ($codes as $code => $details) {
                    if ($code === $invitationCode && !$details['used']) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}