<?php
require_once '../includes/config.php';
requireLogin();

$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Okunmamış mesaj sayısı
$db = getDB();
$unreadCount = $db->query("SELECT COUNT(*) as cnt FROM contact_messages WHERE is_read = 0")->fetch()['cnt'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Celal Kaptan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:#f0f2f5; color:#333; }

        .admin-layout { display:flex; min-height:100vh; }

        /* Sidebar */
        .sidebar {
            width:260px; background:#0a1628; color:#fff; padding:24px 0;
            position:fixed; left:0; top:0; bottom:0; overflow-y:auto; z-index:100;
            transition: transform 0.3s ease;
        }
        .sidebar-logo {
            display:flex; align-items:center; gap:10px; padding:0 24px 24px;
            border-bottom:1px solid rgba(255,255,255,0.1); font-size:1.1rem; font-weight:700;
        }
        .sidebar-logo i { color:#29B8D8; font-size:1.8rem; }
        .sidebar-nav { padding:16px 12px; }
        .sidebar-nav a {
            display:flex; align-items:center; gap:12px; padding:12px 16px;
            color:rgba(255,255,255,0.7); border-radius:10px; font-size:0.9rem;
            text-decoration:none; transition:all 0.3s ease; margin-bottom:4px;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background:rgba(255,255,255,0.1); color:#fff;
        }
        .sidebar-nav a.active { background:#0077b6; }
        .sidebar-nav a i { width:20px; text-align:center; }
        .sidebar-divider { height:1px; background:rgba(255,255,255,0.08); margin:12px 16px; }
        .sidebar-label { font-size:0.7rem; text-transform:uppercase; letter-spacing:1px; color:rgba(255,255,255,0.35); padding:0 16px; margin-bottom:8px; font-weight:600; }
        .sidebar-nav .badge {
            background:#29B8D8; color:#fff; font-size:0.7rem;
            padding:2px 8px; border-radius:50px; margin-left:auto;
        }

        /* Main Content */
        .main-content { margin-left:260px; flex:1; }
        .top-bar {
            background:#fff; padding:16px 32px; display:flex; align-items:center;
            justify-content:space-between; box-shadow:0 2px 10px rgba(0,0,0,0.05);
            position:sticky; top:0; z-index:50;
        }
        .top-bar h2 { font-size:1.3rem; color:#0a1628; }
        .top-bar-actions { display:flex; align-items:center; gap:16px; }
        .top-bar-actions a {
            color:#6c757d; text-decoration:none; font-size:0.9rem;
            display:flex; align-items:center; gap:6px;
        }
        .top-bar-actions a:hover { color:#0077b6; }

        .content { padding:32px; }

        /* Cards */
        .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:20px; margin-bottom:32px; }
        .stat-card {
            background:#fff; padding:24px; border-radius:14px;
            box-shadow:0 2px 10px rgba(0,0,0,0.05); display:flex; align-items:center; gap:16px;
        }
        .stat-icon {
            width:52px; height:52px; border-radius:12px; display:flex;
            align-items:center; justify-content:center; font-size:1.3rem;
        }
        .stat-icon.blue { background:#e3f2fd; color:#0077b6; }
        .stat-icon.green { background:#e8f5e9; color:#25d366; }
        .stat-icon.orange { background:#e3f0fa; color:#0a1628; }
        .stat-icon.purple { background:#f3e5f5; color:#9c27b0; }
        .stat-info h3 { font-size:1.8rem; color:#0a1628; }
        .stat-info p { font-size:0.85rem; color:#6c757d; }

        /* Table */
        .card { background:#fff; border-radius:14px; box-shadow:0 2px 10px rgba(0,0,0,0.05); overflow:hidden; margin-bottom:24px; }
        .card-header { padding:20px 24px; border-bottom:1px solid #e9ecef; display:flex; align-items:center; justify-content:space-between; }
        .card-header h3 { font-size:1.1rem; color:#0a1628; }
        .card-body { padding:24px; }

        table { width:100%; border-collapse:collapse; }
        table th { background:#f8f9fa; padding:12px 16px; text-align:left; font-size:0.85rem; color:#6c757d; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; }
        table td { padding:14px 16px; border-bottom:1px solid #f0f0f0; font-size:0.9rem; }
        table tr:hover td { background:#f8f9fa; }

        /* Forms */
        .form-group { margin-bottom:20px; }
        .form-group label { display:block; margin-bottom:6px; font-weight:600; font-size:0.9rem; color:#495057; }
        .form-group input, .form-group textarea, .form-group select {
            width:100%; padding:12px 16px; border:2px solid #e9ecef; border-radius:10px;
            font-family:inherit; font-size:0.95rem; transition:all 0.3s;
        }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            outline:none; border-color:#0077b6; box-shadow:0 0 0 4px rgba(0,119,182,0.1);
        }
        .form-group textarea { resize:vertical; min-height:100px; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:20px; }

        /* Buttons */
        .btn { display:inline-flex; align-items:center; gap:6px; padding:10px 20px; border-radius:8px; font-family:inherit; font-size:0.9rem; font-weight:600; border:none; cursor:pointer; transition:all 0.3s; text-decoration:none; }
        .btn-primary { background:#0077b6; color:#fff; }
        .btn-primary:hover { background:#005f8d; }
        .btn-danger { background:#dc3545; color:#fff; }
        .btn-danger:hover { background:#c82333; }
        .btn-success { background:#25d366; color:#fff; }
        .btn-success:hover { background:#1da851; }
        .btn-sm { padding:6px 12px; font-size:0.8rem; }
        .btn-outline { background:transparent; border:2px solid #e9ecef; color:#6c757d; }
        .btn-outline:hover { border-color:#0077b6; color:#0077b6; }

        /* Alert */
        .alert { padding:14px 20px; border-radius:10px; margin-bottom:20px; font-size:0.9rem; }
        .alert-success { background:#d4edda; color:#155724; }
        .alert-error { background:#f8d7da; color:#721c24; }

        /* Tabs */
        .tabs { display:flex; gap:4px; margin-bottom:24px; border-bottom:2px solid #e9ecef; padding-bottom:0; }
        .tab { padding:12px 20px; font-size:0.9rem; font-weight:600; color:#6c757d; cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-2px; text-decoration:none; }
        .tab:hover, .tab.active { color:#0077b6; border-bottom-color:#0077b6; }

        /* Image preview */
        .img-preview { width:80px; height:60px; object-fit:cover; border-radius:8px; border:2px solid #e9ecef; }
        .img-preview-lg { width:120px; height:90px; object-fit:cover; border-radius:10px; border:2px solid #e9ecef; }

        /* Status badge */
        .status-badge { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:50px; font-size:0.78rem; font-weight:600; }
        .status-badge.active { background:#d4edda; color:#155724; }
        .status-badge.inactive { background:#f8d7da; color:#721c24; }

        /* Quick actions */
        .quick-actions { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; margin-bottom:32px; }
        .quick-action { display:flex; align-items:center; gap:12px; padding:16px 20px; background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.05); text-decoration:none; color:#333; transition:all 0.3s; border:2px solid transparent; }
        .quick-action:hover { border-color:#0077b6; transform:translateY(-2px); box-shadow:0 4px 15px rgba(0,0,0,0.1); }
        .quick-action i { font-size:1.2rem; width:40px; height:40px; display:flex; align-items:center; justify-content:center; border-radius:10px; }
        .quick-action .qa-text strong { display:block; font-size:0.9rem; }
        .quick-action .qa-text span { font-size:0.8rem; color:#6c757d; }

        /* FAQ builder */
        .faq-builder { border:2px solid #e9ecef; border-radius:10px; padding:16px; }
        .faq-builder-item { display:grid; grid-template-columns:1fr 1fr auto; gap:10px; margin-bottom:10px; align-items:start; }
        .faq-builder-item input, .faq-builder-item textarea { padding:10px 14px; border:2px solid #e9ecef; border-radius:8px; font-family:inherit; font-size:0.9rem; }
        .faq-builder-item textarea { min-height:60px; resize:vertical; }
        .faq-builder-item input:focus, .faq-builder-item textarea:focus { outline:none; border-color:#0077b6; }
        .faq-remove { background:#dc3545; color:#fff; border:none; width:36px; height:36px; border-radius:8px; cursor:pointer; font-size:0.9rem; }
        .faq-add { background:#e3f2fd; color:#0077b6; border:2px dashed #0077b6; padding:10px; border-radius:8px; cursor:pointer; font-size:0.85rem; width:100%; text-align:center; margin-top:8px; }
        .faq-add:hover { background:#0077b6; color:#fff; }

        /* Gallery grid */
        .gallery-admin-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:16px; }
        .gallery-admin-item { position:relative; border-radius:12px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.08); }
        .gallery-admin-item img { width:100%; height:160px; object-fit:cover; display:block; }
        .gallery-admin-item .gallery-actions { position:absolute; top:8px; right:8px; display:flex; gap:4px; }
        .gallery-admin-item .gallery-actions a { width:32px; height:32px; display:flex; align-items:center; justify-content:center; border-radius:8px; font-size:0.8rem; text-decoration:none; }
        .gallery-admin-item .gallery-info { padding:10px; background:#fff; }
        .gallery-admin-item .gallery-info p { font-size:0.8rem; color:#6c757d; margin:0; }

        /* Form helpers */
        .form-help { font-size:0.8rem; color:#6c757d; margin-top:4px; }
        .form-row-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; }
        .form-section { background:#f8f9fa; padding:20px; border-radius:10px; margin-bottom:20px; }
        .form-section h4 { margin-bottom:16px; color:#0a1628; font-size:1rem; display:flex; align-items:center; gap:8px; }

        /* Mobile toggle */
        .sidebar-toggle { display:none; background:none; border:none; color:#0a1628; font-size:1.3rem; cursor:pointer; }

        @media(max-width:768px) {
            .sidebar { transform:translateX(-100%); }
            .sidebar.active { transform:translateX(0); }
            .main-content { margin-left:0; }
            .sidebar-toggle { display:block; }
            .form-row { grid-template-columns:1fr; }
            .form-row-3 { grid-template-columns:1fr; }
            .stats-grid { grid-template-columns:1fr 1fr; }
            .quick-actions { grid-template-columns:1fr; }
            .gallery-admin-grid { grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); }
            .faq-builder-item { grid-template-columns:1fr; }
        }
    </style>
</head>
<body>
<div class="admin-layout">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <img src="../assets/images/logo.png" alt="Celal Kaptan Logo" style="height:70px; width:auto; object-fit:contain;">
        </div>
        <nav class="sidebar-nav">
            <a href="index.php" class="<?= $currentPage === 'index' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <div class="sidebar-divider"></div>
            <p class="sidebar-label">İçerik Yönetimi</p>
            <a href="contents.php" class="<?= $currentPage === 'contents' ? 'active' : '' ?>">
                <i class="fas fa-file-alt"></i> Sayfa İçerikleri
            </a>
            <a href="tours.php" class="<?= $currentPage === 'tours' ? 'active' : '' ?>">
                <i class="fas fa-ship"></i> Turlar
            </a>
            <a href="destinations.php" class="<?= $currentPage === 'destinations' ? 'active' : '' ?>">
                <i class="fas fa-map-marker-alt"></i> Koylar
            </a>
            <a href="gallery.php" class="<?= $currentPage === 'gallery' ? 'active' : '' ?>">
                <i class="fas fa-images"></i> Galeri
            </a>
            <a href="testimonials.php" class="<?= $currentPage === 'testimonials' ? 'active' : '' ?>">
                <i class="fas fa-star"></i> Yorumlar
            </a>
            <div class="sidebar-divider"></div>
            <p class="sidebar-label">İletişim</p>
            <a href="messages.php" class="<?= $currentPage === 'messages' ? 'active' : '' ?>">
                <i class="fas fa-envelope"></i> Mesajlar
                <?php if ($unreadCount > 0): ?>
                <span class="badge"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>
            <div class="sidebar-divider"></div>
            <p class="sidebar-label">Ayarlar</p>
            <a href="settings.php" class="<?= $currentPage === 'settings' ? 'active' : '' ?>">
                <i class="fas fa-cog"></i> Site Ayarları
            </a>
            <a href="seo.php" class="<?= $currentPage === 'seo' ? 'active' : '' ?>">
                <i class="fas fa-search"></i> SEO Ayarları
            </a>
            <a href="password.php" class="<?= $currentPage === 'password' ? 'active' : '' ?>">
                <i class="fas fa-key"></i> Şifre Değiştir
            </a>
            <a href="logout.php" style="margin-top:20px;color:#ff6b6b;">
                <i class="fas fa-sign-out-alt"></i> Çıkış Yap
            </a>
        </nav>
    </aside>
    <div class="main-content">
        <div class="top-bar">
            <div style="display:flex;align-items:center;gap:12px;">
                <button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('active')">
                    <i class="fas fa-bars"></i>
                </button>
                <h2 id="pageTitle">Dashboard</h2>
            </div>
            <div class="top-bar-actions">
                <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Siteyi Gör</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
            </div>
        </div>
        <div class="content">
