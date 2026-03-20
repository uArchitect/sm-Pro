@extends('layouts.public')

@php
    $locale = app()->getLocale();
    $isTr = $locale === 'tr';
@endphp

@section('title', $isTr
    ? 'QR Menü Özellikleri | Kategori, Fotoğraf, Değerlendirme | Sipariş Masanda'
    : 'QR Menu Features | Categories, Photos, Reviews | Siparis Masanda')

@section('meta_description', $isTr
    ? 'Sınırsız ürün, fotoğraflı menü, müşteri değerlendirme, çoklu kullanıcı ve sosyal medya entegrasyonu. Tüm dijital QR menü özelliklerini keşfet.'
    : 'Unlimited products, photo menu, customer reviews, multi-user and social media integration. Discover all digital QR menu features.')

@section('meta_keywords', $isTr
    ? 'qr kod oluşturma, fotoğraflı menü, online menü yönetimi, restoran yönetim paneli, mobil uyumlu menü, anlık menü güncelleme, temassız menü sistemi'
    : 'qr code generator, photo menu, online menu management, restaurant management panel, mobile friendly menu, instant menu update, contactless menu system')

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {"@@type": "ListItem", "position": 1, "name": "{{ $isTr ? 'Ana Sayfa' : 'Home' }}", "item": "{{ locale_route('home') }}"},
        {"@@type": "ListItem", "position": 2, "name": "{{ $isTr ? 'Özellikler' : 'Features' }}", "item": "{{ locale_route('features') }}"}
    ]
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ItemList",
    "name": "{{ $isTr ? 'Sipariş Masanda QR Menü Özellikleri' : 'Siparis Masanda QR Menu Features' }}",
    "description": "{{ $isTr ? 'Restoran ve kafeler için dijital QR menü sistemi özellikleri' : 'Digital QR menu system features for restaurants and cafes' }}",
    "numberOfItems": 16,
    "itemListElement": [
        {"@@type": "ListItem", "position": 1, "name": "{{ $isTr ? 'QR Kod Oluşturma ve Baskı' : 'QR Code Generation & Print' }}"},
        {"@@type": "ListItem", "position": 2, "name": "{{ $isTr ? 'Sınırsız Kategori ve Ürün Yönetimi' : 'Unlimited Category & Product Management' }}"},
        {"@@type": "ListItem", "position": 3, "name": "{{ $isTr ? 'Fotoğraflı Dijital Menü' : 'Digital Menu with Photos' }}"},
        {"@@type": "ListItem", "position": 4, "name": "{{ $isTr ? 'Müşteri Değerlendirme Sistemi' : 'Customer Review System' }}"},
        {"@@type": "ListItem", "position": 5, "name": "{{ $isTr ? 'Çoklu Kullanıcı ve Rol Yönetimi' : 'Multi-User & Role Management' }}"},
        {"@@type": "ListItem", "position": 6, "name": "{{ $isTr ? 'Sosyal Medya Entegrasyonu' : 'Social Media Integration' }}"},
        {"@@type": "ListItem", "position": 7, "name": "{{ $isTr ? 'Mobil Uyumlu Menü Tasarımı' : 'Mobile Friendly Menu Design' }}"},
        {"@@type": "ListItem", "position": 8, "name": "{{ $isTr ? 'Menü Tasarımı (Layout & Renk)' : 'Menu Design (Layout & Color)' }}"},
        {"@@type": "ListItem", "position": 9, "name": "{{ $isTr ? 'Slider ve Banner Yönetimi' : 'Slider & Banner Management' }}"},
        {"@@type": "ListItem", "position": 10, "name": "{{ $isTr ? 'Etkinlik Yönetimi' : 'Event Management' }}"},
        {"@@type": "ListItem", "position": 11, "name": "{{ $isTr ? 'Online Rezervasyon Sistemi' : 'Online Reservation System' }}"},
        {"@@type": "ListItem", "position": 12, "name": "{{ $isTr ? 'QR Ziyaret İstatistikleri' : 'QR Visit Analytics' }}"},
        {"@@type": "ListItem", "position": 13, "name": "{{ $isTr ? 'Destek Talebi Sistemi' : 'Support Ticket System' }}"},
        {"@@type": "ListItem", "position": 14, "name": "{{ $isTr ? 'İşletme Profili Yönetimi' : 'Business Profile Management' }}"},
        {"@@type": "ListItem", "position": 15, "name": "{{ $isTr ? 'Ürün Müsaitlik Durumu Yönetimi' : 'Product Availability Management' }}"},
        {"@@type": "ListItem", "position": 16, "name": "{{ $isTr ? 'Ürün Görüntülenme İstatistikleri' : 'Product View Analytics' }}"}
    ]
}
</script>
{{-- SSS: Özellikler sayfası için FAQ rich snippet --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        {"@@type": "Question", "name": "{{ $isTr ? 'QR menüde hangi özellikler var?' : 'What features does the QR menu have?' }}", "acceptedAnswer": {"@@type": "Answer", "text": "{{ $isTr ? 'Sipariş Masanda ile QR kod oluşturma, sınırsız kategori ve ürün, fotoğraflı menü, müşteri değerlendirmeleri, çoklu kullanıcı, sosyal medya entegrasyonu, mobil uyumlu tasarım, slider/banner yönetimi, etkinlik yönetimi, online rezervasyon sistemi, QR ziyaret istatistikleri, destek talebi sistemi ve işletme profili yönetimi sunulur.' : 'Siparis Masanda offers QR code generation, unlimited categories and products, photo menu, customer reviews, multi-user support, social media integration, mobile-friendly design, slider/banner management, event management, online reservation system, QR visit analytics, support ticket system, and business profile management.' }}"}},
        {"@@type": "Question", "name": "{{ $isTr ? 'Dijital menüye ürün fotoğrafı eklenebilir mi?' : 'Can I add product photos to the digital menu?' }}", "acceptedAnswer": {"@@type": "Answer", "text": "{{ $isTr ? 'Evet. Ürün ve kategori fotoğrafları ekleyebilir, otomatik boyutlandırma ile fotoğraflı menü oluşturabilirsiniz.' : 'Yes. You can add product and category photos and create a photo menu with automatic resizing.' }}"}},
        {"@@type": "Question", "name": "{{ $isTr ? 'Menü fiyatları anlık güncellenebilir mi?' : 'Can menu prices be updated instantly?' }}", "acceptedAnswer": {"@@type": "Answer", "text": "{{ $isTr ? 'Evet. Yönetim panelinden fiyat, açıklama ve sıralama anında güncellenir; değişiklikler hemen menüde görünür.' : 'Yes. You can update prices, descriptions and order from the management panel; changes appear on the menu immediately.' }}"}}
    ]
}
</script>
@endsection

