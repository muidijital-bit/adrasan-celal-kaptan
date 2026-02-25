<?php include 'header.php';

$db = getDB();
$tourCount = $db->query("SELECT COUNT(*) as cnt FROM tours")->fetch()['cnt'];
$destCount = $db->query("SELECT COUNT(*) as cnt FROM destinations")->fetch()['cnt'];
$testimonialCount = $db->query("SELECT COUNT(*) as cnt FROM testimonials")->fetch()['cnt'];
$messageCount = $db->query("SELECT COUNT(*) as cnt FROM contact_messages")->fetch()['cnt'];
$unreadMessages = $db->query("SELECT COUNT(*) as cnt FROM contact_messages WHERE is_read = 0")->fetch()['cnt'];

// Galeri sayısı (tablo yoksa hata vermemesi için)
$galleryCount = 0;
try {
    $galleryCount = $db->query("SELECT COUNT(*) as cnt FROM gallery")->fetch()['cnt'];
} catch (Exception $e) {}

// Son mesajlar
$recentMessages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Aktif destinasyonlar
$activeDests = $db->query("SELECT * FROM destinations WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 5")->fetchAll();
?>

<!-- İstatistikler -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-ship"></i></div>
        <div class="stat-info">
            <h3><?= $tourCount ?></h3>
            <p>Aktif Tur</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-map-marker-alt"></i></div>
        <div class="stat-info">
            <h3><?= $destCount ?></h3>
            <p>Destinasyon</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-star"></i></div>
        <div class="stat-info">
            <h3><?= $testimonialCount ?></h3>
            <p>Müşteri Yorumu</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-images"></i></div>
        <div class="stat-info">
            <h3><?= $galleryCount ?></h3>
            <p>Galeri Görseli</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-envelope"></i></div>
        <div class="stat-info">
            <h3><?= $messageCount ?></h3>
            <p>Toplam Mesaj</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-envelope-open"></i></div>
        <div class="stat-info">
            <h3><?= $unreadMessages ?></h3>
            <p>Okunmamış Mesaj</p>
        </div>
    </div>
</div>

<!-- Hızlı İşlemler -->
<h3 style="margin-bottom:16px;font-size:1rem;color:#0a1628;"><i class="fas fa-bolt"></i> Hızlı İşlemler</h3>
<div class="quick-actions">
    <a href="tours.php?new=1" class="quick-action">
        <i class="fas fa-plus-circle" style="background:#e3f2fd;color:#0077b6;"></i>
        <div class="qa-text">
            <strong>Yeni Tur Ekle</strong>
            <span>Tekne turu oluştur</span>
        </div>
    </a>
    <a href="destinations.php?new=1" class="quick-action">
        <i class="fas fa-map-pin" style="background:#fff3e0;color:#f77f00;"></i>
        <div class="qa-text">
            <strong>Yeni Destinasyon</strong>
            <span>Koy/plaj sayfası ekle</span>
        </div>
    </a>
    <a href="gallery.php" class="quick-action">
        <i class="fas fa-camera" style="background:#e8f5e9;color:#25d366;"></i>
        <div class="qa-text">
            <strong>Görsel Yükle</strong>
            <span>Galeriye ekle</span>
        </div>
    </a>
    <a href="testimonials.php?new=1" class="quick-action">
        <i class="fas fa-star" style="background:#f3e5f5;color:#9c27b0;"></i>
        <div class="qa-text">
            <strong>Yorum Ekle</strong>
            <span>Müşteri yorumu gir</span>
        </div>
    </a>
    <a href="messages.php" class="quick-action">
        <i class="fas fa-inbox" style="background:#fce4ec;color:#e91e63;"></i>
        <div class="qa-text">
            <strong>Mesajlar</strong>
            <span><?= $unreadMessages ?> okunmamış</span>
        </div>
    </a>
    <a href="settings.php" class="quick-action">
        <i class="fas fa-cog" style="background:#fff3e0;color:#ff9800;"></i>
        <div class="qa-text">
            <strong>Site Ayarları</strong>
            <span>Telefon, adres, sosyal medya</span>
        </div>
    </a>
    <a href="../index.php" target="_blank" class="quick-action">
        <i class="fas fa-external-link-alt" style="background:#e0f7fa;color:#00bcd4;"></i>
        <div class="qa-text">
            <strong>Siteyi Görüntüle</strong>
            <span>Yeni sekmede aç</span>
        </div>
    </a>
</div>

<!-- İki sütunlu alan -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
    <!-- Son Mesajlar -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-envelope"></i> Son Mesajlar</h3>
            <a href="messages.php" class="btn btn-sm btn-outline">Tümünü Gör</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>İsim</th>
                    <th>Konu</th>
                    <th>Tarih</th>
                    <th>Durum</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentMessages as $msg): ?>
                <tr>
                    <td>
                        <a href="messages.php?view=<?= $msg['id'] ?>" style="color:#333;text-decoration:none;">
                            <strong><?= sanitize($msg['name']) ?></strong>
                        </a>
                    </td>
                    <td><?= sanitize($msg['subject'] ?: '-') ?></td>
                    <td style="font-size:0.85rem;"><?= date('d.m.Y', strtotime($msg['created_at'])) ?></td>
                    <td>
                        <?php if ($msg['is_read']): ?>
                            <span class="status-badge active"><i class="fas fa-check"></i> Okundu</span>
                        <?php else: ?>
                            <span class="status-badge" style="background:#fff3e0;color:#f77f00;"><i class="fas fa-circle" style="font-size:0.5rem"></i> Yeni</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($recentMessages)): ?>
                <tr><td colspan="4" style="text-align:center;color:#6c757d;">Henüz mesaj yok.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Aktif Destinasyonlar -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-map-marker-alt"></i> Destinasyonlar</h3>
            <a href="destinations.php" class="btn btn-sm btn-outline">Tümünü Gör</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Görsel</th>
                    <th>Destinasyon</th>
                    <th>Durum</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activeDests as $d): ?>
                <tr>
                    <td>
                        <?php if ($d['image']): ?>
                        <img src="<?= SITE_URL ?>/assets/uploads/<?= sanitize($d['image']) ?>" style="width:50px;height:38px;object-fit:cover;border-radius:6px;">
                        <?php else: ?>
                        <div style="width:50px;height:38px;background:#f0f2f5;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#ccc;"><i class="fas fa-image"></i></div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= sanitize($d['title']) ?></strong></td>
                    <td><span class="status-badge active"><i class="fas fa-check-circle"></i> Aktif</span></td>
                    <td><a href="destinations.php?edit=<?= $d['id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></a></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($activeDests)): ?>
                <tr><td colspan="4" style="text-align:center;color:#6c757d;">Henüz destinasyon eklenmemiş.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
