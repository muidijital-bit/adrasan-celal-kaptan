<?php
require_once '../includes/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Kullanıcı adı veya şifre hatalı!';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Giriş - Celal Kaptan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0a1628, #023e8a);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: #fff;
            padding: 48px;
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-logo i {
            font-size: 2.5rem;
            color: #0077b6;
        }
        .login-logo h1 {
            font-size: 1.4rem;
            color: #0a1628;
            margin-top: 8px;
        }
        .login-logo p {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            color: #495057;
        }
        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        .form-group input:focus {
            outline: none;
            border-color: #0077b6;
            box-shadow: 0 0 0 4px rgba(0,119,182,0.1);
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #0077b6;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            background: #005f8d;
            transform: translateY(-1px);
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 20px;
            text-align: center;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #0077b6;
            text-decoration: none;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-logo">
            <img src="../assets/images/logo.png" alt="Celal Kaptan Logo" style="height:100px; width:auto; object-fit:contain;">
            <h1>Celal Kaptan Admin</h1>
            <p>Yönetim paneline giriş yapın</p>
        </div>
        <?php if ($error): ?>
        <div class="error"><?= sanitize($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Kullanıcı Adı</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label>Şifre</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Giriş Yap
            </button>
        </form>
        <div class="back-link">
            <a href="../index.php"><i class="fas fa-arrow-left"></i> Siteye Dön</a>
        </div>
    </div>
</body>
</html>
