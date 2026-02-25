<?php include 'header.php';

$db = getDB();
$success = '';
$error = '';

// Silme
if (isset($_GET['delete'])) {
    // Önce dosyayı bul
    $stmt = $db->prepare("SELECT image FROM gallery WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $img = $stmt->fetch();
    // Veritabanından sil
    $db->prepare("DELETE FROM gallery WHERE id = ?")->execute([$_GET['delete']]);
    header('Location: gallery.php?msg=deleted');
    exit;
}

// Durum değiştir
if (isset($_GET['toggle'])) {
    $db->prepare("UPDATE gallery SET is_active = NOT is_active WHERE id = ?")->execute([$_GET['toggle']]);
    header('Location: gallery.php?msg=updated');
    exit;
}

if (isset($_GET['msg'])) {
    $msgs = ['deleted' => 'Görsel silindi!', 'saved' => 'Görsel kaydedildi!', 'updated' => 'Durum güncellendi!', 'uploaded' => 'Görseller yüklendi!'];
    $success = $msgs[$_GET['msg']] ?? '';
}

// Tekli düzenleme kaydet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = $_POST['id'] ?? '';
    $title = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? '';
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $db->prepare("UPDATE gallery SET title=?, category=?, sort_order=?, is_active=? WHERE id=?")
        ->execute([$title, $category, $sort_order, $is_active, $id]);
    header('Location: gallery.php?msg=saved');
    exit;
}

// Toplu yükleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
    $category = $_POST['category'] ?? 'genel';
    $uploaded = 0;
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $i => $name) {
            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
            if ($_FILES['images']['size'][$i] > MAX_UPLOAD_SIZE) continue;

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) continue;

            $filename = 'gallery_' . time() . '_' . $i . '.' . $ext;
            $title = pathinfo($name, PATHINFO_FILENAME);

            if (move_uploaded_file($_FILES['images']['tmp_name'][$i], UPLOAD_DIR . $filename)) {
                $db->prepare("INSERT INTO gallery (title, image, category, sort_order, is_active) VALUES (?,?,?,?,1)")
                    ->execute([$title, $filename, $category, $uploaded]);
                $uploaded++;
            }
        }
    }

    if ($uploaded > 0) {
        header('Location: gallery.php?msg=uploaded');
    } else {
        $error = 'Hiçbir görsel yüklenemedi. Dosya formatını ve boyutunu kontrol edin.';
    }
}

// Filtreleme
$filterCat = $_GET['cat'] ?? '';
if ($filterCat) {
    $stmt = $db->prepare("SELECT * FROM gallery WHERE category = ? ORDER BY sort_order ASC, id DESC");
    $stmt->execute([$filterCat]);
    $gallery = $stmt->fetchAll();
} else {
    $gallery = $db->query("SELECT * FROM gallery ORDER BY sort_order ASC, id DESC")->fetchAll();
}

// Kategorileri getir
$categories = $db->query("SELECT DISTINCT category FROM gallery ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

// Düzenleme modu
$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM gallery WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editing = $stmt->fetch();
}
?>

<?php if ($success): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= sanitize($success) ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= sanitize($error) ?></div>
<?php endif; ?>

<?php if ($editing): ?>
<!-- Düzenleme Formu -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-edit"></i> Görsel Düzenle</h3>
        <a href="gallery.php" class="btn btn-sm btn-outline"><i class="fas fa-arrow-left"></i> Geri Dön</a>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?= $editing['id'] ?>">

            <div style="display:flex;gap:24px;margin-bottom:24px;">
                <div>
                    <img src="<?= SITE_URL ?>/assets/uploads/<?= sanitize($editing['image']) ?>" style="width:240px;height:180px;object-fit:cover;border-radius:12px;border:2px solid #e9ecef;">
                </div>
                <div style="flex:1;">
                    <div class="form-group">
                        <label>Başlık</label>
                        <input type="text" name="title" value="<?= sanitize($editing['title']) ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="category">
                                <option value="genel" <?= $editing['category'] === 'genel' ? 'selected' : '' ?>>Genel</option>
                                <option value="tekne" <?= $editing['category'] === 'tekne' ? 'selected' : '' ?>>Tekne</option>
                                <option value="koylar" <?= $editing['category'] === 'koylar' ? 'selected' : '' ?>>Koylar</option>
                                <option value="suluada" <?= $editing['category'] === 'suluada' ? 'selected' : '' ?>>Suluada</option>
                                <option value="misafirler" <?= $editing['category'] === 'misafirler' ? 'selected' : '' ?>>Misafirler</option>
                                <option value="yemek" <?= $editing['category'] === 'yemek' ? 'selected' : '' ?>>Yemek</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Sıralama</label>
                            <input type="number" name="sort_order" value="<?= $editing['sort_order'] ?>" min="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label><input type="checkbox" name="is_active" <?= $editing['is_active'] ? 'checked' : '' ?>> Aktif (sitede göster)</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Kaydet</button>
        </form>
    </div>
