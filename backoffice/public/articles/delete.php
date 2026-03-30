<?php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: list.php?error=token');
	exit;
}

$id = (int)($_POST['id'] ?? 0);
$token = (string)($_POST['token'] ?? '');

if ($id <= 0 || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
	header('Location: list.php?error=token');
	exit;
}

$stmt = getDB()->prepare('DELETE FROM articles WHERE id = ?');
$stmt->execute([$id]);

header('Location: list.php?success=deleted');
exit;

