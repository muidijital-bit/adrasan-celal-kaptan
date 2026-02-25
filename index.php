<?php
require_once 'includes/config.php';

$seo = getSEO('anasayfa');
$hero = getContent('hero');
$cta = getContent('cta');
$tours_section = getContent('tours_section');
$koylar_section = getContent('koylar_section');
$tours = getTours();
$testimonials = getTestimonials(6);
$settings = getSiteSettings();
$destinations = getDestinations();

include 'includes/header.php';
?>

<style>
/* Tur footer — butonu ortala */
.tour-footer { justify-content: center; }
.tour-price:empty { display: none; }

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
</style>

    <!-- Hero Section -->
    <style>
    .hero-slides { position:absolute; inset:0; z-index:0; overflow:hidden; }
    .hero-slide  { position:absolute; inset:-8%; background-size:cover; background-position:center; opacity:0; animation:heroFadeWide 10s infinite; }
    .hero-slide:nth-child(1) { animation-delay:0s; }
    .hero-slide:nth-child(2) { animation-delay:5s; background-position:center 65%; }
    @keyframes heroFadeWide {
        0%      { opacity:1; transform:scale(1.0); }
        50%     { opacity:1; transform:scale(0.92); }
        55%     { opacity:0; transform:scale(0.92); }
        100%    { opacity:0; transform:scale(0.92); }
    }
    .hero-badge { font-size:0.78rem; padding:6px 16px; margin-bottom:16px; }
    .hero h1 { font-size:clamp(1.8rem, 4vw, 3rem); margin-bottom:14px; }
    .hero-subtitle { font-size:clamp(0.9rem, 1.6vw, 1rem); margin-bottom:28px; opacity:0.85; }
    .hero-buttons { margin-bottom:32px; }
    .hero-stats { gap:32px; }
    .stat-number { font-size:1.9rem; }
    .stat-label  { font-size:0.78rem; }
    </style>
    <section class="hero" id="anasayfa" style="position:relative;">
        <div class="hero-slides">
            <?php
            $heroSlides = [
                'celal06.jpg',
                'celal08.jpg',
            ];
            foreach ($heroSlides as $slide): ?>
            <div class="hero-slide" style="background-image:url('<?= SITE_URL ?>/assets/uploads/<?= sanitize($slide) ?>')"></div>
            <?php endforeach; ?>
        </div>
        <div class="hero-overlay"></div>
        <div class="hero-particles" id="particles"></div>
        <div class="container hero-content">
            <div class="hero-badge">
                <i class="fas fa-star"></i> Adrasan'ın Güvenilir Tekne Kaptanı
            </div>
            <h1><?= sanitize($hero['title'] ?? 'Adrasan Tekne Turu ile Unutulmaz Bir Deniz Deneyimi') ?></h1>
            <p class="hero-subtitle"><?= sanitize($hero['content'] ?? 'Kristal berraklığındaki turkuaz sularda, eşsiz koylar ve Suluada\'nın muhteşem güzelliklerini keşfedin.') ?></p>
            <div class="hero-buttons">
                <a href="<?= SITE_URL ?>/hizmetlerimiz" class="btn btn-primary btn-lg">
                    <i class="fas fa-ship"></i> Turları İncele
                </a>
                <a href="https://wa.me/<?= sanitize($settings['whatsapp']) ?>" target="_blank" class="btn btn-outline btn-lg">
                    <i class="fab fa-whatsapp"></i> WhatsApp ile Ulaş
                </a>
            </div>
            <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number" data-count="15">0</span>+
                    <span class="stat-label">Yıl Deneyim</span>
                </div>
                <div class="stat">
                    <span class="stat-number" data-count="10000">0</span>+
                    <span class="stat-label">Mutlu Misafir</span>
                </div>
                <div class="stat">
                    <span class="stat-number" data-count="7">0</span>
                    <span class="stat-label">Eşsiz Koy</span>
                </div>
                <div class="stat">
                    <span class="stat-number" data-count="5">0</span>
                    <span class="stat-label">Yıldız Puan</span>
                </div>
            </div>
        </div>
        <div class="scroll-indicator">
            <a href="#turlar"><i class="fas fa-chevron-down"></i></a>
        </div>
    </section>

    <!-- Stats Bar -->
    <section class="stats-bar">
        <div class="container">
            <div class="stats-bar-grid">
                <div class="stats-bar-item" data-aos="fade-up">
                    <i class="fas fa-ship"></i>
                    <div>
                        <strong>3 Farklı Tur</strong>
                        <span>Özel, Suluada ve Koylar</span>
                    </div>
                </div>
                <div class="stats-bar-item" data-aos="fade-up">
                    <i class="fas fa-map-marked-alt"></i>
                    <div>
                        <strong>7 Eşsiz Koy</strong>
                        <span>Keşfedilecek destinasyonlar</span>
                    </div>
                </div>
                <div class="stats-bar-item" data-aos="fade-up">
                    <i class="fas fa-utensils"></i>
                    <div>
                        <strong>Öğle Yemeği Dahil</strong>
                        <span>Tavuk ve balık seçenekleri</span>
                    </div>
                </div>
                <div class="stats-bar-item" data-aos="fade-up">
                    <i class="fas fa-clock"></i>
                    <div>
                        <strong>09:30 - 17:00</strong>
                        <span>Tam gün tur keyfi</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Turlar Section -->
    <section class="section tours" id="turlar">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><i class="fas fa-compass"></i> <?= sanitize($tours_section['subtitle'] ?? 'Turlarımız') ?></span>
                <h2><?= sanitize($tours_section['title'] ?? 'Eşsiz Deniz Deneyimleri') ?></h2>
                <p><?= sanitize($tours_section['content'] ?? 'Adrasan\'ın turkuaz sularında unutulmaz anılar biriktirin') ?></p>
            </div>
            <div class="tours-grid">
                <?php if (!empty($tours)): ?>
                    <?php foreach ($tours as $tour): ?>
                    <div class="tour-card" data-aos="fade-up">
                        <div class="tour-image">
                            <img src="<?= SITE_URL ?>/assets/uploads/<?= sanitize($tour['image'] ?: 'default-tour.jpg') ?>" alt="<?= sanitize($tour['title']) ?>" loading="lazy">
                            <div class="tour-badge"><?= sanitize($tour['duration']) ?></div>
                        </div>
                        <div class="tour-body">
                            <h3><?= sanitize($tour['title']) ?></h3>
                            <p><?= sanitize($tour['short_desc']) ?></p>
                            <div class="tour-details">
                                <?php if ($tour['route']): ?>
                                <div class="tour-route">
                                    <i class="fas fa-route"></i>
                                    <span><?= sanitize($tour['route']) ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if ($tour['includes']): ?>
                                <div class="tour-includes">
                                    <i class="fas fa-check-circle"></i>
                                    <span><?= sanitize($tour['includes']) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="tour-footer">
                                <div class="tour-price">
                                    <?php if ($tour['price']): ?>
                                    <span class="price-label">Fiyat:</span>
                                    <span class="price-value"><?= sanitize($tour['price']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <a href="https://wa.me/<?= sanitize($settings['whatsapp']) ?>?text=<?= urlencode($tour['title'] . ' hakkında bilgi almak istiyorum.') ?>"
                                   target="_blank" class="btn-wa">
                                    <i class="fab fa-whatsapp"></i> WhatsApp Rezervasyon
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Varsayilan turlar (DB'de tur yoksa) -->
                    <div class="tour-card" data-aos="fade-up">
                        <div class="tour-image">
                            <div class="tour-image-placeholder"><i class="fas fa-ship"></i></div>
                            <div class="tour-badge">09:30 - 17:00</div>
                        </div>
                        <div class="tour-body">
                            <h3>Özel Tekne Turu</h3>
                            <p>Size özel tasarlanmış deneyimler ile her tur bir sürpriz. Yoğun talep nedeniyle erken rezervasyon önerilir.</p>
                            <div class="tour-footer">
                                <div class="tour-price"></div>
                                <a href="https://wa.me/<?= sanitize($settings['whatsapp']) ?>?text=<?= urlencode('Özel Tekne Turu hakkında bilgi almak istiyorum.') ?>" target="_blank" class="btn-wa">
                                    <i class="fab fa-whatsapp"></i> WhatsApp Rezervasyon
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="tour-card" data-aos="fade-up">
                        <div class="tour-image">
                            <div class="tour-image-placeholder"><i class="fas fa-sun"></i></div>
                            <div class="tour-badge">09:30 - 17:00</div>
                        </div>
                        <div class="tour-body">
                            <h3>Suluada Tekne Turu</h3>
                            <p>Suluada plajı, tatlı su kaynağı, aşıklar mağarası, amerikan plajı ve deniz feneri koyu rotamızda.</p>
                            <div class="tour-details">
                                <div class="tour-route"><i class="fas fa-route"></i><span>Suluada Plajı, Tatlı Su, Aşıklar Mağarası, Amerikan Plajı</span></div>
                            </div>
                            <div class="tour-footer">
                                <div class="tour-price"></div>
                                <a href="https://wa.me/<?= sanitize($settings['whatsapp']) ?>?text=<?= urlencode('Suluada Tekne Turu hakkında bilgi almak istiyorum.') ?>" target="_blank" class="btn-wa">
                                    <i class="fab fa-whatsapp"></i> WhatsApp Rezervasyon
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="tour-card" data-aos="fade-up">
                        <div class="tour-image">
                            <div class="tour-image-placeholder"><i class="fas fa-water"></i></div>
                            <div class="tour-badge">09:30 - 17:00</div>
                        </div>
                        <div class="tour-body">
                            <h3>Koylar Tekne Turu</h3>
                            <p>Akseki koyu, Sazak koyu, Ceneviz koyu ve Korsan koyu rotamızda sizi bekliyor.</p>
                            <div class="tour-details">
                                <div class="tour-route"><i class="fas fa-route"></i><span>Akseki, Sazak, Ceneviz ve Korsan Koyları</span></div>
                            </div>
                            <div class="tour-footer">
                                <div class="tour-price"></div>
                                <a href="https://wa.me/<?= sanitize($settings['whatsapp']) ?>?text=<?= urlencode('Koylar Tekne Turu hakkında bilgi almak istiyorum.') ?>" target="_blank" class="btn-wa">
                                    <i class="fab fa-whatsapp"></i> WhatsApp Rezervasyon
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>



    <!-- Koylar Galerisi -->
    <section class="section koy-gallery" id="galeri">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><i class="fas fa-water"></i> <?= sanitize($koylar_section['subtitle'] ?? 'Keşfedilecek Koylar') ?></span>
                <h2><?= sanitize($koylar_section['title'] ?? 'Eşsiz Koylarımız') ?></h2>
                <p><?= sanitize($koylar_section['content'] ?? 'Adrasan\'ın turkuaz sularında saklı cennetleri keşfedin') ?></p>
            </div>
            <div class="koy-gallery-grid">
                <?php foreach ($destinations as $dest): ?>
                <a href="<?= SITE_URL ?>/<?= sanitize($dest['slug']) ?>" class="koy-gallery-item" data-aos="fade-up">
                    <div class="koy-gallery-img">
                        <?php if (!empty($dest['image'])): ?>
                        <img src="<?= SITE_URL ?>/assets/uploads/<?= sanitize($dest['image']) ?>"
                             alt="<?= sanitize($dest['title']) ?>" loading="lazy">
                        <?php else: ?>
                        <div class="koy-gallery-placeholder">
                            <i class="fas fa-water"></i>
                        </div>
                        <?php endif; ?>
                        <div class="koy-gallery-overlay">
                            <div class="koy-gallery-info">
                                <span class="koy-gallery-subtitle"><?= sanitize($dest['subtitle'] ?? 'Adrasan') ?></span>
                                <h3><?= sanitize($dest['title']) ?></h3>
                                <span class="koy-gallery-btn">Keşfet <i class="fas fa-arrow-right"></i></span>
                            </div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Yorumlar Section -->
    <section class="section testimonials" id="yorumlar">
        <div class="container">
            <div class="section-header">
                <span class="section-tag"><i class="fas fa-star"></i> Müşteri Yorumları</span>
                <h2>Misafirlerimiz Ne Diyor?</h2>
                <p>Binlerce mutlu misafirimizin deneyimlerini okuyun</p>
            </div>
            <div class="testimonials-slider" id="testimonialSlider">
                <?php foreach ($testimonials as $t): ?>
                <div class="testimonial-card">
                    <div class="testimonial-quote"><i class="fas fa-quote-left"></i></div>
                    <div class="testimonial-stars">
                        <?php for ($i = 0; $i < $t['rating']; $i++): ?>
                        <i class="fas fa-star"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="testimonial-text">"<?= sanitize($t['comment']) ?>"</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div>
                            <strong><?= sanitize($t['name']) ?></strong>
                            <span>Misafir</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section cta-section">
        <div class="cta-overlay"></div>
        <div class="container">
            <div class="cta-content" data-aos="zoom-in">
                <h2><?= sanitize($cta['title'] ?? 'Unutulmaz Bir Tekne Turu İçin Hemen Rezervasyon Yapın') ?></h2>
                <p><?= sanitize($cta['content'] ?? 'Adrasan\'ın turkuaz sularında hayalinizi gerçekleştirin. Erken rezervasyon fırsatlarını kaçırmayın!') ?></p>
                <div class="cta-buttons">
                    <a href="tel:<?= sanitize($settings['phone']) ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-phone"></i> <?= sanitize($settings['phone']) ?>
                    </a>
                    <a href="https://wa.me/<?= sanitize($settings['whatsapp']) ?>" target="_blank" class="btn btn-success btn-lg">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>


<?php include 'includes/footer.php'; ?>