</div>

<?php else: ?>

<!-- Toplu Yükleme -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-cloud-upload-alt"></i> Görsel Yükle</h3>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="upload">
            <div class="form-row">
                <div class="form-group">
                    <label>Görseller (birden fazla seçebilirsiniz)</label>
                    <input type="file" name="images[]" accept="image/*" multiple required>
                    <p class="form-help">JPG, PNG, WebP, GIF - Max 5MB / görsel</p>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="category">
                        <option value="genel">Genel</option>
                        <option value="tekne">Tekne</option>
                        <option value="koylar">Koylar</option>
                        <option value="suluada">Suluada</option>
                        <option value="misafirler">Misafirler</option>
                        <option value="yemek">Yemek</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Yükle</button>
        </form>
    </div>
</div>

<!-- Kategori Filtreleme -->
<div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
    <span style="font-size:0.9rem;color:#6c757d;font-weight:600;">Filtrele:</span>
    <a href="gallery.php" class="btn btn-sm <?= !$filterCat ? 'btn-primary' : 'btn-outline' ?>">Tümü (<?= count($db->query("SELECT id FROM gallery")->fetchAll()) ?>)</a>
    <?php foreach ($categories as $cat): ?>
    <?php $catCount = $db->prepare("SELECT COUNT(*) FROM gallery WHERE category = ?"); $catCount->execute([$cat]); ?>
    <a href="?cat=<?= urlencode($cat) ?>" class="btn btn-sm <?= $filterCat === $cat ? 'btn-primary' : 'btn-outline' ?>"><?= ucfirst(sanitize($cat)) ?> (<?= $catCount->fetchColumn() ?>)</a>
    <?php endforeach; ?>
</div>

<!-- Galeri Listesi -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-images"></i> Görseller <?= $filterCat ? '- ' . ucfirst(sanitize($filterCat)) : '' ?> (<?= count($gallery) ?>)</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($gallery)): ?>
        <div class="gallery-admin-grid">
            <?php foreach ($gallery as $g): ?>
            <div class="gallery-admin-item" style="<?= !$g['is_active'] ? 'opacity:0.5;' : '' ?>">
                <img src="<?= SITE_URL ?>/assets/uploads/<?= sanitize($g['image']) ?>" alt="<?= sanitize($g['title']) ?>">
                <div class="gallery-actions">
                    <a href="?toggle=<?= $g['id'] ?><?= $filterCat ? '&cat=' . urlencode($filterCat) : '' ?>" class="btn btn-sm <?= $g['is_active'] ? 'btn-success' : 'btn-outline' ?>" style="background:<?= $g['is_active'] ? '#25d366' : 'rgba(255,255,255,0.9)' ?>;color:<?= $g['is_active'] ? '#fff' : '#6c757d' ?>" title="<?= $g['is_active'] ? 'Pasife Al' : 'Aktife Al' ?>">
                        <i class="fas fa-<?= $g['is_active'] ? 'eye' : 'eye-slash' ?>"></i>
                    </a>
                    <a href="?delete=<?= $g['id'] ?><?= $filterCat ? '&cat=' . urlencode($filterCat) : '' ?>" class="btn btn-sm" style="background:rgba(220,53,69,0.9);color:#fff;" onclick="return confirm('Bu görseli silmek istediğinize emin misiniz?')" title="Sil">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
                <div class="gallery-info">
                    <p><strong><?= sanitize($g['title'] ?: 'Başlıksız') ?></strong></p>
                    <p><?= ucfirst(sanitize($g['category'])) ?> · Sıra: <?= $g['sort_order'] ?></p>
                    <a href="?edit=<?= $g['id'] ?>" style="font-size:0.8rem;color:#0077b6;">Düzenle</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="text-align:center;padding:40px;color:#6c757d;">
            <i class="fas fa-images" style="font-size:2.5rem;margin-bottom:12px;display:block;"></i>
            <p>Henüz görsel yüklenmemiş.</p>
            <p style="font-size:0.85rem;">Yukarıdaki formu kullanarak görsel yükleyebilirsiniz.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php include 'footer.php'; ?>
