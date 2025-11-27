<?php
// db.php 파일을 포함하여 데이터베이스 연결
require_once __DIR__ . '/db.php';

// GET 파라미터로 넘어온 ID를 정수형으로 변환하여 가져옴 (보안 강화)
$id = (int)($_GET['id'] ?? 0);

// ID를 이용해 데이터베이스에서 게시글 정보 조회 (준비된 구문 사용으로 SQL Injection 방지)
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

// 게시글이 존재하지 않으면 오류 메시지 출력 후 종료
if (!$post) {
    exit('글을 찾을 수 없습니다. <a href="board.php">목록</a>');
}
// ⭐️ 현재 게시글에 첨부된 파일 목록 조회
$stmt_files = $pdo->prepare("SELECT * FROM post_files WHERE post_id = ? ORDER BY id");
$stmt_files->execute([$id]);
$files = $stmt_files->fetchAll();

?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post['title']) ?> | My Website</title>
    <link rel="stylesheet" href="style.css">
    <style>
    /* 기존 스타일 유지 */
    .board { max-width: 900px; margin: 40px auto; }
    .meta { color:#666; margin-bottom:10px; }
    pre { white-space: pre-wrap; word-break: break-word; background: #f9f9f9; padding: 15px; border: 1px solid #eee; border-radius: 4px; }
    .actions { margin-top: 16px; display: flex; gap: 8px; } 
    
        /* ⭐️ 파일 목록 스타일 추가 */
    .file-list {
        margin-top: 20px;
        border-top: 1px solid #eee;
        padding-top: 10px;
        font-size: 0.9em;
    }
    .file-list p {
        margin: 5px 0;
    }
    .file-list a {
        color: #004c99;
        text-decoration: none;
        word-break: break-all; /* 파일명이 길 경우 줄 바꿈 */
    }
    .file-list a:hover {
        text-decoration: underline;
    }


    /* 버튼 간 간격 추가 */
    .btn {
        padding: 8px 14px;
        border: 1px solid #004c99;
        background: #004c99;
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        transition: background 0.3s;
    }
    .btn-outline {
        background: #fff;
        color: #004c99;
        border-color: #004c99;
    }
    .btn-update {
        background: #28a745; /* 수정 버튼을 위한 초록색 계열 */
        border-color: #28a745;
    }
    .btn:hover { background: #003366; }
    .btn-outline:hover { background: #f0f0f0; }
    .btn-update:hover { background: #1e7e34; }
    </style>
</head>
<body>
<header>
    <h1>게시글 보기</h1>
    <nav>
        <a href="index.html">홈</a>
        <a href="about.html">자기소개</a>
        <a href="projects.html">프로젝트</a>
        <a href="board.php">게시판</a>
    </nav>
</header>
<main class="board">
    <h2><?= htmlspecialchars($post['title']) ?></h2>
    <div class="meta">
        작성자:
        <?=
        htmlspecialchars($post['name'])
        ?>
        |
        작성일:
        <?=
        htmlspecialchars($post['created_at']) ?>
    </div>
    <pre><?= htmlspecialchars($post['content']) ?></pre>
        <?php if (!empty($files)): ?>
    <div class="file-list">
        <strong>첨부 파일:</strong>
        <?php foreach ($files as $file): ?>
        <p>
            <!-- 파일 다운로드를 위해 저장 경로를 사용하며, download 속성으로 원본 파일명을 지정합니다. -->
            <a href="<?= htmlspecialchars($file['stored_path']) ?>" download="<?= htmlspecialchars($file['original_name']) ?>">
                <!-- 파일 타입에 따라 아이콘 표시 -->
                [<?= (strpos($file['mime_type'], 'image') !== false) ? 'Image' : 'File' ?>] 
                <?= htmlspecialchars($file['original_name']) ?>
            </a>
        </p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <div class="actions">
        <a class="btn btn-outline" href="board.php">목록</a>

        <a class="btn btn-update" href="edit.php?id=<?= (int)$post['id'] ?>">수정</a>

        <a class="btn" href="delete.php?id=<?= (int)$post['id'] ?>">삭제</a>
    </div>
</main>
<footer>
    <p>&copy; 2025 My Website</p>
</footer>
</body>
</html>
