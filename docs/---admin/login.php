<?php
// docs/admin/login.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Note: For true production, passwords MUST be hashed (password_hash/password_verify)
    $admin_username = 'hope';
    $admin_password = 'Hope78336!'; 

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['is_admin'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Hope Worldwide</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
        }
        .login-card {
            max-width: 400px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: none;
        }
        .btn-brand {
            background-color: #1b6c38;
            color: white;
        }
        .btn-brand:hover {
            background-color: #14522b;
            color: white;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

    <div class="card login-card w-100 mx-3 p-4">
        <div class="card-body text-center">
            <img src="../res/logo.jpg" alt="Logo" class="mb-4 rounded" style="width: 90px;" onerror="this.src='https://placehold.co/90x90?text=Logo'">
            <h3 class="mb-4 fw-bold" style="color: #1b6c38;">Admin Portal</h3>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="" class="text-start">
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary">Username</label>
                    <input type="text" name="username" class="form-control form-control-lg" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary">Password</label>
                    <input type="password" name="password" class="form-control form-control-lg" required>
                </div>
                <button type="submit" class="btn btn-brand w-100 btn-lg fw-semibold">Login</button>
            </form>
        </div>
    </div>

</body>
</html>