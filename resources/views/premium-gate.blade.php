@extends('layouts.app')

@section('title', __('nav.nav.premium'))
@section('page-title', __('nav.nav.premium'))

@section('content')
<style>
.prem-gate {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    min-height: 50vh; text-align: center; padding: 2rem;
}
.prem-icon {
    width: 80px; height: 80px; border-radius: 50%;
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 1.5rem; box-shadow: 0 8px 30px rgba(251, 191, 36, .25);
}
.prem-icon i { font-size: 2rem; color: #fff; }
.prem-title { font-size: 1.4rem; font-weight: 800; color: #1f2937; margin-bottom: .5rem; }
.prem-desc { font-size: .9rem; color: #6b7280; max-width: 420px; line-height: 1.7; }
.prem-features {
    display: flex; flex-wrap: wrap; gap: .75rem; justify-content: center;
    margin-top: 1.5rem; margin-bottom: 1.5rem;
}
.prem-feat {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .4rem .85rem; border-radius: 20px;
    background: rgba(251, 191, 36, .08); color: #92400e;
    font-size: .78rem; font-weight: 600;
}
</style>

<div class="prem-gate">
    <div class="prem-icon"><i class="bi bi-gem"></i></div>
    <div class="prem-title">{{ __('common.premium_title') }}</div>
    <div class="prem-desc">{{ __('common.premium_desc') }}</div>
    <div class="prem-features">
        <span class="prem-feat"><i class="bi bi-images"></i> {{ __('nav.nav.sliders') }}</span>
        <span class="prem-feat"><i class="bi bi-calendar-event"></i> {{ __('nav.nav.events') }}</span>
        <span class="prem-feat"><i class="bi bi-cart-check"></i> {{ __('nav.nav.ordering') }}</span>
    </div>
    <a href="{{ route('support.create') }}" class="btn btn-sm" style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;font-weight:700;padding:.55rem 1.5rem;border-radius:9px;">
        <i class="bi bi-headset me-1"></i> {{ __('common.premium_contact') }}
    </a>
</div>
@endsection
