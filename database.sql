-- ============================================
-- ADRASAN CELAL KAPTAN - Veritabani Semasi
-- Guncellenmis versiyon - Destinasyonlar eklendi
-- ============================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Veritabani olustur
CREATE DATABASE IF NOT EXISTS adrasan_celal_kaptan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE adrasan_celal_kaptan;

-- Admin tablosu
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Varsayilan admin (sifre: admin123)
INSERT INTO admins (username, password) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE username=username;

-- Turlar tablosu
CREATE TABLE IF NOT EXISTS tours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    short_desc TEXT,
    full_desc TEXT,
    price VARCHAR(100),
    duration VARCHAR(100),
    route TEXT,
    `includes` TEXT,
    image VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Destinasyonlar (Koylar ve Plajlar) tablosu
CREATE TABLE IF NOT EXISTS destinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    subtitle VARCHAR(255),
    short_desc TEXT,
    full_desc TEXT,
    history_text TEXT,
    features TEXT,
    faq_json TEXT,
    image VARCHAR(255),
    gallery_images TEXT,
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords TEXT,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Varsayilan destinasyonlar
INSERT INTO destinations (title, slug, subtitle, short_desc, full_desc, history_text, features, faq_json, sort_order) VALUES
('Suluada Tekne Turu', 'suluada-tekne-turu', 'Akdeniz''in Gizli Cenneti',
'Suluada Tekne Turu ile Adrasan''in essiz guzelliklerini kesfedin. Kristal berrakliginda sulari, muhtesem koylari ve keyifli molalari ile unutulmaz bir gun sizi bekliyor.',
'Suluada, Adrasan yakinlarinda bulunan kucuk bir ada olup turkuaz sulari ve beyaz kumlu plajlari ile Turkiye''nin Maldivleri olarak anilmaktadir. Tekne turumuzdagenis oturma alanlari ve guneslenme teraslarina sahip konforlu teknemizle bu essiz adayi ziyaret ediyoruz.\n\nTur boyunca tavuk ve balik secenekleri ile ogle yemegi sunulmaktadir. Alkolsuz icecekler tur ucretine dahildir. Mayo, gunes kremi, havlu ve sapka gibi kisisel esyalarinizi getirmeniz onerilmektedir.',
NULL,
'Muhtesem doga manzaralari|Yuzme ve dinlenme molalari|Guler yuzlu hizmet anlayisi|Ogle yemegi dahil',
'[{"q":"Yuzme guvenli mi?","a":"Evet, genel olarak guvenlidir. Can yelekleri teknede mevcuttur."},{"q":"Yemek dahil mi?","a":"Evet, ogle yemegi ve alkolsuz icecekler tur ucretine dahildir."},{"q":"Ne getirmeliyim?","a":"Mayo, gunes kremi, havlu ve sapka getirmenizi oneririz."}]',
1),

('Suluada Maldivler Plaji', 'suluada-maldivler-plaji', 'Akdeniz''in Gizli Cenneti',
'Suluada Maldivler Plaji, turkuaz sulari ve beyaz kumlu sahilleri ile Turkiye''nin en guzel plajlarindan biridir.',
'Suluada, Adrasan yakinlarinda Antalya''da bulunan kucuk bir ada olup turkuaz sulari ve beyaz kumlu plajlari ile unludur. Turkiye''nin Maldivleri lakabi ile anilmaktadir.\n\nAdadaki beyaz kum, kirectasi olusumlarindan kaynaklanmaktadir. Kristal berrakligindaki turkuaz sulari, deniz kaplumbagalari ve balik turleri barindan deniz yasami, doga temas ile faydalari olan dogal tatli su kaynagi ve kesfedilmeyi bekleyen kayalik olusumlar ve magaralar bulunmaktadir.\n\nAdaya sadece tekne turlari ile ulasim saglanmaktadir. Gunluk turlar sabah saatlerinde Adrasan''dan kalkmaktadir. Lokasyon tamamen dogal olup restoran, kafe veya herhangi bir altyapi bulunmamaktadir.',
NULL,
'Beyaz kumlu sahil|Turkuaz berrak sular|Deniz kaplumbagalari|Dogal tatli su kaynagi|Kayalik olusumlar ve magaralar',
'[{"q":"Yuzme guvenli mi?","a":"Evet, sakin sulari ve berrak goruntusu ile guvenlidir. Yuzme bilmeyenler icin can yelegi bulunmaktadir."},{"q":"Adada tesis var mi?","a":"Hayir, ada tamamen dogaldir. Restoran veya kafe bulunmamaktadir."},{"q":"Nasil ulasabilirim?","a":"Adaya sadece Adrasan''dan kalkis yapan tekne turlari ile ulasilabilir."}]',
2),

