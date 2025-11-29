<?php
session_start();
require_once __DIR__ . '/db.php';

$pageTitle = "프로젝트";
require_once 'header.php';
?>
<style>
    .board {
    text-align: center;
    }
</style>
<main class="board">
<h2>진행한 프로젝트 목록</h2>
<ul>
<li> 시스템 프로그래밍 강의 자료 웹화</li>
<li> AI PPT 자동 생성기</li>
<li> 포트폴리오 사이트 제작</li>
</ul>
</main>
<?php
require_once 'footer.php';
?>