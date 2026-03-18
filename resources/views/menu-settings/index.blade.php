@extends('layouts.app')

@section('title', __('menu_settings.title'))
@section('page-title', __('menu_settings.title'))
@section('page-help', __('menu_settings.page_help'))

@push('styles')
<style>
    .ms-grid { display:grid; grid-template-columns:1fr 380px; gap:1.5rem; align-items:start; }
    @media(max-width:1100px){ .ms-grid { grid-template-columns:1fr; } }

    .layout-cards { display:grid; grid-template-columns:1fr 1fr; gap:.75rem; }
    @media(max-width:540px){ .layout-cards { grid-template-columns:1fr; } }
    .layout-card {
        border:2px solid var(--border); border-radius:var(--radius-card); padding:.85rem;
        cursor:pointer; transition:all .2s; background:#fff; position:relative;
    }
    .layout-card:hover { border-color:rgba(79,70,229,.3); }
    .layout-card.selected { border-color:var(--accent); box-shadow:0 0 0 3px rgba(79,70,229,.12); }
    .layout-card .lc-check {
        position:absolute; top:.6rem; right:.6rem; width:22px; height:22px;
        border-radius:50%; background:var(--accent); color:#fff; display:none;
        align-items:center; justify-content:center; font-size:.7rem;
    }
    .layout-card.selected .lc-check { display:flex; }
    .layout-card .lc-preview {
        height:80px; border-radius:8px; margin-bottom:.6rem; overflow:hidden;
        background:#f1f5f9; display:flex; align-items:center; justify-content:center;
    }
    .layout-card .lc-name { font-size:.82rem; font-weight:700; color:var(--text-primary); }
    .layout-card .lc-desc { font-size:.7rem; color:var(--text-muted); margin-top:.15rem; line-height:1.4; }

    .lc-mini { width:100%; height:100%; padding:6px; display:flex; flex-direction:column; gap:3px; }
    .lc-mini-bar { height:6px; border-radius:3px; background:#e2e8f0; }
    .lc-mini-accent { background:var(--accent); }
    .lc-mini-row { display:flex; gap:3px; flex:1; }
    .lc-mini-block { flex:1; border-radius:4px; background:#e2e8f0; }
    .lc-mini-block-sm { width:30%; border-radius:4px; background:#e2e8f0; }

    .color-row { display:flex; align-items:center; gap:.75rem; margin-bottom:.85rem; }
    .color-row:last-child { margin-bottom:0; }
    .color-label { font-size:.78rem; font-weight:600; color:var(--text-secondary); min-width:110px; }
    .color-input-wrap { position:relative; }
    .color-input-wrap input[type="color"] {
        width:36px; height:36px; border:2px solid #e5e7eb; border-radius:8px;
        padding:2px; cursor:pointer; background:none;
    }
    .color-input-wrap input[type="color"]::-webkit-color-swatch-wrapper { padding:0; }
    .color-input-wrap input[type="color"]::-webkit-color-swatch { border:none; border-radius:5px; }
    .color-hex {
        font-size:.72rem; font-weight:600; font-family:'Inter',monospace; color:var(--text-muted);
        width:72px; text-align:center; border:1px solid #e5e7eb; border-radius:6px; padding:.25rem .4rem;
        background:#fafafa; text-transform:uppercase;
    }

    .preset-chips { display:flex; flex-wrap:wrap; gap:.5rem; }
    .preset-chip {
        display:inline-flex; align-items:center; gap:.4rem; padding:.35rem .7rem;
        border:1.5px solid #e5e7eb; border-radius:var(--radius-card); cursor:pointer;
        font-size:.72rem; font-weight:600; color:var(--text-secondary); background:#fff;
        transition:all .2s;
    }
    .preset-chip:hover { border-color:var(--accent); color:var(--accent); }
    .preset-chip .pc-dot {
        width:14px; height:14px; border-radius:50%; border:1.5px solid rgba(0,0,0,.08);
    }

    .preview-frame-wrap {
        position:sticky; top:80px;
        background:#1e293b; border-radius:var(--radius-card); overflow:hidden;
        box-shadow:0 8px 32px rgba(0,0,0,.12);
    }
    .preview-bar {
        padding:.5rem .75rem; display:flex; align-items:center; gap:.5rem;
        background:#0f172a;
    }
    .preview-bar .pb-dot { width:8px; height:8px; border-radius:50%; }
    .preview-bar .pb-url {
        flex:1; background:#1e293b; border-radius:6px; padding:.2rem .6rem;
        font-size:.65rem; color:#94a3b8; font-family:monospace; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
    }
    .preview-frame-wrap iframe {
        width:100%; height:680px; border:none; background:#fff;
        border-bottom-left-radius:var(--radius-card); border-bottom-right-radius:var(--radius-card);
    }
    @media(max-width:1100px){
        .preview-frame-wrap { position:static; }
        .preview-frame-wrap iframe { height:500px; }
    }
</style>
@endpush

@section('content')
<form method="POST" action="{{ route('menu-settings.update') }}" id="menuSettingsForm">
    @csrf
    @method('PUT')

    @if(session('success'))
    <div class="alert alert-success d-flex align-items-center mb-4" style="border-radius:12px;font-size:.85rem;border:none;background:rgba(34,197,94,.1);color:#166534">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    </div>
    @endif

    <div class="ms-grid">
        <div>
            {{-- Layout Selection --}}
            <div class="sm-card mb-4">
                <div class="sm-card-header">
                    <i class="bi bi-layout-text-window-reverse" style="color:#4F46E5"></i>{{ __('menu_settings.layout_section') }}
                </div>
                <div class="sm-card-body">
                    <input type="hidden" name="layout" id="layoutInput" value="{{ $settings->layout ?? 'accordion' }}">
                    <div class="layout-cards">
                        @php
                            $layouts = [
                                'accordion' => ['icon' => 'bi-list-nested', 'key' => 'accordion'],
                                'tabs'      => ['icon' => 'bi-segmented-nav', 'key' => 'tabs'],
                                'grid'      => ['icon' => 'bi-grid-3x3-gap-fill', 'key' => 'grid'],
                                'elegant'   => ['icon' => 'bi-journal-richtext', 'key' => 'elegant'],
                            ];
                        @endphp
                        @foreach($layouts as $val => $meta)
                        <div class="layout-card {{ ($settings->layout ?? 'accordion') === $val ? 'selected' : '' }}" data-layout="{{ $val }}" onclick="selectLayout('{{ $val }}')">
                            <span class="lc-check"><i class="bi bi-check-lg"></i></span>
                            <div class="lc-preview">
                                @if($val === 'accordion')
                                <div class="lc-mini">
                                    <div class="lc-mini-bar lc-mini-accent" style="width:70%"></div>
                                    <div style="background:#f1f5f9;border-radius:4px;padding:4px;flex:1;display:flex;flex-direction:column;gap:2px">
                                        <div class="lc-mini-bar" style="width:90%"></div>
                                        <div class="lc-mini-bar" style="width:60%"></div>
                                    </div>
                                    <div class="lc-mini-bar" style="width:70%;opacity:.5"></div>
                                    <div class="lc-mini-bar" style="width:70%;opacity:.3"></div>
                                </div>
                                @elseif($val === 'tabs')
                                <div class="lc-mini">
                                    <div style="display:flex;gap:3px">
                                        <div class="lc-mini-bar lc-mini-accent" style="width:25%"></div>
                                        <div class="lc-mini-bar" style="width:25%"></div>
                                        <div class="lc-mini-bar" style="width:25%"></div>
                                    </div>
                                    <div style="flex:1;display:flex;flex-direction:column;gap:3px;padding-top:3px">
                                        <div class="lc-mini-bar" style="width:100%"></div>
                                        <div class="lc-mini-bar" style="width:100%"></div>
                                        <div class="lc-mini-bar" style="width:80%"></div>
                                    </div>
                                </div>
                                @elseif($val === 'grid')
                                <div class="lc-mini">
                                    <div style="display:flex;gap:3px">
                                        <div class="lc-mini-bar lc-mini-accent" style="width:20%"></div>
                                        <div class="lc-mini-bar" style="width:20%"></div>
                                        <div class="lc-mini-bar" style="width:20%"></div>
                                    </div>
                                    <div class="lc-mini-row">
                                        <div class="lc-mini-block"></div>
                                        <div class="lc-mini-block"></div>
                                    </div>
                                    <div class="lc-mini-row">
                                        <div class="lc-mini-block"></div>
                                        <div class="lc-mini-block"></div>
                                    </div>
                                </div>
                                @elseif($val === 'elegant')
                                <div class="lc-mini">
                                    <div class="lc-mini-bar lc-mini-accent" style="height:24px;border-radius:4px"></div>
                                    <div class="lc-mini-row" style="gap:4px">
                                        <div class="lc-mini-block-sm"></div>
                                        <div style="flex:1;display:flex;flex-direction:column;gap:2px">
                                            <div class="lc-mini-bar" style="width:80%"></div>
                                            <div class="lc-mini-bar" style="width:50%"></div>
                                        </div>
                                    </div>
                                    <div class="lc-mini-row" style="gap:4px">
                                        <div class="lc-mini-block-sm"></div>
                                        <div style="flex:1;display:flex;flex-direction:column;gap:2px">
                                            <div class="lc-mini-bar" style="width:70%"></div>
                                            <div class="lc-mini-bar" style="width:40%"></div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="lc-name">{{ __('menu_settings.layout_' . $val) }}</div>
                            <div class="lc-desc">{{ __('menu_settings.layout_' . $val . '_desc') }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Color Customization --}}
            <div class="sm-card mb-4">
                <div class="sm-card-header">
                    <i class="bi bi-palette" style="color:#6366F1"></i>{{ __('menu_settings.colors_section') }}
                </div>
                <div class="sm-card-body">
                    <p class="text-muted small mb-3">{{ __('menu_settings.colors_hint') }}</p>

                    {{-- Presets --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('menu_settings.presets') }}</label>
                        <div class="preset-chips">
                            <button type="button" class="preset-chip" onclick="applyPreset('#4F46E5','#6366F1','#f8fafc','#ffffff','#1e293b','#ffffff','#0f172a')">
                                <span class="pc-dot" style="background:#4F46E5"></span> {{ __('menu_settings.preset_indigo') }}
                            </button>
                            <button type="button" class="preset-chip" onclick="applyPreset('#0f172a','#1e293b','#0f172a','#1e293b','#f1f5f9','#0f172a','#f8fafc')">
                                <span class="pc-dot" style="background:#0f172a"></span> {{ __('menu_settings.preset_dark') }}
                            </button>
                            <button type="button" class="preset-chip" onclick="applyPreset('#dc2626','#ef4444','#fef2f2','#ffffff','#1e293b','#ffffff','#0f172a')">
                                <span class="pc-dot" style="background:#dc2626"></span> {{ __('menu_settings.preset_warm') }}
                            </button>
                            <button type="button" class="preset-chip" onclick="applyPreset('#059669','#10b981','#f0fdf4','#ffffff','#1e293b','#ffffff','#0f172a')">
                                <span class="pc-dot" style="background:#059669"></span> {{ __('menu_settings.preset_green') }}
                            </button>
                            <button type="button" class="preset-chip" onclick="applyPreset('#7c3aed','#8b5cf6','#faf5ff','#ffffff','#1e293b','#ffffff','#0f172a')">
                                <span class="pc-dot" style="background:#7c3aed"></span> {{ __('menu_settings.preset_purple') }}
                            </button>
                            <button type="button" class="preset-chip" onclick="applyPreset('#2563eb','#3b82f6','#eff6ff','#ffffff','#1e293b','#ffffff','#0f172a')">
                                <span class="pc-dot" style="background:#2563eb"></span> {{ __('menu_settings.preset_blue') }}
                            </button>
                        </div>
                    </div>

                    @php
                        $colors = [
                            ['name' => 'primary_color',     'label' => __('menu_settings.color_primary')],
                            ['name' => 'secondary_color',   'label' => __('menu_settings.color_secondary')],
                            ['name' => 'background_color',  'label' => __('menu_settings.color_background')],
                            ['name' => 'card_color',        'label' => __('menu_settings.color_card')],
                            ['name' => 'text_color',        'label' => __('menu_settings.color_text')],
                            ['name' => 'header_bg',         'label' => __('menu_settings.color_header_bg')],
                            ['name' => 'header_text_color', 'label' => __('menu_settings.color_header_text')],
                        ];
                    @endphp
                    @foreach($colors as $c)
                    <div class="color-row">
                        <span class="color-label">{{ $c['label'] }}</span>
                        <div class="color-input-wrap">
                            <input type="color" name="{{ $c['name'] }}" id="color_{{ $c['name'] }}" value="{{ $settings->{$c['name']} ?? '#4F46E5' }}" onchange="syncHex(this)">
                        </div>
                        <input type="text" class="color-hex" id="hex_{{ $c['name'] }}" value="{{ $settings->{$c['name']} ?? '#4F46E5' }}" maxlength="7"
                               oninput="syncColor(this, 'color_{{ $c['name'] }}')" data-target="color_{{ $c['name'] }}">
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Font --}}
            <div class="sm-card mb-4">
                <div class="sm-card-header">
                    <i class="bi bi-fonts" style="color:#4F46E5"></i>{{ __('menu_settings.font_section') }}
                </div>
                <div class="sm-card-body">
                    <select name="font_family" class="form-select" id="fontSelect">
                        @foreach(['Inter', 'Poppins', 'Roboto', 'Nunito', 'Playfair Display', 'Merriweather', 'Lato', 'Open Sans'] as $font)
                        <option value="{{ $font }}" {{ ($settings->font_family ?? 'Inter') === $font ? 'selected' : '' }}>{{ $font }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-accent">
                    <i class="bi bi-check-lg me-1"></i>{{ __('common.save') }}
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="refreshPreview()">
                    <i class="bi bi-arrow-clockwise me-1"></i>{{ __('menu_settings.refresh_preview') }}
                </button>
            </div>
        </div>

        {{-- Live Preview --}}
        <div class="preview-frame-wrap">
            <div class="preview-bar">
                <span class="pb-dot" style="background:#ef4444"></span>
                <span class="pb-dot" style="background:#fbbf24"></span>
                <span class="pb-dot" style="background:#22c55e"></span>
                <span class="pb-url" id="previewUrl">{{ route('public.menu', ['tenantId' => $tenant->id]) }}?preview=1</span>
            </div>
            <iframe id="previewFrame" src="{{ route('public.menu', ['tenantId' => $tenant->id]) }}?preview=1" loading="lazy"></iframe>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function selectLayout(val) {
    document.getElementById('layoutInput').value = val;
    document.querySelectorAll('.layout-card').forEach(function(c) {
        c.classList.toggle('selected', c.getAttribute('data-layout') === val);
    });
}

function syncHex(colorInput) {
    var hexInput = document.getElementById('hex_' + colorInput.name);
    if (hexInput) hexInput.value = colorInput.value.toUpperCase();
}

function syncColor(hexInput, colorId) {
    var v = hexInput.value.trim();
    if (/^#[0-9A-Fa-f]{6}$/.test(v)) {
        document.getElementById(colorId).value = v;
    }
}

function applyPreset(primary, secondary, bg, card, text, headerBg, headerText) {
    var map = {
        primary_color: primary,
        secondary_color: secondary,
        background_color: bg,
        card_color: card,
        text_color: text,
        header_bg: headerBg,
        header_text_color: headerText
    };
    for (var key in map) {
        var colorEl = document.getElementById('color_' + key);
        var hexEl = document.getElementById('hex_' + key);
        if (colorEl) colorEl.value = map[key];
        if (hexEl) hexEl.value = map[key].toUpperCase();
    }
}

function refreshPreview() {
    var frame = document.getElementById('previewFrame');
    if (frame) frame.src = frame.src;
}
</script>
@endpush
