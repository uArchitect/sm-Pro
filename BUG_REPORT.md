# Uygulama Test & Bug Raporu

**Tarih:** 2026-03-20
**Kapsam:** Tüm uygulama — controller, view, middleware, service katmanları
**Metodoloji:** Statik kod analizi + mantık testi + güvenlik taraması

---

## Önem Seviyeleri

| Seviye | Açıklama |
|--------|----------|
| 🔴 **KRİTİK** | Veri kaybı, güvenlik açığı veya uygulamanın işlevsiz kalması |
| 🟠 **YÜKSEK** | Yanlış iş mantığı, önemli kullanıcı deneyimi kırılması |
| 🟡 **ORTA** | Tutarsızlık, eksik doğrulama, potansiyel sorun |
| 🟢 **DÜŞÜK** | İyileştirme önerisi, küçük UX sorunu |

---

## 1. Kimlik Doğrulama & Yetkilendirme

---

### 1.1 🔴 Tenant Sahipliği Doğrulaması Eksik

**Dosya:** `app/Http/Controllers/TenantController.php`
**Kısım:** `edit()` ve `update()` metodları

```php
$tenant = DB::table('tenants')->find(session('tenant_id'));
```

**Sorun:** Session'daki `tenant_id` her seferinde sadece oturumdan okunuyor; bu kullanıcının gerçekten bu tenant'a ait olup olmadığı doğrulanmıyor. Session manipülasyonu ile bir kullanıcı başka bir tenant'ın ayarlarını düzenleyebilir.

**Beklenen Davranış:** `Auth::user()->tenant_id === session('tenant_id')` karşılaştırması yapılmalı veya `where('id', session('tenant_id'))` ile birlikte kullanıcının kendi tenant'ı olduğu da kontrol edilmeli.

---

### 1.2 🟠 Impersonation Sırasında Tenant Deaktivasyon Bypass'ı

**Dosya:** `app/Http/Middleware/TenantMiddleware.php`

```php
if (!$tenant->is_active && !session('impersonating_from')) {
    Auth::logout();
}
```

**Sorun:** Bir tenant deaktive edildiğinde normal kullanıcılar çıkış yapıyor, ancak geliştirici `impersonating_from` session'ı varsa deaktive tenant üzerinde çalışmaya devam edebiliyor. Bu durum kasıtlı olsa da, geliştirici panelinde tenant'ı kapattıktan sonra impersonation modunda kalan bir geliştirici veri değişikliği yapabilir.

**Beklenen Davranış:** Impersonation sırasında tenant deaktive edilirse, geliştirici yalnızca okuma modunda kalabilmeli veya uyarı mesajı gösterilmeli.

---

### 1.3 🟠 Geliştirici Kendi Hesabını Silebilir

**Dosya:** `app/Http/Controllers/DeveloperController.php` — `destroyUser()`

```php
if (!$user || in_array($user->role, ['developer', 'owner'], true)) {
    return back()->with('error', 'Bu kullanıcı silinemez.');
}
```

**Sorun:** `developer` rolü silme koruması var, ancak bir geliştirici başka bir geliştirici hesabını silebilir. Ayrıca geliştirici kendi hesabını silmeye çalıştığında ne olacağı test edilmemiş.

---

### 1.4 🟡 Son Owner Silinebilir

**Dosya:** `app/Http/Controllers/UserController.php` — `destroy()`

```php
if ($user->role === 'owner') {
    return redirect()->route('users.index')->withErrors(['delete' => __('messages.no_owner_delete')]);
}
```

**Sorun:** `owner` rolündeki kullanıcının silinmesi engelleniyor. Ancak eğer aynı tenant'ta birden fazla owner varsa hepsi silinebilir ve tenant sahipsiz kalabilir. Kontrol "bu tenant'ın son owner'ı mı?" şeklinde olmalı.

**Test Senaryosu:** 2 owner oluştur → birini sil → diğerini sil → 0 owner kalan tenant.

---

## 2. Ürün Yönetimi

---

### 2.1 🔴 Ağırlık/Fiyat Seçeneklerinde Mantık Hatası — Yenile Butonu Tutarsızlığı

