@extends('layouts.dev')

@section('title', 'Tüm Kullanıcılar')
@section('page-title', 'Tüm Kullanıcılar')

@section('content')
<style>
.user-row:hover { background:#fafafa; }
.role-pill {
    display:inline-block; font-size:.7rem; font-weight:700; padding:.2rem .55rem;
    border-radius:20px; text-transform:capitalize;
}
.role-owner   { background:rgba(255,107,53,.1); color:#c2410c; }
.role-admin   { background:rgba(99,102,241,.08); color:#6366f1; }
.role-personel{ background:rgba(0,0,0,.04); color:#374151; }
.search-box {
    border:1px solid #e5e7eb; border-radius:9px; padding:.5rem .85rem;
    font-size:.84rem; width:100%; max-width:300px; outline:none;
    transition:border-color .15s,box-shadow .15s;
}
.search-box:focus { border-color:#ef4444; box-shadow:0 0 0 3px rgba(239,68,68,.1); }
</style>

<div class="sm-card">
    <div class="sm-card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-1" style="color:#6366f1"></i>Platform Kullanıcıları</span>
        <div class="d-flex align-items-center gap-2">
            <input type="text" id="userSearch" class="search-box" placeholder="Kullanıcı ara...">
            <span class="badge" style="background:rgba(99,102,241,.1);color:#6366f1;font-size:.75rem;border-radius:6px;padding:.3rem .6rem">
                {{ $users->count() }} kullanıcı
            </span>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table sm-table align-middle mb-0" id="usersTable">
            <thead>
                <tr>
                    <th class="ps-4" style="width:50px">#</th>
                    <th>Ad Soyad</th>
                    <th>E-posta</th>
                    <th>Rol</th>
                    <th>Restoran</th>
                    <th>Kayıt Tarihi</th>
                    <th class="text-end pe-4" style="width:80px">İşlem</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr class="user-row">
                    <td class="ps-4 text-muted small">{{ $u->id }}</td>
                    <td class="fw-semibold">{{ $u->name }}</td>
                    <td class="text-muted small">{{ $u->email }}</td>
                    <td><span class="role-pill role-{{ $u->role }}">{{ $u->role }}</span></td>
                    <td>
                        @if($u->tenant_id && $u->restoran_adi)
                        <a href="{{ route('developer.tenant', $u->tenant_id) }}" style="font-size:.82rem;color:#6366f1;text-decoration:none;font-weight:500">
                            {{ $u->restoran_adi }}
                        </a>
                        @else
                        <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ \Carbon\Carbon::parse($u->created_at)->format('d.m.Y H:i') }}</td>
                    <td class="text-end pe-4">
                        @if($u->role !== 'owner')
                        <form method="POST" action="{{ route('developer.users.destroy', $u->id) }}"
                              onsubmit="return confirm('{{ $u->name }} kullanıcısı silinecek. Onaylıyor musunuz?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Sil"><i class="bi bi-trash"></i></button>
                        </form>
                        @else
                        <span class="text-muted small" title="Owner silinemez"><i class="bi bi-lock" style="font-size:.8rem"></i></span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                        Henüz kullanıcı yok.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('userSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#usersTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
