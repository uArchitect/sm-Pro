@extends('layouts.public')

@php
    $locale = app()->getLocale();
    $isTr = $locale === 'tr';
@endphp

@section('title', $isTr
    ? 'Gizlilik Politikası ve KVKK | Sipariş Masanda'
    : 'Privacy Policy & GDPR | Siparis Masanda')

@section('meta_description', $isTr
    ? 'Sipariş Masanda gizlilik politikası ve KVKK aydınlatma metni. Kişisel verilerinizin nasıl işlendiğini öğrenin.'
    : 'Siparis Masanda privacy policy and GDPR information. Learn how your personal data is processed.')

@section('meta_keywords', $isTr
    ? 'gizlilik politikası, KVKK, kişisel verilerin korunması, sipariş masanda gizlilik, veri güvenliği, çerez politikası'
    : 'privacy policy, GDPR, personal data protection, siparis masanda privacy, data security, cookie policy')

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {"@@type": "ListItem", "position": 1, "name": "{{ $isTr ? 'Ana Sayfa' : 'Home' }}", "item": "{{ locale_route('home') }}"},
        {"@@type": "ListItem", "position": 2, "name": "{{ $isTr ? 'Gizlilik Politikası' : 'Privacy Policy' }}", "item": "{{ locale_route('privacy') }}"}
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
            <h1><span class="accent">{{ $isTr ? 'Gizlilik Politikası' : 'Privacy Policy' }}</span></h1>
            <p class="page-hero-sub">{{ $isTr ? 'Kişisel verilerinizin korunması bizim için önemlidir.' : 'Protecting your personal data is important to us.' }}</p>
        </div>
    </section>

    <section class="legal-section">
        <div class="container">
            <div class="legal-content">
                @if($isTr)
                <h2>1. Veri Sorumlusu</h2>
                <p>Bu gizlilik politikası, <strong>Sipariş Masanda</strong> ("Platform") tarafından sunulan hizmetler kapsamında kişisel verilerin işlenmesine ilişkin 6698 sayılı Kişisel Verilerin Korunması Kanunu (KVKK) uyarınca aydınlatma yükümlülüğünü yerine getirmek amacıyla hazırlanmıştır.</p>

                <h2>2. Toplanan Kişisel Veriler</h2>
                <p>Platform üzerinden aşağıdaki kişisel veriler toplanabilir:</p>
                <ul>
                    <li>Ad, soyad, e-posta adresi (hesap oluşturma)</li>
                    <li>Restoran adı, adresi, telefon numarası (işletme bilgileri)</li>
                    <li>IP adresi ve tarayıcı bilgileri (ziyaret ve değerlendirme kayıtları)</li>
                    <li>Çerez verileri (oturum yönetimi ve dil tercihi)</li>
                </ul>

                <h2>3. Verilerin İşlenme Amaçları</h2>
                <p>Kişisel verileriniz aşağıdaki amaçlarla işlenmektedir:</p>
                <ul>
                    <li>Hesap oluşturma ve kimlik doğrulama</li>
                    <li>Dijital menü hizmetinin sunulması</li>
                    <li>Müşteri değerlendirme sisteminin yönetimi</li>
                    <li>Destek taleplerinin işlenmesi</li>
                    <li>Hizmet kalitesinin iyileştirilmesi</li>
                    <li>Yasal yükümlülüklerin yerine getirilmesi</li>
                </ul>

                <h2>4. Verilerin Aktarılması</h2>
                <p>Kişisel verileriniz, yasal zorunluluklar dışında üçüncü taraflarla paylaşılmaz. Sunucu altyapısı için güvenilir barındırma hizmetleri kullanılmaktadır.</p>

                <h2>5. Veri Güvenliği</h2>
                <p>Verilerinizin güvenliği için teknik ve idari tedbirler alınmaktadır. Şifreler hash algoritmasıyla korunmakta, iletişim şifreleme protokolleri ile güvence altına alınmaktadır.</p>

                <h2>6. Çerez Politikası</h2>
                <p>Platform, oturum yönetimi ve dil tercihi için çerezler kullanmaktadır. Çerezler kişisel olarak tanımlanabilir bilgi içermez.</p>

                <h2>7. Haklarınız</h2>
                <p>KVKK kapsamında aşağıdaki haklara sahipsiniz:</p>
                <ul>
                    <li>Kişisel verilerinizin işlenip işlenmediğini öğrenme</li>
                    <li>İşlenmişse buna ilişkin bilgi talep etme</li>
                    <li>İşlenme amacını ve amacına uygun kullanılıp kullanılmadığını öğrenme</li>
                    <li>Eksik veya yanlış işlenmiş verilerin düzeltilmesini isteme</li>
                    <li>KVKK'nın 7. maddesi kapsamında silinmesini veya yok edilmesini isteme</li>
                </ul>

                <h2>8. İletişim</h2>
                <p>Gizlilik politikası ile ilgili sorularınız için <a href="{{ locale_route('contact') }}" style="color:#6366F1">iletişim sayfamızdan</a> bize ulaşabilirsiniz.</p>
                @else
                <h2>1. Data Controller</h2>
                <p>This privacy policy has been prepared by <strong>Sipariş Masanda</strong> ("Platform") to fulfill its obligation to inform users regarding the processing of personal data within the scope of services provided.</p>

                <h2>2. Personal Data Collected</h2>
                <p>The following personal data may be collected through the Platform:</p>
                <ul>
                    <li>Name, surname, email address (account creation)</li>
                    <li>Restaurant name, address, phone number (business information)</li>
                    <li>IP address and browser information (visit and review records)</li>
                    <li>Cookie data (session management and language preference)</li>
                </ul>

                <h2>3. Purposes of Data Processing</h2>
                <p>Your personal data is processed for the following purposes:</p>
                <ul>
                    <li>Account creation and identity verification</li>
                    <li>Providing digital menu services</li>
                    <li>Managing the customer review system</li>
                    <li>Processing support requests</li>
                    <li>Improving service quality</li>
                    <li>Fulfilling legal obligations</li>
                </ul>

                <h2>4. Data Transfer</h2>
                <p>Your personal data is not shared with third parties except as required by law. Trusted hosting services are used for server infrastructure.</p>

                <h2>5. Data Security</h2>
                <p>Technical and administrative measures are taken to ensure the security of your data. Passwords are protected with hash algorithms, and communications are secured with encryption protocols.</p>

                <h2>6. Cookie Policy</h2>
                <p>The Platform uses cookies for session management and language preference. Cookies do not contain personally identifiable information.</p>

                <h2>7. Your Rights</h2>
                <p>You have the following rights:</p>
                <ul>
                    <li>Learning whether your personal data is being processed</li>
                    <li>Requesting information about processing if it has been done</li>
                    <li>Learning the purpose of processing and whether it is used in accordance with its purpose</li>
                    <li>Requesting correction of incomplete or incorrect data</li>
                    <li>Requesting deletion or destruction of your data</li>
                </ul>

                <h2>8. Contact</h2>
                <p>For questions about this privacy policy, you can reach us through our <a href="{{ locale_route('contact') }}" style="color:#6366F1">contact page</a>.</p>
                @endif

                <div class="legal-update">{{ $isTr ? 'Son güncelleme: Mart 2026' : 'Last updated: March 2026' }}</div>
            </div>
        </div>
    </section>
@endsection