**Dosya:** `app/Http/Controllers/ProductController.php` — `store()` ve `update()`

**Sorun:** `weight_price_options` JSON olarak kaydediliyor ancak aşağıdaki doğrulamalar eksik:
- Aynı grams değerine sahip iki seçenek eklenebilir (örn: 2x 100g farklı fiyatla)
- Fiyatlar ağırlıkla orantılı olmak zorunda değil (500g = 50₺, 1000g = 30₺ kabul eder)
- Minimum 2 seçenek zorunlu değil; tek seçenekle kayıt yapılabiliyor

**Test Senaryosu:**
```
Ağırlık: 100g → Fiyat: 50₺
Ağırlık: 100g → Fiyat: 80₺   ← duplicate, geçmeli mi?
Kaydet → ?
```

---

### 2.2 🟠 Ürün Görüntüleme Sayacı Kaydedilmiyor (Kırık Analitik)

**Dosya:** `app/Http/Controllers/QRController.php` — `publicProduct()`
**Dosya:** `app/Http/Controllers/DashboardController.php`

Dashboard'da "En Çok Görüntülenen Ürünler" kartı `product_views` tablosunu sorgular. `trackProductView()` metodu `QRController::publicProduct()` içinde çağrılıyor; **ancak** ürün listesinde bir ürüne tıklandığında aynı route'a gidiyor, bu kısım doğru çalışıyor.

**Sorun:** `publicMenu()` içinde ürünler gösterilirken `trackProductView()` çağrılmıyor. Müşteri menüde gezindiğinde (ürün detayına gitmeden) görüntülemeler sayılmıyor. Dashboard istatistiklerinin büyük çoğunluğu sıfır kalıyor.

---

### 2.3 🟠 Ürün Sıralama Race Condition

**Dosya:** `app/Http/Controllers/ProductController.php` — `store()`

```php
$maxOrder = DB::table('products')
    ->where('tenant_id', $tenantId)
    ->max('sort_order') ?? 0;
```

**Sorun:** İki eş zamanlı ürün ekleme isteğinde her ikisi de aynı `max_order` değerini okuyabilir ve aynı `sort_order` ile kaydedilebilir. Bu sıralama tutarsızlığına yol açar.

**Aynı sorun:** `CategoryController::store()` içinde de mevcut.

---

### 2.4 🟡 Yinelenen Ürün Adı Engellenmemiş

**Dosya:** `app/Http/Controllers/ProductController.php`

Aynı kategori içinde aynı isimde birden fazla ürün oluşturulabiliyor. Validasyon kurallarında `unique` kısıtı yok.

**Test Senaryosu:** "Adana Kebap" → Kaydet → Tekrar "Adana Kebap" → Kaydet → İkisi de kaydedilir.

---

### 2.5 🟡 Ürün Düzenleme Sırasında Kategori Kontrolü Yetersiz

**Dosya:** `app/Http/Controllers/ProductController.php` — `update()`

```php
if (!DB::table('categories')->where('id', $request->category_id)->where('tenant_id', $tenantId)->exists()) {
    abort(403);
}
```

**Sorun:** `abort(403)` kullanıcıya açıklayıcı bir hata mesajı vermiyor. Form gönderildikten sonra "403 Forbidden" sayfasına düşen kullanıcı ne yapacağını bilemiyor. Validasyon hatasına dönüştürülmeli.

---

### 2.6 🟢 Frontend'de Dosya Boyutu Kontrolü Yok

**Dosya:** `resources/views/products/create.blade.php`, `resources/views/products/edit.blade.php`

Ürün görseli için sadece sunucu tarafında 2MB limiti var. Kullanıcı 10MB dosya seçtiğinde tüm formu gönderiyor, upload tamamlandıktan sonra hata alıyor.

**Beklenen Davranış:** JavaScript ile dosya seçildiği anda boyut kontrolü yapılmalı.

---

## 3. Kategori Yönetimi

---

### 3.1 🟠 Kategori Silindiğinde Yetim Veriler Oluşuyor

**Dosya:** `app/Http/Controllers/CategoryController.php` — `destroy()`

