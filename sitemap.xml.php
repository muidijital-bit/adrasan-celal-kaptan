<?php
header('Content-Type: application/xml; charset=utf-8');
require_once 'includes/config.php';

$db = getDB();
$baseUrl = SITE_URL;

// Ana sayfalar
$mainPages = [
    ['url' => '', 'priority' => '1.00'],
    ['url' => 'hizmetlerimiz', 'priority' => '0.90'],
    ['url' => 'hakkimizda', 'priority' => '0.85'],
    ['url' => 'iletisim', 'priority' => '0.80'],
];

// Hizmetler (aktif olanlar)
$services = $db->query("SELECT id, title FROM services WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();

// Destinasyonlar (aktif olanlar)
$destinations = $db->query("SELECT slug, title FROM destinations WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();

// Turlar (aktif olanlar)
$tours = $db->query("SELECT id, slug, title FROM tours WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();

$today = date('Y-m-d\TH:i:s+00:00');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Ana sayfalar
foreach ($mainPages as $page) {
    $url = $page['url'] ? $baseUrl . '/' . $page['url'] : $baseUrl . '/';
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($url) . "</loc>\n";
    echo "    <lastmod>" . $today . "</lastmod>\n";
    echo "    <priority>" . $page['priority'] . "</priority>\n";
    echo "  </url>\n";
}

// Hizmetler
foreach ($services as $service) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($baseUrl . '/hizmet/' . $service['id']) . "</loc>\n";
    echo "    <lastmod>" . $today . "</lastmod>\n";
    echo "    <priority>0.80</priority>\n";
    echo "  </url>\n";
}

// Destinasyonlar
foreach ($destinations as $dest) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($baseUrl . '/destinasyon/' . $dest['slug']) . "</loc>\n";
    echo "    <lastmod>" . $today . "</lastmod>\n";
    echo "    <priority>0.75</priority>\n";
    echo "  </url>\n";
}

// Turlar
foreach ($tours as $tour) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($baseUrl . '/tour/' . $tour['slug']) . "</loc>\n";
    echo "    <lastmod>" . $today . "</lastmod>\n";
    echo "    <priority>0.75</priority>\n";
    echo "  </url>\n";
}

echo '</urlset>' . "\n";
?>
