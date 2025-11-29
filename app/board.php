<?php
// ⭐️ [필수] 암호 인증 확인 및 세션 시작
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/db.php';

// ⭐️ 파일 첨부 여부 및 조회수를 가져옵니다. professor_name 관련 로직은 제거했습니다.
$stmt = $pdo->prepare("
    SELECT
        p.id, p.title, p.name, p.created_at, p.views,
        COUNT(pf.id) AS file_count
    FROM posts p
    LEFT JOIN post_files pf ON p.id = pf.post_id
    GROUP BY p.id, p.title, p.name, p.created_at, p.views
    ORDER BY p.id DESC
");
$stmt->execute();
$posts = $stmt->fetchAll();

// ⭐️ 페이지 제목 설정
$pageTitle = "게시판";
require_once 'header.php';
?>
<!-- ⭐️ header.php에 있던 <style> 블록은 여기에 그대로 유지합니다. -->
<style>
    /* 기존 스타일 유지 */
    .board { max-width: 900px; margin: 40px auto; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
    .actions {
        text-align: right;
        margin: 20px 0;
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }
    .btn { padding: 8px 14px; border: 1px solid #004c99; background:#004c99; color:#fff;
        text-decoration:none; border-radius:8px; display: inline-block;}
    .btn-outline { background:#fff; color:#004c99; }

    /* 첨부 파일 아이콘 스타일 */
    .attachment-icon {
        font-size: 0.8em;
        color: #e67e22;
        margin-left: 5px;
        vertical-align: middle;
        font-weight: bold;
    }
    /* ⭐️ 푸터 공간 확보 */
    .board { padding-bottom: 60px; }
    /* ⭐️ 조회수 컬럼 스타일 */
    .views-col { width: 50px; text-align: center; }
</style>

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
                <th class="views-col">조회</th>
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
                    <!-- ⭐️ 첨부 파일 아이콘 표시 -->
                    <?php if ((int)$post['file_count'] > 0): ?>
                        <span class="attachment-icon">
                            [File]
                        </span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($post['name']) ?></td>
                <td><?= htmlspecialchars($post['created_at']) ?></td>
                <!-- ⭐️ 조회수 표시 -->
                <td class="views-col"><?= htmlspecialchars($post['views']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($posts)): ?>
            <tr><td colspan="5">게시글이 없습니다.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php
// ⭐️ 공통 푸터 파일 포함
require_once 'footer.php';