<?php
session_start();
require_once __DIR__ . '/db.php';
$pageTitle = "자기소개";
require_once 'header.php';
?>
<main class="board">
<h2>안녕하세요!</h2>
<p>저는 웹 개발과 교육을 좋아하는 사람입니다. HTML, CSS, JS를 활용해 다양한 실습용 페이지를 제작하고 있습니다.</p>
</main>
<?php
require_once 'footer.php';
?>