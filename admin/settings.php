<?php include 'header.php';

$db = getDB();
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        if ($key === 'action') continue;
        $db->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?")
            ->execute([$value, $key]);
    }
    $success = 'Ayarlar başarıyla güncellendi!';
}

// Ayarları gruplara göre çek
$settings = $db->query("SELECT * FROM site_settings ORDER BY id ASC")->fetchAll();
$groups = [];
foreach ($settings as $s) {
    $groups[$s['setting_group']][] = $s;
}

$activeTab = $_GET['tab'] ?? 'general';
?>

<?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

<div class="tabs">
    <a href="?tab=general" class="tab <?= $activeTab === 'general' ? 'active' : '' ?>"><i class="fas fa-cog"></i> Genel</a>
    <a href="?tab=contact" class="tab <?= $activeTab === 'contact' ? 'active' : '' ?>"><i class="fas fa-phone"></i> İletişim</a>
    <a href="?tab=social" class="tab <?= $activeTab === 'social' ? 'active' : '' ?>"><i class="fas fa-share-alt"></i> Sosyal Medya</a>
    <a href="?tab=seo" class="tab <?= $activeTab === 'seo' ? 'active' : '' ?>"><i class="fas fa-code"></i> Entegrasyonlar</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="action" value="save">
            <?php if (isset($groups[$activeTab])): ?>
                <?php foreach ($groups[$activeTab] as $s): ?>
                <div class="form-group">
                    <label><?= sanitize($s['setting_label']) ?></label>
                    <?php if (strlen($s['setting_value']) > 100 || $s['setting_key'] === 'google_maps'): ?>
                        <textarea name="<?= sanitize($s['setting_key']) ?>" rows="4"><?= sanitize($s['setting_value']) ?></textarea>
                    <?php else: ?>
                        <input type="text" name="<?= sanitize($s['setting_key']) ?>" value="<?= sanitize($s['setting_value']) ?>">
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color:#6c757d;">Bu grupta ayar bulunamadı.</p>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Ayarları Kaydet</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
