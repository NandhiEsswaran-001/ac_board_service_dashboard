<?php
require_once 'includes/config.php';

if (isset($_SESSION['user_id'])) {
    $dest = isDashboardUser() ? 'pages/dashboard.php' : 'pages/board_list.php';
    header('Location: ' . $dest);
    exit;
}

$error = '';

if (isset($_GET['timeout'])) {
    $error = 'Your session expired due to inactivity. Please log in again.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $limitMsg = checkLoginRateLimit();
    if ($limitMsg) {
        $error = $limitMsg;
    } elseif ($username && $password) {
        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            clearLoginFailures();

            $_SESSION['user_id']       = $user['id'];
            $_SESSION['username']      = $user['username'];
            $_SESSION['full_name']     = $user['full_name'];
            $_SESSION['role']          = $user['role'];
            $_SESSION['last_activity'] = time();

            $dest = isDashboardUser() ? 'pages/dashboard.php' : 'pages/board_list.php';
            header('Location: ' . $dest);
            exit;
        } else {
            recordLoginFailure();
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please enter username and password.';
    }
}

$cssFile = __DIR__ . '/css/style.css';
$cssVer = file_exists($cssFile) ? filemtime($cssFile) : time();
$jsFile = __DIR__ . '/js/app.js';
$jsVer = file_exists($jsFile) ? filemtime($jsFile) : time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="css/style.css?v=<?= $cssVer ?>">
</head>
<body class="login-page">
<div class="login-box">
    <div class="login-logo">
        <div class="icon">❄</div>
        <h2><?= APP_NAME ?></h2>
        <p>Please login to continue</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <?= csrfField() ?>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter username" autofocus required
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <div class="password-wrap">
                <input type="password" id="password" name="password" placeholder="Enter password" required>
                <button type="button" class="password-toggle" data-toggle-password="password" aria-label="Show password">Show</button>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-lg mt-10">Login</button>
    </form>

    <p style="text-align:center;margin-top:18px;font-size:12px;color:#95a5a6;">
        <?= APP_NAME ?> &copy; <?= date('Y') ?>
    </p>
</div>
<script src="js/app.js?v=<?= $jsVer ?>"></script>
</body>
</html>
