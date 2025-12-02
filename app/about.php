<?php
session_start();
require_once __DIR__ . '/db.php';
$pageTitle = "소개";
require_once 'header.php';
?>
<style>
    .board {
    text-align: center;
    }
</style>
<main class="board">
<h2>😊자료공유실을 만든 이유😊</h2>
<p>자료를 한 곳에 업로드 하고 다운로드 하면 편리 할 것 같아서 만들었습니다.<br>
자료를 한 곳에 모아 빠르고 편리하게 공유하기 위해 만들었습니다. <br>
중요한 자료를 안전하게 보호하기 위해 암호 기능을 적용했습니다.👍</p>
</main>
<?php
require_once 'footer.php';
?>