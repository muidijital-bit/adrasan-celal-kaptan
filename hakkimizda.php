<?php
require_once 'includes/config.php';

$seo = getSEO('hakkimizda');
$aboutHero = getContent('about_hero');
$about = getContent('about');
$vision = getContent('about_vision');
$mission = getContent('about_mission');
$checklistData = getContent('about_checklist');
$settings = getSiteSettings();

// Checklist items — her satır bir madde
$checklistItems = [];
if (!empty($checklistData['content'])) {
    $checklistItems = array_filter(array_map('trim', explode("\n", $checklistData['content'])));
}
if (empty($checklistItems)) {
    $checklistItems = ['Öğle yemeği dahil','Can yeleği & sigorta','Profesyonel rehberlik','7 farklı koy','Sabah 09:30 kalkış','Akşam 17:00 dönüş'];
}

include 'includes/header.php';
?>

<style>
/* ---- Hakkımızda Sayfa Stilleri ---- */

/* İstatistik Şeridi */
.about-stats-strip {
    background: var(--primary);
    padding: 0;
}
.about-stats-inner {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
}
.about-stat-item {
    padding: 28px 20px;
    text-align: center;
    border-right: 1px solid rgba(255,255,255,0.18);
    color: #fff;
}
.about-stat-item:last-child { border-right: none; }
.about-stat-number {
    display: block;
    font-family: var(--font-heading);
    font-size: 2.2rem;
    font-weight: 800;
    color: #fff;
    line-height: 1;
    margin-bottom: 6px;
}
.about-stat-label {
    font-size: 0.82rem;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    opacity: 0.85;
    font-weight: 600;
}

/* Hikaye Bölümü */
.about-story-section { padding: 70px 0; }
.about-story-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
}
.about-img-wrap { position: relative; }
.about-img-wrap img {
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    width: 100%;
    height: 430px;
    object-fit: cover;
}
.about-exp-badge {
    position: absolute;
    bottom: -18px;
    right: -18px;
    background: var(--accent);
    color: #fff;
    padding: 20px 24px;
    border-radius: var(--radius-md);
    text-align: center;
    box-shadow: var(--shadow-md);
}
.about-exp-badge .num { display: block; font-family: var(--font-heading); font-size: 2rem; font-weight: 800; line-height: 1; }
.about-exp-badge .lbl { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; }

.about-story-text .section-tag { margin-bottom: 12px; }
.about-story-text h2 {
    font-family: var(--font-heading);
    font-size: clamp(1.6rem, 3vw, 2.2rem);
    color: var(--dark);
    margin-bottom: 16px;
    line-height: 1.3;
}
.about-story-text p {
    color: var(--gray-600);
    line-height: 1.85;
    margin-bottom: 14px;
    font-size: 1rem;
}

/* Alıntı/Quote */
.about-quote {
    background: var(--gray-100);
    border-left: 4px solid var(--primary);
    padding: 18px 24px;
    border-radius: 0 var(--radius-md) var(--radius-md) 0;
    margin: 20px 0 24px;
    font-style: italic;
    color: var(--gray-700);
    font-size: 0.97rem;
    line-height: 1.7;
}

/* Checklist */
.about-checklist {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px 16px;
    margin-top: 8px;
}
.about-checklist-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    color: var(--gray-700);
    font-weight: 500;
}
.about-checklist-item i { color: var(--primary); font-size: 0.85rem; flex-shrink: 0; }

