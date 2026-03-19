@php
    $locale = app()->getLocale();
    $isTr = $locale === 'tr';
    $baseUrl = url('/');
    try {
        $reviewAgg = \Illuminate\Support\Facades\DB::table('reviews')
            ->selectRaw('COUNT(*) as cnt, ROUND(AVG(rating), 1) as avg')
            ->first();
    } catch (\Throwable $e) {
        $reviewAgg = (object) ['cnt' => 0, 'avg' => 0];
    }
@endphp
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Organization",
    "@@id": "{{ $baseUrl }}/#organization",
    "name": "Sipariş Masanda",
    "alternateName": "Siparis Masanda",
    "url": "{{ $baseUrl }}",
    "logo": {
        "@@type": "ImageObject",
        "url": "{{ asset('og-cover.svg') }}",
        "width": 512,
        "height": 512
    },
    "description": "{{ $isTr ? 'Restoran ve kafeler için dijital QR menü platformu. Ücretsiz başla, dakikalar içinde menünü oluştur.' : 'Digital QR menu platform for restaurants and cafes. Start free, create your menu in minutes.' }}",
    "slogan": "{{ $isTr ? 'Restoranınızı dijitale taşıyın' : 'Take your restaurant digital' }}",
    "foundingDate": "2025",
    "address": {
        "@@type": "PostalAddress",
        "addressCountry": "TR",
        "addressLocality": "İstanbul"
    },
    "contactPoint": [
        {
            "@@type": "ContactPoint",
            "contactType": "customer service",
            "email": "destek@siparismasanda.com",
            "telephone": "+90-507-892-84-90",
            "availableLanguage": ["Turkish", "English"],
            "areaServed": "TR",
            "url": "{{ locale_route('contact') }}"
        },
        {
            "@@type": "ContactPoint",
            "contactType": "sales",
            "telephone": "+90-507-892-84-90",
            "url": "https://wa.me/905078928490",
            "availableLanguage": ["Turkish", "English"],
            "areaServed": "TR"
        }
    ],
    "sameAs": [
        "https://wa.me/905078928490"
    ],
    "knowsAbout": [
        "{{ $isTr ? 'Dijital menü sistemleri' : 'Digital menu systems' }}",
        "{{ $isTr ? 'QR kod menü' : 'QR code menu' }}",
        "{{ $isTr ? 'Restoran teknolojileri' : 'Restaurant technology' }}"
    ]
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebSite",
    "@@id": "{{ $baseUrl }}/#website",
    "url": "{{ $baseUrl }}",
    "name": "Sipariş Masanda",
    "alternateName": "Siparis Masanda",
    "description": "{{ $isTr ? 'Restoran ve kafeler için dijital QR menü sistemi' : 'Digital QR menu system for restaurants and cafes' }}",
    "publisher": { "@@id": "{{ $baseUrl }}/#organization" },
    "inLanguage": ["tr", "en"],
    "potentialAction": {
        "@@type": "SearchAction",
        "target": "{{ $baseUrl }}/blog?q={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "SoftwareApplication",
    "@@id": "{{ $baseUrl }}/#software",
    "name": "Sipariş Masanda",
    "alternateName": "Siparis Masanda",
    "applicationCategory": "BusinessApplication",
    "applicationSubCategory": "Restaurant Management",
    "operatingSystem": "Web",
    "url": "{{ $baseUrl }}",
    "image": "{{ asset('og-cover.svg') }}",
    "description": "{{ $isTr ? 'Restoran ve kafeler için ücretsiz dijital QR menü oluşturma platformu. Sınırsız kategori ve ürün, fotoğraflı menü, müşteri değerlendirmeleri.' : 'Free digital QR menu creation platform for restaurants and cafes. Unlimited categories and products, photo menu, customer reviews.' }}",
    "featureList": "{{ $isTr ? 'QR kod menü, Sınırsız kategori ve ürün, Fotoğraflı dijital menü, Müşteri değerlendirmeleri, Çoklu kullanıcı, Sosyal medya entegrasyonu, Mobil uyumlu tasarım, Slider yönetimi, Etkinlik duyuruları' : 'QR code menu, Unlimited categories and products, Photo digital menu, Customer reviews, Multi-user, Social media integration, Mobile-friendly design, Slider management, Event announcements' }}",
    "offers": [
        {
            "@@type": "Offer",
            "name": "Basic",
            "price": "0",
            "priceCurrency": "TRY",
            "availability": "https://schema.org/InStock",
            "description": "{{ $isTr ? 'Sınırsız kategori, ürün, QR kod, fotoğraflı menü, değerlendirmeler' : 'Unlimited categories, products, QR code, photo menu, reviews' }}"
        },
        {
            "@@type": "Offer",
            "name": "Premium",
            "price": "649",
            "priceCurrency": "TRY",
            "availability": "https://schema.org/InStock",
            "description": "{{ $isTr ? 'Basic + slider, etkinlik yönetimi, öncelikli destek' : 'Basic + sliders, event management, priority support' }}"
        }
    ],
    @if($reviewAgg->cnt > 0)
    "aggregateRating": {
        "@@type": "AggregateRating",
        "ratingValue": "{{ $reviewAgg->avg }}",
        "ratingCount": "{{ $reviewAgg->cnt }}",
        "bestRating": "5",
        "worstRating": "1"
    },
    @endif
    "publisher": { "@@id": "{{ $baseUrl }}/#organization" }
}
</script>
