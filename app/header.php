<?php
//  각 페이지에서 $pageTitle 변수를 설정한 후 불러와야 함
$pageTitle = $pageTitle ?? "게시판";

// DB 연결 객체 $pdo는 각 페이지에서 로드
global $pdo;

// 인증 상태 확인 각 페이지에 session_start()
$is_authenticated = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;

?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | My Website</title>
    <link rel="stylesheet" href="style.css">


</head>
<body>
<div class="logo">
    POLY PLOY 자료공유실📂
</div>
<header>
    <h1><?= htmlspecialchars($pageTitle) ?></h1>
    <nav>
        <a href="index.php">홈</a>
        <a href="about.php">소개</a>
        <a href="projects.php">오늘의 과제</a>

        <?php if ($is_authenticated): ?>
            <a href="board.php" class="prof-tab">자료실</a>
        <?php else: ?>
            <a href="login.php" class="prof-tab">자료실 (🔒암호 필요)</a>
        <?php endif; ?>


        <?php if ($is_authenticated): ?>
            <a href="logout.php" class="prof-tab" style="color: #ffdddd;">[🚪로그아웃]</a>
        <?php endif; ?>
    </nav>
</header>
<main class="board">