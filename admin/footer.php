        </div><!-- .content -->
    </div><!-- .main-content -->
</div><!-- .admin-layout -->
<script>
// Page title
const titles = {
    'index': 'Dashboard',
    'contents': 'Sayfa İçerikleri',
    'tours': 'Tur Yönetimi',
    'destinations': 'Destinasyon Yönetimi',
    'gallery': 'Galeri Yönetimi',
    'testimonials': 'Yorum Yönetimi',
    'messages': 'Mesajlar',
    'seo': 'SEO Ayarları',
    'settings': 'Site Ayarları',
    'password': 'Şifre Değiştir'
};
const page = '<?= $currentPage ?>';
if (titles[page]) document.getElementById('pageTitle').textContent = titles[page];
</script>
</body>
</html>
