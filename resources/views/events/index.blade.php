@extends('layouts.app')

@section('title', __('events.title'))
@section('page-title', __('events.title'))

@section('content')
<style>
.event-card {
    border-radius: 12px; overflow: hidden; border: 1.5px solid #e5e7eb;
    transition: box-shadow .15s, transform .15s; background: #fff;
}
.event-card:hover { box-shadow: 0 4px 15px rgba(0,0,0,.08); transform: translateY(-2px); }
.event-img {
    width: 100%; height: 140px; object-fit: cover; display: block; background: #f3f4f6;
}
.event-body { padding: .85rem 1rem; }
.event-title { font-size: .88rem; font-weight: 700; color: #1f2937; }
.event-desc { font-size: .78rem; color: #6b7280; margin-top: .3rem; line-height: 1.5; }
.event-dates { font-size: .72rem; color: #9ca3af; margin-top: .4rem; display: flex; align-items: center; gap: .3rem; }
.event-active { display: inline-flex; align-items: center; gap: .2rem; font-size: .68rem; font-weight: 700; padding: .15rem .5rem; border-radius: 20px; }
.ea-on  { background: #dcfce7; color: #15803d; }
.ea-off { background: #f3f4f6; color: #6b7280; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <span class="text-muted" style="font-size:.82rem">{{ __('events.total', ['count' => $events->count()]) }}</span>
    <a href="{{ route('events.create') }}" class="btn btn-accent btn-sm">
        <i class="bi bi-plus-lg me-1"></i>{{ __('events.add') }}
    </a>
</div>

@if(session('success'))
<div class="alert alert-success py-2 px-3" style="font-size:.84rem;border-radius:9px">{{ session('success') }}</div>
@endif

@if($events->isEmpty())
<div class="text-center py-5 text-muted">
    <i class="bi bi-calendar-event" style="font-size:2.5rem;opacity:.3"></i>
    <div class="mt-2" style="font-size:.85rem">{{ __('events.no_events') }}</div>
    <a href="{{ route('events.create') }}" class="btn btn-accent btn-sm mt-3">{{ __('events.add_first') }}</a>
</div>
@else
<div class="row g-3">
    @foreach($events as $ev)
    <div class="col-sm-6 col-lg-4">
        <div class="event-card">
            @if($ev->image)
            <img src="{{ asset('storage/' . $ev->image) }}" alt="{{ $ev->title }}" class="event-img">
            @else
            <div class="event-img d-flex align-items-center justify-content-center">
                <i class="bi bi-calendar-event" style="font-size:2rem;color:#d1d5db"></i>
            </div>
            @endif
            <div class="event-body">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div class="event-title">{{ $ev->title }}</div>
                    <span class="event-active {{ $ev->is_active ? 'ea-on' : 'ea-off' }}">
                        {{ $ev->is_active ? __('events.active') : __('events.inactive') }}
                    </span>
                </div>
                @if($ev->description)
                <div class="event-desc">{{ Str::limit($ev->description, 100) }}</div>
                @endif
                <div class="event-dates">
                    <i class="bi bi-calendar3"></i>
                    {{ \Carbon\Carbon::parse($ev->start_date)->format('d.m.Y') }}
                    @if($ev->end_date) — {{ \Carbon\Carbon::parse($ev->end_date)->format('d.m.Y') }} @endif
                </div>
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('events.edit', $ev->id) }}" class="btn btn-sm btn-outline-secondary" style="font-size:.75rem;padding:.2rem .6rem">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form method="POST" action="{{ route('events.destroy', $ev->id) }}"
                          onsubmit="return confirm('{{ __('events.delete_confirm', ['name' => $ev->title]) }}')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" style="font-size:.75rem;padding:.2rem .6rem">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
