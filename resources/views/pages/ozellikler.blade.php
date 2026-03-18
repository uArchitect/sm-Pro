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
    "numberOfItems": 7,
    "itemListElement": [
        {"@@type": "ListItem", "position": 1, "name": "{{ $isTr ? 'QR Kod Oluşturma ve Baskı' : 'QR Code Generation & Print' }}"},
        {"@@type": "ListItem", "position": 2, "name": "{{ $isTr ? 'Sınırsız Kategori ve Ürün Yönetimi' : 'Unlimited Category & Product Management' }}"},
        {"@@type": "ListItem", "position": 3, "name": "{{ $isTr ? 'Fotoğraflı Dijital Menü' : 'Digital Menu with Photos' }}"},
        {"@@type": "ListItem", "position": 4, "name": "{{ $isTr ? 'Müşteri Değerlendirme Sistemi' : 'Customer Review System' }}"},
        {"@@type": "ListItem", "position": 5, "name": "{{ $isTr ? 'Çoklu Kullanıcı ve Rol Yönetimi' : 'Multi-User & Role Management' }}"},
        {"@@type": "ListItem", "position": 6, "name": "{{ $isTr ? 'Sosyal Medya Entegrasyonu' : 'Social Media Integration' }}"},
        {"@@type": "ListItem", "position": 7, "name": "{{ $isTr ? 'Mobil Uyumlu Menü Tasarımı' : 'Mobile Friendly Menu Design' }}"}
    ]
}
</script>
{{-- SSS: Özellikler sayfası için FAQ rich snippet --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "FAQPage",
    "mainEntity": [
        {"@@type": "Question", "name": "{{ $isTr ? 'QR menüde hangi özellikler var?' : 'What features does the QR menu have?' }}", "acceptedAnswer": {"@@type": "Answer", "text": "{{ $isTr ? 'Sipariş Masanda ile QR kod oluşturma, sınırsız kategori ve ürün, fotoğraflı menü, müşteri değerlendirmeleri, çoklu kullanıcı, sosyal medya entegrasyonu ve mobil uyumlu tasarım sunulur.' : 'Siparis Masanda offers QR code generation, unlimited categories and products, photo menu, customer reviews, multi-user support, social media integration, and mobile-friendly design.' }}"}},
        {"@@type": "Question", "name": "{{ $isTr ? 'Dijital menüye ürün fotoğrafı eklenebilir mi?' : 'Can I add product photos to the digital menu?' }}", "acceptedAnswer": {"@@type": "Answer", "text": "{{ $isTr ? 'Evet. Ürün ve kategori fotoğrafları ekleyebilir, otomatik boyutlandırma ile fotoğraflı menü oluşturabilirsiniz.' : 'Yes. You can add product and category photos and create a photo menu with automatic resizing.' }}"}},
        {"@@type": "Question", "name": "{{ $isTr ? 'Menü fiyatları anlık güncellenebilir mi?' : 'Can menu prices be updated instantly?' }}", "acceptedAnswer": {"@@type": "Answer", "text": "{{ $isTr ? 'Evet. Yönetim panelinden fiyat, açıklama ve sıralama anında güncellenir; değişiklikler hemen menüde görünür.' : 'Yes. You can update prices, descriptions and order from the management panel; changes appear on the menu immediately.' }}"}}
    ]
}
</script>
@endsection

@section('styles')
        .features-section{padding:2rem 0 5rem}
        .feat-block{margin-bottom:4rem}
        .feat-block:last-child{margin-bottom:0}
        .feat-block-icon{width:56px;height:56px;border-radius:15px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:1rem}
        .feat-block h2{font-size:1.25rem;font-weight:800;color:#0f172a;margin-bottom:.5rem}
        .feat-block p{font-size:.9rem;color:#64748b;line-height:1.7;max-width:520px}
        .feat-block ul{list-style:none;padding:0;margin:.75rem 0 0}
        .feat-block ul li{font-size:.84rem;color:#475569;padding:.3rem 0;display:flex;align-items:flex-start;gap:.5rem}
        .feat-block ul li i{color:#4F46E5;font-size:.7rem;margin-top:.3rem;flex-shrink:0}
@endsection

@section('content')
    <section class="page-hero">
        <div class="container">
            <h1>{{ $isTr ? 'Dijital' : 'Digital' }} <span class="accent">{{ $isTr ? 'QR Menü Sistemi Özellikleri' : 'QR Menu System Features' }}</span></h1>
            <p class="page-hero-sub">{{ $isTr ? 'Temassız menü sisteminizi güçlendiren tüm özellikler. Online menü yönetimi hiç bu kadar kolay olmamıştı.' : 'All the features that power your contactless menu system. Online menu management has never been this easy.' }}</p>
        </div>
    </section>

    <section class="features-section">
        <div class="container" style="max-width:800px">

            <div class="feat-block">
                <div class="feat-block-icon" style="background:rgba(79,70,229,.12);color:#4F46E5"><i class="bi bi-qr-code"></i></div>
                <h2>{{ $isTr ? 'QR Kod Oluşturma ve Baskı' : 'QR Code Generation & Print' }}</h2>
                <p>{{ $isTr ? 'Restoranınız için benzersiz QR kod oluşturun. Yüksek çözünürlüklü karekod menü dosyasını indirip masalarınıza, tabelalarınıza veya broşürlerinize yerleştirin.' : 'Create a unique QR code for your restaurant. Download the high-resolution QR code file and place it on your tables, signs, or brochures.' }}</p>
                <ul>
                    <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Yüksek çözünürlüklü QR kod indirme' : 'High-resolution QR code download' }}</li>
                    <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Tek QR kod ile tüm menüye erişim' : 'Access entire menu with a single QR code' }}</li>
                    <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Ürün bazlı QR kod oluşturma' : 'Product-level QR code generation' }}</li>
                </ul>
            </div>

            <div class="feat-block">
                <div class="feat-block-icon" style="background:rgba(16,185,129,.12);color:#10b981"><i class="bi bi-grid-3x3-gap"></i></div>
                <h2>{{ $isTr ? 'Sınırsız Kategori ve Ürün Yönetimi' : 'Unlimited Category & Product Management' }}</h2>
                <p>{{ $isTr ? 'Online menü yönetimi ile sınırsız kategori ve ürün ekleyin. Alt kategoriler, sürükle-bırak sıralama ve anlık menü güncelleme ile restoran menü sisteminizi tam kontrol edin.' : 'Add unlimited categories and products with online menu management. Full control of your restaurant menu system with subcategories, drag-and-drop ordering, and instant menu updates.' }}</p>
                <ul>
                    <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Sınırsız kategori ve alt kategori' : 'Unlimited categories and subcategories' }}</li>
                    <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Sürükle-bırak sıralama' : 'Drag-and-drop reordering' }}</li>
                    <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Anlık fiyat ve açıklama güncelleme' : 'Instant price and description updates' }}</li>
                </ul>
            </div>

            <div class="feat-block">
                <div class="feat-block-icon" style="background:rgba(236,72,153,.12);color:#EC4899"><i class="bi bi-images"></i></div>
                <h2>{{ $isTr ? 'Fotoğraflı Dijital Menü' : 'Digital Menu with Photos' }}</h2>
                <p>{{ $isTr ? 'Ürünlerinize fotoğraf ekleyerek fotoğraflı menü oluşturun. Görsel zenginlik müşterilerinizin sipariş kararını kolaylaştırır ve satışlarınızı artırır.' : 'Create a photo menu by adding images to your products. Visual richness makes it easier for customers to decide and increases your sales.' }}</p>
                <ul>
                    <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Ürün ve kategori fotoğrafları' : 'Product and category photos' }}</li>
                    <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Otomatik boyutlandırma ve optimizasyon' : 'Automatic resizing and optimization' }}</li>
                </ul>
            </div>

            <div class="feat-block">
                <div class="feat-block-icon" style="background:rgba(251,191,36,.12);color:#FBBF24"><i class="bi bi-star"></i></div>
                <h2>{{ $isTr ? 'Müşteri Değerlendirme Sistemi' : 'Customer Review System' }}</h2>
                <p>{{ $isTr ? 'Müşterileriniz QR menü üzerinden doğrudan değerlendirme bıraksın. Puanları ve yorumları restoran yönetim panelinden takip edin.' : 'Let your customers leave reviews directly through the QR menu. Track ratings and comments from the restaurant management panel.' }}</p>
                <ul>
                    <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? '5 yıldızlı puanlama' : '5-star rating' }}</li>
                    <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Yorum ve puan yönetimi' : 'Review and rating management' }}</li>
                    <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Günlük tekrar koruma sistemi' : 'Daily duplicate protection' }}</li>
                </ul>
            </div>

            <div class="feat-block">
                <div class="feat-block-icon" style="background:rgba(99,102,241,.12);color:#6366f1"><i class="bi bi-people"></i></div>
                <h2>{{ $isTr ? 'Çoklu Kullanıcı ve Rol Yönetimi' : 'Multi-User & Role Management' }}</h2>
                <p>{{ $isTr ? 'Ekibinizi restoran yönetim paneline davet edin. Owner, admin ve personel rolleriyle yetkilendirme yapın.' : 'Invite your team to the restaurant management panel. Assign permissions with owner, admin, and staff roles.' }}</p>
                <ul>
                    <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Owner, admin, personel rolleri' : 'Owner, admin, staff roles' }}</li>
                    <li><i class="bi bi-check-circle-fill"></i> {{ $isTr ? 'Rol bazlı yetkilendirme' : 'Role-based permissions' }}</li>
                </ul>
            </div>

            <div class="feat-block">
                <div class="feat-block-icon" style="background:rgba(236,72,153,.12);color:#EC4899"><i class="bi bi-instagram"></i></div>
                <h2>{{ $isTr ? 'Sosyal Medya Entegrasyonu' : 'Social Media Integration' }}</h2>
                <p>{{ $isTr ? 'Instagram, Facebook, X ve WhatsApp hesaplarınızı dijital menü sayfanıza ekleyin. Müşterileriniz sizi kolayca takip etsin.' : 'Add your Instagram, Facebook, X, and WhatsApp accounts to your digital menu page. Let your customers follow you easily.' }}</p>
            </div>

            <div class="feat-block">
                <div class="feat-block-icon" style="background:rgba(168,85,247,.12);color:#A855F7"><i class="bi bi-phone"></i></div>
                <h2>{{ $isTr ? 'Mobil Uyumlu Menü Tasarımı' : 'Mobile Friendly Menu Design' }}</h2>
                <p>{{ $isTr ? 'Tüm cihazlarda kusursuz görüntülenen mobil uyumlu menü. Müşterileriniz uygulama indirmeden telefonlarıyla karekod menüyü tarayıp anında erişsin.' : 'Mobile-friendly menu that displays perfectly on all devices. Your customers can scan the QR code menu with their phones and access instantly without downloading an app.' }}</p>
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