/* Değerler Bölümü */
.values-section {
    padding: 60px 0;
    background: var(--gray-100);
}
.values-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}
.value-card {
    background: var(--white);
    border: 1px solid rgba(41,184,216,0.15);
    border-top: 3px solid var(--primary);
    border-radius: var(--radius-lg);
    padding: 32px 28px;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
}
.value-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
    border-top-color: var(--primary);
}
.value-icon {
    width: 52px; height: 52px;
    border-radius: 50%;
    background: var(--primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    color: var(--white);
    margin-bottom: 18px;
    box-shadow: 0 4px 16px rgba(41,184,216,0.3);
}
.value-card h3 { font-family: var(--font-heading); font-size: 1.15rem; color: var(--dark); margin-bottom: 10px; }
.value-card p { color: var(--gray-500); font-size: 0.9rem; line-height: 1.75; margin: 0; }

/* Neden Biz */
.why-section { padding: 60px 0; }
.why-section .section-tag i { color: var(--primary) !important; }
.why-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}
.why-item {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 24px;
    background: var(--white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    border-left: 3px solid transparent;
}
.why-item:hover { border-left-color: var(--primary); box-shadow: var(--shadow-md); transform: translateX(4px); }
.why-item-icon {
    width: 46px; height: 46px;
    border-radius: 10px;
    background: rgba(41,184,216,0.1);
    display: flex; align-items: center; justify-content: center;
    color: var(--primary);
    font-size: 1.1rem;
    flex-shrink: 0;
}
.why-item-text h4 { font-size: 1rem; color: var(--dark); margin-bottom: 5px; font-weight: 700; }
.why-item-text p { color: var(--gray-500); font-size: 0.875rem; line-height: 1.65; margin: 0; }

/* CTA */
.about-cta-section { padding: 50px 0; }

/* Responsive */
@media (max-width: 960px) {
    .about-stats-inner { grid-template-columns: repeat(2, 1fr); }
    .about-stat-item:nth-child(2) { border-right: none; }
    .about-story-grid { grid-template-columns: 1fr; gap: 40px; }
    .about-img-wrap img { height: 300px; }
    .about-exp-badge { right: 10px; bottom: -10px; }
    .values-grid { grid-template-columns: 1fr 1fr; }
    .why-grid { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 600px) {
    .about-stats-inner { grid-template-columns: 1fr 1fr; }
    .values-grid { grid-template-columns: 1fr; }
    .why-grid { grid-template-columns: 1fr; }
    .about-checklist { grid-template-columns: 1fr; }
}
</style>

<!-- Hero -->
<section class="page-hero" <?php if (!empty($aboutHero['image'])): ?>style="background-image:url('<?= SITE_URL ?>/assets/uploads/<?= sanitize($aboutHero['image']) ?>');background-size:cover;background-position:center;"<?php endif; ?>>
    <div class="page-hero-overlay"></div>
    <div class="container">
        <div class="page-hero-content">
            <div class="hero-badge"><i class="fas fa-anchor"></i> <?= sanitize($aboutHero['subtitle'] ?? 'Hakkımızda') ?></div>
            <h1><?= sanitize($aboutHero['title'] ?? '15 Yıldır Adrasan\'da Tekne Turu Hizmetindeyiz') ?></h1>
            <p><?= sanitize($aboutHero['content'] ?? 'Güvenli, konforlu ve lezzetli bir günübirlik tekne turu deneyimi için Celal Kaptan\'a hoş geldiniz') ?></p>
        </div>
    </div>
</section>

<!-- İstatistik Şeridi -->
<div class="about-stats-strip">
    <div class="container">
        <div class="about-stats-inner">
            <div class="about-stat-item">
                <span class="about-stat-number">15+</span>
                <span class="about-stat-label">Yıl Deneyim</span>
            </div>
            <div class="about-stat-item">
                <span class="about-stat-number">10.000+</span>
                <span class="about-stat-label">Mutlu Misafir</span>
            </div>
            <div class="about-stat-item">
                <span class="about-stat-number">7</span>
                <span class="about-stat-label">Eşsiz Koy</span>
            </div>
            <div class="about-stat-item">
                <span class="about-stat-number">7/24</span>
                <span class="about-stat-label">WhatsApp Destek</span>
            </div>
        </div>
    </div>
</div>

<!-- Hikayemiz -->
<section class="about-story-section">
    <div class="container">
        <div class="about-story-grid">
            <div class="about-img-wrap" data-aos="fade-right">
                <img src="<?= SITE_URL ?>/assets/uploads/<?= sanitize($about['image'] ?? 'about.jpg') ?>" alt="Celal Kaptan">
                <div class="about-exp-badge">
                    <span class="num">15+</span>
                    <span class="lbl">Yıl Deneyim</span>
                </div>
            </div>
            <div class="about-story-text" data-aos="fade-left">
                <span class="section-tag"><i class="fas fa-ship"></i> Hikayemiz</span>
                <h2><?= sanitize($about['title'] ?? 'Celal Kaptan — Adrasan\'ın Güvenilir Tekne Kaptanı') ?></h2>
                <p><?= $about['content'] ?? 'Celal Kaptan, denizle iç içe geçen bir hayatın uzmanlarından oluşan bir ekip tarafından kurulmuştur. Denizcilik sektöründeki uzun yıllara dayanan deneyimimizi, misafirperverlik anlayışımızla birleştirerek sizlere güvenli, konforlu ve eğlenceli deneyimler sunuyoruz.' ?></p>
                <p>Suluada'dan Akseki'ye, Amerikan Koyu'ndan Akvaryum'a kadar Adrasan'ın en güzel rotalarını özenle planlıyor, misafirlerimizin güvenli ve mutlu dönmesini sağlıyoruz.</p>
                <blockquote class="about-quote">
                    "Her tekne turunda taze hazırlanmış öğle yemeği, ikramlar ve profesyonel rehberlikle tam gün dolu dolu bir deniz macerası sunuyoruz."
                </blockquote>
                <div class="about-checklist">
                    <?php foreach ($checklistItems as $item): ?>
                    <div class="about-checklist-item"><i class="fas fa-check-circle"></i> <?= sanitize($item) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vizyon, Misyon, Değerler -->
<section class="values-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-tag"><i class="fas fa-compass"></i> Temel İlkelerimiz</span>
            <h2>Vizyon, Misyon ve Değerlerimiz</h2>
            <p>Sizi en iyi şekilde ağırlamak için benimsediğimiz ilkeler</p>
        </div>
        <div class="values-grid">
            <div class="value-card" data-aos="fade-up">
                <div class="value-icon"><i class="fas fa-eye"></i></div>
                <h3><?= sanitize($vision['title'] ?? 'Vizyonumuz') ?></h3>
                <p><?= $vision['content'] ?? 'Tutkulu deniz severleri sürdürülebilir turizmle buluşturarak sektörde öncü bir marka olmak.' ?></p>
            </div>
            <div class="value-card" data-aos="fade-up">
                <div class="value-icon"><i class="fas fa-bullseye"></i></div>
                <h3><?= sanitize($mission['title'] ?? 'Misyonumuz') ?></h3>
                <p><?= $mission['content'] ?? 'Güvenli, konforlu ve çevre dostu deniz yolculukları ile misafir beklentilerini aşmak, insanların denizle bağını güçlendirmek ve kalıcı anılar biriktirmelerini sağlamak.' ?></p>
            </div>
            <div class="value-card" data-aos="fade-up">
                <div class="value-icon"><i class="fas fa-leaf"></i></div>
                <h3>Değerlerimiz</h3>
                <p>Güvenlik, misafir memnuniyeti, doğaya saygı ve sürdürülebilir turizm anlayışı ile hareket ediyoruz. Denizi seviyoruz, onu koruyoruz.</p>
            </div>
        </div>
    </div>
</section>

<!-- Neden Biz -->
<section class="why-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-tag"><i class="fas fa-star"></i> Neden Celal Kaptan?</span>
            <h2>Bizi Tercih Etmeniz İçin 6 Neden</h2>
            <p>Deneyim, güven ve kalite bir arada</p>
        </div>
        <div class="why-grid">
            <div class="why-item" data-aos="fade-up">
                <div class="why-item-icon"><i class="fas fa-users"></i></div>
                <div class="why-item-text">
                    <h4>Deneyimli Ekip</h4>
                    <p>Yılların tecrübesine sahip, güler yüzlü ve profesyonel ekibimizle güvenli bir yolculuk sunuyoruz.</p>
                </div>
            </div>
            <div class="why-item" data-aos="fade-up">
                <div class="why-item-icon"><i class="fas fa-route"></i></div>
                <div class="why-item-text">
                    <h4>Özel Rotalar</h4>
                    <p>Özenle seçilmiş rotalarımız ile Adrasan'ın en güzel koylarını ve gizli cennetlerini keşfediyorsunuz.</p>
                </div>
            </div>
            <div class="why-item" data-aos="fade-up">
                <div class="why-item-icon"><i class="fas fa-heart"></i></div>
                <div class="why-item-text">
                    <h4>Misafir Memnuniyeti</h4>
                    <p>Her detayı düşünerek size unutulmaz anlar yaşatıyoruz. Memnuniyetiniz önceliğimizdir.</p>
                </div>
            </div>
            <div class="why-item" data-aos="fade-up">
                <div class="why-item-icon"><i class="fas fa-tag"></i></div>
                <div class="why-item-text">
                    <h4>Makul Fiyatlar</h4>
                    <p>Kaliteli hizmeti uygun fiyatlarla sunarak herkesin bu eşsiz deneyimi yaşamasını sağlıyoruz.</p>
                </div>
            </div>
            <div class="why-item" data-aos="fade-up">
                <div class="why-item-icon"><i class="fas fa-shield-alt"></i></div>
                <div class="why-item-text">
                    <h4>Tam Güvenlik</h4>
                    <p>Tüm güvenlik ekipmanları ve sigorta ile donatılmış teknelerimizde huzur içinde seyahat edin.</p>
                </div>
            </div>
            <div class="why-item" data-aos="fade-up">
                <div class="why-item-icon"><i class="fas fa-utensils"></i></div>
                <div class="why-item-text">
                    <h4>Lezzetli İkramlar</h4>
                    <p>Taze hazırlanmış öğle yemeği, çay ve meyve ikramları ile dolu dolu bir gün geçirin.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="about-cta-section cta-section">
    <div class="cta-overlay"></div>
    <div class="container">
        <div class="cta-content" data-aos="zoom-in">
            <h2>Bizimle Tanışmak İster Misiniz?</h2>
            <p>Sorularınız veya rezervasyon için bize hemen ulaşın.</p>
            <div class="cta-buttons">
                <a href="https://wa.me/<?= sanitize($settings['whatsapp']) ?>?text=Merhaba,%20tekne%20turu%20hakkında%20bilgi%20almak%20istiyorum."
                   target="_blank" class="btn btn-success btn-lg">
                    <i class="fab fa-whatsapp"></i> WhatsApp ile Yazın
                </a>
                <a href="tel:<?= sanitize($settings['phone']) ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-phone"></i> <?= sanitize($settings['phone']) ?>
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
