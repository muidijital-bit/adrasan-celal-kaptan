<?php include 'header.php';

$db = getDB();
$success = '';

// Silme
if (isset($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM tours WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: tours.php?msg=deleted');
    exit;
}

if (isset($_GET['msg'])) {
    $msgs = ['deleted' => 'Tur silindi!', 'saved' => 'Tur kaydedildi!'];
    $success = $msgs[$_GET['msg']] ?? '';
}

// Kaydet / Güncelle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $title = $_POST['title'] ?? '';
    $slug = $_POST['slug'] ?: preg_replace('/[^a-z0-9-]/', '', str_replace(' ', '-', mb_strtolower($title, 'UTF-8')));
    $short_desc = $_POST['short_desc'] ?? '';
    $full_desc = $_POST['full_desc'] ?? '';
    $price = $_POST['price'] ?? '';
    $duration = $_POST['duration'] ?? '';
    $route = $_POST['route'] ?? '';
    $includes = $_POST['includes'] ?? '';
    $sort_order = $_POST['sort_order'] ?? 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $image = null;
    $removeImage = !empty($_POST['remove_image']);
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp','gif'];
        if (in_array($ext, $allowed)) {
            $filename = 'tour_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR . $filename);
            $image = $filename;
        }
    }

    if ($id) {
        $sql = "UPDATE tours SET title=?, slug=?, short_desc=?, full_desc=?, price=?, duration=?, route=?, `includes`=?, sort_order=?, is_active=?";
        $params = [$title, $slug, $short_desc, $full_desc, $price, $duration, $route, $includes, $sort_order, $is_active];
        if ($image) { $sql .= ", image=?"; $params[] = $image; }
        elseif ($removeImage) { $sql .= ", image=?"; $params[] = ''; }
        $sql .= " WHERE id=?";
        $params[] = $id;
        $db->prepare($sql)->execute($params);
    } else {
        $db->prepare("INSERT INTO tours (title, slug, short_desc, full_desc, price, duration, route, `includes`, image, sort_order, is_active) VALUES (?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([$title, $slug, $short_desc, $full_desc, $price, $duration, $route, $includes, $image, $sort_order, $is_active]);
    }
    header('Location: tours.php?msg=saved');
    exit;
}

$tours = $db->query("SELECT * FROM tours ORDER BY sort_order ASC")->fetchAll();

// Düzenleme modu
$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM tours WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editing = $stmt->fetch();
}
$isNew = isset($_GET['new']);
?>

<?php if ($success): ?>
<div class="alert alert-success"><?= sanitize($success) ?></div>
<?php endif; ?>

<?php if ($editing || $isNew): ?>
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-ship"></i> <?= $editing ? 'Tur Düzenle' : 'Yeni Tur Ekle' ?></h3>
        <a href="tours.php" class="btn btn-sm btn-outline">Geri Dön</a>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <?php if ($editing): ?>
            <input type="hidden" name="id" value="<?= $editing['id'] ?>">
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label>Tur Adı *</label>
                    <input type="text" name="title" value="<?= sanitize($editing['title'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>URL Slug</label>
                    <input type="text" name="slug" value="<?= sanitize($editing['slug'] ?? '') ?>" placeholder="Otomatik oluşturulur">
                </div>
            </div>
            <div class="form-group">
                <label>Kısa Açıklama</label>
                <textarea name="short_desc" rows="3"><?= sanitize($editing['short_desc'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>Detaylı Açıklama</label>
                <textarea name="full_desc" rows="6"><?= sanitize($editing['full_desc'] ?? '') ?></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Fiyat</label>
                    <input type="text" name="price" value="<?= sanitize($editing['price'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Süre</label>
                    <input type="text" name="duration" value="<?= sanitize($editing['duration'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Güzergah</label>
                <input type="text" name="route" value="<?= sanitize($editing['route'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Dahil Olanlar (virgülle ayırın)</label>
                <input type="text" name="includes" value="<?= sanitize($editing['includes'] ?? '') ?>">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Görsel</label>
                    <input type="file" name="image" accept="image/*">
                    <?php if (!empty($editing['image'])): ?>
                    <div style="margin-top:10px;display:flex;align-items:flex-start;gap:14px;">
                        <img src="<?= SITE_URL ?>/assets/uploads/<?= sanitize($editing['image']) ?>"
                             alt="Mevcut görsel"
                             style="max-width:180px;max-height:120px;object-fit:cover;border-radius:8px;border:1px solid #e2e8f0;">
                        <div>
                            <p style="font-size:0.8rem;color:#6c757d;margin-bottom:8px;"><?= sanitize($editing['image']) ?></p>
                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;color:#dc3545;font-size:0.9rem;">
                                <input type="checkbox" name="remove_image" value="1">
                                Görseli kaldır
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Sıralama</label>
                    <input type="number" name="sort_order" value="<?= $editing['sort_order'] ?? 0 ?>">
                </div>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" <?= ($editing['is_active'] ?? 1) ? 'checked' : '' ?>>
                    Aktif (sitede göster)
                </label>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Kaydet</button>
        </form>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-ship"></i> Turlar</h3>
        <a href="?new=1" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Yeni Tur</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Sıra</th>
                <th>Görsel</th>
                <th>Tur Adı</th>
                <th>Fiyat</th>
                <th>Süre</th>
                <th>Durum</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tours as $t): ?>
            <tr>
                <td><?= $t['sort_order'] ?></td>
                <td>
                    <?php if (!empty($t['image'])): ?>
                    <img src="<?= SITE_URL ?>/assets/uploads/<?= sanitize($t['image']) ?>" class="img-preview" alt="">
                    <?php else: ?>
                    <div style="width:80px;height:60px;background:#f0f2f5;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#aaa;"><i class="fas fa-ship"></i></div>
                    <?php endif; ?>
                </td>
                <td><strong><?= sanitize($t['title']) ?></strong></td>
                <td><?= sanitize($t['price']) ?></td>
                <td><?= sanitize($t['duration']) ?></td>
                <td>
                    <?= $t['is_active'] ? '<span style="color:#25d366"><i class="fas fa-check-circle"></i> Aktif</span>' : '<span style="color:#dc3545"><i class="fas fa-times-circle"></i> Pasif</span>' ?>
                </td>
                <td>
                    <a href="?edit=<?= $t['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                    <a href="?delete=<?= $t['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu turu silmek istediğinize emin misiniz?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php include 'footer.php'; ?>
