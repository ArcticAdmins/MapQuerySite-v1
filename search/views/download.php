<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 检查用户是否有权限
if (!isset($_SESSION['username']) || !in_array($_SESSION['username'], [''])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// 从 URL 参数中获取 objectKey
if (!isset($_GET['objectKey'])) {
    echo json_encode(['error' => 'Missing objectKey']);
    exit;
}
$objectKey = $_GET['objectKey'];

// 存储桶访问凭证和信息
$accessKey = '';
$secretKey = '';
$bucketName = '';
$region = '';

// 生成预签名 URL
$expires = '+3 minutes'; // 链接过期时间
$method = 'GET'; // HTTP 方法

$host = "$region.example.com";
$uri = "/$bucketName/$objectKey";
$expiresTimestamp = strtotime($expires);
$expiresString = gmdate('Ymd\THis\Z', $expiresTimestamp);
$dateString = gmdate('Ymd', $expiresTimestamp);

$credentialScope = "$dateString/$region/s3/aws4_request";
$canonicalQueryString = "X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=" . urlencode("$accessKey/$credentialScope") . "&X-Amz-Date=$expiresString&X-Amz-Expires=" . (strtotime($expires) - time()) . "&X-Amz-SignedHeaders=host";
$canonicalHeaders = "host:$host\n";
$signedHeaders = "host";
$payloadHash = "UNSIGNED-PAYLOAD";

$canonicalRequest = "$method\n$uri\n$canonicalQueryString\n$canonicalHeaders\n$signedHeaders\n$payloadHash";
$hashedCanonicalRequest = hash('sha256', $canonicalRequest);

$stringToSign = "AWS4-HMAC-SHA256\n$expiresString\n$credentialScope\n$hashedCanonicalRequest";
$dateKey = hash_hmac('sha256', $dateString, 'AWS4' . $secretKey, true);
$regionKey = hash_hmac('sha256', $region, $dateKey, true);
$serviceKey = hash_hmac('sha256', 's3', $regionKey, true);
$signingKey = hash_hmac('sha256', 'aws4_request', $serviceKey, true);
$signature = hash_hmac('sha256', $stringToSign, $signingKey);

$presignedUrl = "https://$host$uri?$canonicalQueryString&X-Amz-Signature=$signature";

echo json_encode(['url' => $presignedUrl]);
?>