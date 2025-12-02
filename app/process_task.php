<?php
session_start();
require_once __DIR__ . '/db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_content = trim($_POST['task_content']);

    if (!empty($task_content)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO tasks (content) VALUES (:content)");
            $stmt->bindParam(':content', $task_content);
            $stmt->execute();
            header("Location: projects.php"); // 저장이 완료되면 목록 페이지로 돌아감
            exit;

        } catch (PDOException $e) {
            echo "데이터베이스 오류: " . $e->getMessage();
        }
    } else {
        echo "과제 내용을 입력해주세요.";
    }
} else {
    header("Location: projects.php");
    exit;
}
?>