<?php
// 모든 PHP 파일의 시작에는 세션을 시작해야 합니다.
session_start();

// ⭐️ 이곳에 사용할 암호를 설정합니다. (실제 배포 시에는 DB나 환경변수에 저장해야 안전합니다.)
const ACCESS_PASSWORD = 'poly';

// POST로 전송된 암호 받기
$input_password = $_POST['password'] ?? '';

// 암호가 일치하는지 확인
if ($input_password === ACCESS_PASSWORD) {
    // 1. 인증 성공: 세션에 플래그 설정
    $_SESSION['authenticated'] = true;

    // 2. 메인 자료실 페이지로 리디렉션
    header('Location: board.php');
    exit;
} else {
    // 1. 인증 실패: 오류 메시지를 세션에 저장
    $_SESSION['login_error'] = "암호가 일치하지 않습니다. 다시 시도해 주세요.";

    // 2. login.php 페이지로 돌려보내기
    header('Location: login.php');
    exit;
}