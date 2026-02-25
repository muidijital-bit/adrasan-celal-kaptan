<?php
require_once __DIR__ . '/config.php';
$settings = getSiteSettings();
$destinations = getDestinations();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- SEO Meta -->
    <title><?= sanitize($seo['meta_title'] ?? SITE_NAME) ?></title>
    <meta name="description" content="<?= sanitize($seo['meta_description'] ?? '') ?>">
    <meta name="keywords" content="<?= sanitize($seo['meta_keywords'] ?? '') ?>">
    <meta name="robots" content="<?= sanitize($seo['robots'] ?? 'index, follow') ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= sanitize($seo['meta_title'] ?? SITE_NAME) ?>">
    <meta property="og:description" content="<?= sanitize($seo['meta_description'] ?? '') ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= SITE_URL ?>">
    <?php if (!empty($seo['og_image'])): ?>
    <meta property="og:image" content="<?= SITE_URL ?>/assets/uploads/<?= sanitize($seo['og_image']) ?>">
    <?php endif; ?>

    <!-- Canonical -->
    <?php if (!empty($seo['canonical_url'])): ?>
    <link rel="canonical" href="<?= sanitize($seo['canonical_url']) ?>">
    <?php endif; ?>

    <!-- Sitemap -->
    <link rel="sitemap" type="application/xml" href="<?= SITE_URL ?>/sitemap.xml.php">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= SITE_URL ?>/assets/images/favicon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">

    <!-- Google Analytics -->
    <?php if (!empty($settings['google_analytics'])): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= sanitize($settings['google_analytics']) ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= sanitize($settings['google_analytics']) ?>');
    </script>
    <?php endif; ?>

    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "TouristAttraction",
        "name": "<?= sanitize($seo['meta_title'] ?? SITE_NAME) ?>",
        "description": "<?= sanitize($seo['meta_description'] ?? '') ?>",
        "telephone": "<?= sanitize($settings['phone']) ?>",
        "email": "<?= sanitize($settings['email']) ?>",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Adrasan Mahallesi",
            "addressLocality": "Kumluca",
            "addressRegion": "Antalya",
            "postalCode": "07350",
            "addressCountry": "TR"
        }
    }
    </script>
</head>
<body>
    <!-- Preloader -->
    <div id="preloader">
        <div class="loader">
            <i class="fas fa-ship"></i>
            <p>Yükleniyor...</p>
        </div>
    </div>

    <!-- Header / Navbar -->
    <header class="navbar <?= $currentPage !== 'index' ? 'scrolled' : '' ?>" id="navbar">
        <div class="container">
            <a href="<?= SITE_URL ?>/" class="logo">
                <img src="<?= SITE_URL ?>/assets/images/logo.png" alt="Celal Kaptan Logo" class="logo-image">
            </a>
            <nav class="nav-menu" id="navMenu">
                <a href="<?= SITE_URL ?>/" class="nav-link <?= $currentPage === 'index' ? 'active' : '' ?>">Ana Sayfa</a>
                <div class="nav-dropdown">
                    <a href="<?= SITE_URL ?>/hizmetlerimiz" class="nav-link <?= in_array($currentPage, ['hizmetlerimiz', 'destinasyon']) ? 'active' : '' ?>"
                       onclick="if(window.innerWidth<=768){event.preventDefault();this.closest('.nav-dropdown').classList.toggle('open');}">
                        Hizmetlerimiz <i class="fas fa-chevron-down" style="font-size:0.7rem;margin-left:4px;"></i>
                    </a>
                    <div class="dropdown-menu">
                        <a href="<?= SITE_URL ?>/hizmetlerimiz" style="font-weight:700;color:var(--primary);">
                            <i class="fas fa-compass" style="margin-right:6px;"></i> Tüm Hizmetlerimiz
                        </a>
                        <div style="height:1px;background:#eee;margin:4px 12px;"></div>
                        <?php foreach ($destinations as $dest): ?>
                        <a href="<?= SITE_URL ?>/<?= sanitize($dest['slug']) ?>">
                            <i class="fas fa-map-pin" style="margin-right:6px;color:var(--primary);font-size:0.75rem;"></i>
                            <?= sanitize($dest['title']) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <a href="<?= SITE_URL ?>/hakkimizda" class="nav-link <?= $currentPage === 'hakkimizda' ? 'active' : '' ?>">Hakkımızda</a>
                <a href="<?= SITE_URL ?>/galeri" class="nav-link <?= $currentPage === 'galeri' ? 'active' : '' ?>">Galeri</a>
<a href="<?= SITE_URL ?>/iletisim" class="nav-link <?= $currentPage === 'iletisim' ? 'active' : '' ?>">İletişim</a>
                <a href="tel:<?= sanitize($settings['phone']) ?>" class="nav-cta">
                    <i class="fas fa-phone"></i> Hemen Ara
                </a>
            </nav>
            <button class="hamburger" id="hamburger" aria-label="Menü">
                <span></span><span></span><span></span>
            </button>
        </div>
    </header>
