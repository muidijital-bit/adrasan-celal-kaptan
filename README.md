# Adrasan Celal Kaptan — Web Sitesi

PHP + MySQL ile geliştirilmiş tekne turu tanıtım sitesi.

---

## Kurulum (Yeni Geliştirici)

### 1. Gereksinimler
- [XAMPP](https://www.apachefriends.org/) (PHP 8.0+ ve MySQL içerir)
- Git

### 2. Projeyi Klonla
```bash
cd /Applications/XAMPP/xamppfiles/htdocs       # macOS
# veya
cd C:/xampp/htdocs                              # Windows

git clone https://gitlab.com/muimedya-group/adrasan-celal-kaptan.git
```

### 3. Veritabanını Oluştur
1. XAMPP'ı başlat (Apache + MySQL)
2. Tarayıcıda `http://localhost/phpmyadmin` aç
3. Sol menüden **Yeni** → Veritabanı adı: `adrasan_celal_kaptan` → Oluştur
4. `database.sql` dosyasını import et:
   - phpMyAdmin'de `adrasan_celal_kaptan` seçili iken **İçe Aktar** sekmesine tıkla
   - `database.sql` dosyasını seç → Çalıştır

### 4. Config Dosyasını Oluştur
```bash
cp includes/config.example.php includes/config.php
```
`includes/config.php` dosyasını aç ve gerekirse düzenle:
- `SITE_URL` → Kendi localhost yolunu yaz (genellikle değiştirmeye gerek yok)
- `DB_USER` / `DB_PASS` → XAMPP için genellikle `root` / boş bırak

### 5. Görselleri Ekle
Senden alacağın `uploads.zip` dosyasını aç:
```
unzip uploads.zip -d adrasan-celal-kaptan/assets/uploads/
```
Veya zip içeriğini `assets/uploads/` klasörüne kopyala.

### 6. Siteyi Aç
Tarayıcıda: `http://localhost/adrasan-celal-kaptan`

Admin paneli: `http://localhost/adrasan-celal-kaptan/admin`

---

## Proje Yapısı

```
adrasan-celal-kaptan/
├── admin/              Admin paneli
├── api/                Form API (iletişim formu)
├── assets/
│   ├── css/            Stil dosyaları
│   ├── js/             JavaScript
│   └── uploads/        Görseller (git'e dahil değil)
├── includes/
│   ├── config.php      Veritabanı ayarları (git'e dahil değil)
│   ├── config.example.php  Config şablonu
│   ├── header.php
│   └── footer.php
├── index.php           Ana sayfa
├── destinasyon.php     Koy/destinasyon sayfası (şablon)
├── hizmetlerimiz.php
├── hakkimizda.php
├── iletisim.php
├── galeri.php
├── database.sql        Veritabanı yapısı + örnek veriler
└── .htaccess           URL yönlendirme kuralları
```

---

## Önemli Notlar

- `includes/config.php` — şifre içerdiği için git'e dahil edilmez
- `assets/uploads/` — görseller git'e dahil edilmez, ayrıca paylaşılır
- Canlı site: https://adrasancelalkaptan.com.tr
