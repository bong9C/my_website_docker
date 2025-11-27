<?php 
require_once __DIR__ . '/db.php'; 

// ⭐️ 파일 첨부 여부를 확인하기 위해 LEFT JOIN을 사용합니다.
// COUNT(pf.id)를 사용하여 각 게시글별로 첨부 파일 개수를 가져옵니다.
$stmt = $pdo->prepare("
    SELECT 
        p.id, p.title, p.name, p.created_at, 
        COUNT(pf.id) AS file_count
    FROM posts p
    LEFT JOIN post_files pf ON p.id = pf.post_id
    GROUP BY p.id, p.title, p.name, p.created_at
    ORDER BY p.id DESC
");
$stmt->execute();
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<title>게시판 | My Website</title>
<link rel="stylesheet" href="style.css?v=1.2">
<style>
    /* view.php의 기존 스타일은 그대로 둡니다. */
    .board { max-width: 900px; margin: 40px auto; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
    .actions { text-align: right; margin: 20px 0; display: flex; gap: 8px; justify-content: flex-end; }
    .btn { padding: 8px 14px; border: 1px solid #004c99; background:#004c99; color:#fff; text-decoration:none; border-radius:8px; display: inline-block; }
    .btn-outline { background:#fff; color:#004c99; }
    
    /* ⭐️ 첨부 파일 아이콘 스타일 */
    .attachment-icon {
        font-size: 0.8em;
        color: #e67e22; /* 주황색 계열로 눈에 띄게 */
        margin-left: 5px;
        vertical-align: middle;
        font-weight: bold;
    }
</style>
</head>
<body>
<header>
    <h1>게시판</h1>
    <nav>
        <a href="index.html">홈</a>
        <a href="about.html">자기소개</a>
        <a href="projects.html">프로젝트</a>
        <a href="board.php">게시판</a>
    </nav>
</header>
<main class="board">
    <div class="actions">
        <a class="btn" href="write.php">글쓰기</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>번호</th>
                <th>제목</th>
                <th>작성자</th>
                <th>작성일</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
            <tr>
                <td><?= htmlspecialchars($post['id']) ?></td>
                <td>
                    <a href="view.php?id=<?= htmlspecialchars($post['id']) ?>">
                        <?= htmlspecialchars($post['title']) ?>
                    </a>
                    <!-- ⭐️ 첨부 파일 아이콘 표시: [Image] 또는 [File]로 구분할 수 있지만, 여기서는 간단히 [File] 개수로 표시합니다. -->
                    <?php if ((int)$post['file_count'] > 0): ?>
                        <span class="attachment-icon">
                            [File (<?= (int)$post['file_count'] ?>)]
                        </span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($post['name']) ?></td>
                <td><?= htmlspecialchars(date('Y.m.d H:i', strtotime($post['created_at']))) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($posts)): ?>
            <tr><td colspan="4">게시글이 없습니다.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
<footer>
    <p>&copy; 2025 My Website</p>
</footer>
</body>
</html>
