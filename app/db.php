<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 파일 업로드를 위한 상수 정의
const UPLOAD_DIR = __DIR__ . '/uploads';

// 업로드 디렉토리가 없으면 생성
if (!is_dir(UPLOAD_DIR)) {
    // 0777 권한으로 생성 (운영 환경에서는 더 엄격한 권한 설정이 필요)
    if (!mkdir(UPLOAD_DIR, 0777, true)) {
        exit('업로드 디렉토리 생성에 실패했습니다. 권한을 확인하세요: ' . UPLOAD_DIR);
    }
}
$host= getenv('APP_DB_HOST') ?: 'db';
$db= getenv('APP_DB_NAME') ?: 'mysite';
$user= getenv('APP_DB_USER') ?: 'poly';
$pass= getenv('APP_DB_PASS') ?: '1234';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES      => false,
];

try {
$pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
exit('DB 연결 실패: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));

}
