<?php
require_once 'includes/config.php';

$slug = $_GET['slug'] ?? '';
if (empty($slug)) { header('Location: index.php'); exit; }

$db = getDB();
$stmt = $db->prepare("SELECT * FROM tours WHERE slug = ? AND is_active = 1");
$stmt->execute([$slug]);
$tour = $stmt->fetch();

if (!$tour) { header('Location: index.php'); exit; }

$seo = getSEO($slug);
$phone = getSetting('phone', '0543 717 33 78');
$whatsapp = getSetting('whatsapp', '905437173378');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($seo['meta_title'] ?: $tour['title'] . ' | Celal Kaptan') ?></title>
    <meta name="description" content="<?= sanitize($seo['meta_description'] ?: $tour['short_desc']) ?>">
    <meta name="keywords" content="<?= sanitize($seo['meta_keywords'] ?? '') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .tour-hero { background:linear-gradient(135deg,#0a1628,#023e8a); padding:140px 0 80px; color:#fff; text-align:center; }
        .tour-hero h1 { font-family:'Playfair Display',serif; font-size:clamp(2rem,5vw,3.5rem); margin-bottom:12px; }
        .tour-detail { padding:60px 0; }
        .tour-detail-grid { display:grid; grid-template-columns:2fr 1fr; gap:40px; }
        .tour-info-card { background:#fff; padding:32px; border-radius:16px; box-shadow:0 8px 30px rgba(0,0,0,0.08); position:sticky; top:100px; }
        .tour-info-card h3 { margin-bottom:20px; color:#0a1628; }
        .info-row { display:flex; align-items:center; gap:12px; padding:12px 0; border-bottom:1px solid #f0f0f0; }
        .info-row i { color:#0077b6; width:20px; }
        .tour-content p { line-height:1.9; color:#495057; margin-bottom:16px; font-size:1.05rem; }
        @media(max-width:768px) { .tour-detail-grid { grid-template-columns:1fr; } .tour-info-card { position:static; } }
    </style>
</head>
<body>
    <header class="navbar scrolled" id="navbar">
        <div class="container">
            <a href="index.php" class="logo"><img src="assets/images/logo.png" alt="Celal Kaptan Logo" class="logo-image"></a>
            <nav class="nav-menu" id="navMenu">
                <a href="index.php" class="nav-link">Ana Sayfa</a>
                <a href="index.php#turlar" class="nav-link">Turlar</a>
                <a href="index.php#hakkimizda" class="nav-link">Hakkımızda</a>
                <a href="index.php#iletisim" class="nav-link">İletişim</a>
                <a href="tel:<?= sanitize($phone) ?>" class="nav-cta"><i class="fas fa-phone"></i> Hemen Ara</a>
            </nav>
            <button class="hamburger" id="hamburger" aria-label="Menü"><span></span><span></span><span></span></button>
        </div>
    </header>

    <section class="tour-hero">
        <div class="container">
            <span class="hero-badge"><i class="fas fa-ship"></i> <?= sanitize($tour['duration']) ?></span>
            <h1><?= sanitize($tour['title']) ?></h1>
            <p class="hero-subtitle"><?= sanitize($tour['short_desc']) ?></p>
        </div>
    </section>

    <section class="tour-detail">
        <div class="container">
            <div class="tour-detail-grid">
                <div class="tour-content">
                    <?php if ($tour['image']): ?>
                    <img src="assets/uploads/<?= sanitize($tour['image']) ?>" alt="<?= sanitize($tour['title']) ?>" style="border-radius:16px;margin-bottom:24px;width:100%;">
                    <?php endif; ?>
                    <h2 style="font-family:'Playfair Display',serif;margin-bottom:16px;">Tur Detayları</h2>
                    <p><?= nl2br(sanitize($tour['full_desc'])) ?></p>

                    <?php if ($tour['route']): ?>
                    <h3 style="margin-top:32px;margin-bottom:12px;">Güzergah</h3>
                    <p><i class="fas fa-route" style="color:#0077b6;"></i> <?= sanitize($tour['route']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <div class="tour-info-card">
                        <h3><i class="fas fa-info-circle"></i> Tur Bilgileri</h3>
                        <div class="info-row"><i class="fas fa-clock"></i><div><strong>Süre:</strong> <?= sanitize($tour['duration']) ?></div></div>
                        <div class="info-row"><i class="fas fa-tag"></i><div><strong>Fiyat:</strong> <?= sanitize($tour['price']) ?></div></div>
                        <?php if ($tour['includes']): ?>
                        <div class="info-row"><i class="fas fa-check"></i><div><strong>Dahil:</strong> <?= sanitize($tour['includes']) ?></div></div>
                        <?php endif; ?>
                        <div style="margin-top:24px;display:flex;flex-direction:column;gap:12px;">
                            <a href="https://wa.me/<?= sanitize($whatsapp) ?>?text=<?= urlencode($tour['title'] . ' hakkında bilgi almak istiyorum.') ?>" target="_blank" class="btn btn-success" style="justify-content:center;"><i class="fab fa-whatsapp"></i> WhatsApp ile Rezervasyon</a>
                            <a href="tel:<?= sanitize($phone) ?>" class="btn btn-primary" style="justify-content:center;"><i class="fas fa-phone"></i> Hemen Ara</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Adrasan Celal Kaptan. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>

    <a href="https://wa.me/<?= sanitize($whatsapp) ?>" target="_blank" class="whatsapp-float"><i class="fab fa-whatsapp"></i></a>
    <script src="assets/js/main.js"></script>
</body>
</html>
