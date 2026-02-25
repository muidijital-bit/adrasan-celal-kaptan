<?php include 'header.php';

$db = getDB();
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $meta_title = $_POST['meta_title'] ?? '';
    $meta_description = $_POST['meta_description'] ?? '';
    $meta_keywords = $_POST['meta_keywords'] ?? '';
    $robots = $_POST['robots'] ?? 'index, follow';
    $canonical_url = $_POST['canonical_url'] ?? '';

    $image = null;
    if (!empty($_FILES['og_image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['og_image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp'])) {
            $filename = 'og_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['og_image']['tmp_name'], UPLOAD_DIR . $filename);
            $image = $filename;
        }
    }

    $sql = "UPDATE seo_settings SET meta_title=?, meta_description=?, meta_keywords=?, robots=?, canonical_url=?";
    $params = [$meta_title, $meta_description, $meta_keywords, $robots, $canonical_url];
    if ($image) { $sql .= ", og_image=?"; $params[] = $image; }
    $sql .= " WHERE id=?";
    $params[] = $id;

    $db->prepare($sql)->execute($params);
    $success = 'SEO ayarları güncellendi!';
}

$seoList = $db->query("SELECT * FROM seo_settings ORDER BY id ASC")->fetchAll();

$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM seo_settings WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editing = $stmt->fetch();
}
?>

<?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

<?php if ($editing): ?>
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-search"></i> SEO Düzenle: <?= sanitize($editing['page_slug']) ?></h3>
        <a href="seo.php" class="btn btn-sm btn-outline">Geri Dön</a>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $editing['id'] ?>">

            <div class="form-group">
                <label>Meta Başlık (Title Tag) <small style="color:#6c757d;">- Max 60 karakter önerilir</small></label>
                <input type="text" name="meta_title" value="<?= sanitize($editing['meta_title']) ?>" maxlength="70">
                <p style="font-size:0.8rem;color:#6c757d;margin-top:4px;">Google arama sonuçlarında görünen başlık</p>
            </div>

            <div class="form-group">
                <label>Meta Açıklama (Description) <small style="color:#6c757d;">- Max 160 karakter önerilir</small></label>
                <textarea name="meta_description" rows="3" maxlength="200"><?= sanitize($editing['meta_description']) ?></textarea>
                <p style="font-size:0.8rem;color:#6c757d;margin-top:4px;">Google arama sonuçlarında görünen açıklama</p>
            </div>

            <div class="form-group">
                <label>Anahtar Kelimeler (Keywords) <small style="color:#6c757d;">- Virgülle ayırın</small></label>
                <textarea name="meta_keywords" rows="2"><?= sanitize($editing['meta_keywords']) ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Robots</label>
                    <select name="robots">
                        <option value="index, follow" <?= $editing['robots'] === 'index, follow' ? 'selected' : '' ?>>index, follow</option>
                        <option value="noindex, follow" <?= $editing['robots'] === 'noindex, follow' ? 'selected' : '' ?>>noindex, follow</option>
                        <option value="index, nofollow" <?= $editing['robots'] === 'index, nofollow' ? 'selected' : '' ?>>index, nofollow</option>
                        <option value="noindex, nofollow" <?= $editing['robots'] === 'noindex, nofollow' ? 'selected' : '' ?>>noindex, nofollow</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Canonical URL</label>
                    <input type="url" name="canonical_url" value="<?= sanitize($editing['canonical_url']) ?>" placeholder="https://...">
                </div>
            </div>

            <div class="form-group">
                <label>OG Image (Sosyal medya paylaşım görseli)</label>
                <input type="file" name="og_image" accept="image/*">
                <?php if ($editing['og_image']): ?>
                <p style="margin-top:6px;font-size:0.85rem;color:#6c757d;">Mevcut: <?= sanitize($editing['og_image']) ?></p>
                <?php endif; ?>
            </div>

            <!-- SEO Önizleme -->
            <div style="background:#f8f9fa;padding:20px;border-radius:10px;margin-bottom:20px;">
                <p style="font-size:0.8rem;color:#6c757d;margin-bottom:8px;">Google Arama Önizleme:</p>
                <div style="max-width:600px;">
                    <p style="color:#1a0dab;font-size:1.1rem;margin-bottom:2px;"><?= sanitize($editing['meta_title'] ?: 'Sayfa Başlığı') ?></p>
                    <p style="color:#006621;font-size:0.85rem;margin-bottom:4px;">adrasancelalkaptan.com.tr › <?= sanitize($editing['page_slug']) ?></p>
                    <p style="color:#545454;font-size:0.85rem;"><?= sanitize(substr($editing['meta_description'], 0, 160)) ?></p>
                </div>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> SEO Ayarlarını Kaydet</button>
        </form>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-search"></i> SEO Ayarları</h3>
    </div>
    <table>
        <thead><tr><th>Sayfa</th><th>Meta Başlık</th><th>Meta Açıklama</th><th>Robots</th><th>İşlem</th></tr></thead>
        <tbody>
            <?php foreach ($seoList as $s): ?>
            <tr>
                <td><strong><?= sanitize($s['page_slug']) ?></strong></td>
                <td style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= sanitize($s['meta_title']) ?></td>
                <td style="max-width:250px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= sanitize($s['meta_description']) ?></td>
                <td><?= sanitize($s['robots']) ?></td>
                <td><a href="?edit=<?= $s['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Düzenle</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="card" style="margin-top:24px;">
    <div class="card-header">
        <h3><i class="fas fa-lightbulb"></i> SEO İpuçları</h3>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div style="padding:16px;background:#e3f2fd;border-radius:8px;">
                <strong>Meta Başlık</strong><br>
                <small>60 karakteri geçmemeye çalışın. Ana anahtar kelimeyi başa koyun.</small>
            </div>
            <div style="padding:16px;background:#e8f5e9;border-radius:8px;">
                <strong>Meta Açıklama</strong><br>
                <small>150-160 karakter ideal. Harekete geçirici ifadeler kullanın.</small>
            </div>
            <div style="padding:16px;background:#fff3e0;border-radius:8px;">
                <strong>Anahtar Kelimeler</strong><br>
                <small>Her sayfa için 5-10 anahtar kelime yeterli. Doğal kullanın.</small>
            </div>
            <div style="padding:16px;background:#f3e5f5;border-radius:8px;">
                <strong>OG Image</strong><br>
                <small>1200x630 piksel önerilir. Sosyal medya paylaşımlarında görünür.</small>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'footer.php'; ?>
