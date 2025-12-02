<?php
// 세션을 시작
session_start();

// 세션 변수를 모두 초기화
$_SESSION = array();

// 세션 쿠키를 삭제하여 세션을 완전히 종료
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 세션 자체를 파괴
session_destroy();

// 로그인 페이지로 리디렉션
header('Location: login.php');
exit;
