<?php
session_start(); // 세션 시작
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit;
}
// db.php는 업로드 경로 상수를 위해 필요합니다.
require_once __DIR__ . '/db.php';

// ⭐️ 페이지 제목 설정 (header.php에서 사용됨)
$pageTitle = "글쓰기";

// ⭐️ 공통 헤더 파일 포함 (header.php에 <head>와 <body> 시작 태그가 있습니다)
require_once 'header.php';
?>

<!-- ⚠️ 페이지별 스타일: header.php를 불러온 후 <style> 블록을 엽니다. -->
<style>
/* ⭐️ view.php에서 확인한 규격 통일 (CSS 중복을 줄이려면 이 스타일은 style.css로 옮겨야 합니다!) */
.board {
    max-width: 900px;
    margin: 40px auto;
    /* ⭐️ 푸터 공간 확보: 버튼이 잘리는 현상 방지 */
    padding: 0 20px 60px 20px;
    box-sizing: border-box;
    text-align: left; /* 폼 요소 정렬 */
}
/* 폼 요소 스타일은 style.css에 있지만, 혹시 모를 누락을 대비해 기본 스타일을 여기에 유지할게요. */
label { display:block; margin:12px 0 6px; }
input[type="text"], textarea, input[type="file"] { width:100%; padding:10px; border:1px solid #ccc; border-radius:8px; box-sizing: border-box; margin-bottom: 12px;}
textarea { height: 300px; resize: vertical; }
.btn { padding: 8px 14px; border: 1px solid #004c99; background:#004c99; color:#fff; text-decoration:none; border-radius:8px; display: inline-block;}
.btn-outline { background:#fff; color:#004c99; }

/* actions div의 스타일 (버튼 정렬) */
.actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: flex-end; /* 오른쪽 정렬 */
}
</style>

<!-- 폼 시작 (main 태그는 header.php에서 이미 열려 있습니다) -->
    <!-- ⭐️ 파일 업로드를 위해 enctype 추가 -->
    <form action="save.php" method="POST" enctype="multipart/form-data">

        <div class="form-group">
            <label for="name">이름:</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="title">제목:</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="content">내용:</label>
            <textarea id="content" name="content" rows="10" required></textarea>
        </div>

        <!-- ⭐️ 파일 첨부 필드 추가 (다중 파일 허용) -->
        <div class="form-group">
            <label for="files">파일 첨부 (최대 3개):</label>
            <input type="file" id="files" name="files[]" multiple accept="image/*, application/pdf, .zip">
            <small>이미지, PDF, ZIP 파일 등을 첨부할 수 있습니다. (최대 3개)</small>
        </div>

        <div class="actions" style="justify-content: flex-end; margin-top: 15px;">
            <button type="submit" class="btn">등록</button>
            <a href="board.php" class="btn btn-outline">목록</a>
        </div>
    </form>
<!-- 폼 끝 -->

<?php
// ⭐️ 공통 푸터 파일 포함 (footer.php에 </body>와 </html> 태그가 닫혀 있습니다)
require_once 'footer.php';