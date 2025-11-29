<?php
// ⭐️ 이 파일은 각 페이지에서 $pageTitle 변수를 설정한 후 불러와야 합니다.
$pageTitle = $pageTitle ?? "게시판";

// ⭐️ DB 연결 객체 $pdo는 각 페이지에서 로드됩니다.
global $pdo;

// ⭐️ 인증 상태 확인 (login.php, auth.php를 제외한 모든 페이지는 상단에서 이미 session_start()를 실행했으므로 사용 가능)
$is_authenticated = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;

?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | My Website</title>
    <link rel="stylesheet" href="style.css">

    <!-- ⚠️ 페이지별 스타일은 header.php를 불러온 후 각 파일에서 정의해야 합니다. -->

</head>
<body>
<header>
    <h1><?= htmlspecialchars($pageTitle) ?></h1>
    <nav>
        <a href="index.php">홈</a>
        <a href="about.php">소개</a>
        <a href="projects.php">프로젝트</a>

        <!-- ⭐️ [복구] 게시판 탭만 남기고, 로직은 board.php가 처리합니다. -->
        <?php if ($is_authenticated): ?>
            <a href="board.php" class="prof-tab">자료실</a>
        <?php else: ?>
            <a href="login.php" class="prof-tab">자료실 (암호 필요)</a>
        <?php endif; ?>

        <!-- ⭐️ [복구] 로그아웃 버튼만 별도로 표시 -->
        <?php if ($is_authenticated): ?>
            <a href="logout.php" class="prof-tab" style="color: #ffdddd;">[로그아웃]</a>
        <?php endif; ?>
    </nav>
</header>
<main class="board">