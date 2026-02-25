<?php
require_once 'includes/config.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
if (!$slug) {
    header('Location: ' . SITE_URL . '/hizmetlerimiz');
    exit;
}

$destination = getDestination($slug);
if (!$destination) {
    http_response_code(404);
    header('Location: ' . SITE_URL . '/hizmetlerimiz');
    exit;
}

$settings = getSiteSettings();

// SEO - önce destinasyonun kendi meta verileri, sonra DB'deki seo_settings
$seo = [
    'meta_title'       => $destination['meta_title'] ?: $destination['title'] . ' | Adrasan Celal Kaptan',
    'meta_description' => $destination['meta_description'] ?: $destination['short_desc'],
    'meta_keywords'    => $destination['meta_keywords'] ?: '',
    'og_image'         => $destination['image'] ?: '',
    'robots'           => 'index, follow',
    'canonical_url'    => SITE_URL . '/' . $destination['slug'],
];

// Features: pipe ile ayrılmış liste
$features = [];
if (!empty($destination['features'])) {
    $features = array_filter(array_map('trim', explode('|', $destination['features'])));
}

// FAQ: JSON array [{q, a}]
$faqItems = [];
if (!empty($destination['faq_json'])) {
    $decoded = json_decode($destination['faq_json'], true);
    if (is_array($decoded)) $faqItems = $decoded;
}

// Gallery images: virgülle ayrılmış
$galleryImages = [];
if (!empty($destination['gallery_images'])) {
    $galleryImages = array_filter(array_map('trim', explode(',', $destination['gallery_images'])));
}

$currentPage = 'destinasyon';
include 'includes/header.php';
?>

<style>
/* ---- Destinasyon Sayfa Stilleri ---- */

/* Hero */
.dest-hero-bg { position: absolute; inset: 0; background-size: cover; background-position: center; z-index: 0; }
.dest-hero .page-hero-overlay { background: rgba(10,22,40,0.72); }

/* Hızlı Bilgi Şeridi */
.dest-info-strip {
    background: var(--dark);
    color: #fff;
    padding: 0;
}
.dest-info-strip-inner {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    flex-wrap: wrap;
}
.dest-info-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 32px;
    border-right: 1px solid rgba(255,255,255,0.1);
    font-size: 0.9rem;
}
.dest-info-item:last-child { border-right: none; }
.dest-info-item i {
    color: var(--primary);
    font-size: 1.1rem;
    flex-shrink: 0;
}
.dest-info-item strong { color: #fff; display: block; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.6; margin-bottom: 2px; }
.dest-info-item span { color: rgba(255,255,255,0.9); font-weight: 500; }

/* İçerik Grid */
.dest-page-section {
    padding: 50px 0 60px;
}
.dest-content-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 32px;
    align-items: start;
}

