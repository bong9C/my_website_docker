<?php
require_once __DIR__ . '/db.php';

// 이 파일은 각 페이지에서 $pageTitle 변수를 설정한 후 불러와야 합니다.
$pageTitle = $pageTitle ?? "게시판";

// ⭐️ DB 연결 객체가 있다면 교수님 목록을 가져옵니다.
global $pdo;
$professors = [];
if (isset($pdo)) {
    try {
        $stmt_prof = $pdo->query("SELECT DISTINCT professor_name FROM posts WHERE professor_name != '' ORDER BY professor_name ASC");
        $professors = $stmt_prof->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        // 컬럼이 없거나 DB 오류 발생 시, 탭은 비어있도록 설정 (사이트 다운 방지)
        // error_log("DB Error in header.php: " . $e->getMessage());
    }
}

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
</head>
<body>
<header>
    <h1><?= htmlspecialchars($pageTitle) ?></h1>
    <nav>
        <a href="index.php">홈</a>
        <a href="about.php">자기소개</a>
        <a href="projects.php">프로젝트</a>

        <!-- ⭐️ [수정] 전체 자료 탭의 목적지를 인증 상태에 따라 변경합니다. -->
        <?php if ($is_authenticated): ?>
            <!-- 인증 O: 자료실(board.php)로 바로 이동 -->
            <a href="board.php" class="prof-tab">전체 자료</a>
        <?php else: ?>
            <!-- 인증 X: login.php로 이동하여 암호 입력 유도 -->
            <a href="login.php" class="prof-tab">전체 자료 (접속 필요)</a>
        <?php endif; ?>


        <!-- ⭐️ 교수님별 동적 탭 추가 -->
        <?php foreach ($professors as $prof): ?>
            <a href="board.php?prof=<?= urlencode($prof) ?>" class="prof-tab">
                <?= htmlspecialchars($prof) ?>
            </a>
        <?php endforeach; ?>

        <!-- ⭐️ [수정] 로그아웃 버튼만 별도로 표시 -->
        <?php if ($is_authenticated): ?>
            <a href="logout.php" class="prof-tab" style="color: #ffdddd;">[로그아웃]</a>
        <?php endif; ?>
    </nav>
</header>
<main class="board">