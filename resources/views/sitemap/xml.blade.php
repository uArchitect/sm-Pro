<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
@foreach($pages as $page)
    <url>
        <loc>{{ $page['loc'] }}</loc>
        <lastmod>{{ $page['lastmod'] }}</lastmod>
        <changefreq>{{ $page['changefreq'] ?? 'monthly' }}</changefreq>
        <priority>{{ $page['priority'] }}</priority>
@if(!empty($page['alternate']))
        <xhtml:link rel="alternate" hreflang="tr" href="{{ $page['alternate']['tr'] }}" />
        <xhtml:link rel="alternate" hreflang="en" href="{{ $page['alternate']['en'] }}" />
        <xhtml:link rel="alternate" hreflang="x-default" href="{{ $page['alternate']['tr'] }}" />
@endif
    </url>
@endforeach
</urlset>
