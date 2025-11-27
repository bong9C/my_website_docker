<?php
session_start(); // 세션 시작
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/db.php';
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$id]);
}
header('Location: board.php');
exit;
