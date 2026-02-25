<?php
require_once 'includes/config.php';

$seo = getSEO('galeri');
if (empty($seo['meta_title']) || $seo['meta_title'] === SITE_NAME) {
    $seo['meta_title'] = 'Galeri | Adrasan Celal Kaptan';
    $seo['meta_description'] = 'Adrasan tekne turu, Suluada ve koylarımıza ait fotoğraf galerimizi keşfedin.';
}

$settings = getSiteSettings();
$galeriHero = getContent('galeri_hero');
$allImages = getGallery(null, 999);

$usedCats = [];
foreach ($allImages as $img) {
    $usedCats[$img['category']] = true;
}

$catLabels = [
    'genel'      => 'Genel',
    'tekne'      => 'Tekne',
    'koylar'     => 'Koylar',
    'suluada'    => 'Suluada',
    'misafirler' => 'Misafirler',
    'yemek'      => 'Yemek',
];

include 'includes/header.php';
?>

<style>
.gallery-page { padding: 40px 0 100px; }
.gallery-filters { display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; margin-bottom: 48px; }
.gallery-filter-btn { padding: 9px 22px; border: 2px solid var(--gray-300); border-radius: 50px; font-size: 0.88rem; font-weight: 600; background: var(--white); color: var(--gray-500); cursor: pointer; transition: all 0.25s ease; }
.gallery-filter-btn:hover { border-color: var(--primary); color: var(--primary); }
.gallery-filter-btn.active { background: var(--primary); border-color: var(--primary); color: var(--white); }

.gallery-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
.gallery-grid-item { border-radius: var(--radius-md); overflow: hidden; cursor: pointer; position: relative; display: block; transition: transform 0.3s ease, box-shadow 0.3s ease; }
.gallery-grid-item:hover { transform: translateY(-4px); box-shadow: 0 12px 36px rgba(0,0,0,0.18); }
.gallery-grid-item img { width: 100%; height: 220px; object-fit: cover; display: block; transition: transform 0.4s ease; }
.gallery-grid-item:hover img { transform: scale(1.04); }
.gallery-item-overlay { position: absolute; inset: 0; background: rgba(10,22,40,0); display: flex; align-items: center; justify-content: center; transition: background 0.3s ease; }
.gallery-grid-item:hover .gallery-item-overlay { background: rgba(10,22,40,0.35); }
.gallery-item-overlay i { color: var(--white); font-size: 2rem; opacity: 0; transition: opacity 0.3s ease; }
.gallery-grid-item:hover .gallery-item-overlay i { opacity: 1; }
.gallery-empty { text-align: center; padding: 80px 20px; color: var(--gray-500); }
.gallery-empty i { font-size: 4rem; margin-bottom: 16px; display: block; color: var(--gray-300); }

/* Lightbox */
#lightbox { position: fixed; inset: 0; z-index: 9999; background: rgba(5,10,20,0.96); display: none; align-items: center; justify-content: center; flex-direction: column; }
#lightbox.open { display: flex; }
#lightbox-img { max-width: 90vw; max-height: 82vh; object-fit: contain; border-radius: var(--radius-md); box-shadow: 0 24px 80px rgba(0,0,0,0.6); transition: opacity 0.2s ease; }
#lightbox-caption { color: rgba(255,255,255,0.7); margin-top: 16px; font-size: 0.9rem; text-align: center; }
#lightbox-close { position: absolute; top: 20px; right: 24px; background: none; border: none; color: rgba(255,255,255,0.7); font-size: 2rem; cursor: pointer; line-height: 1; transition: color 0.2s; }
#lightbox-close:hover { color: var(--white); }
#lightbox-prev, #lightbox-next { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: var(--white); width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; cursor: pointer; transition: background 0.2s; }
#lightbox-prev { left: 20px; }
#lightbox-next { right: 20px; }
#lightbox-prev:hover, #lightbox-next:hover { background: rgba(255,255,255,0.18); }
#lightbox-counter { position: absolute; bottom: 20px; color: rgba(255,255,255,0.4); font-size: 0.85rem; }

@media (max-width: 1024px) { .gallery-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 640px)  { .gallery-grid { grid-template-columns: repeat(2, 1fr); } .gallery-grid-item img { height: 180px; } }
@media (max-width: 400px)  { .gallery-grid { grid-template-columns: 1fr; } }
</style>

<!-- Page Hero -->
<section class="page-hero" <?php if (!empty($galeriHero['image'])): ?>style="background-image:url('<?= SITE_URL ?>/assets/uploads/<?= sanitize($galeriHero['image']) ?>');background-size:cover;background-position:center;"<?php endif; ?>>
    <div class="page-hero-overlay"></div>
    <div class="container">
        <div class="page-hero-content">
            <span class="hero-badge"><i class="fas fa-camera"></i> <?= sanitize($galeriHero['subtitle'] ?? 'Fotoğraflar') ?></span>
            <h1><?= sanitize($galeriHero['title'] ?? 'Galeri') ?></h1>
            <p><?= sanitize($galeriHero['content'] ?? 'Adrasan\'ın turkuaz sularında yaşadığımız anların fotoğrafları') ?></p>
        </div>
    </div>
