<?php include 'header.php';

$db = getDB();
$success = '';

$sectionLabels = [
    // Ana Sayfa
    'hero'              => '🏠 Ana Sayfa — Hero Görseli & Başlık (arka plan fotoğrafı buradan değişir)',
    'tours_section'     => '🚢 Ana Sayfa — Turlar Bölümü Başlığı',
    'koylar_section'    => '🏝️ Ana Sayfa — Koylar Bölümü Başlığı',
    'cta'               => '📣 Ana Sayfa — Alt Rezervasyon Bandı (büyük CTA)',
    // Hakkımızda
    'about_hero'        => '🧭 Hakkımızda — Hero Görseli & Başlık (sayfa üst fotoğrafı)',
    'about'             => '📖 Hakkımızda — Ana Metin & Hikaye',
    'about_vision'      => '🎯 Hakkımızda — Vizyon Metni',
    'about_mission'     => '🚀 Hakkımızda — Misyon Metni',
    'about_checklist'   => '✅ Hakkımızda — Özellikler Listesi (her satır = bir madde)',
    'why_us'            => '⭐ Hakkımızda — Neden Biz? Bölümü',
    // Hizmetlerimiz
    'services_hero'     => '⚓ Hizmetlerimiz — Hero Görseli & Başlık (sayfa üst fotoğrafı)',
    'services_included' => '✔️ Hizmetlerimiz — Tura Dahil Olanlar (her satır = bir madde)',
    'services_excluded' => '❌ Hizmetlerimiz — Tura Dahil Olmayanlar (her satır = bir madde)',
    // Galeri
    'galeri_hero'       => '🖼️ Galeri — Hero Görseli & Başlık (sayfa üst fotoğrafı)',
    // İletişim
    'contact_hero'      => '📍 İletişim — Hero Görseli & Başlık (sayfa üst fotoğrafı)',
];

// Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;
    $title = $_POST['title'] ?? '';
    $subtitle = $_POST['subtitle'] ?? '';
    $content = $_POST['content'] ?? '';

    // Resim yükleme
    $image = null;
    $removeImage = !empty($_POST['remove_image']);

    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (in_array($ext, $allowed) && $_FILES['image']['size'] <= MAX_UPLOAD_SIZE) {
            $filename = 'content_' . $id . '_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR . $filename);
            $image = $filename;
        }
    }

    $sql = "UPDATE page_contents SET title = ?, subtitle = ?, content = ?, image = ?";
    if ($image) {
        $params = [$title, $subtitle, $content, $image];
    } elseif ($removeImage) {
        $params = [$title, $subtitle, $content, ''];
    } else {
        // Görsel değişmedi, mevcut değeri koru
        $sql = "UPDATE page_contents SET title = ?, subtitle = ?, content = ?";
        $params = [$title, $subtitle, $content];
    }
    $sql .= " WHERE id = ?";
    $params[] = $id;

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $success = 'İçerik başarıyla güncellendi!';
}

$contents = $db->query("SELECT * FROM page_contents ORDER BY sort_order ASC")->fetchAll();

// Düzenleme modu
$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM page_contents WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editing = $stmt->fetch();
}
?>

<?php if ($success): ?>
<div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<?php if ($editing): ?>
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-edit"></i> İçerik Düzenle: <?= $sectionLabels[$editing['section_key']] ?? sanitize($editing['section_key']) ?></h3>
        <a href="contents.php" class="btn btn-sm btn-outline">Geri Dön</a>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $editing['id'] ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>Ana Başlık <small style="color:#888;">(Büyük h2 başlık)</small></label>
                    <input type="text" name="title" value="<?= sanitize($editing['title']) ?>">
                </div>
                <div class="form-group">
                    <label>Üst Etiket <small style="color:#888;">(Küçük renkli etiket, örn: "Turlarımız")</small></label>
                    <input type="text" name="subtitle" value="<?= sanitize($editing['subtitle']) ?>">
                </div>
            </div>
            <div class="form-group">
                <label>İçerik / Açıklama
                    <?php if (in_array($editing['section_key'], ['about_checklist','services_included','services_excluded'])): ?>
                    <small style="color:#0077b6;background:#e3f2fd;padding:2px 8px;border-radius:4px;margin-left:6px;">Her satır ayrı bir madde olarak görüntülenir</small>
                    <?php else: ?>
                    <small style="color:#888;">(Başlığın altındaki açıklama metni)</small>
                    <?php endif; ?>
                </label>
                <textarea name="content" rows="<?= in_array($editing['section_key'], ['about_checklist','services_included','services_excluded']) ? '8' : '4' ?>"><?= sanitize($editing['content']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Görsel <small style="color:#888;">(Opsiyonel – bölüm görseli)</small></label>
                <input type="file" name="image" accept="image/*">
                <?php if ($editing['image']): ?>
                <div style="margin-top:10px;display:flex;align-items:flex-start;gap:14px;">
                    <img src="<?= SITE_URL ?>/assets/uploads/<?= sanitize($editing['image']) ?>"
                         alt="Mevcut görsel"
                         style="max-width:220px;max-height:140px;border-radius:8px;border:1px solid #e2e8f0;">
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
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Kaydet</button>
        </form>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-file-alt"></i> Sayfa İçerikleri</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>Bölüm</th>
                <th>Başlık</th>
                <th>Alt Başlık</th>
                <th>Durum</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contents as $c): ?>
            <tr>
                <td><strong><?= $sectionLabels[$c['section_key']] ?? sanitize($c['section_key']) ?></strong></td>
                <td><?= sanitize($c['title']) ?></td>
                <td><?= sanitize($c['subtitle'] ?: '-') ?></td>
                <td>
                    <?php if ($c['is_active']): ?>
                        <span style="color:#25d366"><i class="fas fa-check-circle"></i> Aktif</span>
                    <?php else: ?>
                        <span style="color:#dc3545"><i class="fas fa-times-circle"></i> Pasif</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="?edit=<?= $c['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Düzenle</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php include 'footer.php'; ?>