```php
DB::table('products')->where('tenant_id', $tenantId)->whereIn('category_id', $categoryIds)->delete();
```

**Sorun:** Kategori silindiğinde ürünler de siliniyor (doğru), ancak:
- Bu ürünlere ait `product_views` kayıtları silinmiyor → Yetim veriler tabloda kalıyor
- Dashboard "En Çok Görüntülenen Ürünler" silinmiş ürün ID'lerini sorgulayabilir → Null dönüş

**Test Senaryosu:** Kategori oluştur → Ürün ekle → Ürün sayfasını ziyaret et (product_views kaydı oluşur) → Kategoriyi sil → `product_views` tablosunu kontrol et → Yetim kayıtlar var mı?

---

### 3.2 🟡 Alt Kategori Döngüsel Referans Kontrolü Yüzeysel

**Dosya:** `app/Http/Controllers/CategoryController.php` — `validateParentCategory()`

```php
if ($currentId && DB::table('categories')->where('tenant_id', $tenantId)->where('parent_id', $currentId)->exists()) {
    return 'Alt kategorisi olan bir kategori başka bir kategorinin altına taşınamaz.';
}
```

**Sorun:** Bu kontrol 3 seviye iç içe geçmeyi engelliyor, ancak doğrudan döngüsel referansı engellemez. Örneğin: A → B sonra B → A (her ikisi de alt kategorisi olmayan leaf node'lar için geçerli olabilir).

---

### 3.3 🟢 Toplu Kategori Eklemede Sessiz Başarı

**Dosya:** `app/Http/Controllers/CategoryController.php` — `storeBulk()`

**Sorun:** Kullanıcı 10 kategori ismi giriyor, 3 tanesi boş bırakılmış. Sistem 7'sini ekliyor ama kullanıcıya "3 satır atlandı" diye bildirim vermiyor. Kullanıcı 10 kategori eklediğini sanabilir.

---

## 4. QR & Kısa Link

---

### 4.1 🟠 is.gd API Zaman Aşımı Kullanıcıyı Bekletiyor

**Dosya:** `app/Services/ShortLinkService.php`

```php
Http::timeout(5)->get('https://is.gd/create.php', [...])
```

**Sorun:** İlk QR sayfası ziyaretinde kısa link yoksa `menuQr()` otomatik olarak is.gd'ye istek atıyor. is.gd yavaş yanıt verirse veya down olursa sayfa 5 saniye boyunca yanıt vermiyor. Bu senkron bir HTTP çağrısı olduğu için web worker veya queue'ya alınmamış.

**Test Senaryosu:** is.gd'yi engelleyin → QR sayfasını açın → Sayfa 5 saniye donuyor.

---

### 4.2 🟡 Kısa Link QR Kodu'nda Gösterilmiyor

**Dosya:** `resources/views/qr/menu.blade.php`

QR kodu kalıcı menü URL'ini (`/menu/{tenantId}`) encode ediyor. Kısa link ayrı gösteriliyor. Kullanıcı kısa linki QR olarak da indirebilmeyi bekleyebilir ancak bu özellik yok.

---

## 5. Rezervasyon Sistemi

---

### 5.1 🔴 Rezervasyonda Kapasite Kontrolü Yok

**Dosya:** `app/Http/Controllers/PublicReservationController.php` — `store()`

**Sorun:** Masanın kapasitesi var (`capacity` kolonu), rezervasyon formunda misafir sayısı isteniyor, ancak `store()` metodunda misafir sayısının masa kapasitesini aşıp aşmadığı **hiç kontrol edilmiyor**.

**Test Senaryosu:** 2 kişilik masaya 10 kişilik rezervasyon → Başarıyla kaydedilir.

---

### 5.2 🔴 Aynı Masa Aynı Zaman Diliminde Çift Rezervasyon Alabilir

**Dosya:** `app/Http/Controllers/PublicReservationController.php` — `store()`

**Sorun:** Aynı masa için aynı tarih ve zaman dilimine birden fazla rezervasyon kaydedilebiliyor. Çakışma kontrolü yok.

**Test Senaryosu:**
1. Masa 1 için 21 Mart 19:00 → Rezervasyon kaydedildi
2. Masa 1 için 21 Mart 19:00 → Yine kaydedildi ✗

---

### 5.3 🟠 Rezervasyon Formunda Telefon Numarası Format Doğrulaması Yok

**Dosya:** `app/Http/Controllers/PublicReservationController.php`

```php
'customer_phone' => 'required|string|max:30',
```

**Sorun:** "aaaaaa", "123", "!@#$%" gibi değerler geçerli kabul ediliyor. İşletme müşteriyi arayamaz.

---

### 5.4 🟡 Geçmiş Tarihe Rezervasyon Yapılabiliyor

**Dosya:** `app/Http/Controllers/PublicReservationController.php`

```php
'reservation_date' => 'required|date',
```

**Sorun:** `after_or_equal:today` kuralı yok. Dün, geçen ay hatta 2020 yılı için rezervasyon oluşturulabiliyor.

**Test Senaryosu:** Tarih alanına "2020-01-01" yaz → Form kabul eder.

---

### 5.5 🟡 Rate Limiting Eksik — Rezervasyon Spam'i

**Dosya:** `routes/web.php`

```php
Route::post('/menu/{tenantId}/reservation', [...]) // throttle middleware yok
```

**Sorun:** Aynı IP'den saniyede yüzlerce rezervasyon gönderilebilir. Review endpoint'inde `throttle:10,1` var ancak rezervasyon endpoint'inde yok.

---

## 6. Geliştirici Paneli

---

### 6.1 🟠 N+1 Sorgu Problemi

**Dosya:** `app/Http/Controllers/DeveloperController.php` — `index()`

```php
$tenants->map(function ($t) {
    $t->user_count     = DB::table('users')->where('tenant_id', $t->id)->count();
    $t->category_count = DB::table('categories')->where('tenant_id', $t->id)->count();
    $t->product_count  = DB::table('products')->where('tenant_id', $t->id)->count();
    ...
});
```

**Sorun:** Her tenant için 4-5 ayrı SQL sorgusu çalıştırılıyor. 100 tenant varsa 500+ sorgu gönderiliyor. Büyük veritabanlarında sayfa açılmaz hale gelir.

**Çözüm:** `GROUP BY tenant_id` ile `COUNT` join'leri kullanılmalı.

---

### 6.2 🟡 Impersonation'dan Çıkış Sorunu

**Dosya:** `app/Http/Controllers/DeveloperController.php` — `stopImpersonate()`

**Sorun:** Geliştirici bir tenant'ı impersonate edip, o tenant silinirse `impersonating_from` session'ındaki kullanıcı ID'si artık veritabanında yok. `stopImpersonate()` o ID'yi bulmaya çalışır → null → Auth::login(null) → hata.

---

### 6.3 🟡 Package Toggle Race Condition

**Dosya:** `app/Http/Controllers/DeveloperController.php` — `togglePackage()`

```php
$newPackage = ($tenant->package ?? 'basic') === 'premium' ? 'basic' : 'premium';
DB::table('tenants')->where('id', $id)->update(['package' => $newPackage]);
```

**Sorun:** İki sekmeden aynı anda toggle butonuna basılırsa ikisi de aynı anda "basic" okuyup "premium" yazar ya da çakışma oluşur. Atomik güncelleme kullanılmalı.

---

## 7. Menü Ayarları (Premium)

---

### 7.1 🟡 Font Family Whitelist Yok

**Dosya:** `app/Http/Controllers/MenuSettingsController.php`

```php
'font_family' => 'required|string|max:50',
```

**Sorun:** Font adı doğrulanmıyor. Kötü niyetli bir kullanıcı `'; font-family: 'Hacked` gibi bir değer girebilir. Genel menüde CSS injection riski.

**Çözüm:** İzin verilen fontların listesi (`Inter`, `Roboto`, `Poppins` vb.) whitelist olarak tanımlanmalı.

---

### 7.2 🟢 Renk Kontrast Kontrolü Yok

**Dosya:** `app/Http/Controllers/MenuSettingsController.php`

**Sorun:** Arka plan rengi beyaz (#FFFFFF), metin rengi de beyaz (#FFFFFF) yapılabiliyor. Menü görünmez hale geliyor.

---

## 8. Destek & Yorumlar

---

### 8.1 🟡 Destek Bileti Yanıtında Dosya Eki Yok

**Dosya:** `app/Http/Controllers/SupportController.php` — `reply()`

**Sorun:** Yanıt formu sadece metin kabul ediyor. Ekran görüntüsü veya log dosyası eklenemez. Kullanıcı "bug şu ekran görüntüsünde" diyemiyor.

---

### 8.2 🟢 Yorum Silme Geri Alınamaz

**Dosya:** `app/Http/Controllers/ReviewController.php` — `destroy()`

```php
DB::table('reviews')->where('id', $id)->where('tenant_id', $tenantId)->delete();
```

**Sorun:** Hard delete — silinen yorum kurtarılamaz. Yanlışlıkla tıklamaya karşı "Emin misiniz?" onayı yok (frontend'de confirm yoksa).

---

## 9. Dosya Yükleme Güvenliği

---

### 9.1 🟠 Yüklenen Dosyanın İçeriği Kontrol Edilmiyor

**Dosya:** `app/Http/Controllers/CategoryController.php`, `ProductController.php`, `TenantController.php`

```php
$request->validate([
    'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
]);
```

**Sorun:** `mimes` kontrolü MIME type'ı header'dan okur, dosya içeriğini gerçek anlamda analiz etmez. Dosya uzantısı değiştirilerek zararlı içerik yüklenmeye çalışılabilir. `getimagesize()` ile gerçek image doğrulaması yapılmalı.

---

### 9.2 🟡 Eski Görseller Silinmiyor (Slider İstisnası)

**Dosya:** `app/Http/Controllers/TenantController.php` — `update()`

```php
if ($request->hasFile('logo')) {
    // ... yeni dosya yükleniyor
    // Eski dosya silinmiyor!
}
```

**Sorun:** Her güncelleme yeni bir dosya oluşturuyor. Eski logo `uploads/` klasöründe kalıyor. Zamanla disk dolabilir. `ProductController::update()` ve `CategoryController::update()` eski dosyayı siliyor; `TenantController` silmiyor — tutarsız.

---

## 10. Dil & Çeviri

---

### 10.1 🟡 Bazı Metin Alanları Çevrilmemiş

**Dosya:** `resources/views/qr/menu.blade.php` (kısa link başlığı), `resources/views/dashboard/index.blade.php`

**Sorun:** "Kısa Link", "En Çok Görüntülenen Ürünler" gibi başlıklar hardcoded Türkçe. Dil İngilizce'ye geçildiğinde bu alanlar Türkçe kalmaya devam ediyor.

**Etkilenen alanlar:**
- Dashboard → "En Çok Görüntülenen Ürünler" başlığı
- QR sayfası → "Kısa Link" başlığı
- QR sayfası → "Yenile / Yeni Link Oluştur" butonu

---

### 10.2 🟢 Hata Mesajları Dil Dosyalarında Eksik

**Dosya:** `lang/tr/messages.php`, `lang/en/messages.php`

Bazı controller'larda doğrudan Türkçe string kullanılıyor:

```php
return back()->with('error', 'Bu zone bu tenant\'a ait değil.');
```

`__('messages.zone_not_belongs')` gibi çevrilebilir key kullanılmalı.

---

## 11. Genel Güvenlik

---

### 11.1 🟠 Hata Mesajlarında Kullanıcı Verisi Escape'siz

**Dosya:** `app/Http/Controllers/DeveloperController.php`

```php
return back()->with('success', "{$user->name} silindi.");
```

**Sorun:** `$user->name` doğrudan string interpolation ile mesaja ekleniyor. Blade template'de `{{ session('success') }}` kullanılırsa otomatik escape olur, ancak `{!! session('success') !!}` ile kullanılan yerlerde XSS riski var.

---

### 11.2 🟡 Log Kayıtları Yetersiz

**Dosya:** Tüm controller'lar

Mevcut loglar şu formatta:
```php
Log::info('Kategori silindi.', ['tenant_id' => $tenantId, 'category_id' => $id]);
```

**Eksikler:**
- Kim sildi? (`user_id` yok)
- IP adresi (`request()->ip()` yok)
- Silinen verinin içeriği (kategori adı, ürün sayısı)

---

## 12. Performans

---

### 12.1 🟡 Tüm Sayfalarda tenant Sorgusu Tekrar Ediliyor

**Dosya:** `resources/views/layouts/app.blade.php`

```php
$tenant = session('tenant_id') ? DB::table('tenants')->find(session('tenant_id')) : null;
```

Her sayfa render'ında tenant verisi veritabanından çekiliyor. Cache veya middleware'de tutulabilir.

---

### 12.2 🟡 Ürün İndeksi Tüm Ürünleri Çekiyor

**Dosya:** `app/Http/Controllers/ProductController.php` — `index()`

**Sorun:** Sayfalama (pagination) yok. 1000 ürünü olan bir tenant tüm ürünleri tek sorguda çekiyor. DataTables client-side filtreleme yapıyor, sunucu yük altında.

---

## 13. QR Menü Sayfası (Public)

---

### 13.1 🟡 Arama Fonksiyonu Tüm Kategorileri Açıyor

**Dosya:** `resources/views/public/menu.blade.php` — JavaScript `applySearch()`

```javascript
collapses.forEach(function(el) {
    bootstrap.Collapse.getOrCreateInstance(el).show();
});
```

**Sorun:** Accordion layout'ta arama yapıldığında tüm kategoriler açılıyor. 20 kategori varsa arama yapan müşteri dev bir scroll ile karşılaşıyor. Sadece eşleşen kategorilerin açılması daha iyi UX olurdu.

---

### 13.2 🟡 Masaüstü Uyarı Sayfasında Dil Hardcoded

**Dosya:** `resources/views/public/menu.blade.php`

```php
{{ $locale === 'tr' ? 'Sipariş Masanda QR Menüsü' : 'Siparis Masanda QR Menu' }}
```

**Sorun:** Masaüstü uyarı mesajı `__()` helper yerine inline ternary ile yazılmış. İngilizce versiyonda "Siparis" (Türkçe kökenli) yazıyor — çevirisiz.

---

### 13.3 🟢 Rezervasyon Butonu Çevrilmemiş

**Dosya:** `resources/views/public/menu.blade.php`

```php
{{ $locale === 'tr' ? 'Rezervasyon' : 'Reservation' }}
```

Bu doğru, ancak aynı sayfanın diğer yerlerinde `__()` helper kullanılıyor. Tutarsız yaklaşım — hepsi ya `__()` ya da inline ternary olmalı.

---

## 14. Özet Tablo

| # | Kısım | Sorun | Seviye |
|---|-------|-------|--------|
| 1.1 | Auth | Tenant sahipliği doğrulanmıyor | 🔴 Kritik |
| 1.2 | Auth | Impersonation deaktivasyon bypass | 🟠 Yüksek |
| 1.3 | Auth | Geliştirici kendi hesabını silebilir | 🟠 Yüksek |
| 1.4 | Auth | Son owner silinebilir | 🟡 Orta |
| 2.1 | Ürünler | Ağırlık/fiyat çift değer kontrolü yok | 🟠 Yüksek |
| 2.2 | Ürünler | Ürün görüntüleme sayacı çalışmıyor | 🟠 Yüksek |
| 2.3 | Ürünler | Sıralama race condition | 🟠 Yüksek |
| 2.4 | Ürünler | Yinelenen ürün adı kontrolü yok | 🟡 Orta |
| 2.5 | Ürünler | 403 yerine doğrulama hatası verilmeli | 🟡 Orta |
| 2.6 | Ürünler | Frontend dosya boyutu kontrolü yok | 🟢 Düşük |
| 3.1 | Kategoriler | product_views yetim veri sorunu | 🟠 Yüksek |
| 3.2 | Kategoriler | Döngüsel referans kontrolü yüzeysel | 🟡 Orta |
| 3.3 | Kategoriler | Toplu eklemede atlanma bildirimi yok | 🟢 Düşük |
| 4.1 | QR/Link | is.gd timeout sayfa donmasına neden oluyor | 🟠 Yüksek |
| 4.2 | QR/Link | Kısa linkin QR'ı oluşturulamıyor | 🟢 Düşük |
| 5.1 | Rezervasyon | Kapasite kontrolü yok | 🔴 Kritik |
| 5.2 | Rezervasyon | Çift rezervasyon engellenmemiş | 🔴 Kritik |
| 5.3 | Rezervasyon | Telefon format doğrulaması yok | 🟠 Yüksek |
| 5.4 | Rezervasyon | Geçmiş tarih kabul ediliyor | 🟡 Orta |
| 5.5 | Rezervasyon | Rate limiting eksik | 🟡 Orta |
| 6.1 | Dev Panel | N+1 sorgu (tenant listesi) | 🟠 Yüksek |
| 6.2 | Dev Panel | Silinmiş tenant impersonation crash | 🟡 Orta |
| 6.3 | Dev Panel | Package toggle race condition | 🟡 Orta |
| 7.1 | Menü Ayarları | Font whitelist yok (CSS injection) | 🟡 Orta |
| 7.2 | Menü Ayarları | Renk kontrast kontrolü yok | 🟢 Düşük |
| 8.1 | Destek | Bilette dosya eki desteği yok | 🟡 Orta |
| 8.2 | Yorumlar | Silme geri alınamaz, onay yok | 🟢 Düşük |
| 9.1 | Dosya Yükleme | Gerçek MIME doğrulaması yok | 🟠 Yüksek |
| 9.2 | Dosya Yükleme | Tenant logosunu güncellemede eski dosya silinmiyor | 🟡 Orta |
| 10.1 | Dil | Hardcoded Türkçe metinler | 🟡 Orta |
| 10.2 | Dil | Bazı hata mesajları çevrilmemiş | 🟢 Düşük |
| 11.1 | Güvenlik | Hata mesajında escape eksikliği | 🟡 Orta |
| 11.2 | Güvenlik | Log kayıtları yetersiz | 🟡 Orta |
| 12.1 | Performans | Her sayfada tenant sorgusu | 🟡 Orta |
| 12.2 | Performans | Ürün indexinde sayfalama yok | 🟡 Orta |
| 13.1 | QR Menü | Arama tüm kategorileri açıyor | 🟡 Orta |
| 13.2 | QR Menü | Masaüstü uyarısı çevrilmemiş | 🟡 Orta |
| 13.3 | QR Menü | Rezervasyon butonu tutarsız çeviri | 🟢 Düşük |

---

## 15. Öncelik Sırası (Önerilen Düzeltme Planı)

### Önce Düzelt (Bu Hafta)
1. **5.2** — Çift rezervasyon engelle (unique constraint + kontrol)
2. **5.1** — Rezervasyon kapasite kontrolü ekle
3. **1.1** — Tenant sahipliği doğrulamasını güçlendir
4. **2.2** — Ürün görüntüleme sayacının neden çalışmadığını doğrula
5. **9.1** — `getimagesize()` ile gerçek image doğrulaması ekle

### Sonra Düzelt (Bu Sprint)
6. **4.1** — is.gd çağrısını queue/async'e taşı
7. **5.3** — Telefon format validasyonu (regex)
8. **5.4** — `after_or_equal:today` kuralı ekle
9. **5.5** — Rezervasyon route'una `throttle:10,1` ekle
10. **3.1** — Kategori silindiğinde `product_views` temizle
11. **2.3** — Sıralama için `DB::raw` veya lock kullan
12. **6.1** — Developer index N+1 sorgularını join'e çevir
13. **9.2** — TenantController'da eski logo silme ekle
14. **7.1** — Font family whitelist kontrolü
15. **10.1** — Hardcoded Türkçe metinleri dil dosyalarına taşı

### İleride İyileştir (Backlog)
- Soft delete implementasyonu
- Daha güçlü audit log
- Frontend dosya boyutu kontrolü
- Ürün pagination
- Renk kontrast doğrulaması

---

*Bu rapor 2026-03-20 tarihinde statik kod analizi ile oluşturulmuştur. Dinamik testler (integration test, load test) bu raporun kapsamı dışındadır.*