('Akseki Koyu', 'suluada-akseki-koyu', 'Doganin Sessiz Kucagi',
'Akseki Koyu, Adrasan''in gizli cennetlerinden biri olup huzur ve dogal guzellik bir arada sunmaktadir.',
'Akseki Koyu, yemyesil bitki ortusu, kristal berrakligindaki sulari ve sakin atmosferi ile ziyaretcilerine unutulmaz deneyimler sunmaktadir. Sehir hayatinin gurultusunden kacipmak ve dogayla bas basa kalmak isteyenler icin mukemmel bir destinasyondur.\n\nKristal berrakligindaki sularda yuzme, kalabaliktan uzak huzurlu doga deneyimi, yemyesil bitki ortusu ile cevrili muhtesem manzaralar ve zengin sualti yasami ile snorkeling imkani sizi bekliyor.',
'Likya medeniyeti doneminde kucuk bir ticaret noktasi olarak kullanilan koy, adini bolgedeki acik renkli kayalarin gunes isigini yansitmasindan almaktadir. Dogal magaralari antik caglardan beri siginak olarak kullanilmis ve korsanlardan korunma amaci gütmüstür.',
'Kristal berrak sular|Huzurlu atmosfer|Muhtesem manzaralar|Snorkeling imkani',
'[{"q":"Yuzme guvenli mi?","a":"Evet, genel olarak guvenlidir. Can yelegi onerilir."},{"q":"Tesis var mi?","a":"Hayir, tamamen dogal bir alandir."},{"q":"Snorkeling yapilabilir mi?","a":"Evet, berrak sulari ve zengin sualti yasami ile snorkeling icin mukemmeldir."}]',
3),

('Akvaryum Koyu', 'suluada-akvaryum-koyu', 'Berrak Sularin Buyusu',
'Akvaryum Koyu, adini cam gibi berrak ve turkuaz rengi sularindan almaktadir.',
'Akvaryum Koyu, o kadar berrak sulara sahiptir ki balik ve mercanlarisifir noktasindan gorebilirsiniz. Dalicilar ve snorkeling tutkunlari icin bir cennet niteligindedir.\n\nRengarenk deniz yasami ve mercan olusumlarini, kalabaliktan uzak huzurlu atmosferi, mukemmel yuzme kosullarini ve turkuaz rengi sularin buyusunu burada deneyimleyebilirsiniz.',
'Tarihte Likya korsanlari tarafindan bir siginak olarak kullanildigi bilinmektedir.',
'Cam gibi berrak sular|Rengarenk deniz yasami|Huzurlu atmosfer|Snorkeling cenneti',
'[{"q":"Yuzme guvenli mi?","a":"Evet, genel olarak guvenlidir."},{"q":"Tesis var mi?","a":"Hayir, tamamen dogal bir alandir."},{"q":"Snorkeling yapilabilir mi?","a":"Evet, berrak sulari sayesinde snorkeling icin idealdir."}]',
4),

