<?php
// PHP 세션을 시작합니다. 모든 페이지에서 세션을 사용하려면 db.php 상단에 이 코드를 넣어야 하지만,
// 지금은 이 파일에서만 세션을 사용합니다.
session_start();

// 이미 로그인(인증)된 상태라면 메인 페이지로 리디렉션
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    header('Location: board.php');
    exit;
}

// 폼을 제출했지만 암호가 틀렸을 때 메시지를 표시
$error_message = '';
if (isset($_SESSION['login_error'])) {
    $error_message = $_SESSION['login_error'];
    unset($_SESSION['login_error']); // 메시지를 한 번 보여준 후 세션에서 제거
}

// ⭐️ 페이지 제목 설정
$pageTitle = "접속 인증";
?>
<!-- ⭐️ header.php 파일 구조를 수동으로 가져와서, DB 접속 없이 단순 HTML만 표시합니다. -->
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
    <h1>강의 자료실 접속 인증</h1>
    <nav>
        <a href="index.php">홈</a>
        <a href="about.php">자기소개</a>
        <a href="projects.php">프로젝트</a>
        <!-- 게시판 대신 로그인으로 대체 -->
    </nav>
</header>
<main class="board" style="max-width: 400px;">
    <h2>접속 암호를 입력하세요</h2>
    
    <?php if ($error_message): ?>
    <div style="color: red; margin-bottom: 15px; border: 1px solid red; padding: 10px; border-radius: 5px;">
        <?= htmlspecialchars($error_message) ?>
    </div>
    <?php endif; ?>

    <form action="auth.php" method="POST">
        <div class="form-group">
            <label for="password">암호:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="actions" style="justify-content: flex-start;">
            <button type="submit" class="btn">접속</button>
        </div>
    </form>
</main>
<footer>
    <p>&copy; 2025 My Website</p>
</footer>
</body>
</html>
