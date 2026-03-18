@extends('layouts.public')

@php
    $locale = app()->getLocale();
    $isTr = $locale === 'tr';
@endphp

@section('title', $isTr
    ? 'Kullanım Koşulları | Sipariş Masanda'
    : 'Terms of Use | Siparis Masanda')

@section('meta_description', $isTr
    ? 'Sipariş Masanda kullanım koşulları. Platform kullanım şartları ve sorumluluklar hakkında bilgi edinin.'
    : 'Siparis Masanda terms of use. Learn about platform usage terms and responsibilities.')

@section('meta_keywords', $isTr
    ? 'kullanım koşulları, kullanım şartları, sipariş masanda şartlar, platform kuralları, hizmet koşulları'
    : 'terms of use, terms of service, siparis masanda terms, platform rules, service conditions')

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {"@@type": "ListItem", "position": 1, "name": "{{ $isTr ? 'Ana Sayfa' : 'Home' }}", "item": "{{ locale_route('home') }}"},
        {"@@type": "ListItem", "position": 2, "name": "{{ $isTr ? 'Kullanım Koşulları' : 'Terms of Use' }}", "item": "{{ locale_route('terms') }}"}
    ]
}
</script>
@endsection

@section('styles')
        .legal-section{padding:2rem 0 5rem}
        .legal-content{max-width:760px;margin:0 auto;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:18px;padding:2.5rem 2rem}
        .legal-content h2{font-size:1.1rem;font-weight:800;color:#fff;margin:2rem 0 .6rem}
        .legal-content h2:first-child{margin-top:0}
        .legal-content p,.legal-content li{font-size:.86rem;color:rgba(255,255,255,.5);line-height:1.8}
        .legal-content ul{padding-left:1.25rem;margin:.5rem 0}
        .legal-content strong{color:rgba(255,255,255,.7)}
        .legal-update{font-size:.75rem;color:rgba(255,255,255,.25);text-align:center;margin-top:1.5rem}
@endsection

@section('content')
    <section class="page-hero">
        <div class="container">
            <h1><span class="accent">{{ $isTr ? 'Kullanım Koşulları' : 'Terms of Use' }}</span></h1>
            <p class="page-hero-sub">{{ $isTr ? 'Platformumuzu kullanmadan önce lütfen aşağıdaki koşulları okuyun.' : 'Please read the following terms before using our platform.' }}</p>
        </div>
    </section>

    <section class="legal-section">
        <div class="container">
            <div class="legal-content">
                @if($isTr)
                <h2>1. Hizmet Tanımı</h2>
                <p><strong>Sipariş Masanda</strong> ("Platform"), restoran, kafe ve yeme-içme işletmelerine dijital QR menü oluşturma ve yönetme hizmeti sunan bir web platformudur.</p>

                <h2>2. Hesap Oluşturma</h2>
                <p>Platformu kullanmak için bir hesap oluşturmanız gerekmektedir. Hesap oluştururken verdiğiniz bilgilerin doğru ve güncel olması sizin sorumluluğunuzdadır. Hesap güvenliğinizden (şifre gizliliği dahil) siz sorumlusunuz.</p>

                <h2>3. Kullanım Kuralları</h2>
                <p>Platform kullanıcıları aşağıdaki kurallara uymakla yükümlüdür:</p>
                <ul>
                    <li>Yasalara aykırı içerik yüklememek</li>
                    <li>Üçüncü tarafların haklarını ihlal etmemek</li>
                    <li>Platformun güvenliğini tehlikeye atacak işlemler yapmamak</li>
                    <li>Spam, zararlı yazılım veya yanıltıcı içerik paylaşmamak</li>
                    <li>Başka kullanıcıların verilerine yetkisiz erişim sağlamamak</li>
                </ul>

                <h2>4. İçerik Sorumluluğu</h2>
                <p>Platforma yüklenen menü içerikleri (ürün adları, açıklamaları, fiyatları, fotoğrafları) tamamen kullanıcının sorumluluğundadır. Sipariş Masanda, kullanıcıların yüklediği içeriklerden dolayı sorumluluk kabul etmez.</p>

                <h2>5. Fikri Mülkiyet</h2>
                <p>Platform arayüzü, tasarımı, logosu ve yazılımı Sipariş Masanda'ya aittir. Kullanıcılar, platforma yükledikleri içerikler üzerindeki haklarını korur.</p>

                <h2>6. Hizmet Değişiklikleri</h2>
                <p>Sipariş Masanda, platformun özelliklerini, fiyatlandırmasını veya kullanım koşullarını önceden bildirimde bulunarak değiştirme hakkını saklı tutar.</p>

                <h2>7. Hesap Askıya Alma ve Sonlandırma</h2>
                <p>Kullanım kurallarına aykırı davranan hesaplar uyarı yapılmaksızın askıya alınabilir veya sonlandırılabilir. Kullanıcılar istedikleri zaman hesaplarını kapatabilir.</p>

                <h2>8. Sorumluluk Sınırlaması</h2>
                <p>Platform "olduğu gibi" sunulmaktadır. Sipariş Masanda, hizmet kesintileri, veri kayıpları veya platform kullanımından doğabilecek dolaylı zararlardan sorumlu değildir.</p>

                <h2>9. Uygulanacak Hukuk</h2>
                <p>Bu kullanım koşulları Türkiye Cumhuriyeti yasalarına tabidir.</p>

                <h2>10. İletişim</h2>
                <p>Kullanım koşulları ile ilgili sorularınız için <a href="{{ locale_route('contact') }}" style="color:#FF8C42">iletişim sayfamızdan</a> bize ulaşabilirsiniz.</p>
                @else
                <h2>1. Service Description</h2>
                <p><strong>Sipariş Masanda</strong> ("Platform") is a web platform that provides digital QR menu creation and management services for restaurants, cafes, and food businesses.</p>

                <h2>2. Account Creation</h2>
                <p>You need to create an account to use the Platform. You are responsible for ensuring that the information you provide when creating an account is accurate and up-to-date. You are responsible for your account security (including password confidentiality).</p>

                <h2>3. Usage Rules</h2>
                <p>Platform users are obligated to comply with the following rules:</p>
                <ul>
                    <li>Not uploading illegal content</li>
                    <li>Not violating the rights of third parties</li>
                    <li>Not performing actions that endanger the security of the Platform</li>
                    <li>Not sharing spam, malware, or misleading content</li>
                    <li>Not gaining unauthorized access to other users' data</li>
                </ul>

                <h2>4. Content Responsibility</h2>
                <p>Menu content uploaded to the Platform (product names, descriptions, prices, photos) is entirely the responsibility of the user. Sipariş Masanda does not accept responsibility for content uploaded by users.</p>

                <h2>5. Intellectual Property</h2>
                <p>The Platform interface, design, logo, and software belong to Sipariş Masanda. Users retain their rights to the content they upload to the platform.</p>

                <h2>6. Service Changes</h2>
                <p>Sipariş Masanda reserves the right to change the platform's features, pricing, or terms of use with prior notice.</p>

                <h2>7. Account Suspension and Termination</h2>
                <p>Accounts that violate usage rules may be suspended or terminated without warning. Users can close their accounts at any time.</p>

                <h2>8. Limitation of Liability</h2>
                <p>The Platform is provided "as is". Sipariş Masanda is not liable for service interruptions, data loss, or indirect damages that may arise from platform use.</p>

                <h2>9. Governing Law</h2>
                <p>These terms of use are governed by the laws of the Republic of Turkey.</p>

                <h2>10. Contact</h2>
                <p>For questions about these terms of use, you can reach us through our <a href="{{ locale_route('contact') }}" style="color:#FF8C42">contact page</a>.</p>
                @endif

                <div class="legal-update">{{ $isTr ? 'Son güncelleme: Mart 2026' : 'Last updated: March 2026' }}</div>
            </div>
        </div>
    </section>
@endsection