('Amerikan Koyu', 'suluada-amerikan-koyu', 'Adrasan''in Sakin ve Gizemli Cenneti',
'Amerikan Koyu, Antalya''nin en populer koylarindan biri olup dogal guzelligi ve kristal berrak denizi ile ziyaretcileri buyulemektedir.',
'Amerikan Koyu, sehir hayatindan kacis ve huzurlu bir deniz keyfi arayanlar icin ideal bir destinasyondur. Turkuaz rengi denizi yuzme ve snorkeling icin uygundur, doga ile cevrili huzurlu atmosferi, kayalik yapilarinin olusturdugu muhtesem gun batimi manzaralari ve zengin sualti yasami kesif icin firsat sunmaktadir.\n\nKoya sadece tekne turu ile ulasilabilir olmasi, dogal yapisinin korunmasini saglamaktadir.',
'Koyun adi, II. Dunya Savasi sonrasinda bolgede kisa sureligine gorev yapan Amerikan Donanmasindan gelmektedir. Yerel anlatilara gore, bir donem Amerikan askeri personeli icin kucuk bir egitim ve dinlenme alani olarak hizmet vermistir.',
'Turkuaz deniz|Huzurlu atmosfer|Gun batimi manzarasi|Zengin sualti yasami|Sadece tekne ile ulasim',
'[{"q":"Nasil gidilir?","a":"Amerikan Koyu''na sadece Adrasan''dan kalkis yapan tekne turlari ile ulasilabilir."},{"q":"Yuzme guvenli mi?","a":"Evet, sakin ve berrak sulari ile guvenlidir."},{"q":"Snorkeling yapilabilir mi?","a":"Evet, zengin deniz yasami ile snorkeling icin idealdir."}]',
5),

('Hacivat Koyu', 'suluada-hacivat-koyu', 'Doganin Kucaginda Sakli Bir Cennet',
'Hacivat Koyu, Adrasan''in en ozel koylarindan biri olup essiz dogal guzelligi ve sakin atmosferi ile ziyaretcilerine huzurlu bir deneyim sunmaktadir.',
'Koyun adi, girisindeki Turk golge oyunu Karagoz''deki Hacivat karakterine benzeyen kaya olusumlarindan gelmektedir.\n\nTurkuaz kristal berrak sulari yuzme icin ideal, kendine ozgu Hacivat seklindeki kaya olusumlar, gurultuden uzak huzurlu ortam, muhtesem snorkeling firsatlari ve bozulmamis dogal peyzaj sizi beklemektedir.',
'Koyun girisindeki kaya olusumlarinin Turk golge oyunu Karagoz''deki Hacivat karakterine benzemesi nedeniyle bu ismi almistir.',
'Turkuaz berrak sular|Hacivat kaya olusumu|Huzurlu ortam|Snorkeling firsati|Dogal peyzaj',
'[{"q":"Nasil gidilir?","a":"Adrasan''dan kalkis yapan tekne turlari ile ulasilabilir."},{"q":"Yuzme guvenli mi?","a":"Evet, can yelegi onerilir."},{"q":"Snorkeling yapilabilir mi?","a":"Evet, berrak sulari ve zengin deniz yasami ile idealdir."}]',
6),

('Karatas Koyu', 'suluada-karatas-koyu', 'Adrasan''in Sakli Huzur Noktasi',
'Karatas Koyu, sakin atmosferi ve berrak denizi ile kesfedilmeyi bekleyen gizli bir cennettir.',
'Kristal berrakligindaki turkuaz ve mavi sulari yuzme icin ideal, turist kalabalagindan uzak huzurlu atmosfer, etkileyici cevredeki kayalik olusumlar ve snorkeling ile dalisa uygun zengin deniz yasami burada sizi beklemektedir.\n\nKoya Adrasan''dan kalkan tekne turlari ile ulasilabilir ve bu sayede dogal bakir yapisi korunmaktadir.',
'Koy, adini girisindeki buyuk siyah kayalik olusumlardan almaktadir. Yerel inanisa gore, mitolojik donemlerdedenizcilik kurban torenlerinin yapildigi bir yer olmus ve kucuk balikci teknelerine siginak gorevi gormüstür.',
'Turkuaz berrak sular|Huzurlu atmosfer|Etkileyici kayaliklar|Zengin deniz yasami',
'[{"q":"Nasil gidilir?","a":"Adrasan''dan kalkan tekne turlari ile ulasilabilir."},{"q":"Yuzme guvenli mi?","a":"Evet, sakin sulari ile guvenlidir."},{"q":"Ne yapilabilir?","a":"Yuzme, guneslenme, snorkeling ve dalis yapilabilir."}]',
7);

