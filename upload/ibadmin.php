<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qgroup_id = $_POST['qgroup_id'];
    $quser_id = $_POST['quser_id'];

    $post_url = "https://icebear.heimnad.top/search/lib/invitecode.php";

    // 获取当前的邀请码数据
    $invite_codes_data = json_decode(file_get_contents($post_url), true);
    if (!is_array($invite_codes_data)) {
        $invite_codes_data = [];
    }

    // 提取所有的邀请码
    $invite_codes = [];
    foreach ($invite_codes_data as $user_codes) {
        foreach ($user_codes as $code => $value) {
            $invite_codes[] = $code;
        }
    }

    // 生成新的邀请码
    do {
        $new_invite_code = bin2hex(random_bytes(4));
    } while (in_array($new_invite_code, $invite_codes));

    // 在当前的邀请码数据中添加新的邀请码
    if (!isset($invite_codes_data[$qgroup_id])) {
        $invite_codes_data[$qgroup_id] = [];
    }
    $invite_codes_data[$qgroup_id][$new_invite_code] = [
        'for' => $quser_id,
        'used' => false
    ];

    // 将更新后的邀请码数据发送回服务器
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($invite_codes_data),
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($post_url, false, $context);
    $response = json_decode($result, true);

    if ($response['message'] === '邀请码添加成功.') {
        echo json_encode(['message' => "邀请码: $new_invite_code 已生成, 用于邀请用户: $quser_id", 'invite_code' => $new_invite_code]);
    } else {
        echo json_encode(['message' => "邀请码生成失败."]);
    }
} else {
    // 显示邀请表单的HTML代码
    echo '
    <!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>生成邀请码</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    </head>
    <body>
        <div class="container mt-5">
            <h1 class="mb-4">生成邀请码</h1>
            <form id="inviteForm">
                <div class="form-group">
                    <label for="qgroup_id">群组ID</label>
                    <select class="form-control" id="qgroup_id" name="qgroup_id" required>
                        <option value="975354009">1 群-975354009</option>
                        <option value="808489237">2 群-808489237</option>
                        <option value="670927144">3 群-670927144</option>
                        <option value="891404371">4 群-891404371</option>
                        <option value="907346829">5 群-907346829</option>
                        <option value="428391473">6 群-428391473</option>
                        <option value="669481964">7 群-669481964</option>
                        <option value="238415217">熊熊猫猫地图吃🍉 群-238415217</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="quser_id">被邀请用户ID</label>
                    <input type="text" class="form-control" id="quser_id" name="quser_id" required>
                </div>
                <button type="submit" class="btn btn-primary">生成邀请码</button>
            </form>
            <div id="result" class="mt-3"></div>
        </div>

        <script>
            $(document).ready(function() {
                $("#inviteForm").on("submit", function(event) {
                    event.preventDefault();
                    $.ajax({
                        url: "",
                        method: "POST",
                        data: $(this).serialize(),
                        success: function(response) {
                            response = JSON.parse(response);
                            alert(response.message);
                            if (response.invite_code) {
                                navigator.clipboard.writeText(response.invite_code).then(function() {
                                    alert("邀请码已复制到剪贴板: " + response.invite_code);
                                }, function(err) {
                                    console.error("无法复制邀请码: ", err);
                                });
                            }
                        }
                    });
                });
            });
        </script>
    </body>
    </html>
    ';
}
?>