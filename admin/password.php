<?php include 'header.php';

$db = getDB();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $stmt = $db->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch();

    if (!password_verify($current, $admin['password'])) {
        $error = 'Mevcut şifre hatalı!';
    } elseif (strlen($new) < 6) {
        $error = 'Yeni şifre en az 6 karakter olmalıdır!';
    } elseif ($new !== $confirm) {
        $error = 'Yeni şifreler eşleşmiyor!';
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $db->prepare("UPDATE admins SET password = ? WHERE id = ?")->execute([$hash, $_SESSION['admin_id']]);
        $success = 'Şifreniz başarıyla değiştirildi!';
    }
}
?>

<?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= sanitize($error) ?></div><?php endif; ?>

<div class="card" style="max-width:500px;">
    <div class="card-header">
        <h3><i class="fas fa-key"></i> Şifre Değiştir</h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label>Mevcut Şifre</label>
                <input type="password" name="current_password" required>
            </div>
            <div class="form-group">
                <label>Yeni Şifre</label>
                <input type="password" name="new_password" required minlength="6">
            </div>
            <div class="form-group">
                <label>Yeni Şifre (Tekrar)</label>
                <input type="password" name="confirm_password" required minlength="6">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Şifreyi Güncelle</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
