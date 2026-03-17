@extends('layouts.public')

@php
    $locale = app()->getLocale();
    $isTr = $locale === 'tr';
@endphp

@section('title', $isTr
    ? 'İletişim | Sipariş Masanda Destek ve Satış'
    : 'Contact | Siparis Masanda Support & Sales')

@section('meta_description', $isTr
    ? 'Sipariş Masanda ile iletişime geç. Sorularını yanıtlayalım, demo ayarlayalım. E-posta, WhatsApp ve form ile ulaş.'
    : 'Get in touch with Siparis Masanda. Let us answer your questions and set up a demo. Reach us via email, WhatsApp and form.')

@section('meta_keywords', $isTr
    ? 'sipariş masanda iletişim, qr menü destek, dijital menü iletişim'
    : 'siparis masanda contact, qr menu support, digital menu contact')

@section('schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {"@@type": "ListItem", "position": 1, "name": "{{ $isTr ? 'Ana Sayfa' : 'Home' }}", "item": "{{ url('/') }}"},
        {"@@type": "ListItem", "position": 2, "name": "{{ $isTr ? 'İletişim' : 'Contact' }}", "item": "{{ route('contact') }}"}
    ]
}
</script>
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Organization",
    "name": "Sipariş Masanda",
    "url": "https://siparismasanda.com/",
    "logo": "{{ asset('og-cover.svg') }}",
    "contactPoint": {
        "@@type": "ContactPoint",
        "contactType": "customer service",
        "email": "destek@siparismasanda.com",
        "telephone": "+90-507-892-84-90",
        "availableLanguage": ["Turkish", "English"],
        "contactOption": "TollFree",
        "areaServed": "TR"
    },
    "sameAs": [
        "https://wa.me/905078928490"
    ]
}
</script>
@endsection

