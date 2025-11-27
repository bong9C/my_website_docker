<?php
// db.php를 포함하여 DB 연결($pdo)과 UPLOAD_DIR 상수를 가져옵니다.
require_once __DIR__ . '/db.php'; 

$name = trim($_POST['name'] ?? '');
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

// 1. 필수 항목 검증
if ($name === '' || $title === '' || $content === '') {
    exit('필수 항목이 비었습니다. <a href="write.php">뒤로</a>');
}

try {
    // PDO 트랜잭션 시작 (게시글과 파일 저장이 모두 성공해야 커밋)
    $pdo->beginTransaction();

    // 2. 게시글 데이터베이스 저장
    $stmt = $pdo->prepare("INSERT INTO posts (name, title, content) VALUES (?, ?, ?)");
    $stmt->execute([$name, $title, $content]);
    $post_id = $pdo->lastInsertId(); // 방금 삽입된 게시글의 ID를 가져옴

    // 3. 파일 업로드 처리
    if (isset($_FILES['files']) && !empty($_FILES['files']['name'][0])) {
        
        $files_to_process = count($_FILES['files']['name']);
        if ($files_to_process > 3) { // 파일 개수 제한
            throw new Exception("파일은 최대 3개까지만 첨부할 수 있습니다.");
        }

        // $_FILES 배열은 다중 파일 업로드 시 구조가 복잡하므로 순회하기 편하게 재구성합니다.
        $files = [];
        for ($i = 0; $i < $files_to_process; $i++) {
            $files[] = [
                'name' => $_FILES['files']['name'][$i],
                'type' => $_FILES['files']['type'][$i],
                'tmp_name' => $_FILES['files']['tmp_name'][$i],
                'error' => $_FILES['files']['error'][$i],
                'size' => $_FILES['files']['size'][$i],
            ];
        }

        foreach ($files as $file) {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                // 오류 코드가 4 (UPLOAD_ERR_NO_FILE)가 아니면 처리 중단
                if ($file['error'] !== UPLOAD_ERR_NO_FILE) {
                    throw new Exception("파일 업로드 오류: 코드 " . $file['error']);
                }
                continue; 
            }

            // 파일 이름 중복 방지를 위해 고유한 파일 이름 생성
            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_file_name = uniqid() . '.' . $file_extension;
            $destination_path = UPLOAD_DIR . '/' . $new_file_name;

            // 임시 경로에서 최종 저장 경로로 파일 이동
            if (move_uploaded_file($file['tmp_name'], $destination_path)) {
                
                // 파일 메타데이터를 데이터베이스에 저장
                $sql_file = "INSERT INTO post_files (post_id, original_name, stored_path, mime_type) VALUES (?, ?, ?, ?)";
                $stmt_file = $pdo->prepare($sql_file);
                
                // 저장 경로는 웹에서 접근 가능하도록 'uploads/'와 파일 이름을 합쳐 저장합니다.
                $stmt_file->execute([
                    $post_id, 
                    $file['name'], 
                    'uploads/' . $new_file_name, // 웹 접근 경로 (예: uploads/abcdef123.jpg)
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
    header('Location: view.php?id=' . $post_id);
    exit;

} catch (Exception $e) {
    // 오류 발생 시 롤백 (게시글 및 파일 메타데이터 저장 취소)
    $pdo->rollBack();
    // 사용자에게 오류 메시지 출력
    exit('글쓰기 중 오류 발생: ' . htmlspecialchars($e->getMessage()));
}
