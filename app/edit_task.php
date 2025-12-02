<?php
session_start();
require_once __DIR__ . '/db.php';

global $pdo;
$task = null;
$task_id = null;

// --- 1. POST 요청 (수정 내용 저장) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $task_id = (int)$_POST['task_id'];
    $task_content = trim($_POST['task_content']);

    if (!empty($task_content) && $task_id > 0) {
        try {
            // SQL: tasks 테이블의 content 내용을 업데이트
            $stmt = $pdo->prepare("UPDATE tasks SET content = :content WHERE id = :id");
            $stmt->bindParam(':content', $task_content);
            $stmt->bindParam(':id', $task_id, PDO::PARAM_INT);
            $stmt->execute();

            // 수정 성공 후 목록 페이지로 이동
            header("Location: projects.php");
            exit;

        } catch (PDOException $e) {
            echo "데이터베이스 오류: 과제 수정 실패. " . $e->getMessage();
        }
    }
}

// --- 2. GET 요청 (수정 폼 보여주기) ---

// 수정할 ID를 URL 파라미터에서 가져옵니다.
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $task_id = (int)$_GET['id'];

    try {
        // 현재 과제 내용을 DB에서 불러옵니다.
        $stmt = $pdo->prepare("SELECT content FROM tasks WHERE id = :id");
        $stmt->bindParam(':id', $task_id, PDO::PARAM_INT);
        $stmt->execute();
        $task = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$task) {
            // 해당 ID의 과제가 없는 경우
            exit("해당 과제를 찾을 수 없습니다.");
        }
    } catch (PDOException $e) {
        exit("DB 연결 오류: " . $e->getMessage());
    }
} else {
    // ID가 없을 경우 목록 페이지로 이동
    header("Location: projects.php");
    exit;
}

// 페이지 제목 설정 및 헤더 포함
$pageTitle = "과제 수정";
require_once 'header.php';
?>

<style>
    .edit-container { max-width: 600px; margin: 40px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
    .edit-container textarea { width: 100%; min-height: 150px; padding: 10px; box-sizing: border-box; margin-bottom: 15px; }
    .edit-container input[type="submit"] { padding: 10px 20px; background-color: #004c99; color: white; border: none; border-radius: 4px; cursor: pointer; }
    .edit-container .actions { text-align: right; }
</style>

<main class="board">
    <div class="edit-container">
        <h2>과제 수정 (ID: <?= htmlspecialchars($task_id) ?>)</h2>
        <form method="POST" action="edit_task.php">

            <input type="hidden" name="task_id" value="<?= htmlspecialchars($task_id) ?>">

            <label for="task_content">과제 내용:</label>
            <textarea id="task_content" name="task_content" required><?= htmlspecialchars($task['content']) ?></textarea>

            <div class="actions">
                <a href="projects.php" class="btn btn-outline">취소</a>
                <button type="submit" class="btn">수정 완료 및 저장</button>
            </div>
        </form>
    </div>
</main>

<?php
require_once 'footer.php';
?>