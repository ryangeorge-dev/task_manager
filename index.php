<?php
require_once 'init.php';
require_login();

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';

$filter = $_GET['filter'] ?? 'all';

$query = "SELECT * FROM tasks WHERE user_id = :uid";
$params = [':uid' => $userId];

if ($filter === 'completed') {
    $query .= " AND is_completed = 1";
} elseif ($filter === 'pending') {
    $query .= " AND is_completed = 0";
}

$query .= " ORDER BY deadline IS NULL, deadline ASC, created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Manager Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="topbar">
    <div class="topbar-left">
        <h1>Task Manager</h1>
    </div>
    <div class="topbar-right">
        <span class="user-label">Hi, <?= htmlspecialchars($username) ?></span>
        <a href="logout.php" class="btn small">Logout</a>
    </div>
</header>

<main class="container">
    <section class="panel">
        <div class="panel-header">
            <h2>Your Tasks</h2>
            <button id="toggleAddForm" class="btn small">+ New Task</button>
        </div>

        <div id="addTaskForm" class="panel-body hidden">
            <form action="task_actions.php" method="post" class="form-grid">
                <input type="hidden" name="action" value="create">

                <label>
                    Title
                    <input type="text" name="title" required>
                </label>

                <label>
                    Category
                    <input type="text" name="category" placeholder="Work, School, Personal...">
                </label>

                <label>
                    Deadline
                    <input type="date" name="deadline">
                </label>

                <label class="full-width">
                    Description
                    <textarea name="description" rows="2" placeholder="Optional details"></textarea>
                </label>

                <button type="submit" class="btn primary full-width">Add Task</button>
            </form>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <h3>Task List</h3>
            <div class="filters">
                <a href="?filter=all" class="<?= $filter === 'all' ? 'active-filter' : '' ?>">All</a>
                <a href="?filter=pending" class="<?= $filter === 'pending' ? 'active-filter' : '' ?>">Pending</a>
                <a href="?filter=completed" class="<?= $filter === 'completed' ? 'active-filter' : '' ?>">Completed</a>
            </div>
        </div>

        <div class="panel-body">
            <?php if (empty($tasks)): ?>
                <p class="empty-state">No tasks yet. Add your first one!</p>
            <?php else: ?>
                <table class="task-table">
                    <thead>
                        <tr>
                            <th>Done</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <?php
                        $deadline = $task['deadline'];
                        $isCompleted = (int)$task['is_completed'] === 1;
                        ?>
                        <tr
                            class="task-row <?= $isCompleted ? 'completed' : '' ?>"
                            data-deadline="<?= htmlspecialchars($deadline ?? '') ?>"
                        >
                            <td>
                                <form action="task_actions.php" method="post">
                                    <input type="hidden" name="action" value="toggle_complete">
                                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                    <input type="checkbox" onchange="this.form.submit()" <?= $isCompleted ? 'checked' : '' ?>>
                                </form>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($task['title']) ?></strong>
                                <?php if (!empty($task['description'])): ?>
                                    <div class="task-desc">
                                        <?= nl2br(htmlspecialchars($task['description'])) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($task['category'] ?? '') ?></td>
                            <td class="deadline-cell">
                                <?= $deadline ? htmlspecialchars($deadline) : '—' ?>
                            </td>
                            <td>
                                <span class="status-badge">
                                    <?= $isCompleted ? 'Completed' : 'Pending' ?>
                                </span>
                            </td>
                            <td>
                                <button
                                    class="btn tiny secondary edit-btn"
                                    data-task='<?= json_encode([
                                        'id' => $task['id'],
                                        'title' => $task['title'],
                                        'category' => $task['category'],
                                        'deadline' => $task['deadline'],
                                        'description' => $task['description']
                                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>'>
                                    Edit
                                </button>
                                <form action="task_actions.php" method="post" class="inline-form" onsubmit="return confirm('Delete this task?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                    <button type="submit" class="btn tiny danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </section>
</main>

<div id="editModal" class="modal hidden">
    <div class="modal-content">
        <h3>Edit Task</h3>
        <form id="editTaskForm" action="task_actions.php" method="post" class="form-grid">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="task_id" id="edit_task_id">

            <label>
                Title
                <input type="text" name="title" id="edit_title" required>
            </label>

            <label>
                Category
                <input type="text" name="category" id="edit_category">
            </label>

            <label>
                Deadline
                <input type="date" name="deadline" id="edit_deadline">
            </label>

            <label class="full-width">
                Description
                <textarea name="description" id="edit_description" rows="2"></textarea>
            </label>

            <div class="modal-actions">
                <button type="button" id="cancelEdit" class="btn">Cancel</button>
                <button type="submit" class="btn primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
