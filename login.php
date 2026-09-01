<?php
require_once 'init.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$registered = isset($_GET['registered']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = trim($_POST['username'] ?? '');
    $password        = $_POST['password'] ?? '';

    if ($usernameOrEmail === '' || $password === '') {
        $errors[] = "Username/email and password are required.";
    } else {
        $stmt = $pdo->prepare(
            "SELECT id, username, password_hash
             FROM users
             WHERE username = ? OR email = ?
             LIMIT 1"
        );

        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);

        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            header('Location: index.php');
            exit;
        } else {
            $errors[] = "Invalid login credentials.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Task Manager</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="auth-page">
<div class="auth-card">
    <h1>Log In</h1>

    <?php if ($registered): ?>
        <div class="alert alert-success">
            Registration successful! Please log in.
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e): ?>
                <p><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>
            Username or Email
            <input type="text" name="username" required>
        </label>

        <label>
            Password
            <input type="password" name="password" required>
        </label>

        <button type="submit" class="btn primary">Log In</button>
    </form>

    <p class="switch-auth">
        No account? <a href="register.php">Sign up</a>
    </p>
</div>
</body>
</html>
