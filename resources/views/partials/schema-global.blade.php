@php
    $locale = app()->getLocale();
    $isTr = $locale === 'tr';
    $baseUrl = url('/');
@endphp
{{-- Organization + WebSite: tüm sayfalarda rich snippet (Knowledge Panel, sitelinks) için --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Organization",
    "@@id": "{{ $baseUrl }}/#organization",
    "name": "Sipariş Masanda",
    "url": "{{ $baseUrl }}",
    "logo": {
        "@@type": "ImageObject",
        "url": "{{ asset('og-cover.svg') }}"
    },
    "description": "{{ $isTr ? 'Restoran ve kafeler için dijital QR menü platformu' : 'Digital QR menu platform for restaurants and cafes' }}",
    "foundingDate": "2025",
    "address": {
        "@@type": "PostalAddress",
        "addressCountry": "TR"
    },
    "contactPoint": {
        "@@type": "ContactPoint",
        "contactType": "customer service",
        "email": "destek@siparismasanda.com",
        "telephone": "+90-507-892-84-90",
        "availableLanguage": ["Turkish", "English"],
        "areaServed": "TR",
        "url": "{{ route('contact') }}"
    },
    "sameAs": ["https://wa.me/905078928490"]
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebSite",
    "@@id": "{{ $baseUrl }}/#website",
    "url": "{{ $baseUrl }}",
    "name": "Sipariş Masanda",
    "description": "{{ $isTr ? 'Restoran ve kafeler için dijital QR menü sistemi' : 'Digital QR menu system for restaurants and cafes' }}",
    "publisher": { "@@id": "{{ $baseUrl }}/#organization" },
    "inLanguage": ["tr", "en"]
}
</script>
