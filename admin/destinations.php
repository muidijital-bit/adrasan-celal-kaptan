<?php include 'header.php';

$db = getDB();
$success = '';

// Silme
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $db->prepare("DELETE FROM destinations WHERE id = ?")->execute([$_GET['delete']]);
    header('Location: destinations.php?msg=deleted');
    exit;
}

// Galeri resim silme
if (isset($_GET['del_img']) && isset($_GET['dest_id'])) {
    $destId = intval($_GET['dest_id']);
    $imgName = basename($_GET['del_img']);
    $stmt = $db->prepare("SELECT gallery_images FROM destinations WHERE id = ?");
    $stmt->execute([$destId]);
    $row = $stmt->fetch();
    if ($row) {
        $imgs = array_filter(array_map('trim', explode(',', $row['gallery_images'] ?? '')));
        $imgs = array_values(array_filter($imgs, fn($i) => $i !== $imgName));
        $db->prepare("UPDATE destinations SET gallery_images = ? WHERE id = ?")->execute([implode(',', $imgs) ?: null, $destId]);
    }
    header("Location: destinations.php?edit=$destId");
    exit;
}

// Kaydet
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = intval($_POST['id'] ?? 0);
    $title       = trim($_POST['title'] ?? '');
    $slug        = trim($_POST['slug'] ?? '');
    $subtitle    = trim($_POST['subtitle'] ?? '');
    $short_desc  = trim($_POST['short_desc'] ?? '');
    $full_desc   = trim($_POST['full_desc'] ?? '');
    $history_text = trim($_POST['history_text'] ?? '');
    $features    = trim($_POST['features'] ?? '');
    $sort_order  = intval($_POST['sort_order'] ?? 0);
    $is_active   = isset($_POST['is_active']) ? 1 : 0;
    $meta_title  = trim($_POST['meta_title'] ?? '');
    $meta_desc   = trim($_POST['meta_description'] ?? '');
    $meta_kw     = trim($_POST['meta_keywords'] ?? '');

    // FAQ JSON oluştur
    $faqQs  = $_POST['faq_q'] ?? [];
    $faqAs  = $_POST['faq_a'] ?? [];
    $faqArr = [];
    foreach ($faqQs as $i => $q) {
        $q = trim($q); $a = trim($faqAs[$i] ?? '');
        if ($q && $a) $faqArr[] = ['q' => $q, 'a' => $a];
    }
    $faq_json = !empty($faqArr) ? json_encode($faqArr, JSON_UNESCAPED_UNICODE) : null;

    // Ana görsel yükle
    $image = $_POST['existing_image'] ?? null;
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp']) && $_FILES['image']['size'] <= MAX_UPLOAD_SIZE) {
            $filename = 'dest_' . $slug . '_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_DIR . $filename);
            $image = $filename;
        }
    }

    // Galeri resimleri yükle
    $gallery_images = trim($_POST['existing_gallery'] ?? '');
    if (!empty($_FILES['gallery']['name'][0])) {
        $newImgs = [];
        foreach ($_FILES['gallery']['tmp_name'] as $i => $tmpName) {
            if (!$_FILES['gallery']['error'][$i]) {
                $ext = strtolower(pathinfo($_FILES['gallery']['name'][$i], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                    $filename = 'dest_gal_' . $slug . '_' . time() . '_' . $i . '.' . $ext;
                    move_uploaded_file($tmpName, UPLOAD_DIR . $filename);
                    $newImgs[] = $filename;
                }
            }
        }
        if ($newImgs) {
            $gallery_images = trim(($gallery_images ? $gallery_images . ',' : '') . implode(',', $newImgs), ',');
        }
    }

    if ($id > 0) {
        $stmt = $db->prepare("UPDATE destinations SET title=?, slug=?, subtitle=?, short_desc=?, full_desc=?, history_text=?, features=?, faq_json=?, image=?, gallery_images=?, meta_title=?, meta_description=?, meta_keywords=?, sort_order=?, is_active=?, updated_at=NOW() WHERE id=?");
        $stmt->execute([$title, $slug, $subtitle, $short_desc, $full_desc, $history_text, $features, $faq_json, $image, $gallery_images ?: null, $meta_title, $meta_desc, $meta_kw, $sort_order, $is_active, $id]);
    } else {
        $stmt = $db->prepare("INSERT INTO destinations (title, slug, subtitle, short_desc, full_desc, history_text, features, faq_json, image, gallery_images, meta_title, meta_description, meta_keywords, sort_order, is_active) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$title, $slug, $subtitle, $short_desc, $full_desc, $history_text, $features, $faq_json, $image, $gallery_images ?: null, $meta_title, $meta_desc, $meta_kw, $sort_order, $is_active]);
        $id = $db->lastInsertId();
    }

    header("Location: destinations.php?edit=$id&saved=1");
    exit;
}

$destinations = $db->query("SELECT * FROM destinations ORDER BY sort_order ASC")->fetchAll();

$editing = null;
if (isset($_GET['edit'])) {
    if ($_GET['edit'] === 'new') {
        // Yeni destinasyon - boş form
        $editing = [
            'id' => 0, 'title' => '', 'slug' => '', 'subtitle' => '',
            'short_desc' => '', 'full_desc' => '', 'history_text' => '',
            'features' => '', 'faq_json' => '', 'image' => '',
            'gallery_images' => '', 'meta_title' => '', 'meta_description' => '',
            'meta_keywords' => '', 'sort_order' => count($destinations) + 1, 'is_active' => 1,
            'faq_items' => [], 'gallery_list' => []
        ];
    } elseif (is_numeric($_GET['edit'])) {
        $stmt = $db->prepare("SELECT * FROM destinations WHERE id = ?");
        $stmt->execute([$_GET['edit']]);
        $editing = $stmt->fetch();
        if ($editing) {
            $editing['faq_items'] = !empty($editing['faq_json']) ? (json_decode($editing['faq_json'], true) ?: []) : [];
            $editing['gallery_list'] = !empty($editing['gallery_images']) ? array_filter(array_map('trim', explode(',', $editing['gallery_images']))) : [];
        }
    }
}
?>

<script>document.getElementById('pageTitle').textContent = '<?= $editing ? 'Destinasyon Düzenle' : 'Destinasyonlar' ?>';</script>

<?php if (isset($_GET['saved'])): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> Destinasyon başarıyla kaydedildi!</div>
<?php endif; ?>
<?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
<div class="alert alert-error"><i class="fas fa-trash"></i> Destinasyon silindi.</div>
<?php endif; ?>

<?php if ($editing): ?>
<!-- DÜZENLEME FORMU -->
<div style="margin-bottom:16px;">
    <a href="destinations.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Listeye Dön</a>
    <a href="<?= SITE_URL ?>/<?= sanitize($editing['slug']) ?>" target="_blank" class="btn btn-sm btn-outline" style="margin-left:8px;"><i class="fas fa-external-link-alt"></i> Sayfayı Gör</a>
</div>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $editing['id'] ?>">
    <input type="hidden" name="existing_image" value="<?= sanitize($editing['image'] ?? '') ?>">
    <input type="hidden" name="existing_gallery" value="<?= sanitize($editing['gallery_images'] ?? '') ?>">

    <!-- Temel Bilgiler -->
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header"><h3><i class="fas fa-info-circle"></i> Temel Bilgiler</h3></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Başlık *</label>
                    <input type="text" name="title" value="<?= sanitize($editing['title']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Slug (URL) *</label>
                    <input type="text" name="slug" value="<?= sanitize($editing['slug']) ?>" required>
                    <p class="form-help">Örn: suluada-akseki-koyu</p>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Alt Başlık</label>
                    <input type="text" name="subtitle" value="<?= sanitize($editing['subtitle'] ?? '') ?>">
                    <p class="form-help">Hero bölümünde küçük etiket olarak gösterilir</p>
                </div>
                <div class="form-row" style="grid-template-columns:80px 1fr;gap:12px;align-items:end;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Sıra</label>
                        <input type="number" name="sort_order" value="<?= intval($editing['sort_order']) ?>">
                    </div>
                    <div class="form-group" style="margin-bottom:0;display:flex;align-items:center;gap:10px;padding-top:28px;">
                        <input type="checkbox" name="is_active" id="is_active" <?= $editing['is_active'] ? 'checked' : '' ?> style="width:auto;">
                        <label for="is_active" style="margin-bottom:0;">Aktif</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Kısa Açıklama</label>
                <textarea name="short_desc" rows="3"><?= sanitize($editing['short_desc'] ?? '') ?></textarea>
                <p class="form-help">Hero bölümünde gösterilir, SEO için de kullanılır</p>
            </div>
        </div>
    </div>

    <!-- İçerik -->
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header"><h3><i class="fas fa-align-left"></i> Sayfa İçeriği</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label>Tam Açıklama (Genel Bilgi)</label>
                <textarea name="full_desc" rows="8"><?= sanitize($editing['full_desc'] ?? '') ?></textarea>
                <p class="form-help">Her paragraf ayrı satıra yazılabilir</p>
            </div>
            <div class="form-group">
                <label>Tarihçe</label>
                <textarea name="history_text" rows="4"><?= sanitize($editing['history_text'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>Öne Çıkan Özellikler</label>
                <input type="text" name="features" value="<?= sanitize($editing['features'] ?? '') ?>">
                <p class="form-help">Özellikler arasına | koyun. Örn: Kristal sular|Yüzme|Snorkeling</p>
            </div>
        </div>
    </div>

    <!-- Görseller -->
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header"><h3><i class="fas fa-images"></i> Görseller</h3></div>
        <div class="card-body">
            <div class="form-section">
                <h4><i class="fas fa-image"></i> Ana Görsel (Hero)</h4>
                <?php if (!empty($editing['image'])): ?>
                <div style="margin-bottom:12px;">
                    <img src="<?= SITE_URL ?>/assets/uploads/<?= sanitize($editing['image']) ?>" class="img-preview-lg" alt="">
                    <p class="form-help" style="margin-top:6px;"><?= sanitize($editing['image']) ?></p>
                </div>
                <?php endif; ?>
                <input type="file" name="image" accept="image/*">
                <p class="form-help">Önerilen: 1920×1080px, max 5MB (JPG, PNG, WebP)</p>
            </div>
            <div class="form-section">
                <h4><i class="fas fa-th"></i> Galeri Fotoğrafları</h4>
                <?php if (!empty($editing['gallery_list'])): ?>
                <div class="gallery-admin-grid" style="margin-bottom:16px;">
                    <?php foreach ($editing['gallery_list'] as $img): ?>
                    <div class="gallery-admin-item">
                        <img src="<?= SITE_URL ?>/assets/uploads/<?= sanitize($img) ?>" alt="">
                        <div class="gallery-actions">
                            <a href="?edit=<?= $editing['id'] ?>&del_img=<?= urlencode($img) ?>&dest_id=<?= $editing['id'] ?>"
                               onclick="return confirm('Bu fotoğrafı kaldırmak istediğinizden emin misiniz?')"
                               style="background:#dc3545;color:#fff;">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p style="color:#6c757d;font-size:0.9rem;margin-bottom:12px;">Henüz galeri fotoğrafı eklenmemiş.</p>
                <?php endif; ?>
                <input type="file" name="gallery[]" accept="image/*" multiple>
                <p class="form-help">Birden fazla fotoğraf seçebilirsiniz (max 5MB/adet)</p>
            </div>
        </div>
    </div>

    <!-- FAQ -->
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header"><h3><i class="fas fa-question-circle"></i> S.S.S. (Sık Sorulan Sorular)</h3></div>
        <div class="card-body">
            <div class="faq-builder" id="faqBuilder">
                <?php foreach ($editing['faq_items'] as $i => $faq): ?>
                <div class="faq-builder-item">
                    <input type="text" name="faq_q[]" placeholder="Soru" value="<?= sanitize($faq['q'] ?? '') ?>">
                    <textarea name="faq_a[]" placeholder="Cevap"><?= sanitize($faq['a'] ?? '') ?></textarea>
                    <button type="button" class="faq-remove" onclick="this.closest('.faq-builder-item').remove()"><i class="fas fa-times"></i></button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="faq-add" onclick="addFaq()"><i class="fas fa-plus"></i> Soru Ekle</button>
        </div>
    </div>

    <!-- SEO -->
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header"><h3><i class="fas fa-search"></i> SEO Ayarları</h3></div>
        <div class="card-body">
            <div class="form-group">
                <label>Meta Başlık</label>
                <input type="text" name="meta_title" value="<?= sanitize($editing['meta_title'] ?? '') ?>">
                <p class="form-help">Boş bırakılırsa sayfa başlığı kullanılır</p>
            </div>
            <div class="form-group">
                <label>Meta Açıklama</label>
                <textarea name="meta_description" rows="3"><?= sanitize($editing['meta_description'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>Anahtar Kelimeler</label>
                <input type="text" name="meta_keywords" value="<?= sanitize($editing['meta_keywords'] ?? '') ?>">
            </div>
        </div>
    </div>

    <div style="display:flex;gap:12px;margin-bottom:40px;">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Kaydet</button>
        <a href="destinations.php" class="btn btn-outline">İptal</a>
    </div>
</form>

<?php else: ?>
<!-- LİSTE -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-map-marker-alt"></i> Destinasyonlar</h3>
        <a href="destinations.php?edit=new" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Yeni Ekle</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Sıra</th>
                <th>Görsel</th>
                <th>Başlık</th>
                <th>Slug</th>
                <th>Durum</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($destinations as $d): ?>
            <tr>
                <td><?= $d['sort_order'] ?></td>
                <td>
                    <?php if ($d['image']): ?>
                    <img src="<?= SITE_URL ?>/assets/uploads/<?= sanitize($d['image']) ?>" class="img-preview" alt="">
                    <?php else: ?>
                    <div style="width:80px;height:60px;background:#e9ecef;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#adb5bd;"><i class="fas fa-image"></i></div>
                    <?php endif; ?>
                </td>
                <td><strong><?= sanitize($d['title']) ?></strong><br><small style="color:#6c757d;"><?= sanitize($d['subtitle'] ?? '') ?></small></td>
                <td><code style="font-size:0.8rem;background:#f0f2f5;padding:2px 6px;border-radius:4px;">/<?= sanitize($d['slug']) ?></code></td>
                <td>
                    <?php if ($d['is_active']): ?>
                    <span class="status-badge active"><i class="fas fa-circle" style="font-size:0.5rem;"></i> Aktif</span>
                    <?php else: ?>
                    <span class="status-badge inactive"><i class="fas fa-circle" style="font-size:0.5rem;"></i> Pasif</span>
                    <?php endif; ?>
                </td>
                <td style="display:flex;gap:6px;flex-wrap:wrap;">
                    <a href="?edit=<?= $d['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Düzenle</a>
                    <a href="<?= SITE_URL ?>/<?= sanitize($d['slug']) ?>" target="_blank" class="btn btn-sm btn-outline"><i class="fas fa-eye"></i></a>
                    <a href="?delete=<?= $d['id'] ?>" class="btn btn-sm btn-danger"
                       onclick="return confirm('Bu destinasyonu silmek istediğinizden emin misiniz?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<script>
function addFaq() {
    const builder = document.getElementById('faqBuilder');
    const div = document.createElement('div');
    div.className = 'faq-builder-item';
    div.innerHTML = `
        <input type="text" name="faq_q[]" placeholder="Soru">
        <textarea name="faq_a[]" placeholder="Cevap"></textarea>
        <button type="button" class="faq-remove" onclick="this.closest('.faq-builder-item').remove()"><i class="fas fa-times"></i></button>
    `;
    builder.appendChild(div);
}
</script>

<?php include 'footer.php'; ?>
