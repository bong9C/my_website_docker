<?php
// ⭐️ [필수] 암호 인증 확인 및 세션 시작
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/db.php';

// GET 파라미터로 넘어온 ID를 정수형으로 변환하여 가져옴 (보안 강화)
$id = (int)($_GET['id'] ?? 0);

// 1. ID를 이용해 데이터베이스에서 게시글 정보 조회
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

// 게시글이 존재하지 않으면 오류 메시지 출력 후 종료
if (!$post) {
    exit('글을 찾을 수 없습니다. <a href="board.php">목록</a>');
}

// ⭐️ [조회수 증가 로직 추가]
// 2. views 컬럼 값을 1 증가시킵니다.
$stmt_update = $pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?");
$stmt_update->execute([$id]);

// ⭐️ 현재 게시글에 첨부된 파일 목록 조회
$stmt_files = $pdo->prepare("SELECT * FROM post_files WHERE post_id = ? ORDER BY id");
$stmt_files->execute([$id]);
$files = $stmt_files->fetchAll();

// ⭐️ 페이지 제목 설정
$pageTitle = "게시글 보기";
require_once 'header.php';
?>
<!-- ⭐️ view.php에만 필요한 스타일은 <style> 블록 안에 유지합니다. -->
<style>
    /* view.php의 기존 스타일 유지 */
    .board {
        max-width: 900px;
        margin: 40px auto;
        padding: 0 20px 60px 20px; /* 푸터 공간 확보 */
        box-sizing: border-box;
    }
    .meta { color:#666; margin-bottom:10px; }
    pre { white-space: pre-wrap; word-break: break-word; background: #f9f9f9; padding: 15px; border: 1px solid #eee; border-radius: 4px; }
    .actions {
        margin-top: 10px;
        margin-bottom: 20px;
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end; /* 오른쪽 정렬 */
    }

    /* 이미지 갤러리 스타일 */
    .attachment-section { margin-top: 30px; padding-top: 20px; border-top: 2px dashed #ddd; }
    .attachment-section h3 { font-size: 1.2em; color: #004c99; margin-top: 0; margin-bottom: 15px; }
    .images-gallery { display: flex; margin: 0 -5px 20px -5px; flex-wrap: wrap; justify-content: flex-start; }
    .attached-image {
        margin-bottom: 25px; text-align: center;
        flex: 0 0 calc(33.333% - 10px);
        margin: 0 5px; box-sizing: border-box;
    }
    .attached-image img { width: 100%; max-width: 100%; height: auto; border: 1px solid #ccc; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); display: block; margin: 0 auto;}
    /* 모바일 대응 미디어 쿼리는 그대로 유지 */
    @media (max-width: 900px) { .attached-image { flex: 0 0 calc(50% - 10px); } }
    @media (max-width: 600px) { .attached-image { flex: 0 0 100%; margin: 0; padding: 0; } .images-gallery { margin: 0; } }
    .image-caption { font-size: 0.9em; color: #555; margin-top: 8px; }
    /* 일반 파일 목록 스타일 등은 생략 */
    </style>

    <h2><?= htmlspecialchars($post['title']) ?></h2>

    <!-- ⭐️ [이동] 버튼을 제목 바로 아래 (우측 상단)에 배치합니다. -->
    <div class="actions">
        <a class="btn btn-update" href="edit.php?id=<?= $id ?>">수정</a>
        <a class="btn" href="delete.php?id=<?= $id ?>" onclick="return confirm('정말로 삭제하시겠습니까?');">삭제</a>
        <a class="btn btn-outline" href="board.php">목록</a>
    </div>

    <div class="meta">
        작성자: <?= htmlspecialchars($post['name']); ?>
        | 작성일: <?= $post['created_at']; ?>
        <!-- ⭐️ 조회수 표시 추가 (조회수 증가는 이미 위에서 처리했기 때문에 post['views'] 대신 최신 값인 post['views'] + 1을 사용합니다) -->
        | 조회수: <?= $post['views'] + 1 ?>
    </div>

    <pre><?= htmlspecialchars($post['content']) ?></pre>

    <!-- 첨부 파일 목록 및 이미지 출력 로직은 그대로 유지 -->
    <?php if (!empty($files)): ?>
    <div class="attachment-section">
        <h3>첨부 파일</h3>
        <div class="images-gallery">
        <?php foreach ($files as $file): ?>
            <?php $is_image = strpos($file['mime_type'], 'image/') !== false; ?>
            <?php if ($is_image): ?>
                <div class="attached-image">
                    <img src="<?= htmlspecialchars($file['stored_path']) ?>" alt="<?= htmlspecialchars($file['original_name']) ?>">
                    <p class="image-caption">
                        <?= htmlspecialchars($file['original_name']) ?>
                        (<a href="<?= htmlspecialchars($file['stored_path']) ?>" download="<?= htmlspecialchars($file['original_name']) ?>">다운로드</a>)
                    </p>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        </div>
        <!-- 일반 파일 목록은 이미지 아래에 표시 -->
        <?php foreach ($files as $file): ?>
            <?php $is_image = strpos($file['mime_type'], 'image/') !== false; ?>
            <?php if (!$is_image): ?>
                <p class="attached-file">
                    [File]
                    <a href="<?= htmlspecialchars($file['stored_path']) ?>" download="<?= htmlspecialchars($file['original_name']) ?>">
                        <?= htmlspecialchars($file['original_name']) ?>
                    </a>
                </p>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

<?php
// ⭐️ 공통 푸터 파일 포함
require_once 'footer.php';