@extends('layouts.app')

@section('title', __('users.management'))
@section('page-title', __('users.management'))

@section('content')
<style>
.sm-search { border:1.5px solid #e5e7eb; border-radius:9px; padding:.4rem .75rem .4rem 2rem; font-size:.82rem; font-family:'Inter',sans-serif; transition:border-color .15s,box-shadow .15s; width:200px; background:#fff; }
.sm-search:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(255,107,53,.12); outline:none; }
.search-wrap { position:relative; }
.search-wrap i { position:absolute; left:.7rem; top:50%; transform:translateY(-50%); color:#98a2b3; font-size:.8rem; pointer-events:none; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-muted small">{{ __('users.total', ['count' => $users->count()]) }}</span>
    <div class="d-flex gap-2 align-items-center">
        <div class="search-wrap">
            <i class="bi bi-search"></i>
            <input type="text" id="userSearch" class="sm-search" placeholder="{{ __('users.management') }}...">
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-accent btn-sm">
            <i class="bi bi-person-plus me-1"></i>{{ __('users.add') }}
        </a>
    </div>
</div>

<div class="sm-card">
    <div class="table-responsive">
        <table class="table sm-table table-hover align-middle mb-0" id="usersTable">
            <thead>
                <tr>
                    <th class="ps-4">#</th>
                    <th>{{ __('users.full_name') }}</th>
                    <th>{{ __('users.email') }}</th>
                    <th>{{ __('users.role') }}</th>
                    <th>{{ __('users.registered_at') }}</th>
                    <th class="text-end pe-4">{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="ps-4 text-muted small">{{ $user->id }}</td>
                    <td class="fw-semibold">{{ $user->name }}</td>
                    <td class="text-muted small">{{ $user->email }}</td>
                    <td>
                        @php $rc = $user->role === 'owner' ? 'warning text-dark' : ($user->role === 'admin' ? 'primary' : 'secondary'); @endphp
                        <span class="badge bg-{{ $rc }}">{{ __('nav.roles.' . $user->role) }}</span>
                    </td>
                    <td class="text-muted small">{{ \Carbon\Carbon::parse($user->created_at)->locale(app()->getLocale())->format('d.m.Y') }}</td>
                    <td class="text-end pe-4">
                        @if($user->role !== 'owner')
                        <form method="POST" action="{{ route('users.destroy', $user->id) }}"
                              onsubmit="return confirm({{ json_encode(__('users.delete_confirm', ['name' => $user->name])) }})">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @else
                        <span class="text-muted small">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-people fs-2 d-block mb-2"></i>
                        {{ __('users.no_staff') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    const table = $('#usersTable').DataTable({
        ordering: true,
        paging: true,
        pageLength: 25,
        dom: 't<"d-flex justify-content-between align-items-center mt-3"ip>',
        language: {
            info: '_START_–_END_ / _TOTAL_',
            infoEmpty: '',
            infoFiltered: '',
            zeroRecords: '<div class="text-center py-4 text-muted"><i class="bi bi-search fs-3 d-block mb-2 opacity-25"></i>{{ __("users.no_staff") }}</div>',
            paginate: { previous: '‹', next: '›' }
        },
        columnDefs: [
            { targets: [0, 5], searchable: false },
            { targets: [5], orderable: false }
        ],
        order: [[0, 'asc']]
    });
    $('#userSearch').on('keyup', function() { table.search(this.value).draw(); });
});
</script>
@endpush
@endsection
