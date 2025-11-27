<?php
session_start(); // ⭐️ 세션을 맨 위에서 시작

require_once __DIR__ . '/db.php'; // ⭐️ DB 연결을 여기서 먼저 처리하여 $pdo 생성

$pageTitle = "홈";
require_once 'header.php'; // header.php는 이제 $pdo를 안전하게 사용합니다.
?>
<main class="board">
<h2>환영합니다!</h2>
<p>이곳은 HTML과 CSS로 만든 간단한 홈페이지입니다.</p>
</main>
<?php
require_once 'footer.php';
?>