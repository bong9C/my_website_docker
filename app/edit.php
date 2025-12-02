<?php
// 암호 인증 인증되지 않으면 login.php로 리디렉션
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/db.php';

$id = (int)($_GET['id'] ?? 0);

// 2. POST 요청인지 확인 (수정 완료 버튼을 눌렀는지)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $updated_title = $_POST['title'] ?? '';
    $updated_content = $_POST['content'] ?? '';
    $post_id = (int)$_POST['id'] ?? 0;

    $delete_files = $_POST['delete_files'] ?? []; // 삭제할 파일 ID 배열
    $new_files = $_FILES['files'] ?? null; // 새로 업로드된 파일

    // 1. 필수 데이터 검증
    if (empty($updated_title) || empty($updated_content) || $post_id === 0) {
         exit('필수 정보가 누락되었습니다.');
    }

    try {
        $pdo->beginTransaction();

        // A. 게시글 UPDATE 쿼리 실행 (professor_name 필드 제거)
        $sql = "UPDATE posts SET title = ?, content = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$updated_title, $updated_content, $post_id]);

        if (!$success) { throw new Exception("게시글 수정에 실패했습니다."); }

        // B. 파일 삭제 처리 (기존 파일 체크박스)
        if (!empty($delete_files)) {
            // 1. DB에서 삭제할 파일의 서버 저장 경로를 조회
            $placeholders = implode(',', array_fill(0, count($delete_files), '?'));
            $sql_select = "SELECT stored_path FROM post_files WHERE id IN ($placeholders) AND post_id = ?";
            $stmt_select = $pdo->prepare($sql_select);
            $stmt_select->execute(array_merge($delete_files, [$post_id]));
            $files_to_delete = $stmt_select->fetchAll(PDO::FETCH_COLUMN);

            // 2. DB 메타데이터 삭제
            $sql_delete = "DELETE FROM post_files WHERE id IN ($placeholders) AND post_id = ?";
            $stmt_delete = $pdo->prepare($sql_delete);
            $stmt_delete->execute(array_merge($delete_files, [$post_id]));

            // 3. 실제 서버 파일 삭제
            foreach ($files_to_delete as $stored_path) {
                $full_path = str_replace('uploads/', UPLOAD_DIR . '/', $stored_path);
                if (file_exists($full_path)) { unlink($full_path); }
            }
        }

        // C. 새 파일 업로드 처리
        if ($new_files && !empty($new_files['name'][0])) {

            $files_to_process = count($new_files['name']);
            if ($files_to_process > 3) { throw new Exception("파일은 최대 3개까지만 첨부할 수 있습니다."); }

            // 다중 파일 배열 정리
            $files = [];
            for ($i = 0; $i < $files_to_process; $i++) {
                if ($_FILES['files']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                    $files[] = [
                        'name' => $new_files['name'][$i], 'type' => $new_files['type'][$i],
                        'tmp_name' => $new_files['tmp_name'][$i], 'error' => $new_files['error'][$i],
                        'size' => $new_files['size'][$i],
                    ];
                }
            }

            foreach ($files as $file) {
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception("파일 업로드 오류: 코드 " . $file['error']);
                }

                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $new_file_name = uniqid() . '.' . $file_extension;
                $destination_path = UPLOAD_DIR . '/' . $new_file_name;

                if (move_uploaded_file($file['tmp_name'], $destination_path)) {
                    $sql_file = "INSERT INTO post_files (post_id, original_name, stored_path, mime_type) VALUES (?, ?, ?, ?)";
                    $stmt_file = $pdo->prepare($sql_file);
                    $stmt_file->execute([ $post_id, $file['name'], 'uploads/' . $new_file_name, $file['type'] ]);
                } else {
                    throw new Exception("파일을 서버에 저장하는 데 실패했습니다.");
                }
            }
        }

        $pdo->commit();
        header("Location: view.php?id=" . $post_id);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        exit('게시글 수정 중 오류 발생: ' . htmlspecialchars($e->getMessage()));
    }
}

// 3. GET 요청 (수정 페이지를 처음 로드할 때)
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) { exit('수정할 글을 찾을 수 없습니다. <a href="board.php">목록</a>'); }

$stmt_files = $pdo->prepare("SELECT * FROM post_files WHERE post_id = ? ORDER BY id");
$stmt_files->execute([$id]);
$files = $stmt_files->fetchAll();

$pageTitle = "게시글 수정";
require_once 'header.php';
?>

<style>
    .board {
        max-width: 900px;
        margin: 40px auto;
        padding: 0 20px 60px 20px; /* 푸터 공간 확보 */
        box-sizing: border-box;
        text-align: left; /* 폼 요소 정렬 */
    }
    /* 폼 요소 스타일 */
    .form-group { margin-bottom: 15px; }
    label { display:block; margin:12px 0 6px; font-weight: bold; }
    input[type="text"], textarea, input[type="file"] { width:100%; padding:10px; border:1px solid #ccc; border-radius:8px; box-sizing: border-box; margin-bottom: 12px;}
    textarea { height: 300px; resize: vertical; }
    /* 버튼 스타일 */
    .actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end; /* 오른쪽 정렬 */
    }
</style>

<!-- 폼 시작 -->
    <form method="POST" action="edit.php?id=<?= $id ?>" enctype="multipart/form-data">

        <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">

        <div class="form-group">
            <label for="title">제목:</label>
            <input type="text" name="title" id="title" value="<?= htmlspecialchars($post['title']) ?>" required>
        </div>

        <div class="form-group">
            <label for="content">내용:</label>
            <textarea name="content" id="content" rows="10" required><?= htmlspecialchars($post['content']) ?></textarea>
        </div>

        <!--  기존 첨부 파일 목록 및 삭제 옵션 -->
        <?php if (!empty($files)): ?>
        <div class="form-group">
            <strong>기존 첨부 파일:</strong><br>
            <?php foreach ($files as $file): ?>
            <div style="margin-top: 5px; margin-left: 10px;">
                <input type="checkbox" name="delete_files[]" id="file_<?= $file['id'] ?>" value="<?= $file['id'] ?>">
                <label for="file_<?= $file['id'] ?>" style="display: inline; font-weight: normal; cursor: pointer;">
                    [<?= (strpos($file['mime_type'], 'image') !== false) ? 'Image' : 'File' ?>]
                    <?= htmlspecialchars($file['original_name']) ?> (삭제)
                </label>
            </div>
            <?php endforeach; ?>
            <small style="display: block; margin-top: 10px;">삭제할 파일은 체크하고, '수정 완료' 버튼을 눌러주세요.</small>
        </div>
        <?php endif; ?>

        <!-- 새 파일 첨부 필드 -->
        <div class="form-group">
            <label for="files">새 파일 첨부 (최대 3개):</label>
            <input type="file" id="files" name="files[]" multiple accept="image/*, application/pdf, .zip">
            <small>새로 첨부할 파일을 선택하세요. (Ctrl/Cmd 키를 누른 채 여러 파일 선택 가능)</small>
        </div>

        <!-- 버튼 액션 영역 -->
        <div class="actions" style="margin-top: 15px;">
            <button type="submit" class="btn btn-update">수정 완료</button>
            <a href="view.php?id=<?= $id ?>" class="btn btn-outline">취소</a>
        </div>
    </form>
<!-- 폼 끝 -->
<?php
require_once 'footer.php';