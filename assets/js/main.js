// ============================================
// ADRASAN CELAL KAPTAN - Ana JavaScript
// ============================================

document.addEventListener('DOMContentLoaded', function() {

    // --- Preloader ---
    window.addEventListener('load', function() {
        const preloader = document.getElementById('preloader');
        if (preloader) {
            setTimeout(() => preloader.classList.add('loaded'), 500);
            setTimeout(() => preloader.remove(), 1000);
        }
    });

    // --- Navbar Scroll ---
    const navbar = document.getElementById('navbar');
    const backToTop = document.getElementById('backToTop');

    // Ic sayfalarda navbar her zaman scrolled
    if (navbar && navbar.classList.contains('scrolled')) {
        navbar.dataset.fixed = 'true';
    }

    window.addEventListener('scroll', function() {
        const scrollY = window.scrollY;

        if (navbar) {
            if (scrollY > 50) {
                navbar.classList.add('scrolled');
            } else if (!navbar.dataset.fixed) {
                navbar.classList.remove('scrolled');
            }
        }

        if (backToTop) {
            if (scrollY > 400) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        }

        updateActiveNav();
    });

    // Back to top click
    if (backToTop) {
        backToTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // --- Mobile Menu ---
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('navMenu');

    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            this.classList.toggle('active');
            navMenu.classList.toggle('active');
            document.body.style.overflow = navMenu.classList.contains('active') ? 'hidden' : '';
        });

        navMenu.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
                document.body.style.overflow = '';
            });
        });

        document.addEventListener('click', function(e) {
            if (!navMenu.contains(e.target) && !hamburger.contains(e.target) && navMenu.classList.contains('active')) {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

    // --- Dropdown Menu (Mobile) ---
    const dropdowns = document.querySelectorAll('.nav-dropdown');
    dropdowns.forEach(dropdown => {
        const link = dropdown.querySelector('.nav-link');
        if (link && window.innerWidth <= 768) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                dropdown.classList.toggle('open');
            });
        }
    });

    // --- Active Nav Link ---
    function updateActiveNav() {
        const sections = document.querySelectorAll('section[id]');
        let current = '';

        sections.forEach(section => {
            const top = section.offsetTop - 100;
            if (window.scrollY >= top) {
                current = section.getAttribute('id');
            }
        });

        document.querySelectorAll('.nav-menu > .nav-link').forEach(link => {
            const href = link.getAttribute('href');
            if (href && href.startsWith('#') && href === '#' + current) {
                link.classList.add('active');
            }
        });
    }

    // --- Counter Animation ---
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-number[data-count]');
        counters.forEach(counter => {
            if (counter.dataset.animated) return;

            const target = parseInt(counter.dataset.count);
            const duration = 2000;
            const step = Math.ceil(target / (duration / 16));
            let current = 0;

            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = current.toLocaleString('tr-TR');
            }, 16);

            counter.dataset.animated = 'true';
        });
    }

    // --- Scroll Animations ---
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('aos-animate');
                if (entry.target.closest('.hero-stats') || entry.target.querySelector('.stat-number')) {
                    animateCounters();
                }
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('[data-aos]').forEach(el => observer.observe(el));

    const heroStats = document.querySelector('.hero-stats');
    if (heroStats) observer.observe(heroStats);

    const heroSection = document.querySelector('.hero');
    if (heroSection) {
        const heroObs = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) animateCounters();
            });
        }, { threshold: 0.3 });
        heroObs.observe(heroSection);
    }

    // --- Hero Particles ---
    function createParticles() {
        const container = document.getElementById('particles');
        if (!container) return;

        for (let i = 0; i < 30; i++) {
            const particle = document.createElement('div');
            particle.style.cssText = `
                position: absolute;
                width: ${Math.random() * 4 + 2}px;
                height: ${Math.random() * 4 + 2}px;
                background: rgba(255,255,255,${Math.random() * 0.3 + 0.1});
                border-radius: 50%;
                left: ${Math.random() * 100}%;
                top: ${Math.random() * 100}%;
                animation: particleFloat ${Math.random() * 10 + 10}s linear infinite;
                animation-delay: -${Math.random() * 10}s;
            `;
            container.appendChild(particle);
        }

        if (!document.getElementById('particleStyle')) {
            const style = document.createElement('style');
            style.id = 'particleStyle';
            style.textContent = `
                @keyframes particleFloat {
                    0% { transform: translate(0, 0) rotate(0deg); opacity: 0; }
                    10% { opacity: 1; }
                    90% { opacity: 1; }
                    100% { transform: translate(${Math.random() > 0.5 ? '' : '-'}100px, -200px) rotate(360deg); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }
    }

    createParticles();

    // --- FAQ Accordion ---
    document.querySelectorAll('.faq-item').forEach(item => {
        const question = item.querySelector('.faq-question');
        if (question) {
            question.addEventListener('click', function() {
                document.querySelectorAll('.faq-item').forEach(other => {
                    if (other !== item) other.classList.remove('open');
                });
                item.classList.toggle('open');
            });
        }
    });

    // --- Contact Form ---
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const response = document.getElementById('formResponse');
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Gönderiliyor...';
            submitBtn.disabled = true;

            fetch(this.action || 'api/contact.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                response.className = 'form-response ' + (data.success ? 'success' : 'error');
                response.textContent = data.message;
                response.style.display = 'block';

                if (data.success) {
                    contactForm.reset();
                    setTimeout(() => { response.style.display = 'none'; }, 5000);
                }
            })
            .catch(() => {
                response.className = 'form-response error';
                response.textContent = 'Bir hata oluştu. Lütfen tekrar deneyin.';
                response.style.display = 'block';
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // --- Smooth Scroll ---
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

});
