<?php
require_once 'includes/config.php';

$seo = getSEO('iletisim');
$contactHero = getContent('contact_hero');
$settings = getSiteSettings();

include 'includes/header.php';
?>

<style>
/* ---- İletişim Sayfa Stilleri ---- */
.contact-page { padding: 60px 0 80px; }

/* İki sütun grid */
.contact-main-grid {
    display: grid;
    grid-template-columns: 1fr 420px;
    gap: 40px;
    align-items: start;
}

/* Sol: Form */
.contact-form-box {
    background: var(--white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    padding: 40px;
}
.contact-form-box h2 {
    font-family: var(--font-heading);
    font-size: 1.6rem;
    color: var(--dark);
    margin-bottom: 6px;
}
.contact-form-box > p {
    color: var(--gray-500);
    margin-bottom: 28px;
    font-size: 0.95rem;
}

/* Sağ: Bilgiler */
.contact-info-box {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.contact-info-card {
    background: var(--white);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
    padding: 20px 24px;
    display: flex;
    align-items: center;
    gap: 18px;
    transition: var(--transition);
}
.contact-info-card:hover { box-shadow: var(--shadow-md); transform: translateX(4px); }
.contact-info-icon {
    width: 50px; height: 50px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.contact-info-icon.phone  { background: rgba(41,184,216,0.12); color: var(--primary); }
.contact-info-icon.wa     { background: rgba(37,211,102,0.12); color: #25d366; }
.contact-info-icon.mail   { background: rgba(247,127,0,0.12);  color: var(--accent); }
.contact-info-icon.addr   { background: rgba(41,184,216,0.12); color: var(--primary); }
.contact-info-icon.clock  { background: rgba(45,55,72,0.10);   color: var(--dark); }

.contact-info-text { flex: 1; }
.contact-info-text span { display: block; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-400); margin-bottom: 3px; font-weight: 600; }
.contact-info-text a,
.contact-info-text strong { font-size: 0.95rem; color: var(--dark); font-weight: 600; }
.contact-info-text a:hover { color: var(--primary); }
.contact-info-text p { font-size: 0.9rem; color: var(--gray-500); margin: 0; }

/* WhatsApp büyük buton */
.contact-wa-btn {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    background: #25d366;
    color: var(--white) !important;
    padding: 16px 24px;
    border-radius: var(--radius-md);
    font-weight: 700;
    font-size: 1rem;
    transition: var(--transition);
    text-decoration: none;
    box-shadow: 0 4px 16px rgba(37,211,102,0.35);
}
.contact-wa-btn:hover { background: #1ebe5b; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(37,211,102,0.45); }
.contact-wa-btn i { font-size: 1.3rem; }

/* Harita */
.contact-map-section { padding: 0 0 60px; }
.contact-map-wrap {
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    line-height: 0;
}
.contact-map-wrap iframe { display: block; width: 100%; border: 0; }

/* Responsive */
@media (max-width: 960px) {
    .contact-main-grid { grid-template-columns: 1fr; }
    .contact-info-box { flex-direction: row; flex-wrap: wrap; }
    .contact-info-card { flex: 1 1 calc(50% - 8px); }
}
@media (max-width: 600px) {
    .contact-info-box { flex-direction: column; }
    .contact-info-card { flex: 1 1 100%; }
    .contact-form-box { padding: 24px; }
}
</style>

<!-- Page Hero -->
<section class="page-hero" <?php if (!empty($contactHero['image'])): ?>style="background-image:url('<?= SITE_URL ?>/assets/uploads/<?= sanitize($contactHero['image']) ?>');background-size:cover;background-position:center;"<?php endif; ?>>
    <div class="page-hero-overlay"></div>
    <div class="container">
        <div class="page-hero-content">
            <div class="hero-badge"><i class="fas fa-envelope"></i> İletişim</div>
            <h1><?= sanitize($contactHero['title'] ?? 'Bize Ulaşın') ?></h1>
            <p><?= sanitize($contactHero['content'] ?? 'Rezervasyon ve bilgi için bize ulaşın, en kısa sürede dönüş yaparız.') ?></p>
        </div>
    </div>
</section>

<!-- Ana İletişim Bölümü -->
<div class="contact-page">
    <div class="container">
        <div class="contact-main-grid">

            <!-- Sol: Form -->
            <div class="contact-form-box" data-aos="fade-right">
                <h2>Mesaj Gönderin</h2>
                <p>Formu doldurun, en kısa sürede size ulaşalım.</p>
                <form class="contact-form" id="contactForm" method="POST" action="<?= SITE_URL ?>/api/contact.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Adınız Soyadınız *</label>
                            <input type="text" name="name" placeholder="Adınız Soyadınız" required>
                        </div>
                        <div class="form-group">
                            <label>Telefon Numaranız *</label>
                            <input type="tel" name="phone" placeholder="0500 000 00 00" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>E-posta Adresiniz</label>
                            <input type="email" name="email" placeholder="ornek@email.com">
                        </div>
                        <div class="form-group">
                            <label>Konu</label>
                            <select name="subject">
                                <option value="">Konu Seçin</option>
                                <option value="Suluada Tekne Turu">Suluada Tekne Turu</option>
                                <option value="Koylar Tekne Turu">Koylar Tekne Turu</option>
                                <option value="Özel Tekne Turu">Özel Tekne Turu</option>
                                <option value="Fiyat Bilgisi">Fiyat Bilgisi</option>
                                <option value="Genel Bilgi">Genel Bilgi</option>
                                <option value="Diğer">Diğer</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Mesajınız *</label>
                        <textarea name="message" rows="6" placeholder="Mesajınızı buraya yazın..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">
                        <i class="fas fa-paper-plane"></i> Mesaj Gönder
                    </button>
                    <div class="form-response" id="formResponse"></div>
                </form>
            </div>

            <!-- Sağ: Bilgiler -->
            <div class="contact-info-box" data-aos="fade-left">

                <div class="contact-info-card">
                    <div class="contact-info-icon phone"><i class="fas fa-phone"></i></div>
                    <div class="contact-info-text">
                        <span>Telefon</span>
                        <a href="tel:<?= sanitize($settings['phone']) ?>"><?= sanitize($settings['phone']) ?></a>
                    </div>
                </div>

                <div class="contact-info-card">
                    <div class="contact-info-icon wa"><i class="fab fa-whatsapp"></i></div>
                    <div class="contact-info-text">
                        <span>WhatsApp</span>
                        <a href="https://wa.me/<?= sanitize($settings['whatsapp']) ?>" target="_blank">Hemen Yazın</a>
                    </div>
                </div>

                <div class="contact-info-card">
                    <div class="contact-info-icon mail"><i class="fas fa-envelope"></i></div>
                    <div class="contact-info-text">
                        <span>E-posta</span>
                        <a href="mailto:<?= sanitize($settings['email']) ?>"><?= sanitize($settings['email']) ?></a>
                    </div>
                </div>

                <div class="contact-info-card">
                    <div class="contact-info-icon addr"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="contact-info-text">
                        <span>Adres</span>
                        <p><?= sanitize($settings['address']) ?></p>
                    </div>
                </div>

                <div class="contact-info-card">
                    <div class="contact-info-icon clock"><i class="fas fa-clock"></i></div>
                    <div class="contact-info-text">
                        <span>Çalışma Saatleri</span>
                        <strong><?= sanitize($settings['working_hours']) ?></strong>
                        <p style="margin-top:2px;font-size:0.82rem;">Tur Saatleri: 09:30 – 17:00</p>
                    </div>
                </div>

                <a href="https://wa.me/<?= sanitize($settings['whatsapp']) ?>?text=Merhaba,%20tekne%20turu%20hakk%C4%B1nda%20bilgi%20almak%20istiyorum."
                   target="_blank" class="contact-wa-btn">
                    <i class="fab fa-whatsapp"></i> WhatsApp ile Rezervasyon
                </a>

            </div>
        </div>
    </div>
</div>

<!-- Harita -->
<?php if (!empty($settings['google_maps'])): ?>
<div class="contact-map-section">
    <div class="container">
        <div class="contact-map-wrap">
            <?= $settings['google_maps'] ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
