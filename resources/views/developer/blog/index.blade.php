@extends('layouts.dev')

@section('title', 'Blog Yönetimi')
@section('page-title', 'Blog')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0 small">Yayınlanan yazılar sitede /blog sayfasında listelenir.</p>
    <a href="{{ route('developer.blog.create') }}" class="btn btn-accent btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Yeni Yazı
    </a>
</div>

<div class="sm-card">
    <div class="sm-card-header">
        <i class="bi bi-journal-text me-1" style="color:#6366f1"></i>Yazılar
    </div>
    <div class="sm-card-body p-0">
        @if($posts->isEmpty())
            <div class="p-4 text-center text-muted">Henüz yazı yok. İlk yazıyı ekleyin.</div>
        @else
            <div class="table-responsive">
                <table class="table sm-table mb-0">
                    <thead>
                        <tr>
                            <th>Başlık</th>
                            <th>Slug</th>
                            <th>Durum</th>
                            <th>Yazar</th>
                            <th>Tarih</th>
                            <th style="width:120px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($posts as $p)
                        <tr>
                            <td>
                                <a href="{{ route('blog.show', $p->slug) }}" target="_blank" class="text-decoration-none fw-600">{{ Str::limit($p->title, 45) }}</a>
                            </td>
                            <td><code class="small">{{ Str::limit($p->slug, 25) }}</code></td>
                            <td>
                                @if($p->is_published)
                                    <span class="badge bg-success">Yayında</span>
                                @else
                                    <span class="badge bg-secondary">Taslak</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $p->author_name ?? '—' }}</td>
                            <td class="small text-muted">{{ $p->published_at ? \Carbon\Carbon::parse($p->published_at)->format('d.m.Y') : \Carbon\Carbon::parse($p->created_at)->format('d.m.Y') }}</td>
                            <td>
                                <a href="{{ route('developer.blog.edit', $p->id) }}" class="btn btn-sm btn-outline-secondary">Düzenle</a>
                                <form method="POST" action="{{ route('developer.blog.destroy', $p->id) }}" class="d-inline" onsubmit="return confirm('Bu yazıyı silmek istediğinize emin misiniz?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Sil</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
