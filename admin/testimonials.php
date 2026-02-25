<?php include 'header.php';

$db = getDB();
$success = '';

if (isset($_GET['delete'])) {
    $db->prepare("DELETE FROM testimonials WHERE id = ?")->execute([$_GET['delete']]);
    header('Location: testimonials.php?msg=deleted'); exit;
}

if (isset($_GET['msg'])) {
    $msgs = ['deleted' => 'Yorum silindi!', 'saved' => 'Yorum kaydedildi!'];
    $success = $msgs[$_GET['msg']] ?? '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $rating = $_POST['rating'] ?? 5;
    $comment = $_POST['comment'] ?? '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($id) {
        $db->prepare("UPDATE testimonials SET name=?, rating=?, comment=?, is_active=? WHERE id=?")
            ->execute([$name, $rating, $comment, $is_active, $id]);
    } else {
        $db->prepare("INSERT INTO testimonials (name, rating, comment, is_active) VALUES (?,?,?,?)")
            ->execute([$name, $rating, $comment, $is_active]);
    }
    header('Location: testimonials.php?msg=saved'); exit;
}

$testimonials = $db->query("SELECT * FROM testimonials ORDER BY created_at DESC")->fetchAll();

$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editing = $stmt->fetch();
}
$isNew = isset($_GET['new']);
?>

<?php if ($success): ?><div class="alert alert-success"><?= sanitize($success) ?></div><?php endif; ?>

<?php if ($editing || $isNew): ?>
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-star"></i> <?= $editing ? 'Yorum Düzenle' : 'Yeni Yorum Ekle' ?></h3>
        <a href="testimonials.php" class="btn btn-sm btn-outline">Geri Dön</a>
    </div>
    <div class="card-body">
        <form method="POST">
            <?php if ($editing): ?><input type="hidden" name="id" value="<?= $editing['id'] ?>"><?php endif; ?>
            <div class="form-row">
                <div class="form-group">
                    <label>İsim *</label>
                    <input type="text" name="name" value="<?= sanitize($editing['name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Puan (1-5)</label>
                    <select name="rating">
                        <?php for ($i=5; $i>=1; $i--): ?>
                        <option value="<?= $i ?>" <?= ($editing['rating'] ?? 5) == $i ? 'selected' : '' ?>><?= $i ?> Yıldız</option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Yorum *</label>
                <textarea name="comment" rows="4" required><?= sanitize($editing['comment'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="is_active" <?= ($editing['is_active'] ?? 1) ? 'checked' : '' ?>> Aktif</label>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Kaydet</button>
        </form>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-star"></i> Müşteri Yorumları</h3>
        <a href="?new=1" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Yeni Yorum</a>
    </div>
    <table>
        <thead><tr><th>İsim</th><th>Puan</th><th>Yorum</th><th>Durum</th><th>İşlem</th></tr></thead>
        <tbody>
            <?php foreach ($testimonials as $t): ?>
            <tr>
                <td><strong><?= sanitize($t['name']) ?></strong></td>
                <td><?php for($i=0;$i<$t['rating'];$i++) echo '<i class="fas fa-star" style="color:#f77f00;font-size:0.8rem"></i>'; ?></td>
                <td style="max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= sanitize($t['comment']) ?></td>
                <td><?= $t['is_active'] ? '<span style="color:#25d366">Aktif</span>' : '<span style="color:#dc3545">Pasif</span>' ?></td>
                <td>
                    <a href="?edit=<?= $t['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                    <a href="?delete=<?= $t['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğinize emin misiniz?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php include 'footer.php'; ?>
