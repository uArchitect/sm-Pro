@extends('layouts.app')

@section('title', __('reviews.title'))
@section('page-title', __('reviews.title'))
@section('page-help', __('reviews.page_help'))

@section('content')
<style>
.review-stars { color:#FBBF24; font-size:.9rem; letter-spacing:1px; }
.review-stars .empty { color:#e5e7eb; }
.rating-bar-wrap { display:flex; align-items:center; gap:.5rem; margin-bottom:.3rem; }
.rating-bar-label { font-size:.78rem; font-weight:600; width:20px; text-align:center; color:#374151; }
.rating-bar-track { flex:1; height:8px; background:#f3f4f6; border-radius:4px; overflow:hidden; }
.rating-bar-fill { height:100%; background:linear-gradient(90deg,#FBBF24,#F59E0B); border-radius:4px; transition:width .4s; }
.rating-bar-count { font-size:.75rem; color:#98a2b3; width:30px; text-align:right; }
.avg-big { font-size:2.5rem; font-weight:900; color:#111827; line-height:1; }
.avg-stars { font-size:1.2rem; color:#FBBF24; }
.sm-search { border:1.5px solid #e5e7eb; border-radius:9px; padding:.4rem .75rem .4rem 2rem; font-size:.82rem; font-family:'Inter',sans-serif; transition:border-color .15s,box-shadow .15s; width:200px; background:#fff; }
.sm-search:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(79,70,229,.12); outline:none; }
.search-wrap { position:relative; }
.search-wrap i { position:absolute; left:.7rem; top:50%; transform:translateY(-50%); color:#98a2b3; font-size:.8rem; pointer-events:none; }
</style>

{{-- Summary Card --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="sm-card h-100">
            <div class="sm-card-body text-center py-4">
                <div class="avg-big">{{ number_format($stats->avg_rating, 1) }}</div>
                <div class="avg-stars my-1">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star{{ $i <= round($stats->avg_rating) ? '-fill' : '' }}"></i>
                    @endfor
                </div>
                <div class="text-muted small">{{ __('reviews.total_reviews', ['count' => $stats->total]) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="sm-card h-100">
            <div class="sm-card-body py-3">
                @for($r = 5; $r >= 1; $r--)
                @php $cnt = $distribution[$r] ?? 0; $pct = $stats->total > 0 ? ($cnt / $stats->total * 100) : 0; @endphp
                <div class="rating-bar-wrap">
                    <div class="rating-bar-label">{{ $r }}<i class="bi bi-star-fill ms-1" style="font-size:.65rem;color:#FBBF24"></i></div>
                    <div class="rating-bar-track"><div class="rating-bar-fill" style="width:{{ $pct }}%"></div></div>
                    <div class="rating-bar-count">{{ $cnt }}</div>
                </div>
                @endfor
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted small">{{ __('reviews.total_reviews', ['count' => $stats->total]) }}</span>
    <div class="search-wrap">
        <i class="bi bi-search"></i>
        <input type="text" id="reviewSearch" class="sm-search" placeholder="{{ __('reviews.search') }}">
    </div>
</div>

<div class="sm-card">
    <div class="table-responsive">
        <table class="table sm-table align-middle mb-0" id="reviewsTable">
            <thead>
                <tr>
                    <th style="width:140px">{{ __('reviews.date') }}</th>
                    <th style="width:150px">{{ __('reviews.customer') }}</th>
                    <th style="width:120px">{{ __('reviews.rating') }}</th>
                    <th>{{ __('reviews.comment') }}</th>
                    <th class="text-end pe-4" style="width:60px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($reviews as $review)
                <tr>
                    <td class="text-muted small">{{ \Carbon\Carbon::parse($review->created_at)->format('d.m.Y H:i') }}</td>
                    <td class="fw-semibold">{{ $review->customer_name ?: __('reviews.anonymous') }}</td>
                    <td>
                        <span class="review-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : ' empty' }}"></i>
                            @endfor
                        </span>
                    </td>
                    <td class="text-muted small">{{ $review->comment ?: '—' }}</td>
                    <td class="text-end pe-4">
                        <form method="POST" action="{{ route('reviews.destroy', $review->id) }}"
                              onsubmit="return confirm({{ json_encode(__('reviews.delete_confirm')) }})">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    const table = $('#reviewsTable').DataTable({
        ordering: true,
        paging: true,
        pageLength: 25,
        dom: 't<"d-flex justify-content-between align-items-center mt-3"ip>',
        language: {
            info: '_START_–_END_ / _TOTAL_',
            infoEmpty: '',
            infoFiltered: '',
            emptyTable: '<div class="text-center py-4 text-muted"><i class="bi bi-star fs-3 d-block mb-2 opacity-25"></i>{{ __("reviews.no_reviews") }}</div>',
            zeroRecords: '<div class="text-center py-4 text-muted"><i class="bi bi-search fs-3 d-block mb-2 opacity-25"></i>{{ __("reviews.no_reviews") }}</div>',
            paginate: { previous: '‹', next: '›' }
        },
        columnDefs: [
            { targets: [4], searchable: false, orderable: false }
        ],
        order: [[0, 'desc']]
    });
    $('#reviewSearch').on('keyup', function() { table.search(this.value).draw(); });
});
</script>
@endpush
@endsection
