<?php
require_once 'init.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($username === '' || $email === '' || $password === '' || $confirm === '') {
        $errors[] = "All fields are required.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1");
        $stmt->execute([':u' => $username, ':e' => $email]);
        if ($stmt->fetch()) {
            $errors[] = "Username or email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare(
                "INSERT INTO users (username, email, password_hash) VALUES (:u, :e, :p)"
            );
            $insert->execute([':u' => $username, ':e' => $email, ':p' => $hash]);
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Task Manager</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="auth-page">
<div class="auth-card">
    <h1>Sign Up</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $e): ?>
                <p><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>
            Username
            <input type="text" name="username" required>
        </label>

        <label>
            Email
            <input type="email" name="email" required>
        </label>

        <label>
            Password
            <input type="password" name="password" required>
        </label>

        <label>
            Confirm Password
            <input type="password" name="confirm_password" required>
        </label>

        <button type="submit" class="btn primary">Create Account</button>
    </form>

    <p class="switch-auth">
        Already have an account? <a href="login.php">Log in</a>
    </p>
</div>
</body>
</html>
