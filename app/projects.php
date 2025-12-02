<?php
session_start();
require_once __DIR__ . '/db.php';

$pageTitle = "오늘의 과제";
require_once 'header.php';
?>
<style>
    .board {
    text-align: center;
    }
</style>
<main class="board">
    <h2>오늘의 과제</h2>

    <div class="actions" style="margin-bottom: 20px;">
        <button id="showFormButton" class="btn">➕ 새로운 과제 입력</button>
    </div>
    <br>

<form id="taskInputForm" method="POST" action="process_task.php" style="display:none; margin: 20px auto; max-width: 400px;">
    <label for="task_content">과제 내용:</label>
    <textarea id="task_content" name="task_content" required
              style="width: 100%; min-height: 100px; box-sizing: border-box;"></textarea>
    <button type="submit" class="btn">과제저장</button>
</form>

    <hr>
    <h2>진행 중인 과제 목록</h2>
<div class="task-list" style="text-align: left; margin-top: 20px;">
    <?php
    try {
        global $pdo;
        // ID와 CONTENT를 모두 가져옵니다.
        $tasks = $pdo->query("SELECT id, content FROM tasks ORDER BY id DESC")->fetchAll();

        if (!empty($tasks)) {
            foreach ($tasks as $task) {
                // ✨ 공지 형태의 박스 디자인으로 출력합니다.
                echo '<div class="task-item" style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; border-radius: 5px; white-space: pre-wrap;">';
                // 3번 문제 임시 해결: white-space: pre-wrap; 이 여러 줄 입력을 허용합니다.

                // 과제 내용 출력
                echo '<strong>[과제 번호: ' . htmlspecialchars($task['id']) . ']</strong><br>';
                echo nl2br(htmlspecialchars($task['content'])); // nl2br로 줄바꿈을 HTML <br>로 변환

                // 4번 문제 해결: 수정/삭제 버튼 추가
                echo '<div class="task-actions" style="margin-top: 10px; text-align: right;">';
                echo '<a href="edit_task.php?id=' . htmlspecialchars($task['id']) . '"class="btn btn-update" style="margin-right: 5px;">수정</a>';
                echo '<a href="delete_task.php?id=' . htmlspecialchars($task['id']) . '"class="btn" onclick="return confirm(\'정말로 삭제하시겠습니까?\')">삭제</a>';
                echo '</div>'; // task-actions

                echo '</div>'; // task-item
            }
        } else {
            echo '<p>아직 등록된 과제가 없습니다!</p>';
        }
    } catch (PDOException $e) {
        echo "<p style='color: red;'>과제 목록을 불러오는 데 오류가 발생했습니다.</p>";
    }
    ?>
</div>
</main>
<div style="height: 50px;"></div>
<script>
    document.getElementById('showFormButton').addEventListener('click', function() {
        const form = document.getElementById('taskInputForm');

        // 폼을 보여주거나 숨깁니다.
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
            this.textContent = '❌ 입력 폼 닫기'; // 버튼 텍스트 변경
        } else {
            form.style.display = 'none';
            this.textContent = '➕ 새로운 과제 입력'; // 버튼 텍스트 복구
        }
    });
</script>
<?php
require_once 'footer.php';
?>

</body>
</html>