<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.title') }} — {{ __('common.app_name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: 'Inter', sans-serif; overflow: hidden; }

        /* ══ LAYOUT ══ */
        .page { display: flex; height: 100vh; }

        /* ══ LEFT DARK PANEL ══ */
        .panel-dark {
            flex: 1; position: relative; overflow: hidden;
            display: flex; flex-direction: column; justify-content: space-between;
            padding: 2.5rem 3rem;
        }
        .panel-dark::before {
            content: ''; position: absolute; inset: 0; z-index: 0;
            background: url('https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1400&q=90&auto=format&fit=crop') center/cover no-repeat;
        }
        .panel-dark::after {
            content: ''; position: absolute; inset: 0; z-index: 1;
            background: linear-gradient(170deg, rgba(12,8,4,.9) 0%, rgba(15,10,5,.8) 50%, rgba(8,5,2,.93) 100%);
        }
        .panel-dark > * { position: relative; z-index: 2; }

        /* Inline logo */
        .brand-inline { display: flex; align-items: center; gap: .85rem; }
        .brand-icon {
            width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
            background: linear-gradient(135deg, #FF6B35, #FF8C42);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; color: #fff;
            box-shadow: 0 8px 24px rgba(255,107,53,.5);
        }
        .brand-text-wrap { line-height: 1; }
        .brand-name  { font-size: 1.05rem; font-weight: 800; color: #fff; letter-spacing: -.01em; }
        .brand-tagline { font-size: .68rem; color: rgba(255,255,255,.8); font-weight: 500; margin-top: .2rem; letter-spacing: .04em; text-transform: uppercase; }

        /* Hero */
        .hero-block { max-width: 460px; }
        .hero-block h1 {
            font-size: clamp(2rem, 3.5vw, 2.75rem); font-weight: 900; color: #fff;
            line-height: 1.12; letter-spacing: -.02em; margin-bottom: 1rem;
        }
        .hero-block h1 em {
            font-style: normal;
            background: linear-gradient(90deg, #FF6B35, #FFB347);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .hero-block p { font-size: .92rem; color: #fff; line-height: 1.72; max-width: 360px; }

        /* Pills */
        .pill-row { display: flex; flex-wrap: wrap; gap: .55rem; margin-top: 2rem; }
        .pill {
            display: inline-flex; align-items: center; gap: .45rem;
            padding: .38rem .9rem; border-radius: 999px;
            background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12);
            font-size: .78rem; color: rgba(255,255,255,.75); font-weight: 500;
        }
        .pill i { color: #FF6B35; font-size: .8rem; }

        /* Left footer */
        .left-footer { font-size: .75rem; color: #fff; letter-spacing: .02em; }

        /* ══ RIGHT WHITE PANEL ══ */
        .panel-white {
            width: 490px; flex-shrink: 0; background: #fff;
            display: flex; flex-direction: column;
            box-shadow: -24px 0 80px rgba(0,0,0,.18);
            overflow: hidden; /* clip the slider */
            position: relative;
        }

        /* Sliding wrapper — holds login + register side by side */
        .slide-wrap {
            display: flex;
            width: 200%;
            height: 100%;
            transition: transform .45s cubic-bezier(.77,0,.175,1);
        }
        .panel-white.show-reg .slide-wrap { transform: translateX(-50%); }

        /* Each slide takes exactly half = full panel width */
        .slide-pane {
            width: 50%;
            flex-shrink: 0;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2.75rem 3rem;
        }
        /* Register has more fields, allow scroll without centering */
        .slide-pane.reg-pane { justify-content: flex-start; padding-top: 3rem; }

        /* ── Form typography ── */
        .form-eyebrow { font-size: .68rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: #FF6B35; margin-bottom: .5rem; }
        .form-title   { font-size: 1.6rem; font-weight: 900; color: #0f172a; letter-spacing: -.02em; line-height: 1.2; margin-bottom: .35rem; }
        .form-sub     { font-size: .82rem; color: #94a3b8; margin-bottom: 1.75rem; line-height: 1.5; }

        /* Section divider (register) */
        .sec-div {
            font-size: .65rem; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; color: #94a3b8;
            border-bottom: 1px solid #f1f5f9; padding-bottom: .45rem;
            margin: 1.1rem 0 .85rem;
        }

        /* ── Inputs ── */
        .f-group { margin-bottom: .95rem; }
        .f-label { display: block; font-size: .75rem; font-weight: 600; color: #475569; margin-bottom: .4rem; }
        .f-field  { position: relative; }
        .f-icon   { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #cbd5e1; font-size: .88rem; pointer-events: none; }
        .f-input {
            width: 100%; height: 44px; padding: 0 .9rem 0 2.5rem;
            border: 1.5px solid #e2e8f0; border-radius: 10px;
            font-size: .875rem; font-family: 'Inter', sans-serif;
            color: #0f172a; background: #f8fafc; outline: none;
            transition: border-color .15s, box-shadow .15s, background .15s;
            -webkit-appearance: none;
        }
        .f-input::placeholder { color: #c0c9d4; }
        .f-input:focus { border-color: #FF6B35; background: #fff; box-shadow: 0 0 0 3px rgba(255,107,53,.12); }
        .f-input.is-invalid { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,.1); }
        .f-err { font-size: .73rem; color: #ef4444; margin-top: .3rem; }
        .pw-toggle {
            position: absolute; right: 11px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; color: #cbd5e1; font-size: .88rem; padding: 0;
            transition: color .15s;
        }
        .pw-toggle:hover { color: #94a3b8; }

        /* ── Remember ── */
        .remember-row { display: flex; align-items: center; gap: .55rem; margin: .2rem 0 1.4rem; }
        .f-check {
            width: 16px; height: 16px; border-radius: 4px;
            appearance: none; -webkit-appearance: none;
            border: 1.5px solid #cbd5e1; background: #f8fafc;
            cursor: pointer; transition: all .15s; flex-shrink: 0;
        }
        .f-check:checked { background: #FF6B35; border-color: #FF6B35; }
        .f-check:checked::after { content: '✓'; display: block; text-align: center; font-size: .65rem; color: #fff; line-height: 14px; }
        .remember-label { font-size: .8rem; color: #64748b; cursor: pointer; }

        /* ── Submit ── */
        .btn-submit {
            width: 100%; height: 48px;
            background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%);
            border: none; border-radius: 11px;
            color: #fff; font-weight: 700; font-size: .9rem; font-family: 'Inter', sans-serif;
            cursor: pointer; box-shadow: 0 4px 20px rgba(255,107,53,.36);
            transition: transform .15s, box-shadow .15s;
            display: flex; align-items: center; justify-content: center; gap: .5rem;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(255,107,53,.5); }
        .btn-submit:active { transform: translateY(0); }

        /* ── Error box ── */
        .err-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: .65rem .9rem; margin-bottom: 1.1rem; }
        .err-box p { font-size: .78rem; color: #dc2626; margin: .1rem 0; line-height: 1.5; }

        /* ── Footer link ── */
        .form-footer {
            margin-top: 1.35rem; padding-top: 1.35rem;
            border-top: 1px solid #f1f5f9;
            text-align: center; font-size: .8rem; color: #94a3b8;
        }
        .form-footer a { color: #FF6B35; text-decoration: none; font-weight: 700; cursor: pointer; }
        .form-footer a:hover { text-decoration: underline; }

        /* ══ RESPONSIVE ══ */
        @media (max-width: 900px) {
            .panel-dark { display: none; }
            .panel-white { width: 100%; box-shadow: none; }
            .slide-pane { padding: 2.5rem 2rem; }
        }
        @media (max-width: 480px) {
            .slide-pane { padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>

<div class="page">

    {{-- ═══ LEFT: Dark photo panel ═══ --}}
    <div class="panel-dark">
        <div class="brand-inline">
            <div class="brand-icon"><i class="bi bi-qr-code-scan"></i></div>
            <div class="brand-text-wrap">
                <div class="brand-name">{{ __('common.app_name') }}</div>
                <div class="brand-tagline">{{ __('auth.brand_tagline') }}</div>
            </div>
        </div>

        <div class="hero-block">
            <h1>{!! __('auth.hero_title') !!}</h1>
            <p>{{ __('auth.hero_sub') }}</p>
            <div class="pill-row">
                <span class="pill"><i class="bi bi-qr-code"></i> {{ __('auth.pill_qr') }}</span>
                <span class="pill"><i class="bi bi-images"></i> {{ __('auth.pill_photos') }}</span>
                <span class="pill"><i class="bi bi-people"></i> {{ __('auth.pill_team') }}</span>
                <span class="pill"><i class="bi bi-phone"></i> {{ __('auth.pill_mobile') }}</span>
            </div>
        </div>

        <div class="left-footer">© {{ date('Y') }} {{ __('common.app_name') }} · {{ __('auth.copyright') }}</div>
    </div>

    {{-- ═══ RIGHT: Sliding form panel ═══ --}}
    <div class="panel-white {{ ($showRegister ?? false) || $errors->has('firma_adi') || $errors->has('restoran_adi') ? 'show-reg' : '' }}" id="panelRight">
        <div class="slide-wrap" id="slideWrap">

            {{-- ─── LOGIN PANE ─── --}}
            <div class="slide-pane">
                <div class="form-eyebrow">{{ __('auth.management') }}</div>
                <div class="form-title">{{ __('auth.welcome_back') }}</div>
                <div class="form-sub">{{ __('auth.login_sub') }}</div>

                @if($errors->has('email') || $errors->has('password'))
                <div class="err-box">
                    @foreach($errors->get('email') as $e)<p><i class="bi bi-exclamation-circle me-1"></i>{{ $e }}</p>@endforeach
                    @foreach($errors->get('password') as $e)<p><i class="bi bi-exclamation-circle me-1"></i>{{ $e }}</p>@endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" novalidate>
                    @csrf
                    <div class="f-group">
                        <label class="f-label" for="email">{{ __('auth.email') }}</label>
                        <div class="f-field">
                            <i class="bi bi-envelope f-icon"></i>
                            <input type="email" id="email" name="email"
                                   class="f-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                   value="{{ old('email') }}" placeholder="{{ __('auth.email_placeholder') }}"
                                   required autofocus autocomplete="email">
                        </div>
                    </div>
                    <div class="f-group">
                        <label class="f-label" for="password">{{ __('auth.password') }}</label>
                        <div class="f-field">
                            <i class="bi bi-lock f-icon"></i>
                            <input type="password" id="password" name="password"
                                   class="f-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                   placeholder="••••••••" required autocomplete="current-password"
                                   style="padding-right:2.6rem">
                            <button type="button" class="pw-toggle" onclick="togglePw('password','pwIcon')">
                                <i class="bi bi-eye" id="pwIcon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="remember-row">
                        <input type="checkbox" id="remember" name="remember" class="f-check">
                        <label for="remember" class="remember-label">{{ __('auth.remember') }}</label>
                    </div>
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-box-arrow-in-right"></i> {{ __('auth.login_btn') }}
                    </button>
                </form>

                <div class="form-footer">
                    {{ __('auth.no_account') }}
                    <a onclick="openReg()" href="{{ route('register') }}">{{ __('auth.create_account') }}</a>
                </div>
            </div>

            {{-- ─── REGISTER PANE ─── --}}
            <div class="slide-pane reg-pane">
                <div class="form-eyebrow">{{ __('auth.new_account') }}</div>
                <div class="form-title">{{ __('auth.register_title') }}</div>
                <div class="form-sub">{{ __('auth.register_sub') }}</div>

                @if($errors->has('firma_adi') || $errors->has('restoran_adi') || $errors->has('name') || ($errors->has('email') && ($showRegister ?? false)))
                <div class="err-box">
                    @foreach($errors->all() as $e)<p><i class="bi bi-exclamation-circle me-1"></i>{{ $e }}</p>@endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('register') }}" novalidate>
                    @csrf

                    <div class="sec-div">{{ __('auth.company_info') }}</div>
                    <div class="f-group">
                        <label class="f-label">{{ __('auth.company_name') }}</label>
                        <div class="f-field">
                            <i class="bi bi-building f-icon"></i>
                            <input type="text" name="firma_adi"
                                   class="f-input {{ $errors->has('firma_adi') ? 'is-invalid' : '' }}"
                                   value="{{ old('firma_adi') }}" placeholder="{{ __('auth.company_placeholder') }}" required>
                        </div>
                        @error('firma_adi')<div class="f-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="f-group">
                        <label class="f-label">{{ __('auth.restaurant_name') }}</label>
                        <div class="f-field">
                            <i class="bi bi-shop f-icon"></i>
                            <input type="text" name="restoran_adi"
                                   class="f-input {{ $errors->has('restoran_adi') ? 'is-invalid' : '' }}"
                                   value="{{ old('restoran_adi') }}" placeholder="{{ __('auth.restaurant_placeholder') }}" required>
                        </div>
                        @error('restoran_adi')<div class="f-err">{{ $message }}</div>@enderror
                    </div>

                    <div class="sec-div">{{ __('auth.account_info') }}</div>
                    <div class="f-group">
                        <label class="f-label">{{ __('auth.full_name') }}</label>
                        <div class="f-field">
                            <i class="bi bi-person f-icon"></i>
                            <input type="text" name="name"
                                   class="f-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                   value="{{ old('name') }}" placeholder="{{ __('auth.full_name_placeholder') }}" required>
                        </div>
                        @error('name')<div class="f-err">{{ $message }}</div>@enderror
                    </div>
                    <div class="f-group">
                        <label class="f-label">{{ __('auth.email') }}</label>
                        <div class="f-field">
                            <i class="bi bi-envelope f-icon"></i>
                            <input type="email" name="email"
                                   class="f-input {{ ($errors->has('email') && ($showRegister ?? false)) ? 'is-invalid' : '' }}"
                                   value="{{ old('email') }}" placeholder="{{ __('auth.email_placeholder') }}" required>
                        </div>
                    </div>
                    <div class="f-group">
                        <label class="f-label">{{ __('auth.password') }}</label>
                        <div class="f-field">
                            <i class="bi bi-lock f-icon"></i>
                            <input type="password" name="password"
                                   class="f-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                   placeholder="{{ __('auth.password_min') }}" required minlength="8"
                                   style="padding-right:2.6rem">
                            <button type="button" class="pw-toggle" onclick="togglePw('reg-password','regPwIcon')">
                                <i class="bi bi-eye" id="regPwIcon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="f-group" style="margin-bottom:1.4rem">
                        <label class="f-label">{{ __('auth.password_confirm') }}</label>
                        <div class="f-field">
                            <i class="bi bi-lock-fill f-icon"></i>
                            <input type="password" name="password_confirmation"
                                   class="f-input" placeholder="{{ __('auth.password_confirm_placeholder') }}" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="bi bi-rocket-takeoff"></i> {{ __('auth.create_btn') }}
                    </button>
                </form>

                <div class="form-footer">
                    {{ __('auth.have_account') }}
                    <a onclick="closeReg()" href="{{ route('login') }}">{{ __('auth.sign_in') }}</a>
                </div>
            </div>

        </div>{{-- /slide-wrap --}}
    </div>{{-- /panel-white --}}

</div>{{-- /page --}}

<script>
const panel = document.getElementById('panelRight');

function openReg(e) {
    if (e) e.preventDefault();
    panel.classList.add('show-reg');
}
function closeReg(e) {
    if (e) e.preventDefault();
    panel.classList.remove('show-reg');
}
function togglePw(inputId, iconId) {
    const inp  = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!inp) return;
    inp.type = inp.type === 'password' ? 'text' : 'password';
    icon.className = inp.type === 'text' ? 'bi bi-eye-slash' : 'bi bi-eye';
}

// Give password field in register pane a proper id
document.querySelector('.reg-pane input[name="password"]').id = 'reg-password';

// Intercept anchor clicks to avoid navigation for slide
document.querySelectorAll('a[onclick]').forEach(a => {
    a.addEventListener('click', function(e) { e.preventDefault(); });
});
</script>
</body>
</html>
