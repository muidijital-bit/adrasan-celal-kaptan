<?php
require_once 'includes/config.php';

$seo = getSEO('hizmetlerimiz');
$servicesHero = getContent('services_hero');
$includedData = getContent('services_included');
$excludedData = getContent('services_excluded');
$tours = getTours();
$destinations = getDestinations();
$settings = getSiteSettings();

// Dahil / Dahil Değil — her satır bir madde
$includedItems = [];
if (!empty($includedData['content'])) {
    $includedItems = array_filter(array_map('trim', explode("\n", $includedData['content'])));
}
if (empty($includedItems)) {
    $includedItems = ['Öğle yemeği (tavuk veya balık seçeneği)','Gün boyu çay ve taze meyve ikramı','Can yeleği ve tüm güvenlik ekipmanı','Deneyimli kaptan ve yardımcı personel','3–4 koyda yüzme ve snorkel molası','En güzel manzaralarda fotoğraf durağı'];
}
$excludedItems = [];
if (!empty($excludedData['content'])) {
    $excludedItems = array_filter(array_map('trim', explode("\n", $excludedData['content'])));
}
if (empty($excludedItems)) {
    $excludedItems = ['Alkolsüz ve alkollü içecekler','Su sporları ekipmanları','Otelden iskeleye ulaşım','Kişisel harcamalar ve isteğe bağlı aktiviteler'];
}

include 'includes/header.php';
?>

<style>
/* --- Hizmetlerimiz Sayfa Stilleri --- */

/* Hızlı Bilgi Şeridi */
.services-info-bar {
    background: var(--primary);
    padding: 0;
}
.services-info-bar-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
}
.sib-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 20px 24px;
    border-right: 1px solid rgba(255,255,255,0.15);
    color: var(--white);
}
.sib-item:last-child { border-right: none; }
.sib-item i { font-size: 1.5rem; opacity: 0.9; flex-shrink: 0; }
.sib-item strong { display: block; font-size: 0.95rem; font-weight: 700; }
.sib-item span { font-size: 0.78rem; opacity: 0.8; }

