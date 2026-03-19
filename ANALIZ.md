# Sipariş Masanda — Kapsamlı Uygulama Analizi
> Tarih: Mart 2026 | Analiz: Tam Kaynak Kodu İncelemesi

---

## İçindekiler

1. [Uygulama Nasıl Çalışır](#1-uygulama-nasıl-çalışır)
2. [Genel Skor](#2-genel-skor)
3. [Güçlü Noktalar](#3-güçlü-noktalar)
4. [Buglar ve Teknik Sorunlar](#4-buglar-ve-teknik-sorunlar)
5. [İyileştirilmesi Gerekenler](#5-iyileştirilmesi-gerekenler)
6. [SEO Analizi](#6-seo-analizi)
7. [Rakip Analizi ve Eksik Özellikler](#7-rakip-analizi-ve-eksik-özellikler)
8. [Global Expansion Yol Haritası](#8-global-expansion-yol-haritası)

---

## 1. Uygulama Nasıl Çalışır

### Genel Mimari

**Sipariş Masanda**, restoranlar ve kafeler için SaaS tabanlı dijital QR menü platformudur. Laravel 12 üzerine kurulu, çok kiracılı (multi-tenant) bir web uygulamasıdır.

```
[Restoran Sahibi] → Kayıt → Tenant oluşturulur → Dashboard
[Müşteri]         → QR Tara → /menu/{tenantId} → Menüyü görür
```

### Tenant (Kiracı) Modeli

Her kayıt olan restoran bir `tenant` oluşturur. Tüm veriler `tenant_id` ile izole edilir. Middleware katmanı oturum bazlı tenant bağlamını yönetir.

```
tenants → users (owner/admin/personel)
       → categories → products
       → reviews
       → sliders (premium)
       → events (premium)
       → reservation_zones → reservation_tables → reservations (premium)
       → menu_settings (premium)
       → qr_visits
       → support_tickets
```

### Paket Modeli (Freemium)

| Özellik | Basic (₺0/ay) | Premium (₺199/ay) |
|---------|--------------|-------------------|
| Sınırsız Kategori & Ürün | ✅ | ✅ |
| QR Kod Oluşturma | ✅ | ✅ |
| Fotoğraflı Menü | ✅ | ✅ |
| Müşteri Değerlendirmeleri | ✅ | ✅ |
| Çoklu Kullanıcı (owner/admin/personel) | ✅ | ✅ |
| Sosyal Medya Entegrasyonu | ✅ | ✅ |
| QR Ziyaret İstatistikleri | ✅ | ✅ |
| Destek Talebi | ✅ | ✅ |
| Slider/Banner Yönetimi | ❌ | ✅ |
| Etkinlik Yönetimi | ❌ | ✅ |
| Online Rezervasyon Sistemi | ❌ | ✅ |
| Menü Tasarımı (Layout & Renk) | ❌ | ✅ |
| Öncelikli Destek | ❌ | ✅ |

### Kullanıcı Akışı

1. **Kayıt**: Restoran adı, isim, e-posta, şifre → `tenants` + `users(owner)` oluşturulur → otomatik login
2. **Dashboard**: QR link, istatistikler (ziyaret, değerlendirme, kategori, ürün sayısı), kurulum rehberi
3. **Menü Yönetimi**: Kategori → Alt Kategori → Ürün (fotoğraf, fiyat, açıklama)
4. **QR Kod**: `/menu/qr` → SVG format, yüksek çözünürlük, indirilebilir
5. **Müşteri Deneyimi**: `/menu/{id}` → Kategori filtreleme, ürün detayı, değerlendirme bırakma, rezervasyon (premium)

### Teknoloji Stack

| Katman | Teknoloji |
|--------|-----------|
| Backend | Laravel 12 / PHP 8.2+ |
| Veritabanı | MySQL |
| Frontend CSS | Bootstrap 5.3.3 + Custom |
| Frontend JS | jQuery 3.7.1 + Vanilla JS |
| Build Tool | Vite 6 + TailwindCSS 4 |
| QR Kütüphane | SimpleSoftwareIO/simple-qrcode |
| İkon | Bootstrap Icons 1.11.3 |
| Tablo | DataTables 1.13.8 |

---

## 2. Genel Skor

### Özet Puan Tablosu

| Kategori | Puan | Açıklama |
|----------|------|----------|
| **Kod Kalitesi** | 6.5/10 | Laravel standartları kısmen uygulanmış, Eloquent yerine Query Builder ağırlıklı |
| **Güvenlik** | 5.5/10 | APP_DEBUG=true prod'da, şifre sıfırlama yok, 2FA yok |
| **Performans** | 6/10 | Cache yok, N+1 riski var, CDN yok |
| **SEO** | 6.5/10 | Schema markup iyi, ama menü URL'leri SEO-hostile |
| **UX/UI** | 6/10 | Fonksiyonel ama modern değil, mobile experience zayıf |
| **Özellik Genişliği** | 5.5/10 | Rakipler çok geride bırakıyor |
| **Global Hazırlık** | 2.5/10 | Tamamen TR-odaklı, global için hazır değil |
| **Ölçeklenebilirlik** | 5/10 | Monolitik, session-based tenant, API yok |
| **İş Modeli** | 6/10 | Freemium mantıklı ama ödeme altyapısı yok |

### **Genel Ortalama: 5.6 / 10**

> MVP seviyesinde çalışan, Türkiye pazarında işe yarayan bir ürün. Global için köklü değişiklikler gerekiyor.

---

## 3. Güçlü Noktalar

### Teknik Güçlü Yönler

- **Sağlam Multi-Tenant Mimari**: `tenant_id` izolasyonu middleware seviyesinde tutarlı uygulanmış. Veri sızıntısı riski düşük.
- **Rol Tabanlı Yetkilendirme**: `owner / admin / personel / developer` rolleri middleware ile korunuyor. Atomic atama yapısı temiz.
- **Transaction Kullanımı**: Kayıt, rezervasyon gibi kritik operasyonlar `DB::transaction()` ile sarılmış. Veri tutarlılığı korunuyor.
- **IP Tabanlı Tekrar Koruması**: Değerlendirme ve QR ziyaret takibinde günlük IP filtresi var. Sahte veri oluşturma engelleniyor.
- **Rezervasyon Çakışma Kontrolü**: Aynı masa/tarih/saat çakışması sorgu seviyesinde önleniyor.
- **Developer İmpersonation**: Destek için tenant kimliğine bürünme özelliği mevcut.
- **Çift Dil Desteği (TR/EN)**: Tüm sayfalar ternary operatörlerle iki dil destekliyor. Route sistemi `/en/` prefix'iyle ayrılmış.

### Ürün Güçlü Yönleri

- **Freemium Model**: Ücretsiz plan çekici ve gerçekten değerli. Müşteri edinme maliyeti düşük.
- **3 Adım Onboarding**: Kayıt → Menü Ekle → QR Paylaş akışı çok basit. Teknik bilgi gerektirmiyor.
- **Premium Gate**: Kilitli özelliklere tıklandığında düzgün yönlendirme var. Upsell akışı açık.
- **Gerçek Zamanlı Menü**: Değişiklikler anında yayına giriyor, yeniden QR basmak gerekmiyor.
- **Ürün Bazlı QR**: Her ürün için ayrı QR kod oluşturulabiliyor. Masa üstü tanıtımlar için iyi.
- **Sitemap.xml Otomasyonu**: Blog yazıları dahil sitemap dinamik oluşturuluyor.

### Pazarlama Güçlü Yönleri

- **Schema Markup Kapsamı**: HowTo, FAQPage, BreadcrumbList, ItemList, Product+AggregateOffer — hepsi mevcut.
- **Blog Altyapısı**: SEO için developer tarafından yönetilen blog sistemi var.
- **Demo Menü**: `/demo` route'u potansiyel müşterilere canlı gösterim yapıyor.
- **WhatsApp Yönlendirme**: Landing sayfasında float widget var, müşteri iletişimi kolay.

---

## 4. Buglar ve Teknik Sorunlar

### 🔴 Kritik (Production'da Risk Oluşturuyor)

**BUG-01: APP_DEBUG=true Production Ortamında**
- Dosya: `.env`
- Sorun: `APP_DEBUG=true` iken PHP hata mesajları ve stack trace kullanıcıya gösteriliyor. Veritabanı yapısı, dosya yolları, env değerleri açığa çıkabilir.
- Çözüm: `APP_DEBUG=false` yapılmalı, hata sayfaları özelleştirilmeli.

**BUG-02: Şifre Sıfırlama Mekanizması Yok**
- Şifreyi unutan kullanıcı hesabına erişemez. `AuthController`'da sadece login ve register var.
- Çözüm: Laravel'in hazır `Password::sendResetLink()` sistemi entegre edilmeli.

**BUG-03: E-posta Doğrulama Yok**
- Sahte e-posta ile kayıt açık. Spam hesaplar oluşturulabilir.
- Çözüm: `MustVerifyEmail` implement edilmeli.

**BUG-04: Login'de Rate Limiting Yok**
- `AuthController@login` üzerinde throttle middleware yok. Brute force saldırısına açık.
- Çözüm: `Route::middleware('throttle:10,1')` eklenmeli.

**BUG-05: Premium Faturalama Altyapısı Görünür Değil**
- `package` alanı veritabanında `basic/premium/enterprise` olarak var ama ödeme alma sistemi (Stripe, iyzico vb.) kaynak kodda yok. Premium yükseltme manuel yapılıyor olabilir.
- Çözüm: Gerçek ödeme entegrasyonu olmadan freemium model sürdürülemez.

### 🟠 Önemli (UX ve Güvenlik Etkisi)

**BUG-06: Destek Talebi JSON Yapısı Kırılganlığı**
- `support_tickets.admin_reply` alanı JSON formatında konuşma tutturuyor. Bu yaklaşım arama, filtreleme ve ölçek açısından sorunlu. Eski format uyumluluğu için backward-compat kod yazılmış — bu teknik borç birikimi.
- Çözüm: Ayrı `support_messages` tablosu oluşturulmalı.

**BUG-07: Duplicate İkon Kullanımı (Ozellikler Sayfası)**
- Hem "Fotoğraflı Dijital Menü" hem "Slider ve Banner Yönetimi" kartında `bi bi-images` ikonu kullanılmış.
- Çözüm: Slider için `bi bi-layout-text-window` veya `bi bi-collection-play` kullanılmalı.

**BUG-08: WhatsApp Numarası Hard-Coded**
- `resources/views/layouts/public.blade.php` içinde `+905078928490` numarası sabit kodlanmış.
- Sorun: Her ortam değişikliğinde view dosyası editlemek gerekiyor. Global expansion'da değiştirilmesi unutulabilir.
- Çözüm: `.env` veya `config/app.php`'ye taşınmalı.

**BUG-09: APP_LOCALE=en ama Site Türkçe**
- `.env`'de `APP_LOCALE=en` yazıyor. Locale yönetimi session/cookie bazlı yapılmış ama default locale tutarsız.
- Sorun: İlk ziyarette dil algılama sıkıntısı yaşanabilir.

**BUG-10: Rezervasyon E-posta Bildirimi Yok**
- Müşteri rezervasyon yaptığında e-posta onayı gitmiyor. Restoran sahibine de e-posta gönderilmiyor. Sadece in-app notification var.
- Sorun: Müşteri deneyimi ciddi şekilde zayıflatıyor.

**BUG-11: Sipariş Sayfasında "Coming Soon" — Ama Premium Badge Gösteriliyor**
- Sidebar'da "Ordering" özelliği premium badge ile gösteriliyor ama içerik yok. Kullanıcı tıklayınca ne olduğu belirsiz.
- Çözüm: Ya gerçek içerik eklenmeli ya da "Yakında" badge'i ile devre dışı bırakılmalı.

**BUG-12: TailwindCSS 4 + Bootstrap 5 Çift Framework**
- `package.json`'da TailwindCSS 4 var ama gerçek stilleme Bootstrap 5 ile yapılmış. İki framework birlikte kullanımı CSS çakışmasına ve gereksiz bundle boyutuna yol açar.

**BUG-13: Rezervasyon Timezone Sorunu**
- `APP_TIMEZONE=UTC` ama restoranlar Türkiye'de çalışıyor (UTC+3). Rezervasyon tarih/saati UTC'de kayıtlı olduğunda 3 saat kayması yaşanabilir.

**BUG-14: Pricing Sayfasında Rezervasyon Özelliği Eksik**
- Premium plan özellik listesinde "Rezervasyon Sistemi" yazmıyor ama bu özellik premium kapsamında. Fiyat sayfası eksik bilgi veriyor.

### 🟡 Küçük (UX Sorunları)

**BUG-15: Kategori Seçimi Olmadan Ürün Eklenebiliyor mu?**
- Ürün oluştururken `category_id` zorunlu mu belirsiz. Validation incelenmeli.

**BUG-16: Müşteri Değerlendirmesinde İsim Opsiyonel ama Label'da Belirtilmemiyor**
- Kullanıcı arayüzünde hangi alanın zorunlu olduğu açık değil.

**BUG-17: Dashboard'da Kurulum Rehberi Kalıcı Gösterilebilir**
- Setup guide tamamlandıktan sonra kaybolması için sadece kategori ve ürün varlığı kontrol ediliyor. Logonun eksikliği veya sosyal medyanın eksikliği kontrol edilmiyor.

---

## 5. İyileştirilmesi Gerekenler

### Güvenlik Katmanı

- [ ] `APP_DEBUG=false` üretimde
- [ ] Login throttle: `throttle:5,1`
- [ ] E-posta doğrulama (`MustVerifyEmail`)
- [ ] Şifre sıfırlama akışı
- [ ] 2FA (iki faktörlü doğrulama) — en azından TOTP
- [ ] Session timeout konfigürasyonu
- [ ] Content Security Policy (CSP) header
- [ ] `robots.txt` kontrolü (admin route'ları engellenmeli)

### Ödeme Entegrasyonu (En Kritik İş Kaybı)

- [ ] **iyzico** veya **Stripe** entegrasyonu (Türkiye için iyzico, global için Stripe)
- [ ] Abonelik yönetimi (aylık/yıllık, otomatik yenileme)
- [ ] Fatura/makbuz e-posta gönderimi
- [ ] İptal akışı (downgrade to Basic)
- [ ] Başarısız ödeme akışı
- [ ] `webhooks` ile ödeme durumu güncelleme

### Teknik Mimari İyileştirmeleri

- [ ] **Eloquent ORM'e Geçiş**: Query Builder tüm kontrollerde kullanılmış. Relationship tanımları ile daha okunabilir ve test edilebilir kod.
- [ ] **Model Sayısını Artır**: Sadece `User` modeli var. `Tenant`, `Category`, `Product`, `Review`, `Reservation` modelleri oluşturulmalı.
- [ ] **Service Layer Ekle**: Controller'lar çok şişman. İş mantığı Service sınıflarına taşınmalı.
- [ ] **Cache Katmanı**: Sık değişmeyen veriler (menü kategorileri, ürünler) Redis/memcached ile cache'lenmeli. Kalabalık menülerde DB baskısını azaltır.
- [ ] **Queue ile E-posta**: Bildirim e-postaları queue'ya alınmalı. Senkron gönderim zaman aşımına yol açar.
- [ ] **API Layer**: REST API eklenmesi mobil uygulama ve 3. parti entegrasyonlara kapı açar.
- [ ] **Destek Talebi Tablosu Refactor**: `admin_reply` JSON kolonundan `support_messages` ilişkili tablosuna geçiş.

### UX İyileştirmeleri

- [ ] **Onboarding Wizard**: Kayıt sonrası adım adım menü kurulum sihirbazı (kategori ekle → ürün ekle → logo yükle → QR indir)
- [ ] **Menü Önizlemesi**: Dashboard'dan çıkmadan menünün nasıl göründüğünü görebilme (iframe veya popup)
- [ ] **Bulk Import**: Excel/CSV ile toplu ürün yükleme (büyük menüler için kritik)
- [ ] **Ürün Kopyalama**: Benzer ürünleri hızlı oluşturmak için "Kopyala" butonu
- [ ] **Resim Kırpma**: Upload sırasında in-browser kırpma aracı (şu an sadece resize var)
- [ ] **Klavye Kısayolları**: Yönetim panelinde hızlı gezinme
- [ ] **Dark Mode**: Gece çalışan restoran çalışanları için
- [ ] **E-posta Bildirimleri**: Rezervasyon geldi, değerlendirme geldi, destek yanıtlandı

### Müşteri Deneyimi (Public Menü)

- [ ] **Arama**: Menüde ürün arama özelliği (büyük menülerde şart)
- [ ] **Favori Ürünler**: Müşteri beğendiği ürünleri yerel olarak işaretleyebilsin
- [ ] **Dil Seçimi**: Menüde dil değiştirme (Türkçe/İngilizce menü)
- [ ] **Ürün Detayı Zenginleştirme**: Kalori, alerjen bilgisi, içindekiler
- [ ] **Ürün Etiketleri**: "Yeni", "Önerilen", "Vegan", "Glutensiz" badge'leri
- [ ] **Sipariş Sepeti**: Müşteri ürün seçip garsondan sipariş isteyebilsin (masa servisi)
- [ ] **Menü Paylaşma**: WhatsApp/sosyal medya paylaşım butonları

### Analitik ve Raporlama

- [ ] **Grafik Dashboard**: Sadece sayı değil, zaman serisi grafikleri (7 gün/30 gün trend)
- [ ] **En Çok Görüntülenen Ürünler**: Hangi ürünler QR ile en çok açılıyor?
- [ ] **Rezervasyon Analitikleri**: Doluluk oranı, en yoğun saatler
- [ ] **Değerlendirme Trendi**: Puan trendi grafiği
- [ ] **Dışa Aktarma**: Veri CSV olarak indirilebilmeli

---

## 6. SEO Analizi

### Mevcut Durumun Güçlü Yönleri ✅

| Özellik | Durum | Not |
|---------|-------|-----|
| Schema.org Markup | ✅ İyi | HowTo, FAQPage, BreadcrumbList, ItemList, Product+Offer |
| Meta Title/Description | ✅ Mevcut | Tüm sayfalar optimize edilmiş |
| Meta Keywords | ✅ Mevcut | TR ve EN versiyonları var |
| Sitemap.xml | ✅ Otomatik | Blog yazıları dahil dinamik |
| Hreflang alternates | ✅ Sitemap'te | Dil alternatifleri sitemap'e eklenmiş |
| Canonical Tag | ✅ Landing'de | `@section('canonical')` landing'de var |
| Responsive Design | ✅ | Mobile-first Bootstrap |
| Blog Altyapısı | ✅ | SEO içerik üretimi için hazır |
| Demo Sayfası | ✅ | Canlı ürün gösterimi |

### Kritik SEO Eksiklikleri ❌

**SEO-01: Public Menü URL'leri Arama Motoruna Kapalı**
```
/menu/7               ← KÖTÜ: Sayısal ID, anlamlı değil
/menu/pizza-palace    ← İYİ: Slug tabanlı, SEO değeri yüksek
```
Her restoranın menüsü indexlenebilirse binlerce long-tail keyword yakalanır. Şu an bu fırsat tamamen kaçırılıyor.

**SEO-02: Menü Sayfaları `noindex` Olmalı Veya Optimize Edilmeli**
`/menu/{id}` sayfalarının hangi durumlarda indexlendiği belirsiz. İçerik çok ince (thin content) olabilir.

**SEO-03: Menü Sayfalarında LocalBusiness Schema Yok**
Her restoran menüsü için `LocalBusiness` schema eklenmeli:
```json
{
  "@type": "Restaurant",
  "name": "...",
  "address": {...},
  "telephone": "...",
  "menu": "/menu/..."
}
```
Bu Google'ın Restoran bilgi kutusunda görünmeyi sağlar.

**SEO-04: İç Linkleme Stratejisi Yok**
Landing → Özellikler → Fiyatlar → Blog zinciri var ama sistematik iç linkleme planı yok. Blog yazıları özelliklere, özellikler fiyata daha güçlü linklemeli.

**SEO-05: Hreflang Tag'ları Sayfada Eksik**
Sitemap'te alternate var ama `<head>` içinde `<link rel="alternate" hreflang="...">` tag'ları yok. Google her iki formatı da bekler.

```html
<!-- Her sayfanın <head>'ine eklenmeli -->
<link rel="alternate" hreflang="tr" href="https://siparismasanda.com/ozellikler" />
<link rel="alternate" hreflang="en" href="https://siparismasanda.com/en/features" />
<link rel="alternate" hreflang="x-default" href="https://siparismasanda.com/ozellikler" />
```

**SEO-06: Canonical Sadece Ana Sayfada**
Diğer sayfalarda (`/ozellikler`, `/fiyatlar`, `/blog/*`) canonical tag yok. Çift içerik riskini artırıyor.

**SEO-07: robots.txt Kontrolü Gerekli**
Admin paneli (`/dashboard`, `/users`, `/developer`) arama motoru tarafından indexlenmemeli. `robots.txt` dosyası incelenmeli.

**SEO-08: Core Web Vitals Optimizasyonu Eksik**
- Bootstrap CDN + özel CSS = büyük CSS paketi
- TailwindCSS 4 + Bootstrap 5 = çift framework yükü
- Görsel optimizasyonu yok (WebP format, lazy loading)
- JavaScript defer/async stratejisi belirsiz

**SEO-09: Open Graph ve Twitter Card Eksik**
Sosyal medyada paylaşıldığında önizleme görsel/metin yok. Landing sayfasında `og:image`, `og:description` tag'ları yok.

**SEO-10: Blog İçerik Stratejisi Gerekli**
Blog altyapısı var ama içerik yeterli mi? "Dijital menü nedir", "QR menü nasıl yapılır", "restoran dijitalleşme" gibi keyword clusterları hedeflenmeli.

### SEO Puan Tahmini (Ahrefs/SEMrush Metriği)

```
Domain Authority: Düşük (yeni site)
Backlink Profili: Muhtemelen zayıf
Keyword Rankings TR: Orta (niche kelimeler)
Keyword Rankings EN/Global: Neredeyse sıfır
Technical SEO: 6/10
On-Page SEO: 7/10
```

---

## 7. Rakip Analizi ve Eksik Özellikler

### Ana Rakipler

| Rakip | Köken | Güçlü Yön | Fiyat |
|-------|-------|-----------|-------|
| **Menuu** | TR | Basit UX, hızlı onboarding | Ücretsiz + paid |
| **QR Tiger** | SG | Global, çok dil, API | $7-45/ay |
| **Flipdish** | IE | POS entegrasyonu, online sipariş | Kurumsal |
| **GloriaFood** | RO | Online sipariş, ödeme | Ücretsiz + komisyon |
| **Lightspeed Restaurant** | CA | Full POS, analitik | $69+/ay |
| **Square for Restaurants** | US | Ödeme + POS + menü | Komisyon bazlı |
| **Menu Tiger** | US | AI menü, 3D yiyecek görseli | $19-89/ay |
| **Tableo** | TR | Rezervasyon odaklı | ₺500+/ay |

### Sipariş Masanda'da OLMAYAN Kritik Özellikler

#### Tier 1 — Global'e Çıkmak İçin Zorunlu

**1. Online Sipariş + Ödeme (In-Menu Ordering)**
Müşteri menüden ürün seçip ödeme yapabilmeli. "Coming Soon" durumundan çıkarılmalı. Bu olmadan GloriaFood, Flipdish ile rekabet imkânsız.

**2. Çoklu Para Birimi**
₺ hardcoded. Dolar, Euro, Pound, Dirhem desteği olmadan global mümkün değil.

**3. Çoklu Dil (Menü İçeriği)**
Şu an arayüz TR/EN ama menü içeriği (ürün adları, açıklamalar) sadece bir dilde. Restoranlar ürünlerini birden fazla dilde girebilmeli.

**4. Özel Domain Desteği**
`menu.pizzapalace.com` gibi white-label alan adı. Kurumsal müşteri için şart.

**5. REST API**
Entegrasyon olmadan POS sistemleri, üçüncü taraf uygulamalar Sipariş Masanda'ya bağlanamaz.

#### Tier 2 — Rekabetçi Olmak İçin Gerekli

**6. Alerjen & Diyet Bilgisi**
Avrupa'da zorunlu yasal gereklilik (EU Food Information Regulation). Vegan, glutensiz, laktozsuz etiketleri.

**7. Ürün Varyantları / Seçenekler**
"Büyük / Orta / Küçük", "Extra peynir +₺10", "Sos seçimi" gibi modifier'lar. Şu an ürün tek fiyat/tanım ile giriyor.

**8. İndirim / Kupon Sistemi**
Promosyon kodu, indirimli fiyat, "Mutlu Saat" kampanyası.

**9. Sadakat Programı**
Müşteri puan biriktirsin, sıradaki siparişte indirim kazansın.

**10. Masa QR Sistemi (Table-Specific QR)**
Her masaya farklı QR → müşteri taradığında masa numarası otomatik algılanır → siparişe masa bilgisi eklenir.

**11. Bildirim Sistemi (Push / E-posta)**
- Rezervasyon geldi → restoran e-posta alır
- Rezervasyon onaylandı → müşteri e-posta alır
- Değerlendirme geldi → bildirim
- Menü güncellendi → seçili müşterilere bildirim

**12. Gelişmiş Analitik**
- Saatlik ziyaret grafiği
- En çok görüntülenen ürünler
- Dönüşüm analizi (görüntüle → sipariş)
- Coğrafi analiz

**13. Google Maps / Haritalar Entegrasyonu**
Menü sayfasında restoranın konumunu göster, "Yol Tarifi Al" butonu.

**14. WhatsApp Sipariş Entegrasyonu**
"Sipariş Ver" → WhatsApp'ta önceden dolu mesaj ile yönlendirme.

#### Tier 3 — Farklılaşma Özellikleri

**15. AI Menü Açıklaması Üretimi**
Ürün adı verilince AI otomatik çekici açıklama önersin.

**16. Ürün Görseli AI Üretimi**
Fotoğraf olmayan ürünler için AI görsel önerisi.

**17. QR Tasarım Özelleştirme**
Renk, logo yerleştirme, frame seçeneği olan branded QR kodlar.

**18. Çevrimdışı Menü (PWA)**
İnternet kesintisinde bile menünün açık kalması için Progressive Web App.

**19. NFC Desteği**
Modern telefonlar QR yerine NFC tap ile menüye erişebilir.

**20. Personel Sipariş Alma**
Garson kendi tabletinden sipariş alır, mutfağa iletir (KDS entegrasyonu).

---

## 8. Global Expansion Yol Haritası

### Faz 1 — Altyapı Düzeltme (1-2 Ay)

**Amaç**: Güvenlik açıklarını kapat, teknik borcu azalt.

- [ ] `APP_DEBUG=false`
- [ ] Şifre sıfırlama
- [ ] E-posta doğrulama
- [ ] Login rate limiting
- [ ] Hreflang tag'ları
- [ ] Open Graph meta tag'ları
- [ ] Canonical tag tüm sayfalara
- [ ] Ödeme sistemi (iyzico Türkiye için)
- [ ] E-posta bildirimleri (rezervasyon, değerlendirme)

### Faz 2 — Ürün Güçlendirme (2-4 Ay)

**Amaç**: Türkiye'deki retention'ı artır, churn'ü azalt.

- [ ] Menü içi arama
- [ ] Bulk ürün import (Excel/CSV)
- [ ] Ürün varyantları (boy seçimi, ekstralar)
- [ ] Alerjen ve diyet etiketleri
- [ ] Masa bazlı QR (masa numarası algılama)
- [ ] Gelişmiş analitik dashboard (grafikler)
- [ ] WhatsApp sipariş entegrasyonu
- [ ] Google Maps entegrasyonu
- [ ] Push bildirim desteği
- [ ] LocalBusiness schema (menü sayfalarına)

### Faz 3 — Global Hazırlık (4-8 Ay)

**Amaç**: Türkiye dışı pazarlara açılma.

- [ ] **Çoklu para birimi** (USD, EUR, GBP, AED, SAR)
- [ ] **Çoklu dil (menü içeriği)** — ürünler çok dilde girilebilsin
- [ ] **Timezone yapılandırması** — tenant kendi saat dilimini seçsin
- [ ] **Stripe entegrasyonu** — global ödeme
- [ ] **Özel domain** desteği (CNAME, SSL otomasyonu)
- [ ] **REST API** (v1) — üçüncü taraf entegrasyonlar
- [ ] **GDPR uyumluluğu** — cookie consent, veri silme hakkı (Avrupa için zorunlu)
- [ ] **CCPA uyumluluğu** — Kaliforniya gizlilik yasası (ABD için)
- [ ] **Halal/Kosher** etiket desteği (Orta Doğu/Yahudi pazarları)
- [ ] İngilizce landing page optimizasyonu (mevcut TR odaklı)
- [ ] Global rakip keyword'lerini hedefleyen blog içeriği

### Faz 4 — Büyüme (8-18 Ay)

**Amaç**: Ölçeklenebilir gelir, market share kazanımı.

- [ ] **Online Sipariş + Ödeme** (in-menu checkout)
- [ ] **Sadakat programı**
- [ ] **İndirim / kupon sistemi**
- [ ] **Mobil uygulama** (React Native veya Flutter)
- [ ] **POS entegrasyonu** (Adyen, Revel, Toast)
- [ ] **AI menü açıklama üretici**
- [ ] **White-label** (bayilik sistemi)
- [ ] **Franchise yönetimi** (çok lokasyonlu işletmeler)
- [ ] **Marketplace**: Restoran müşterilerine malzeme, aksesuar satışı

### Hedef Pazarlar (Öncelik Sırası)

1. **Türkiye** (mevcut) → Pazar liderliği hedefi
2. **Orta Doğu** (BAE, Suudi Arabistan) → Yüksek restoran yoğunluğu, dijital dönüşüm hızlı
3. **Balkanlar** (Bulgaristan, Romanya, Sırbistan) → Yakın coğrafya, düşük rekabet
4. **Almanya** (Türk diasporası) → Yerleşik topluluk, satış kanalı mevcut
5. **İngiltere** → Güçlü gastronomi pazarı, yüksek digital adoption
6. **ABD** (Türk restoranları, etnik pazarlar) → Büyük market, yüksek fiyat toleransı

### Fiyatlandırma Stratejisi (Global)

| Pazar | Basic | Premium | Pro (Yeni) |
|-------|-------|---------|-----------|
| Türkiye | ₺0 | ₺199/ay | ₺499/ay |
| Orta Doğu | $0 | $15/ay | $35/ay |
| Avrupa | €0 | €15/ay | €35/ay |
| ABD | $0 | $19/ay | $49/ay |

> Şu anki ₺199/ay = ~$6 USD. Bu fiyat global için çok düşük, değer algısını düşürür.

---

## Sonuç Özeti

**Sipariş Masanda bugün:**
- Türkiye'de küçük/orta restoranlar için işe yarayan bir MVP
- Freemium model doğru kurgulanmış ama ödeme sistemi eksik
- Teknik altyapı sağlam temelde ama teknik borç birikmiş
- SEO çalışmaları başlanmış ama global için yetersiz

**Global'e çıkmak için gereken en kritik 5 değişiklik:**
1. **Ödeme Sistemi** (iyzico + Stripe) — gelir olmadan büyüme yok
2. **Çoklu Para Birimi + Dil** — lokalizasyon olmadan global yok
3. **Online Sipariş** — bu olmadan değer önerisi zayıf kalır
4. **API** — ekosistem olmadan lock-in ve ölçek sağlanamaz
5. **GDPR Uyumluluğu** — Avrupa pazarı için yasal zorunluluk

---

*Analiz, kaynak kodu, controller'lar, view'lar, migration dosyaları, route tanımları ve dil dosyalarının tam incelemesiyle oluşturulmuştur.*
