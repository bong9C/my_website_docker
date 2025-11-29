<?php
session_start(); // ⭐️ 세션을 맨 위에서 시작

require_once __DIR__ . '/db.php'; // ⭐️ DB 연결을 여기서 먼저 처리하여 $pdo 생성

$pageTitle = "poly poly 자료공유실";
require_once 'header.php'; // header.php는 이제 $pdo를 안전하게 사용합니다.
?>
<style>
    .board {
    text-align: center;
    }
</style>
<main class="board">
<h2>환영합니다!</h2>
<p>이곳은 폴리텍 인천 강의자료 공유실입니다!<br>
    게시판에 안내받은 비밀번호를 입력하셔서 들어가세요! </p>
</main>
<?php
require_once 'footer.php';
?>