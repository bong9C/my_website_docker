<?php 

?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>글쓰기 | My Website</title>
    <link rel="stylesheet" href="style.css">
    </head>
<body>
<header>
    <h1>글쓰기</h1>
    <nav>
        <a href="index.html">홈</a>
        <a href="about.html">자기소개</a>
        <a href="projects.html">프로젝트</a>
        <a href="board.php">게시판</a>
    </nav>
</header>
<main class="board">
    <form action="save.php" method="POST"  enctype="multipart/form-data"> 
    
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
        
                <div class="form-group">
            <label for="files">파일 첨부 (최대 3개):</label>
            <input type="file" id="files" name="files[]" multiple accept="image/*, application/pdf, .zip">
            <small>이미지, PDF, ZIP 파일 등을 첨부할 수 있습니다. (최대 3개)</small>
        </div>
        
        <button type="submit" class="btn">등록</button>
        <a href="board.php" class="btn btn-outline">목록</a>
    </form>
</main>
<footer>
    <p>&copy; 2025 My Website</p>
</footer>
</body>
</html>