@section('styles')
        .features-section{padding:2.5rem 0 5.5rem}
        /* Index'teki gibi kartları yan yana göstermek için grid + card stilleri */
        .features-grid{margin-top:2rem}
        .feat-card{
            background:#fff;border:1px solid #e2e8f0;border-radius:18px;
            padding:1.6rem;transition:all .25s;height:100%;
            box-shadow:0 1px 4px rgba(0,0,0,.05);
            display:flex; flex-direction:column;
        }
        .feat-card:hover{border-color:#c7d2fe;transform:translateY(-4px);box-shadow:0 12px 40px rgba(79,70,229,.1)}
        .feat-block-icon{width:56px;height:56px;border-radius:15px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:1.05rem}
        .feat-card h2{font-size:1.18rem;font-weight:800;color:#0f172a;margin-bottom:.55rem;line-height:1.25}
        .feat-card p{font-size:.9rem;color:#64748b;line-height:1.75;margin:0 0 1.05rem;max-width:none}
        .feat-card ul{list-style:none;padding:0;margin:.2rem 0 0; display:flex; flex-direction:column; gap:.35rem}
        .feat-card ul li{font-size:.84rem;color:#475569;padding:0;display:flex;align-items:flex-start;gap:.55rem}
        .feat-card ul li i{color:#4F46E5;font-size:.7rem;margin-top:.3rem;flex-shrink:0}
        .feat-card .mt-auto{flex-grow:0}
@endsection

@section('content')
    <section class="page-hero">
        <div class="container">
            <h1>{{ $isTr ? 'Dijital' : 'Digital' }} <span class="accent">{{ $isTr ? 'QR Menü Sistemi Özellikleri' : 'QR Menu System Features' }}</span></h1>
            <p class="page-hero-sub">{{ $isTr ? 'Temassız menü sisteminizi güçlendiren tüm özellikler. Online menü yönetimi hiç bu kadar kolay olmamıştı.' : 'All the features that power your contactless menu system. Online menu management has never been this easy.' }}</p>
        </div>
    </section>

    <section class="features-section">
        <div class="container" style="max-width:1100px">
            <div class="features-grid">
                <div class="row g-4">

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="feat-card">
                            <div class="feat-block-icon" style="background:rgba(79,70,229,.12);color:#4F46E5"><i class="bi bi-qr-code"></i></div>
                            <h2>{{ $isTr ? 'QR Kod Oluşturma ve Baskı' : 'QR Code Generation & Print' }}</h2>
                            <p>{{ $isTr ? 'Restoranınız için benzersiz QR kod oluşturun. Yüksek çözünürlüklü karekod menü dosyasını indirip masalarınıza, tabelalarınıza veya broşürlerinize yerleştirin.' : 'Create a unique QR code for your restaurant. Download the high-resolution QR code file and place it on your tables, signs, or brochures.' }}</p>
                            <ul>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Yüksek çözünürlüklü QR kod indirme' : 'High-resolution QR code download' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Tek QR kod ile tüm menüye erişim' : 'Access entire menu with a single QR code' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Ürün bazlı QR kod oluşturma' : 'Product-level QR code generation' }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="feat-card">
                            <div class="feat-block-icon" style="background:rgba(16,185,129,.12);color:#10b981"><i class="bi bi-grid-3x3-gap"></i></div>
                            <h2>{{ $isTr ? 'Sınırsız Kategori ve Ürün Yönetimi' : 'Unlimited Category & Product Management' }}</h2>
                            <p>{{ $isTr ? 'Online menü yönetimi ile sınırsız kategori ve ürün ekleyin. Alt kategoriler, sürükle-bırak sıralama ve anlık menü güncelleme ile restoran menü sisteminizi tam kontrol edin.' : 'Add unlimited categories and products with online menu management. Full control of your restaurant menu system with subcategories, drag-and-drop ordering, and instant menu updates.' }}</p>
                            <ul>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Sınırsız kategori ve alt kategori' : 'Unlimited categories and subcategories' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Sürükle-bırak sıralama' : 'Drag-and-drop reordering' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Anlık fiyat ve açıklama güncelleme' : 'Instant price and description updates' }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="feat-card">
                            <div class="feat-block-icon" style="background:rgba(236,72,153,.12);color:#EC4899"><i class="bi bi-images"></i></div>
                            <h2>{{ $isTr ? 'Fotoğraflı Dijital Menü' : 'Digital Menu with Photos' }}</h2>
                            <p>{{ $isTr ? 'Ürünlerinize fotoğraf ekleyerek fotoğraflı menü oluşturun. Görsel zenginlik müşterilerinizin sipariş kararını kolaylaştırır ve satışlarınızı artırır.' : 'Create a photo menu by adding images to your products. Visual richness makes it easier for customers to decide and increases your sales.' }}</p>
                            <ul>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Ürün ve kategori fotoğrafları' : 'Product and category photos' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Otomatik boyutlandırma ve optimizasyon' : 'Automatic resizing and optimization' }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="feat-card">
                            <div class="feat-block-icon" style="background:rgba(251,191,36,.12);color:#FBBF24"><i class="bi bi-star"></i></div>
                            <h2>{{ $isTr ? 'Müşteri Değerlendirme Sistemi' : 'Customer Review System' }}</h2>
                            <p>{{ $isTr ? 'Müşterileriniz QR menü üzerinden doğrudan değerlendirme bıraksın. Puanları ve yorumları restoran yönetim panelinden takip edin.' : 'Let your customers leave reviews directly through the QR menu. Track ratings and comments from the restaurant management panel.' }}</p>
                            <ul>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? '5 yıldızlı puanlama' : '5-star rating' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Yorum ve puan yönetimi' : 'Review and rating management' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Günlük tekrar koruma sistemi' : 'Daily duplicate protection' }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="feat-card">
                            <div class="feat-block-icon" style="background:rgba(99,102,241,.12);color:#6366f1"><i class="bi bi-people"></i></div>
                            <h2>{{ $isTr ? 'Çoklu Kullanıcı ve Rol Yönetimi' : 'Multi-User & Role Management' }}</h2>
                            <p>{{ $isTr ? 'Ekibinizi restoran yönetim paneline davet edin. Owner, admin ve personel rolleriyle yetkilendirme yapın.' : 'Invite your team to the restaurant management panel. Assign permissions with owner, admin, and staff roles.' }}</p>
                            <ul>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Owner, admin, personel rolleri' : 'Owner, admin, staff roles' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Rol bazlı yetkilendirme' : 'Role-based permissions' }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="feat-card">
                            <div class="feat-block-icon" style="background:rgba(236,72,153,.12);color:#EC4899"><i class="bi bi-instagram"></i></div>
                            <h2>{{ $isTr ? 'Sosyal Medya Entegrasyonu' : 'Social Media Integration' }}</h2>
                            <p>{{ $isTr ? 'Instagram, Facebook, X ve WhatsApp hesaplarınızı dijital menü sayfanıza ekleyin. Müşterileriniz sizi kolayca takip etsin.' : 'Add your Instagram, Facebook, X, and WhatsApp accounts to your digital menu page. Let your customers follow you easily.' }}</p>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="feat-card">
                            <div class="feat-block-icon" style="background:rgba(168,85,247,.12);color:#A855F7"><i class="bi bi-phone"></i></div>
                            <h2>{{ $isTr ? 'Mobil Uyumlu Menü Tasarımı' : 'Mobile Friendly Menu Design' }}</h2>
                            <p>{{ $isTr ? 'Tüm cihazlarda kusursuz görüntülenen mobil uyumlu menü. Müşterileriniz uygulama indirmeden telefonlarıyla karekod menüyü tarayıp anında erişsin.' : 'Mobile-friendly menu that displays perfectly on all devices. Your customers can scan the QR code menu with their phones and access instantly without downloading an app.' }}</p>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="feat-card">
                            <div class="feat-block-icon" style="background:rgba(99,102,241,.12);color:#6366f1"><i class="bi bi-palette"></i></div>
                            <h2>{{ $isTr ? 'Menü Tasarımı (Layout & Renk)' : 'Menu Design (Layout & Color)' }}</h2>
                            <p>{{ $isTr ? 'QR menü ve dijital menü görünümünü markanıza uyarlayın. Premium pakette dört farklı menü düzeni ve tam renk özelleştirmesi ile restoran menünüzü benzersiz kılın. Yönetim panelinden canlı önizleme ile değişiklikleri anında görün.' : 'Customize your QR menu and digital menu appearance to match your brand. With four distinct menu layouts and full color customization in the premium plan, make your restaurant menu unique. Preview changes live from the management panel before publishing.' }}</p>
                            <ul>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Akordeon düzeni: Açılır kapanır kategorilerle sade menü' : 'Accordion layout: Clean menu with collapsible categories' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Sekmeli düzen: Kategori sekmeleri ile hızlı geçiş' : 'Tab layout: Quick switching with category tabs' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Izgara düzeni: Ürün kartları ile görsel menü' : 'Grid layout: Visual menu with product cards' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Elegant düzen: Dergi tarzı büyük bölümler' : 'Elegant layout: Magazine-style sections' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Renk ve tema özelleştirmesi (ana renk, arka plan, başlık)' : 'Color and theme customization (primary, background, header)' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Canlı önizleme ile kaydetmeden önce test' : 'Live preview before saving' }}</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Slider / Banner Yönetimi - Premium --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="feat-card">
                            <div class="feat-block-icon" style="background:rgba(245,158,11,.12);color:#F59E0B"><i class="bi bi-images"></i></div>
                            <h2>{{ $isTr ? 'Slider ve Banner Yönetimi' : 'Slider & Banner Management' }}</h2>
                            <p>{{ $isTr ? 'Menü sayfanızın üstüne fotoğraflı slider ekleyin. Kampanya görsellerini, yeni ürünleri veya özel teklifleri kaydırmalı banner ile müşterilerinize anında gösterin.' : 'Add a photo slider to the top of your menu page. Show campaigns, new products, or special offers to your customers instantly with a scrollable banner.' }}</p>
                            <ul>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Birden fazla görsel yükleme' : 'Upload multiple images' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Başlık ve açıklama ekleme' : 'Add title and description' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Sürükle-bırak sıralama' : 'Drag-and-drop ordering' }}</li>
                            </ul>
                            <div class="mt-auto pt-2">
                                <span class="badge" style="background:rgba(245,158,11,.15);color:#B45309;font-size:.75rem;font-weight:600;border-radius:6px;padding:.25rem .6rem">
                                    <i class="bi bi-star-fill me-1"></i>{{ $isTr ? 'Premium' : 'Premium' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Etkinlik Yönetimi - Premium --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="feat-card">
                            <div class="feat-block-icon" style="background:rgba(20,184,166,.12);color:#14B8A6"><i class="bi bi-calendar-event"></i></div>
                            <h2>{{ $isTr ? 'Etkinlik Yönetimi' : 'Event Management' }}</h2>
                            <p>{{ $isTr ? 'Restoranınızdaki özel geceleri, müzik etkinliklerini veya özel günleri dijital menünüzde duyurun. Tarih aralığı, açıklama ve fotoğraf ile zengin etkinlik kartları oluşturun.' : 'Announce special nights, music events, or special occasions at your restaurant on your digital menu. Create rich event cards with date range, description, and photo.' }}</p>
                            <ul>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Başlangıç ve bitiş tarihi belirleme' : 'Set start and end dates' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Etkinlik fotoğrafı ekleme' : 'Add event photo' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Aktif/pasif durumu yönetimi' : 'Active/inactive status management' }}</li>
                            </ul>
                            <div class="mt-auto pt-2">
                                <span class="badge" style="background:rgba(245,158,11,.15);color:#B45309;font-size:.75rem;font-weight:600;border-radius:6px;padding:.25rem .6rem">
                                    <i class="bi bi-star-fill me-1"></i>{{ $isTr ? 'Premium' : 'Premium' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Online Rezervasyon Sistemi - Premium --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="feat-card">
                            <div class="feat-block-icon" style="background:rgba(99,102,241,.12);color:#6366F1"><i class="bi bi-calendar-check"></i></div>
                            <h2>{{ $isTr ? 'Online Rezervasyon Sistemi' : 'Online Reservation System' }}</h2>
                            <p>{{ $isTr ? 'Müşterileriniz QR menüden doğrudan masa rezervasyonu yapsın. Bölge ve masa tanımlayın; gelen rezervasyonları panelden onaylayın veya iptal edin.' : 'Let your customers make table reservations directly from the QR menu. Define zones and tables; approve or cancel incoming reservations from the panel.' }}</p>
                            <ul>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Bölge ve masa tanımlama' : 'Zone and table definitions' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Müşteri adı, kişi sayısı, tarih ve saat' : 'Guest name, party size, date & time' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Bekliyor / Onaylandı / İptal durumu' : 'Pending / Confirmed / Cancelled status' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Yeni rezervasyon bildirimleri' : 'New reservation notifications' }}</li>
                            </ul>
                            <div class="mt-auto pt-2">
                                <span class="badge" style="background:rgba(245,158,11,.15);color:#B45309;font-size:.75rem;font-weight:600;border-radius:6px;padding:.25rem .6rem">
                                    <i class="bi bi-star-fill me-1"></i>{{ $isTr ? 'Premium' : 'Premium' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- QR Ziyaret İstatistikleri - Ücretsiz --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="feat-card">
                            <div class="feat-block-icon" style="background:rgba(16,185,129,.12);color:#10b981"><i class="bi bi-bar-chart-line"></i></div>
                            <h2>{{ $isTr ? 'QR Ziyaret İstatistikleri' : 'QR Visit Analytics' }}</h2>
                            <p>{{ $isTr ? 'Menünüzün kaç kez tarandığını takip edin. Bugünkü ve toplam QR menü ziyaret sayılarını yönetim paneli kontrol panelinizden anlık olarak görün.' : 'Track how many times your menu has been scanned. View today\'s and total QR menu visit counts instantly from your management panel dashboard.' }}</p>
                            <ul>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Günlük QR tarama sayısı' : 'Daily QR scan count' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Toplam ziyaret sayısı' : 'Total visit count' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Kontrol panelinde anlık görüntüleme' : 'Real-time display on dashboard' }}</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Destek Talebi Sistemi - Ücretsiz --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="feat-card">
                            <div class="feat-block-icon" style="background:rgba(239,68,68,.12);color:#EF4444"><i class="bi bi-headset"></i></div>
                            <h2>{{ $isTr ? 'Destek Talebi Sistemi' : 'Support Ticket System' }}</h2>
                            <p>{{ $isTr ? 'Yönetim panelinizden destek talebi oluşturun ve yanıtları takip edin. Soru, öneri veya teknik sorunlarınızı hızla bildirin; destek ekibi panel üzerinden yanıtlasın.' : 'Create support requests from your management panel and track replies. Report questions, suggestions, or technical issues quickly; the support team responds through the panel.' }}</p>
                            <ul>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Talep oluşturma ve takip' : 'Create and track tickets' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Mesajlaşma ile hızlı destek' : 'Fast support via messaging' }}</li>
                            </ul>
                        </div>
                    </div>

                    {{-- İşletme Profili Yönetimi - Ücretsiz --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="feat-card">
                            <div class="feat-block-icon" style="background:rgba(79,70,229,.12);color:#4F46E5"><i class="bi bi-shop"></i></div>
                            <h2>{{ $isTr ? 'İşletme Profili Yönetimi' : 'Business Profile Management' }}</h2>
                            <p>{{ $isTr ? 'Restoran adı, logo, adres ve iletişim bilgilerinizi tek bir panelden yönetin. İşletme profiliniz dijital menü sayfanızda otomatik olarak görüntülenir.' : 'Manage your restaurant name, logo, address, and contact information from a single panel. Your business profile is automatically displayed on your digital menu page.' }}</p>
                            <ul>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Restoran adı ve logo' : 'Restaurant name and logo' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Adres ve iletişim bilgileri' : 'Address and contact details' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Çalışma saatleri ve açıklama' : 'Opening hours and description' }}</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Ürün Müsaitlik Durumu - Ücretsiz --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="feat-card">
                            <div class="feat-block-icon" style="background:rgba(16,185,129,.12);color:#10b981"><i class="bi bi-toggle-on"></i></div>
                            <h2>{{ $isTr ? 'Ürün Müsaitlik Durumu Yönetimi' : 'Product Availability Management' }}</h2>
                            <p>{{ $isTr ? 'Ürünlerinizi anında müsait veya tükendi olarak işaretleyin. Stok durumu dijital menüde müşterilere gerçek zamanlı olarak yansır; sipariş karışıklığı yaşanmaz.' : 'Instantly mark products as available or sold out. Availability status is reflected to customers on the digital menu in real time, preventing order confusion.' }}</p>
                            <ul>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Tek tıkla müsait/tükendi toggle' : 'One-click available/sold-out toggle' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Müşteri menüsünde anlık güncelleme' : 'Instant update on customer menu' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Uygulama veya yenileme gerektirmez' : 'No app or page reload required' }}</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Ürün Görüntülenme İstatistikleri - Ücretsiz --}}
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="feat-card">
                            <div class="feat-block-icon" style="background:rgba(99,102,241,.12);color:#6366f1"><i class="bi bi-eye"></i></div>
                            <h2>{{ $isTr ? 'Ürün Görüntülenme İstatistikleri' : 'Product View Analytics' }}</h2>
                            <p>{{ $isTr ? 'Her ürünün kaç kez görüntülendiğini takip edin. Hangi ürünlerin müşteri ilgisini çektiğini anlayın ve menünüzü bu verilere göre optimize edin.' : 'Track how many times each product has been viewed. Understand which products attract customer interest and optimize your menu based on this data.' }}</p>
                            <ul>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Ürün bazlı görüntülenme sayısı' : 'Per-product view count' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'En çok ilgi gören ürünleri keşfet' : 'Discover most-viewed products' }}</li>
                                <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Menü optimizasyonu için veri odaklı karar' : 'Data-driven decisions for menu optimization' }}</li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section class="py-4">
        <div class="container" style="max-width:800px">
            <div class="text-center">
                <p style="font-size:.88rem;color:#64748b;line-height:1.7">
                    {{ $isTr
                        ? 'Dijital menü ve QR menü kullanımı hakkında daha fazla bilgi edinmek ister misiniz?'
                        : 'Want to learn more about digital menu and QR menu usage?' }}
                    <a href="{{ locale_route('blog') }}" style="color:#6366F1;text-decoration:underline">{{ $isTr ? 'Blog yazılarımızı okuyun' : 'Read our blog' }}</a>
                    {{ $isTr ? 'veya' : 'or' }}
                    <a href="{{ locale_route('pricing') }}" style="color:#6366F1;text-decoration:underline">{{ $isTr ? 'fiyat planlarını karşılaştırın' : 'compare pricing plans' }}</a>.
                </p>
            </div>
        </div>
    </section>

    <section class="cta-bar" style="padding-top:0">
        <div class="container">
            <div class="cta-box">
                <h2>{{ $isTr ? 'Tüm Bu Özellikleri Ücretsiz Deneyin' : 'Try All These Features for Free' }}</h2>
                <p>{{ $isTr ? 'Hemen ücretsiz hesap oluşturun ve dijital menünüzü dakikalar içinde yayına alın.' : 'Create a free account now and publish your digital menu in minutes.' }}</p>
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <a href="{{ route('register') }}" class="hero-btn-primary" style="position:relative">
                        <i class="bi bi-rocket-takeoff"></i> {{ $isTr ? 'Ücretsiz Başla' : 'Start Free' }}
                    </a>
                    <a href="{{ locale_route('pricing') }}" class="hero-btn-outline" style="position:relative">
                        <i class="bi bi-tag"></i> {{ $isTr ? 'Fiyatları Gör' : 'View Pricing' }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

