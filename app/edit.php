<?php
// 🚨 [필수 수정] 1. db.php를 포함하여 데이터베이스 연결($pdo) 객체를 가져옵니다.
require_once __DIR__ . '/db.php'; 

$id = (int)($_GET['id'] ?? 0); // URL에서 ID를 받아옵니다.

// 2. POST 요청인지 확인 (사용자가 "수정 완료" 버튼을 눌렀는지)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 폼 제출 시 수정 처리 로직 시작

    // 폼 데이터가 POST로 넘어왔으므로 $_POST 배열에서 안전하게 데이터를 받습니다.
    $updated_title = $_POST['title'] ?? '';
    $updated_content = $_POST['content'] ?? '';
    $post_id = (int)$_POST['id'] ?? 0;
    
    // ⭐️ 파일 삭제 및 새 파일 업로드를 위한 데이터
    $delete_files = $_POST['delete_files'] ?? []; // 삭제할 파일 ID 배열
    $new_files = $_FILES['files'] ?? null; // 새로 업로드된 파일

    // 데이터 검증 (필요한 경우)
    if (empty($updated_title) || empty($updated_content) || $post_id === 0) {
         exit('필수 정보가 누락되었습니다.');
    }
    
    try {
        $pdo->beginTransaction(); // 트랜잭션 시작

        // A. 게시글 UPDATE 쿼리 작성 및 실행
        $sql = "UPDATE posts SET title = ?, content = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$updated_title, $updated_content, $post_id]);

        if (!$success) {
            throw new Exception("게시글 수정에 실패했습니다.");
        }

        // B. ⭐️ 파일 삭제 처리
        if (!empty($delete_files)) {
            // 삭제할 파일의 정보를 DB에서 조회
            $placeholders = implode(',', array_fill(0, count($delete_files), '?'));
            $sql_select = "SELECT stored_path FROM post_files WHERE id IN ($placeholders) AND post_id = ?";
            $stmt_select = $pdo->prepare($sql_select);
            $stmt_select->execute(array_merge($delete_files, [$post_id]));
            $files_to_delete = $stmt_select->fetchAll(PDO::FETCH_COLUMN);

            // DB에서 파일 메타데이터 삭제
            $sql_delete = "DELETE FROM post_files WHERE id IN ($placeholders) AND post_id = ?";
            $stmt_delete = $pdo->prepare($sql_delete);
            $stmt_delete->execute(array_merge($delete_files, [$post_id]));
            
            // 실제 파일 시스템에서 파일 삭제
            foreach ($files_to_delete as $stored_path) {
                // 저장 경로(uploads/파일명)를 실제 서버 경로로 변환
                $full_path = str_replace('uploads/', UPLOAD_DIR . '/', $stored_path);
                if (file_exists($full_path)) {
                    unlink($full_path); // 파일 삭제
                }
            }
        }
        
        // C. ⭐️ 새 파일 업로드 처리 (save.php와 동일 로직)
        if ($new_files && !empty($new_files['name'][0])) {
            
            $files_to_process = count($new_files['name']);
            if ($files_to_process > 3) { // 파일 개수 제한
                throw new Exception("파일은 최대 3개까지만 첨부할 수 있습니다.");
            }

            // 파일 배열 재구성
            $files = [];
            for ($i = 0; $i < $files_to_process; $i++) {
                $files[] = [
                    'name' => $new_files['name'][$i],
                    'type' => $new_files['type'][$i],
                    'tmp_name' => $new_files['tmp_name'][$i],
                    'error' => $new_files['error'][$i],
                    'size' => $new_files['size'][$i],
                ];
            }

            foreach ($files as $file) {
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    if ($file['error'] !== UPLOAD_ERR_NO_FILE) {
                         throw new Exception("파일 업로드 오류: 코드 " . $file['error']);
                    }
                    continue; 
                }

                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $new_file_name = uniqid() . '.' . $file_extension;
                $destination_path = UPLOAD_DIR . '/' . $new_file_name;

                if (move_uploaded_file($file['tmp_name'], $destination_path)) {
                    $sql_file = "INSERT INTO post_files (post_id, original_name, stored_path, mime_type) VALUES (?, ?, ?, ?)";
                    $stmt_file = $pdo->prepare($sql_file);
                    $stmt_file->execute([
                        $post_id, 
                        $file['name'], 
                        'uploads/' . $new_file_name,
                        $file['type']
                    ]);
                } else {
                    throw new Exception("파일을 서버에 저장하는 데 실패했습니다.");
                }
            }
        }
        
        // 모든 작업 성공 시 커밋
        $pdo->commit();
        
        // 성공 시 게시글 보기 페이지로 리디렉션
        header("Location: view.php?id=" . $post_id);
        exit;

    } catch (Exception $e) {
        // 오류 발생 시 롤백
        $pdo->rollBack();
        exit('게시글 수정 중 오류 발생: ' . htmlspecialchars($e->getMessage()));
    }
}

// 3. GET 요청 (수정 페이지를 처음 로드할 때)
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) { exit('수정할 글을 찾을 수 없습니다. <a href="board.php">목록</a>'); }

// ⭐️ 현재 게시글에 첨부된 파일 목록 조회 (폼에 표시용)
$stmt_files = $pdo->prepare("SELECT * FROM post_files WHERE post_id = ? ORDER BY id");
$stmt_files->execute([$id]);
$files = $stmt_files->fetchAll();

?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>게시글 수정 | <?= htmlspecialchars($post['title']) ?></title>
    <link rel="stylesheet" href="style.css?v=1.2">
    </head>
<body>
    <header><h1>게시글 수정</h1></header>
    <main class="board">
        <!-- ⭐️ 파일 업로드를 위해 enctype 추가 -->
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
            
            <!-- ⭐️ 기존 첨부 파일 목록 및 삭제 옵션 -->
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
            
            <!-- ⭐️ 새 파일 첨부 필드 -->
            <div class="form-group">
                <label for="files">새 파일 첨부 (최대 3개):</label>
                <input type="file" id="files" name="files[]" multiple accept="image/*, application/pdf, .zip">
                <small>새로 첨부할 파일을 선택하세요.</small>
            </div>
            
            <button type="submit" class="btn btn-update">수정 완료</button>
            <a class="btn btn-outline" href="view.php?id=<?= $id ?>">취소</a>
        </form>
    </main>
    <footer><p>&copy; 2025 My Website</p></footer>
</body>
</html>