</section>

<div class="gallery-page">
    <div class="container">

        <?php if (!empty($allImages)): ?>

        <!-- Filtreler -->
        <div class="gallery-filters">
            <button class="gallery-filter-btn active" data-filter="all">
                Tümü <span style="opacity:0.6;font-size:0.8em;">(<?= count($allImages) ?>)</span>
            </button>
            <?php foreach ($catLabels as $key => $label): ?>
                <?php if (isset($usedCats[$key])): ?>
                <?php $cnt = count(array_filter($allImages, fn($i) => $i['category'] === $key)); ?>
                <button class="gallery-filter-btn" data-filter="<?= $key ?>">
                    <?= $label ?> <span style="opacity:0.6;font-size:0.8em;">(<?= $cnt ?>)</span>
                </button>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Grid -->
        <div class="gallery-grid" id="galleryGrid">
            <?php foreach ($allImages as $i => $img): ?>
            <div class="gallery-grid-item"
                 data-category="<?= sanitize($img['category']) ?>"
                 data-index="<?= $i ?>"
                 onclick="openLightbox(<?= $i ?>)">
                <img src="<?= SITE_URL ?>/assets/uploads/<?= sanitize($img['image']) ?>"
                     alt="<?= sanitize($img['title'] ?: 'Adrasan Celal Kaptan') ?>"
                     loading="lazy">
                <div class="gallery-item-overlay"><i class="fas fa-expand"></i></div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php else: ?>
        <div class="gallery-empty">
            <i class="fas fa-camera"></i>
            <h3>Henüz fotoğraf eklenmemiş</h3>
            <p>Yakında burada güzel anlar yer alacak.</p>
        </div>
        <?php endif; ?>

    </div>
</div>

<!-- Lightbox -->
<div id="lightbox" role="dialog">
    <button id="lightbox-close" onclick="closeLightbox()"><i class="fas fa-times"></i></button>
    <button id="lightbox-prev" onclick="shiftLightbox(-1)"><i class="fas fa-chevron-left"></i></button>
    <img id="lightbox-img" src="" alt="">
    <button id="lightbox-next" onclick="shiftLightbox(1)"><i class="fas fa-chevron-right"></i></button>
    <div id="lightbox-caption"></div>
    <div id="lightbox-counter"></div>
</div>

<script>
const allImages = <?= json_encode(array_map(fn($img) => [
    'src'      => SITE_URL . '/assets/uploads/' . $img['image'],
    'title'    => $img['title'] ?: '',
    'category' => $img['category'],
], $allImages)) ?>;

let visibleIndexes = allImages.map((_, i) => i);
let currentPos = 0;

document.querySelectorAll('.gallery-filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.gallery-filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const filter = btn.dataset.filter;
        visibleIndexes = [];
        document.querySelectorAll('.gallery-grid-item').forEach(item => {
            const show = filter === 'all' || item.dataset.category === filter;
            item.style.display = show ? 'block' : 'none';
            if (show) visibleIndexes.push(parseInt(item.dataset.index));
        });
    });
});

function openLightbox(idx) {
    const pos = visibleIndexes.indexOf(idx);
    currentPos = pos >= 0 ? pos : 0;
    renderLightbox();
    document.getElementById('lightbox').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function renderLightbox() {
    const img = allImages[visibleIndexes[currentPos]];
    const el  = document.getElementById('lightbox-img');
    el.style.opacity = '0';
    setTimeout(() => {
        el.src = img.src; el.alt = img.title;
        document.getElementById('lightbox-caption').textContent = img.title;
        document.getElementById('lightbox-counter').textContent = (currentPos + 1) + ' / ' + visibleIndexes.length;
        el.style.opacity = '1';
    }, 150);
}

function shiftLightbox(dir) {
    currentPos = (currentPos + dir + visibleIndexes.length) % visibleIndexes.length;
    renderLightbox();
}

function closeLightbox() {
    document.getElementById('lightbox').classList.remove('open');
    document.body.style.overflow = '';
}

document.getElementById('lightbox').addEventListener('click', e => {
    if (e.target === document.getElementById('lightbox')) closeLightbox();
});

document.addEventListener('keydown', e => {
    if (!document.getElementById('lightbox').classList.contains('open')) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') shiftLightbox(-1);
    if (e.key === 'ArrowRight') shiftLightbox(1);
});
</script>

<?php include 'includes/footer.php'; ?>