-- Galeri tablosu
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    image VARCHAR(255) NOT NULL,
    category VARCHAR(100) DEFAULT 'genel',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sayfa icerikleri
CREATE TABLE IF NOT EXISTS page_contents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(255),
    subtitle VARCHAR(255),
    content TEXT,
    image VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Varsayilan sayfa icerikleri
INSERT INTO page_contents (section_key, title, subtitle, content, sort_order) VALUES
('hero', 'Adrasan Tekne Turu ile Unutulmaz Bir Deniz Deneyimi', 'Adrasan''in En Iyi Tekne Turu', 'Kristal berrakligindaki turkuaz sularda, essiz koylar ve Suluada''nin muhtesem guzelliklerini kesfedin. Celal Kaptan ile guvenli, konforlu ve keyifli bir tekne turu deneyimi sizi bekliyor.', 1),
('about', 'Denizle Ic Ice Bir Hayat', 'Hakkimizda', 'Celal Kaptan, denizle ic ice gecen bir hayatin uzmanlarindan olusan bir ekip tarafindan kurulmustur. Denizcilik sektorundeki uzun yillara dayanan deneyimimizi, misafirperverlik anlayisimizla birlestirerek sizlere guvenli, konforlu ve eglenceli deneyimler sunuyoruz.', 2),
('about_vision', 'Vizyonumuz', NULL, 'Tutkulu deniz severleri surdurulebilir turizmle bulusturarak sektorde oncu bir marka olmak.', 3),
('about_mission', 'Misyonumuz', NULL, 'Guvenli, konforlu ve cevre dostu deniz yolculuklari ile misafir beklentilerini asmak, insanlarin denizle bagini guclendirmek ve kalici anilar biriktirmelerini saglamak.', 4),
('why_us', 'Neden Celal Kaptan?', 'Bizi Tercih Etmeniz Icin Nedenler', 'Deneyimli ekibimiz, ozenle secilmis rotalarimiz ve misafir memnuniyetine verdigi oncelik ile unutulmaz anlar yasatiyoruz.', 5),
('cta', 'Unutulmaz Bir Tekne Turu Icin Hemen Rezervasyon Yapin', NULL, 'Adrasan''in turkuaz sularinda hayalinizi gerceklestirin. Erken rezervasyon firsatlarini kacirmayin!', 6),
('services_hero', 'Hizmetlerimiz', 'Turlarimiz ve Destinasyonlarimiz', 'Adrasan''in essiz koylarini ve Suluada''nin turkuaz sularini kesfedin. Her tur ozenle planlanmis rotalar ve profesyonel hizmet ile sizleri bekliyor.', 7),
('contact_hero', 'Iletisim', 'Bize Ulasin', 'Sorulariniz, rezervasyon ve bilgi almak icin bizimle iletisime gecin. En kisa surede donüs yapmaktayiz.', 8)
ON DUPLICATE KEY UPDATE section_key=section_key;

-- Site ayarlari
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_label VARCHAR(255),
    setting_group VARCHAR(50) DEFAULT 'general'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Varsayilan ayarlar
