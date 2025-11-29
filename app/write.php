<?php
// ⭐️ [필수] 암호 인증 확인 및 세션 시작
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/db.php';

$pageTitle = "글쓰기";
require_once 'header.php';
?>
<!-- ⭐️ header.php에 있던 <style> 블록은 여기에 그대로 유지합니다. -->
<style>
    /* 폼 스타일 */
    .board { max-width: 700px; margin: 40px auto; text-align: left; padding-bottom: 60px; }
    .form-group { margin-bottom: 15px; }
    label { display: block; margin: 12px 0 6px; font-weight: bold; }
    input[type="text"], textarea, input[type="file"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        box-sizing: border-box;
    }
    textarea { height: 200px; resize: vertical; }
    /* 버튼 스타일은 style.css에 통합됨 */
    .actions {
        margin-top: 15px;
        display: flex;
        gap: 8px;
        justify-content: flex-end; /* 오른쪽 정렬 */
    }
    .btn { display: inline-block; }
</style>

    <h2>새 게시글 작성</h2>

    <!-- ⭐️ 파일 업로드를 위해 enctype="multipart/form-data" 속성 필수 -->
    <form method="POST" action="save.php" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">작성자:</label>
            <input type="text" name="name" id="name" required>
        </div>

        <!-- ⭐️ [제거됨] professor_name 필드 제거 -->

        <div class="form-group">
            <label for="title">제목:</label>
            <input type="text" name="title" id="title" required>
        </div>

        <div class="form-group">
            <label for="content">내용:</label>
            <textarea name="content" id="content" rows="10" required></textarea>
        </div>

        <!-- ⭐️ 파일 첨부 필드 -->
        <div class="form-group">
            <label for="files">파일 첨부 (최대 3개):</label>
            <input type="file" id="files" name="files[]" multiple accept="image/*, application/pdf, .zip">
            <small>이미지, PDF, ZIP 파일 등을 첨부할 수 있습니다. (Ctrl/Cmd 키로 다중 선택)</small>
        </div>

        <div class="actions">
            <button type="submit" class="btn">저장하기</button>
            <a href="board.php" class="btn btn-outline">목록</a>
        </div>
    </form>

<?php
require_once 'footer.php';
?>