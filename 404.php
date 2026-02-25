<?php
require_once 'includes/config.php';

http_response_code(404);

$seo = [
    'meta_title'       => 'Sayfa Bulunamadı | Adrasan Celal Kaptan',
    'meta_description' => 'Aradığınız sayfa bulunamadı.',
    'meta_keywords'    => '',
    'robots'           => 'noindex, nofollow',
    'og_image'         => '',
    'canonical_url'    => '',
];
$settings = getSiteSettings();

include 'includes/header.php';
?>

<style>
.error-page {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 80px 20px;
}
.error-content { max-width: 560px; margin: 0 auto; }
.error-code {
    font-family: var(--font-heading);
    font-size: clamp(6rem, 20vw, 10rem);
    font-weight: 700;
    line-height: 1;
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 8px;
}
.error-icon { font-size: 3rem; color: var(--primary-light); margin-bottom: 24px; }
.error-content h2 { font-family: var(--font-heading); font-size: 2rem; color: var(--dark); margin-bottom: 12px; }
.error-content p { color: var(--gray-500); font-size: 1.05rem; margin-bottom: 36px; line-height: 1.7; }
.error-buttons { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
</style>

<div style="padding-top:105px;">
    <div class="error-page">
        <div class="error-content" data-aos="zoom-in">
            <div class="error-code">404</div>
            <div class="error-icon"><i class="fas fa-anchor"></i></div>
            <h2>Sayfa Bulunamadı</h2>
            <p>Aradığınız sayfa kaldırılmış, taşınmış ya da hiç var olmamış olabilir. Rüzgar sizi yanlış yöne götürmüş olabilir.</p>
            <div class="error-buttons">
                <a href="<?= SITE_URL ?>/" class="btn btn-primary btn-lg">
                    <i class="fas fa-home"></i> Ana Sayfaya Dön
                </a>
                <a href="<?= SITE_URL ?>/iletisim" class="btn btn-outline" style="color:var(--dark);border-color:var(--gray-300);">
                    <i class="fas fa-envelope"></i> İletişim
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
