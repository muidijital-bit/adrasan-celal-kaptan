    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="<?= SITE_URL ?>/" class="logo">
                        <img src="<?= SITE_URL ?>/assets/images/logo.png" alt="Celal Kaptan Logo" class="logo-image">
                    </a>
                    <p>Adrasan'da 15+ yıldır güvenilir tekne turu hizmeti sunuyoruz. Suluada, Akseki, Akvaryum ve Amerikan koylarında tam gün turlarımızla yaz tatilini unutulmaz kılıyoruz.</p>
                    <div class="footer-socials">
                        <?php if (!empty($settings['instagram'])): ?>
                        <a href="<?= sanitize($settings['instagram']) ?>" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['facebook'])): ?>
                        <a href="<?= sanitize($settings['facebook']) ?>" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($settings['youtube'])): ?>
                        <a href="<?= sanitize($settings['youtube']) ?>" target="_blank" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="footer-links">
                    <h4>Hızlı Linkler</h4>
                    <a href="<?= SITE_URL ?>/">Ana Sayfa</a>
                    <a href="<?= SITE_URL ?>/hizmetlerimiz">Hizmetlerimiz</a>
                    <a href="<?= SITE_URL ?>/galeri">Galeri</a>
                    <a href="<?= SITE_URL ?>/hakkimizda">Hakkımızda</a>
                    <a href="<?= SITE_URL ?>/iletisim">İletişim</a>
                </div>
                <div class="footer-links">
                    <h4>Destinasyonlar</h4>
                    <?php
                    $footerDests = getDestinations();
                    foreach (array_slice($footerDests, 0, 5) as $fd):
                    ?>
                    <a href="<?= SITE_URL ?>/destinasyon/<?= sanitize($fd['slug']) ?>"><?= sanitize($fd['title']) ?></a>
                    <?php endforeach; ?>
                </div>
                <div class="footer-contact">
                    <h4>İletişim</h4>
                    <p><i class="fas fa-phone"></i> <a href="tel:<?= sanitize($settings['phone']) ?>"><?= sanitize($settings['phone']) ?></a></p>
                    <p><i class="fas fa-envelope"></i> <a href="mailto:<?= sanitize($settings['email']) ?>"><?= sanitize($settings['email']) ?></a></p>
                    <p><i class="fas fa-map-marker-alt"></i> <?= sanitize($settings['address']) ?></p>
                    <p><i class="fas fa-clock"></i> <?= sanitize($settings['working_hours']) ?></p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Adrasan Celal Kaptan. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/<?= sanitize($settings['whatsapp']) ?>" target="_blank" class="whatsapp-float" title="WhatsApp ile ulaşın">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Back to Top -->
    <button class="back-to-top" id="backToTop" aria-label="Yukarı">
        <i class="fas fa-chevron-up"></i>
    </button>

    <script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
