# Blog sayfaları — SEO, ASO, GEO ayarları

Bu belge blog liste ve yazı sayfalarındaki arama / paylaşım / dil–bölge ayarlarını özetler.

---

## Mevcut durum (kontrol sonrası)

### SEO (arama motoru optimizasyonu)

| Öğe | Blog listesi | Blog yazı sayfası |
|-----|----------------|-------------------|
| **Title** | ✅ `Blog \| Sipariş Masanda` | ✅ `meta_title` veya yazı başlığı + site adı |
| **Meta description** | ✅ Sabit açıklama | ✅ `meta_description` veya body’den kesit |
| **Meta keywords** | ✅ Eklendi (TR/EN) | ✅ Eklendi (varsayılan + başlık) |
| **Canonical** | ✅ `route('blog')` | ✅ `route('blog.show', slug)` |
| **Robots** | ✅ Layout varsayılanı (index, follow) | ✅ Aynı |
| **Schema.org** | ✅ Blog + BreadcrumbList | ✅ Article + BreadcrumbList |
| **H1** | ✅ Tek H1 | ✅ Yazı başlığı |

### ASO / paylaşım (Open Graph, Twitter, zengin sonuç)

| Öğe | Blog listesi | Blog yazı sayfası |
|-----|----------------|-------------------|
| **og:type** | website (layout varsayılanı) | ✅ article |
| **og:url** | canonical | canonical |
| **og:title** | title | title |
| **og:description** | meta_description | meta_description |
| **og:image** | varsayılan görsel | ✅ Öne çıkan görsel veya varsayılan |
| **og:site_name** | Sipariş Masanda | Aynı |
| **og:locale** | tr_TR / en_US (layout) | Aynı |
| **article:published_time** | — | ✅ ISO 8601 |
| **article:modified_time** | — | ✅ ISO 8601 |
| **article:author** | — | ✅ Varsa yazar adı |
| **Twitter card** | summary_large_image | Aynı |

### GEO / dil ve bölge

| Öğe | Açıklama |
|-----|----------|
| **hreflang** | ✅ Layout’ta tüm sayfalarda: `tr`, `en`, `x-default` (mevcut URL + `?lang=tr` / `?lang=en`) |
| **html lang** | ✅ `lang="{{ str_replace('_', '-', $locale) }}"` (tr / en) |
| **og:locale** | ✅ tr_TR / en_US |
| **Coğrafi meta** | Yok (blog için zorunlu değil; ihtiyaç olursa geo.region eklenebilir) |

---

## Yapılan eklemeler (bu kontrolde)

1. **Blog listesi:** `meta_keywords` (TR/EN) eklendi.
2. **Blog yazı sayfası:** `meta_keywords` (varsayılan anahtar kelimeler + yazı başlığı) eklendi.
3. **Blog yazı sayfası:** `head_extra` ile `article:published_time`, `article:modified_time`, `article:author` meta etiketleri eklendi.
4. **Layout:** `@yield('head_extra')` eklendi (makale sayfalarında ek meta için).

---

## Öneriler

- **Yazı bazlı keywords:** İleride `blog_posts` tablosuna `meta_keywords` alanı eklenirse, yazı sayfasında bu alan kullanılabilir.
- **GEO:** Sadece Türkiye/TR hedefleniyorsa mevcut hreflang yeterli; çok dilli büyümede ek `hreflang` ve `og:locale:alternate` düşünülebilir.
- **Sitemap:** Blog URL’leri sitemap’te yer alıyor (routes/web.php’deki sitemap mantığı).
