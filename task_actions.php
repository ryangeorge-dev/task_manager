<?php
require_once 'init.php';
require_login();

$userId = $_SESSION['user_id'];

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'create':
        $title       = trim($_POST['title'] ?? '');
        $category    = trim($_POST['category'] ?? '');
        $deadline    = $_POST['deadline'] ?? null;
        $description = trim($_POST['description'] ?? '');

        if ($title !== '') {
            $stmt = $pdo->prepare(
                "INSERT INTO tasks (user_id, title, category, deadline, description)
                 VALUES (:uid, :t, :c, :d, :desc)"
            );
            $stmt->execute([
                ':uid'  => $userId,
                ':t'    => $title,
                ':c'    => $category ?: null,
                ':d'    => $deadline ?: null,
                ':desc' => $description ?: null,
            ]);
        }
        break;

    case 'update':
        $taskId      = (int)($_POST['task_id'] ?? 0);
        $title       = trim($_POST['title'] ?? '');
        $category    = trim($_POST['category'] ?? '');
        $deadline    = $_POST['deadline'] ?? null;
        $description = trim($_POST['description'] ?? '');

        if ($taskId > 0 && $title !== '') {
            $stmt = $pdo->prepare(
                "UPDATE tasks 
                 SET title = :t, category = :c, deadline = :d, description = :desc, updated_at = NOW()
                 WHERE id = :id AND user_id = :uid"
            );
            $stmt->execute([
                ':t'    => $title,
                ':c'    => $category ?: null,
                ':d'    => $deadline ?: null,
                ':desc' => $description ?: null,
                ':id'   => $taskId,
                ':uid'  => $userId,
            ]);
        }
        break;

    case 'delete':
        $taskId = (int)($_POST['task_id'] ?? 0);
        if ($taskId > 0) {
            $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id AND user_id = :uid");
            $stmt->execute([
                ':id'  => $taskId,
                ':uid' => $userId,
            ]);
        }
        break;

    case 'toggle_complete':
        $taskId = (int)($_POST['task_id'] ?? 0);
        if ($taskId > 0) {
            $stmt = $pdo->prepare(
                "UPDATE tasks 
                 SET is_completed = 1 - is_completed, updated_at = NOW()
                 WHERE id = :id AND user_id = :uid"
            );
            $stmt->execute([
                ':id'  => $taskId,
                ':uid' => $userId,
            ]);
        }
        break;
}

header('Location: index.php');
exit;