@section('styles')
        .contact-section{padding:2rem 0 5rem}
        .contact-card{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:18px;padding:1.75rem;height:100%;transition:all .25s}
        .contact-card:hover{background:rgba(255,255,255,.05);border-color:rgba(255,107,53,.15);transform:translateY(-3px)}
        .contact-card-icon{width:48px;height:48px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;margin-bottom:.75rem}
        .contact-card h3{font-size:.95rem;font-weight:700;color:#fff;margin-bottom:.3rem}
        .contact-card p{font-size:.82rem;color:rgba(255,255,255,.45);line-height:1.6;margin:0}
        .contact-card a{color:#FF8C42;text-decoration:none;font-weight:600}
        .contact-card a:hover{text-decoration:underline}
        .contact-form{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:18px;padding:2rem;max-width:640px;margin:2.5rem auto 0}
        .cf-label{display:block;font-size:.75rem;font-weight:600;color:rgba(255,255,255,.5);margin-bottom:.3rem}
        .cf-input{width:100%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:#fff;border-radius:10px;padding:.6rem .85rem;font-size:.85rem;font-family:inherit;outline:none;margin-bottom:.85rem;transition:border-color .2s}
        .cf-input::placeholder{color:rgba(255,255,255,.2)}
        .cf-input:focus{border-color:#FF6B35;box-shadow:0 0 0 3px rgba(255,107,53,.12)}
        textarea.cf-input{resize:vertical;min-height:110px}
        .response-time{font-size:.78rem;color:rgba(255,255,255,.35);margin-top:.75rem;text-align:center}
@endsection

@section('content')
    <section class="page-hero">
        <div class="container">
            <h1><span class="accent">{{ $isTr ? 'Bize Ulaşın' : 'Get in Touch' }}</span></h1>
            <p class="page-hero-sub">{{ $isTr ? 'Sorularınızı yanıtlayalım, size özel demo ayarlayalım. Dijital menü yolculuğunuzda yanınızdayız.' : 'Let us answer your questions and set up a personalized demo. We are with you on your digital menu journey.' }}</p>
        </div>
    </section>

    <section class="contact-section">
        <div class="container">
            <div class="row g-4 justify-content-center" style="max-width:860px;margin:0 auto">
                <div class="col-md-4">
                    <div class="contact-card text-center">
                        <div class="contact-card-icon mx-auto" style="background:rgba(37,211,102,.12);color:#25D366"><i class="bi bi-whatsapp"></i></div>
                        <h3>WhatsApp</h3>
                        <p><a href="https://wa.me/905078928490" target="_blank">+90 507 892 84 90</a></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-card text-center">
                        <div class="contact-card-icon mx-auto" style="background:rgba(255,107,53,.12);color:#FF6B35"><i class="bi bi-envelope"></i></div>
                        <h3>{{ $isTr ? 'E-posta' : 'Email' }}</h3>
                        <p><a href="mailto:destek@siparismasanda.com">destek@siparismasanda.com</a></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-card text-center">
                        <div class="contact-card-icon mx-auto" style="background:rgba(99,102,241,.12);color:#6366f1"><i class="bi bi-clock"></i></div>
                        <h3>{{ $isTr ? 'Yanıt Süresi' : 'Response Time' }}</h3>
                        <p>{{ $isTr ? 'Genellikle 2 saat içinde yanıt veririz' : 'We usually respond within 2 hours' }}</p>
                    </div>
                </div>
            </div>

            <div class="contact-form" id="contactFormWrap">
                @if(session('contact_success'))
                <div style="text-align:center;padding:2rem 1rem">
                    <div style="width:56px;height:56px;border-radius:16px;background:rgba(16,185,129,.12);display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#10b981;margin:0 auto .85rem">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div style="font-size:1rem;font-weight:700;color:#fff;margin-bottom:.35rem">{{ $isTr ? 'Mesajınız Gönderildi!' : 'Message Sent!' }}</div>
                    <div style="font-size:.84rem;color:rgba(255,255,255,.5);line-height:1.6">{{ $isTr ? 'En kısa sürede size geri dönüş yapacağız.' : 'We will get back to you as soon as possible.' }}</div>
                </div>
                @else
                <h3 style="font-size:1rem;font-weight:700;color:#fff;margin-bottom:1.25rem;text-align:center">
                    <i class="bi bi-chat-dots text-warning me-1"></i>
                    {{ $isTr ? 'Mesaj Gönderin' : 'Send a Message' }}
                </h3>
                <form id="contactForm">
                    <label class="cf-label">{{ $isTr ? 'Adınız' : 'Your Name' }} *</label>
                    <input type="text" name="name" id="cf_name" class="cf-input" placeholder="{{ $isTr ? 'Adınız Soyadınız' : 'Your Full Name' }}" required>

                    <label class="cf-label">{{ $isTr ? 'E-posta' : 'Email' }} *</label>
                    <input type="email" name="email" id="cf_email" class="cf-input" placeholder="{{ $isTr ? 'ornek@restoran.com' : 'example@restaurant.com' }}" required>

                    <label class="cf-label">{{ $isTr ? 'Telefon' : 'Phone' }}</label>
                    <input type="tel" name="phone" id="cf_phone" class="cf-input" placeholder="{{ $isTr ? '05xx xxx xx xx' : '+90 5xx xxx xx xx' }}">

                    <label class="cf-label">{{ $isTr ? 'Mesajınız' : 'Your Message' }} *</label>
                    <textarea name="message" id="cf_message" class="cf-input" placeholder="{{ $isTr ? 'Nasıl yardımcı olabiliriz?' : 'How can we help you?' }}" required></textarea>

                    <div id="cf_errors" style="display:none;color:#fca5a5;font-size:.78rem;margin-bottom:.75rem;line-height:1.5"></div>

                    <button type="submit" id="cf_btn" class="hero-btn-primary w-100 justify-content-center" style="padding:.7rem 1.5rem">
                        <i class="bi bi-send"></i> <span>{{ $isTr ? 'Gönder' : 'Send' }}</span>
                    </button>
                </form>
                <div class="response-time">
                    <i class="bi bi-shield-check me-1"></i>
                    {{ $isTr ? 'Bilgileriniz gizli tutulur. Genellikle 2 saat içinde yanıt veririz.' : 'Your information is kept private. We usually respond within 2 hours.' }}
                </div>
                @endif
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function () {
                var form = document.getElementById('contactForm');
                if (!form) return;

                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    var btn = document.getElementById('cf_btn');
                    var errBox = document.getElementById('cf_errors');
                    errBox.style.display = 'none';
                    errBox.innerHTML = '';
                    btn.disabled = true;
                    btn.querySelector('span').textContent = '{{ $isTr ? "Gönderiliyor..." : "Sending..." }}';

                    fetch('{{ url("/iletisim/gonder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            name: document.getElementById('cf_name').value,
                            email: document.getElementById('cf_email').value,
                            phone: document.getElementById('cf_phone').value,
                            message: document.getElementById('cf_message').value
                        })
                    })
                    .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
                    .then(function (result) {
                        if (result.ok && result.data.success) {
                            document.getElementById('contactFormWrap').innerHTML =
                                '<div style="text-align:center;padding:2rem 1rem">' +
                                '<div style="width:56px;height:56px;border-radius:16px;background:rgba(16,185,129,.12);display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#10b981;margin:0 auto .85rem"><i class="bi bi-check-circle-fill"></i></div>' +
                                '<div style="font-size:1rem;font-weight:700;color:#fff;margin-bottom:.35rem">{{ $isTr ? "Mesajınız Gönderildi!" : "Message Sent!" }}</div>' +
                                '<div style="font-size:.84rem;color:rgba(255,255,255,.5);line-height:1.6">{{ $isTr ? "En kısa sürede size geri dönüş yapacağız." : "We will get back to you as soon as possible." }}</div>' +
                                '</div>';
                        } else {
                            var msgs = [];
                            if (result.data.errors) {
                                Object.keys(result.data.errors).forEach(function (k) {
                                    msgs = msgs.concat(result.data.errors[k]);
                                });
                            } else if (result.data.message) {
                                msgs.push(result.data.message);
                            }
                            errBox.innerHTML = msgs.join('<br>');
                            errBox.style.display = 'block';
                            btn.disabled = false;
                            btn.querySelector('span').textContent = '{{ $isTr ? "Gönder" : "Send" }}';
                        }
                    })
                    .catch(function () {
                        errBox.innerHTML = '{{ $isTr ? "Bir hata oluştu. Lütfen tekrar deneyin." : "An error occurred. Please try again." }}';
                        errBox.style.display = 'block';
                        btn.disabled = false;
                        btn.querySelector('span').textContent = '{{ $isTr ? "Gönder" : "Send" }}';
                    });
                });
            });
            </script>
        </div>
    </section>
@endsection