INSERT INTO site_settings (setting_key, setting_value, setting_label, setting_group) VALUES
('site_title', 'Adrasan Celal Kaptan', 'Site Basligi', 'general'),
('site_description', 'Adrasan tekne turu, Suluada tekne turu ve koylar turlari ile sizlere hizmet ediyoruz.', 'Site Aciklamasi', 'general'),
('working_hours', '09:30 - 17:00', 'Calisma Saatleri', 'general'),
('phone', '0543 717 33 78', 'Telefon', 'contact'),
('phone2', '', 'Telefon 2', 'contact'),
('email', 'adrasancelalkaptan@gmail.com', 'E-posta', 'contact'),
('address', 'Adrasan, Kumluca, Antalya', 'Adres', 'contact'),
('whatsapp', '905437173378', 'WhatsApp Numarasi', 'contact'),
('instagram', '', 'Instagram URL', 'social'),
('facebook', '', 'Facebook URL', 'social'),
('youtube', '', 'YouTube URL', 'social'),
('tiktok', '', 'TikTok URL', 'social'),
('google_analytics', '', 'Google Analytics ID', 'seo'),
('google_maps', '', 'Google Maps Embed Kodu', 'seo')
ON DUPLICATE KEY UPDATE setting_key=setting_key;

-- SEO ayarlari
CREATE TABLE IF NOT EXISTS seo_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_slug VARCHAR(100) NOT NULL UNIQUE,
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords TEXT,
    robots VARCHAR(50) DEFAULT 'index, follow',
    canonical_url VARCHAR(500),
    og_image VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Varsayilan SEO ayarlari
INSERT INTO seo_settings (page_slug, meta_title, meta_description, meta_keywords) VALUES
('anasayfa', 'Adrasan Celal Kaptan | Tekne Turu - Suluada - Koylar Turu', 'Adrasan tekne turu, Suluada tekne turu ve koylar turlari ile unutulmaz bir deniz deneyimi. Celal Kaptan ile guvenli ve konforlu tekne turlari.', 'adrasan tekne turu, suluada tekne turu, koylar turu, adrasan, celal kaptan, tekne turu antalya'),
('hizmetlerimiz', 'Hizmetlerimiz | Adrasan Celal Kaptan Tekne Turlari', 'Ozel tekne turu, Suluada tekne turu ve koylar tekne turu. Adrasan''in en guzel koylarini kesfedin.', 'adrasan hizmetler, tekne turu cesitleri, suluada turu, ozel tekne, koylar turu'),
('hakkimizda', 'Hakkimizda | Adrasan Celal Kaptan', 'Celal Kaptan, denizle ic ice gecen bir hayatin uzmanlarindan olusan ekibiyle Adrasan''da tekne turu hizmeti vermektedir.', 'celal kaptan hakkinda, adrasan tekne turu firma, deneyimli ekip'),
('iletisim', 'Iletisim | Adrasan Celal Kaptan', 'Adrasan Celal Kaptan tekne turu icin iletisim bilgileri, rezervasyon ve sorulariniz.', 'adrasan iletisim, tekne turu rezervasyon, celal kaptan telefon')
ON DUPLICATE KEY UPDATE page_slug=page_slug;

-- Yorumlar tablosu
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    rating TINYINT DEFAULT 5,
    comment TEXT NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Varsayilan yorumlar
INSERT INTO testimonials (name, rating, comment) VALUES
('Halil Ibrahim', 5, 'Profesyonel ve guler yuzlu ekibi, harika rotalari ve konforlu tekneleri ile gercekten unutulmaz bir deneyim sunuyor. Kesinlikle tavsiye ederim!'),
('Kadircan', 5, 'Bilgili kaptan ve ilgili personel ile essiz bir deneyim. Koylar muhtesemdi ve hizmet kalitesi cok yuksekti.'),
('Mert', 5, 'Harika bir atmosferde gecen gun, guler yuzlu personel ve mukemmel hizmetle taclandi. Tekrar gelmek istiyoruz!')
ON DUPLICATE KEY UPDATE name=name;

-- Iletisim mesajlari
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(50) NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