/* Sol Alan */
.dest-text {
    margin-bottom: 28px;
}
.dest-text h2 {
    font-family: var(--font-heading);
    font-size: 1.5rem;
    color: var(--dark);
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.dest-description {
    color: var(--gray-700);
    line-height: 1.85;
    font-size: 1rem;
}
.dest-description p { margin-bottom: 12px; }
.dest-description p:last-child { margin-bottom: 0; }

/* Özellikler */
.dest-features { margin-bottom: 28px; }
.dest-features h3 {
    font-family: var(--font-heading);
    font-size: 1.2rem;
    color: var(--dark);
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.dest-features h3 i { color: var(--accent); }
.dest-features-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}
.dest-feature-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: var(--gray-100);
    border-radius: var(--radius-sm);
    transition: var(--transition);
}
.dest-feature-item:hover { background: #e3f2fd; }
.dest-feature-item i { color: var(--success); flex-shrink: 0; font-size: 0.9rem; }
.dest-feature-item span { font-size: 0.92rem; color: var(--gray-700); }

/* Tarihçe */
.dest-history {
    background: var(--gray-100);
    padding: 24px 28px;
    border-radius: var(--radius-lg);
    margin-bottom: 28px;
    border-left: 4px solid var(--primary);
}
.dest-history h3 {
    font-family: var(--font-heading);
    font-size: 1.2rem;
    color: var(--dark);
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.dest-history h3 i { color: var(--primary); }
.dest-history p { color: var(--gray-700); line-height: 1.8; font-size: 0.95rem; }

/* Galeri */
.dest-gallery { margin-bottom: 28px; }
.dest-gallery h3 {
    font-family: var(--font-heading);
    font-size: 1.2rem;
    color: var(--dark);
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.dest-gallery h3 i { color: var(--primary); }
.dest-gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 10px;
}
.dest-gallery-grid a {
    display: block;
    border-radius: var(--radius-md);
    overflow: hidden;
    height: 140px;
}
.dest-gallery-grid img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.dest-gallery-grid a:hover img { transform: scale(1.06); }

/* Sidebar */
.dest-sidebar {
    position: sticky;
    top: 100px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.dest-sidebar-card {
    background: var(--white);
    padding: 24px;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
}
.dest-sidebar-card h3 {
    font-size: 1rem;
    color: var(--dark);
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 2px solid var(--gray-200);
    display: flex;
    align-items: center;
    gap: 8px;
}
.dest-sidebar-card h3 i { color: var(--primary); }

/* Rezervasyon kartı */
.dest-rez-card {
    background: var(--dark);
    color: #fff;
}
.dest-rez-card h3 { color: #fff; border-bottom-color: rgba(255,255,255,0.15); }
.dest-rez-card p { font-size: 0.88rem; opacity: 0.75; margin-bottom: 16px; line-height: 1.7; }
.sidebar-cta { display: flex; flex-direction: column; gap: 10px; }
.btn-block { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 13px; font-size: 0.95rem; border-radius: var(--radius-md); font-weight: 600; text-decoration: none; transition: var(--transition); }
.btn-success { background: #25d366; color: #fff; box-shadow: 0 4px 14px rgba(37,211,102,0.35); }
.btn-success:hover { background: #1ebe5b; transform: translateY(-2px); }
.btn-outline-light { border: 1px solid rgba(255,255,255,0.3); color: rgba(255,255,255,0.85); }
.btn-outline-light:hover { border-color: rgba(255,255,255,0.7); color: #fff; background: rgba(255,255,255,0.08); }

/* Sidebar bilgi satırları */
.sidebar-info-row {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid var(--gray-200);
    font-size: 0.9rem;
}
.sidebar-info-row:last-child { border-bottom: none; padding-bottom: 0; }
.sidebar-info-row > i { color: var(--primary); width: 18px; flex-shrink: 0; margin-top: 2px; }
.sidebar-info-row div strong { color: var(--dark); display: block; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 2px; }
.sidebar-info-row div span { color: var(--gray-600); }

/* Diğer koylar */
.dest-other-list a {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 9px 0;
    border-bottom: 1px solid var(--gray-200);
    color: var(--gray-700);
    font-size: 0.9rem;
    text-decoration: none;
    transition: var(--transition);
}
.dest-other-list a:last-child { border-bottom: none; padding-bottom: 0; }
.dest-other-list a:hover { color: var(--primary); padding-left: 6px; }
.dest-other-list a i { color: var(--primary); font-size: 0.75rem; flex-shrink: 0; }

/* CTA */
.dest-cta-section {
    padding: 50px 0;
}

/* FAQ */
.dest-faq-section {
    padding: 50px 0 60px;
    background: var(--gray-100);
}
.faq-full-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    max-width: 960px;
    margin: 0 auto;
}
.faq-full-grid .faq-item { background: var(--white); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }

/* Responsive */
@media (max-width: 960px) {
    .dest-content-grid { grid-template-columns: 1fr; }
    .dest-sidebar { position: static; }
    .faq-full-grid { grid-template-columns: 1fr; }
    .dest-info-item { padding: 14px 20px; }
}
@media (max-width: 600px) {
    .dest-features-grid { grid-template-columns: 1fr; }
    .dest-info-strip-inner { justify-content: flex-start; }
    .dest-info-item { border-right: none; border-bottom: 1px solid rgba(255,255,255,0.1); flex: 1 1 50%; }
}
</style>

<!-- Hero -->
<section class="page-hero dest-hero">
    <?php if (!empty($destination['image'])): ?>
    <div class="dest-hero-bg" style="background-image:url('<?= SITE_URL ?>/assets/uploads/<?= sanitize($destination['image']) ?>')"></div>
    <?php endif; ?>
    <div class="page-hero-overlay"></div>
    <div class="container">
        <div class="page-hero-content">
            <div class="hero-badge"><i class="fas fa-map-marker-alt"></i> <?= sanitize($destination['subtitle'] ?? 'Adrasan') ?></div>
            <h1><?= sanitize($destination['title']) ?></h1>
            <?php if (!empty($destination['short_desc'])): ?>
            <p><?= sanitize($destination['short_desc']) ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Hızlı Bilgi Şeridi -->
<div class="dest-info-strip">
    <div class="container">
        <div class="dest-info-strip-inner">
            <div class="dest-info-item">
                <i class="fas fa-clock"></i>
                <div><strong>Kalkış Saati</strong><span>09:30</span></div>
            </div>
            <div class="dest-info-item">
                <i class="fas fa-hourglass-half"></i>
                <div><strong>Tur Süresi</strong><span>~7.5 Saat</span></div>
            </div>
            <div class="dest-info-item">
                <i class="fas fa-utensils"></i>
                <div><strong>Öğle Yemeği</strong><span>Dahil</span></div>
            </div>
            <div class="dest-info-item">
                <i class="fas fa-life-ring"></i>
                <div><strong>Can Yeleği</strong><span>Dahil</span></div>
            </div>
            <div class="dest-info-item">
                <i class="fas fa-ship"></i>
                <div><strong>Ulaşım</strong><span>Tekne ile</span></div>
            </div>
        </div>
    </div>
</div>

<!-- Ana İçerik -->
<section class="dest-page-section">
    <div class="container">
        <div class="dest-content-grid">

            <!-- Sol: İçerik -->
            <div class="dest-main">

                <!-- Tam Açıklama -->
                <?php if (!empty($destination['full_desc'])): ?>
                <div class="dest-text" data-aos="fade-up">
                    <h2><i class="fas fa-info-circle" style="color:var(--primary);"></i> <?= sanitize($destination['title']) ?> Hakkında</h2>
                    <div class="dest-description">
                        <?php foreach (explode("\n", $destination['full_desc']) as $paragraph):
                            if (trim($paragraph) === '') continue; ?>
                        <p><?= sanitize(trim($paragraph)) ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Öne Çıkan Özellikler -->
                <?php if (!empty($features)): ?>
                <div class="dest-features" data-aos="fade-up">
                    <h3><i class="fas fa-star"></i> Öne Çıkan Özellikler</h3>
                    <div class="dest-features-grid">
                        <?php foreach ($features as $feature): ?>
                        <div class="dest-feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span><?= sanitize($feature) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Tarihçe -->
                <?php if (!empty($destination['history_text'])): ?>
                <div class="dest-history" data-aos="fade-up">
                    <h3><i class="fas fa-history"></i> Tarihçe</h3>
                    <p><?= sanitize($destination['history_text']) ?></p>
                </div>
                <?php endif; ?>

                <!-- Galeri -->
                <?php if (!empty($galleryImages)): ?>
                <div class="dest-gallery" data-aos="fade-up">
                    <h3><i class="fas fa-images"></i> Galeri</h3>
                    <div class="dest-gallery-grid">
                        <?php foreach ($galleryImages as $img): ?>
                        <a href="<?= SITE_URL ?>/assets/uploads/<?= sanitize($img) ?>" target="_blank">
                            <img src="<?= SITE_URL ?>/assets/uploads/<?= sanitize($img) ?>" alt="<?= sanitize($destination['title']) ?>" loading="lazy">
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- Sağ: Sidebar -->
            <div class="dest-sidebar">

                <!-- Rezervasyon Kartı -->
                <div class="dest-sidebar-card dest-rez-card" data-aos="fade-left">
                    <h3><i class="fas fa-calendar-check"></i> Rezervasyon</h3>
                    <p>Bu koyu keşfetmek için hemen rezervasyon yapın. WhatsApp üzerinden 7/24 anlık yanıt veriyoruz.</p>
                    <div class="sidebar-cta">
                        <a href="https://wa.me/<?= sanitize($settings['whatsapp']) ?>?text=<?= urlencode($destination['title'] . ' hakkında bilgi almak istiyorum.') ?>"
                           target="_blank" class="btn-block btn-success">
                            <i class="fab fa-whatsapp"></i> WhatsApp ile Rezervasyon
                        </a>
                        <a href="tel:<?= sanitize($settings['phone']) ?>" class="btn-block btn-outline-light">
                            <i class="fas fa-phone"></i> <?= sanitize($settings['phone']) ?>
                        </a>
                    </div>
                </div>

                <!-- Tur Bilgileri -->
                <div class="dest-sidebar-card" data-aos="fade-left">
                    <h3><i class="fas fa-info-circle"></i> Tur Bilgileri</h3>
                    <div class="sidebar-info-row">
                        <i class="fas fa-clock"></i>
                        <div><strong>Çalışma Saatleri</strong><span><?= sanitize($settings['working_hours']) ?></span></div>
                    </div>
                    <div class="sidebar-info-row">
                        <i class="fas fa-map-marker-alt"></i>
                        <div><strong>Kalkış Noktası</strong><span>Adrasan İskelesi</span></div>
                    </div>
                    <div class="sidebar-info-row">
                        <i class="fas fa-utensils"></i>
                        <div><strong>Yemek</strong><span>Öğle yemeği dahil</span></div>
                    </div>
                    <div class="sidebar-info-row">
                        <i class="fas fa-life-ring"></i>
                        <div><strong>Güvenlik</strong><span>Can yeleği & sigorta</span></div>
                    </div>
                </div>

                <!-- Diğer Koylar -->
                <div class="dest-sidebar-card" data-aos="fade-left">
                    <h3><i class="fas fa-compass"></i> Diğer Destinasyonlar</h3>
                    <div class="dest-other-list">
                        <?php
                        $otherDests = getDestinations();
                        foreach ($otherDests as $d):
                            if ($d['slug'] === $slug) continue;
                        ?>
                        <a href="<?= SITE_URL ?>/<?= sanitize($d['slug']) ?>">
                            <i class="fas fa-map-pin"></i>
                            <?= sanitize($d['title']) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- S.S.S. -->
<?php if (!empty($faqItems)): ?>
<section class="dest-faq-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-tag"><i class="fas fa-question-circle"></i> S.S.S.</span>
            <h2>Sık Sorulan Sorular</h2>
            <p><?= sanitize($destination['title']) ?> hakkında merak edilenler</p>
        </div>
        <div class="faq-full-grid" data-aos="fade-up">
            <?php foreach ($faqItems as $faq): ?>
            <div class="faq-item">
                <button class="faq-question">
                    <?= sanitize($faq['q'] ?? $faq['question'] ?? '') ?>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p><?= sanitize($faq['a'] ?? $faq['answer'] ?? '') ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="dest-cta-section cta-section">
    <div class="cta-overlay"></div>
    <div class="container">
        <div class="cta-content" data-aos="zoom-in">
            <h2><?= sanitize($destination['title']) ?> için Rezervasyon Yapın</h2>
            <p>Bu eşsiz koyu keşfetmek için hemen rezervasyon yapın.</p>
            <div class="cta-buttons">
                <a href="https://wa.me/<?= sanitize($settings['whatsapp']) ?>?text=<?= urlencode($destination['title'] . ' için rezervasyon yapmak istiyorum.') ?>"
                   target="_blank" class="btn btn-success btn-lg">
                    <i class="fab fa-whatsapp"></i> WhatsApp ile Rezervasyon
                </a>
                <a href="tel:<?= sanitize($settings['phone']) ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-phone"></i> <?= sanitize($settings['phone']) ?>
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