/* Dahil / Dahil Değil */
.includes-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 32px;
}
.includes-box {
    background: var(--white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.includes-box-head {
    padding: 18px 28px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 700;
    font-size: 1rem;
}
.includes-box-head.green { background: rgba(34,197,94,0.1); color: #16a34a; }
.includes-box-head.red   { background: rgba(239,68,68,0.08); color: #dc2626; }
.includes-box-head i { font-size: 1.1rem; }
.includes-list { padding: 20px 28px; display: flex; flex-direction: column; gap: 12px; }
.includes-list li {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 0.92rem;
    color: var(--gray-600);
    list-style: none;
}
.includes-list li i.fa-check { color: #16a34a; font-size: 0.85rem; flex-shrink: 0; }
.includes-list li i.fa-times  { color: #dc2626; font-size: 0.85rem; flex-shrink: 0; }

/* Steps — Yatay Timeline */
.steps-timeline {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
    position: relative;
}
.steps-timeline::before {
    content: '';
    position: absolute;
    top: 44px;
    left: calc(12.5% + 8px);
    right: calc(12.5% + 8px);
    height: 2px;
    background: linear-gradient(90deg, var(--primary), var(--primary-light));
    z-index: 0;
}
.step-tl {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 0 16px;
    position: relative;
    z-index: 1;
}
.step-tl-num {
    width: 56px; height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    color: var(--white);
    font-family: var(--font-heading);
    font-size: 1.3rem;
    font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 16px rgba(41,184,216,0.4);
    margin-bottom: 20px;
}
.step-tl h3 { font-size: 1rem; color: var(--dark); margin-bottom: 8px; }
.step-tl p  { font-size: 0.87rem; color: var(--gray-500); line-height: 1.6; }

/* Tur footer — butonu ortala */
.tour-footer { justify-content: center; }
.tour-price:empty { display: none; }

/* WhatsApp rezervasyon butonu */
.btn-wa {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: #25d366;
    color: #fff !important;
    padding: 10px 18px;
    border-radius: var(--radius-md);
    font-weight: 700;
    font-size: 0.88rem;
    text-decoration: none;
    transition: var(--transition);
    box-shadow: 0 4px 14px rgba(37,211,102,0.3);
    white-space: nowrap;
    flex-shrink: 0;
}
.btn-wa:hover { background: #1ebe5b; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37,211,102,0.4); }
.btn-wa i { font-size: 1rem; }

/* Responsive */
@media (max-width: 960px) {
    .services-info-bar-grid { grid-template-columns: repeat(2, 1fr); }
    .sib-item:nth-child(2) { border-right: none; }
    .includes-grid { grid-template-columns: 1fr; }
    .steps-timeline { grid-template-columns: repeat(2, 1fr); gap: 32px; }
    .steps-timeline::before { display: none; }
}
@media (max-width: 600px) {
    .services-info-bar-grid { grid-template-columns: 1fr 1fr; }
    .steps-timeline { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 400px) {
    .services-info-bar-grid { grid-template-columns: 1fr; }
    .sib-item { border-right: none; border-bottom: 1px solid rgba(255,255,255,0.15); }
}
</style>

<!-- Page Hero -->
<section class="page-hero" <?php if (!empty($servicesHero['image'])): ?>style="background-image:url('<?= SITE_URL ?>/assets/uploads/<?= sanitize($servicesHero['image']) ?>');background-size:cover;background-position:center;"<?php endif; ?>>
    <div class="page-hero-overlay"></div>
    <div class="container">
        <div class="page-hero-content">
            <div class="hero-badge"><i class="fas fa-compass"></i> Hizmetlerimiz</div>
            <h1><?= sanitize($servicesHero['title'] ?? 'Adrasan Tekne Turu Paketleri') ?></h1>
            <p><?= sanitize($servicesHero['content'] ?? 'Suluada turu, koylar turu ve özel charter — size en uygun paketi seçin.') ?></p>
        </div>
    </div>
</section>

<!-- Hızlı Bilgi Şeridi -->
<div class="services-info-bar">
    <div class="container">
        <div class="services-info-bar-grid">
            <div class="sib-item">
                <i class="fas fa-clock"></i>
                <div><strong>09:30 – 17:00</strong><span>Tam gün tur</span></div>
            </div>
            <div class="sib-item">
                <i class="fas fa-utensils"></i>
                <div><strong>Öğle Yemeği Dahil</strong><span>Tavuk veya balık seçeneği</span></div>
            </div>
            <div class="sib-item">
                <i class="fas fa-shield-alt"></i>
                <div><strong>Tam Güvenlik</strong><span>Can yeleği & ekipman</span></div>
            </div>
            <div class="sib-item">
                <i class="fas fa-anchor"></i>
                <div><strong>3–4 Koy Durağı</strong><span>Yüzme & snorkel imkânı</span></div>
            </div>
        </div>
    </div>
</div>

<!-- Tur Paketleri -->
<section class="section tours" style="background:var(--gray-100);">
    <div class="container">
        <div class="section-header">
            <span class="section-tag"><i class="fas fa-ship"></i> Tur Paketlerimiz</span>
            <h2>Adrasan Tekne Turu Paketleri</h2>
            <p>Suluada turu, koylar turu ve özel charter — size en uygun paketi seçin</p>
        </div>
        <div class="tours-grid">
            <?php if (!empty($tours)): ?>
                <?php foreach ($tours as $tour): ?>
                <div class="tour-card" data-aos="fade-up">
                    <div class="tour-image">
                        <?php if ($tour['image']): ?>
                        <img src="<?= SITE_URL ?>/assets/uploads/<?= sanitize($tour['image']) ?>" alt="<?= sanitize($tour['title']) ?>" loading="lazy">
                        <?php else: ?>
                        <div class="tour-image-placeholder"><i class="fas fa-ship"></i></div>
                        <?php endif; ?>
                        <div class="tour-badge"><?= sanitize($tour['duration']) ?></div>
                    </div>
                    <div class="tour-body">
                        <h3><?= sanitize($tour['title']) ?></h3>
                        <p><?= sanitize($tour['short_desc']) ?></p>
                        <div class="tour-details">
                            <?php if ($tour['route']): ?>
                            <div class="tour-route"><i class="fas fa-route"></i><span><?= sanitize($tour['route']) ?></span></div>
                            <?php endif; ?>
                            <?php if ($tour['includes']): ?>
                            <div class="tour-includes"><i class="fas fa-check-circle"></i><span><?= sanitize($tour['includes']) ?></span></div>
                            <?php endif; ?>
                        </div>
                        <div class="tour-footer">
                            <div class="tour-price">
                                <?php if ($tour['price']): ?>
                                <span class="price-label">Fiyat:</span>
                                <span class="price-value"><?= sanitize($tour['price']) ?></span>
                                <?php endif; ?>
                            </div>
                            <a href="https://wa.me/<?= sanitize($settings['whatsapp']) ?>?text=<?= urlencode($tour['title'] . ' hakkında bilgi almak istiyorum.') ?>" target="_blank" class="btn-wa">
                                <i class="fab fa-whatsapp"></i> WhatsApp Rezervasyon
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="tour-card" data-aos="fade-up">
                    <div class="tour-image"><div class="tour-image-placeholder"><i class="fas fa-ship"></i></div><div class="tour-badge">09:30 – 17:00</div></div>
                    <div class="tour-body">
                        <h3>Özel Tekne Turu</h3>
                        <p>Grubunuza özel, istediğiniz rota ve saatte düzenlenen özel charter hizmeti.</p>
                        <div class="tour-footer"><div class="tour-price"></div><a href="https://wa.me/<?= sanitize($settings['whatsapp']) ?>?text=<?= urlencode('Özel Tekne Turu hakkında bilgi almak istiyorum.') ?>" target="_blank" class="btn-wa"><i class="fab fa-whatsapp"></i> WhatsApp Rezervasyon</a></div>
                    </div>
                </div>
                <div class="tour-card" data-aos="fade-up">
                    <div class="tour-image"><div class="tour-image-placeholder"><i class="fas fa-sun"></i></div><div class="tour-badge">09:30 – 17:00</div></div>
                    <div class="tour-body">
                        <h3>Suluada Tekne Turu</h3>
                        <p>Akdeniz'in Maldivleri Suluada'yı keşfedin. Tatlı su kaynağı, aşıklar mağarası ve kristal koylar.</p>
                        <div class="tour-route" style="margin:8px 0;"><i class="fas fa-route"></i><span style="font-size:0.85rem;color:var(--gray-500);">Suluada Plajı · Tatlı Su · Aşıklar Mağarası · Amerikan Plajı</span></div>
                        <div class="tour-footer"><div class="tour-price"></div><a href="https://wa.me/<?= sanitize($settings['whatsapp']) ?>?text=<?= urlencode('Suluada Tekne Turu hakkında bilgi almak istiyorum.') ?>" target="_blank" class="btn-wa"><i class="fab fa-whatsapp"></i> WhatsApp Rezervasyon</a></div>
                    </div>
                </div>
                <div class="tour-card" data-aos="fade-up">
                    <div class="tour-image"><div class="tour-image-placeholder"><i class="fas fa-water"></i></div><div class="tour-badge">09:30 – 17:00</div></div>
                    <div class="tour-body">
                        <h3>Koylar Tekne Turu</h3>
                        <p>Adrasan'ın saklı cennetlerini keşfedin. Her biri birbirinden güzel 4 koyda yüzme molası.</p>
                        <div class="tour-route" style="margin:8px 0;"><i class="fas fa-route"></i><span style="font-size:0.85rem;color:var(--gray-500);">Akseki · Sazak · Ceneviz · Korsan Koyu</span></div>
                        <div class="tour-footer"><div class="tour-price"></div><a href="https://wa.me/<?= sanitize($settings['whatsapp']) ?>?text=<?= urlencode('Koylar Tekne Turu hakkında bilgi almak istiyorum.') ?>" target="_blank" class="btn-wa"><i class="fab fa-whatsapp"></i> WhatsApp Rezervasyon</a></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Dahil / Dahil Değil -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag"><i class="fas fa-list-check"></i> Tur Kapsamı</span>
            <h2>Turlarımıza Ne Dahil?</h2>
            <p>Tur paketinizde nelerin yer aldığını önceden öğrenin</p>
        </div>
        <div class="includes-grid" data-aos="fade-up">
            <div class="includes-box">
                <div class="includes-box-head green">
                    <i class="fas fa-check-circle"></i> Turlarımıza Dahil
                </div>
                <ul class="includes-list">
                    <?php foreach ($includedItems as $item): ?>
                    <li><i class="fas fa-check"></i> <?= sanitize($item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="includes-box">
                <div class="includes-box-head red">
                    <i class="fas fa-times-circle"></i> Turlarımıza Dahil Değil
                </div>
                <ul class="includes-list">
                    <?php foreach ($excludedItems as $item): ?>
                    <li><i class="fas fa-times"></i> <?= sanitize($item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Rezervasyon Nasıl Yapılır -->
<section class="section" style="background:var(--gray-100);">
    <div class="container">
        <div class="section-header">
            <span class="section-tag"><i class="fas fa-list-ol"></i> Nasıl Rezervasyon Yapılır?</span>
            <h2>4 Adımda Tekne Turu</h2>
            <p>Rezervasyon süreciniz son derece kolay</p>
        </div>
        <div class="steps-timeline" data-aos="fade-up">
            <div class="step-tl">
                <div class="step-tl-num">1</div>
                <h3>Tur Seçin</h3>
                <p>Suluada, Koylar veya Özel Tur seçeneklerinden size uygun olanı belirleyin</p>
            </div>
            <div class="step-tl">
                <div class="step-tl-num">2</div>
                <h3>Bize Ulaşın</h3>
                <p>WhatsApp veya telefonla arayın, tarih ve kişi sayısını bildirin</p>
            </div>
            <div class="step-tl">
                <div class="step-tl-num">3</div>
                <h3>Yerinizi Garantileyin</h3>
                <p>Ön ödemeyle yerinizi ayırtın, kapasite dolmadan erken rezervasyon yapın</p>
            </div>
            <div class="step-tl">
                <div class="step-tl-num">4</div>
                <h3>Keyfinize Bakın</h3>
                <p>Belirlenen saatte iskelede hazır olun, gerisini bize bırakın!</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section cta-section">
    <div class="cta-overlay"></div>
    <div class="container">
        <div class="cta-content" data-aos="zoom-in">
            <h2>Adrasan Tekne Turu Rezervasyonu — Hemen Arayın</h2>
            <p>2026 sezonu doluyor. Suluada veya koylar turu için yerinizi şimdiden garantileyin.</p>
            <div class="cta-buttons">
                <a href="tel:<?= sanitize($settings['phone']) ?>" class="btn btn-primary btn-lg"><i class="fas fa-phone"></i> <?= sanitize($settings['phone']) ?></a>
                <a href="https://wa.me/<?= sanitize($settings['whatsapp']) ?>" target="_blank" class="btn btn-success btn-lg"><i class="fab fa-whatsapp"></i> WhatsApp</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
