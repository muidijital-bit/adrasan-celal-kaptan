<?php
// ============================================
// ADRASAN CELAL KAPTAN - Yapilandirma Sablonu
// ============================================
// Bu dosyayi kopyala: cp includes/config.example.php includes/config.php
// Sonra config.php icindeki bilgileri kendi ortamina gore duzenle.

// Veritabani Ayarlari
define('DB_HOST', 'localhost');
define('DB_NAME', 'adrasan_celal_kaptan');
define('DB_USER', 'root');        // XAMPP icin genellikle root
define('DB_PASS', '');            // XAMPP icin genellikle bos
define('DB_CHARSET', 'utf8mb4');

// Site Ayarlari
// Localhost: 'http://localhost/adrasan-celal-kaptan'
// Canli:     'https://adrasancelalkaptan.com.tr'
define('SITE_URL', 'http://localhost/adrasan-celal-kaptan');
define('SITE_NAME', 'Adrasan Celal Kaptan');
define('ADMIN_EMAIL', 'adrasancelalkaptan@gmail.com');

// Upload Ayarlari (degistirme)
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// Oturum baslat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Veritabani baglantisi
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die("Veritabani baglanti hatasi: " . $e->getMessage());
        }
    }
    return $pdo;
}

// Guvenlik fonksiyonlari
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// SEO meta verilerini getir
function getSEO($page_slug) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM seo_settings WHERE page_slug = ?");
    $stmt->execute([$page_slug]);
    return $stmt->fetch() ?: [
        'meta_title' => SITE_NAME,
        'meta_description' => 'Adrasan Celal Kaptan ile unutulmaz tekne turu deneyimi',
        'meta_keywords' => 'adrasan, tekne turu, suluada',
        'og_image' => '',
        'robots' => 'index, follow',
        'canonical_url' => ''
    ];
}

// Sayfa icerigini getir
function getContent($section) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM page_contents WHERE section_key = ?");
    $stmt->execute([$section]);
    return $stmt->fetch();
}

// Site ayarini getir
function getSetting($key, $default = '') {
    $db = getDB();
    $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : $default;
}

// Destinasyon getir (slug ile)
function getDestination($slug) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM destinations WHERE slug = ? AND is_active = 1");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

// Tum aktif destinasyonlari getir
function getDestinations() {
    $db = getDB();
    return $db->query("SELECT * FROM destinations WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();
}

// Tum aktif turlari getir
function getTours() {
    $db = getDB();
    return $db->query("SELECT * FROM tours WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();
}

// Aktif yorumlari getir
function getTestimonials($limit = 6) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY created_at DESC LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

// Galeri resimlerini getir
function getGallery($category = null, $limit = 12) {
    $db = getDB();
    if ($category) {
        $stmt = $db->prepare("SELECT * FROM gallery WHERE is_active = 1 AND category = ? ORDER BY sort_order ASC LIMIT ?");
        $stmt->execute([$category, $limit]);
    } else {
        $stmt = $db->prepare("SELECT * FROM gallery WHERE is_active = 1 ORDER BY sort_order ASC LIMIT ?");
        $stmt->execute([$limit]);
    }
    return $stmt->fetchAll();
}

// Ortak header icin site ayarlarini getir
function getSiteSettings() {
    return [
        'phone'          => getSetting('phone', '0543 717 33 78'),
        'email'          => getSetting('email', 'adrasancelalkaptan@gmail.com'),
        'address'        => getSetting('address', 'Adrasan, Kumluca, Antalya'),
        'whatsapp'       => getSetting('whatsapp', '905437173378'),
        'instagram'      => getSetting('instagram', ''),
        'facebook'       => getSetting('facebook', ''),
        'youtube'        => getSetting('youtube', ''),
        'working_hours'  => getSetting('working_hours', '09:30 - 17:00'),
        'google_analytics' => getSetting('google_analytics', ''),
        'google_maps'    => getSetting('google_maps', ''),
    ];
}
