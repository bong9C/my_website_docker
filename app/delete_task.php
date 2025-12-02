<?php
session_start();
// db.php 연결 객체 $pdo 로드
require_once __DIR__ . '/db.php';

// 1. DELETE는 보안상 POST로 처리하는 것이 권장되나, 현재는 GET으로 처리합니다.
// URL 파라미터로 id를 받습니다.
if (isset($_GET['id']) && is_numeric($_GET['id'])) {

    $task_id = (int)$_GET['id'];

    try {
        global $pdo;

        // 2. SQL 삽입 공격 방지를 위해 Prepared Statement 사용
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");

        // 3. ID 바인딩 및 실행
        $stmt->bindParam(':id', $task_id, PDO::PARAM_INT);
        $stmt->execute();

        // 4. 삭제 성공 후 목록 페이지로 이동
        // Note: 사용자가 삭제 성공 메시지를 못 보지만, 목록이 새로고침되므로 확인 가능
        header("Location: projects.php");
        exit;

    } catch (PDOException $e) {
        // 데이터베이스 오류 발생 시
        echo "데이터베이스 오류: 과제 삭제 실패. " . $e->getMessage();
    }
} else {
    // id가 전달되지 않았을 경우 목록 페이지로 이동
    header("Location: projects.php");
    exit;
}
?>