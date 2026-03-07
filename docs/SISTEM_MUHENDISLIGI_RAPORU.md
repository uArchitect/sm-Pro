# Sistem Mühendisliği İnceleme Raporu — sm-Pro

**Tarih:** 2026-03-07  
**Kapsam:** Laravel restoran yönetim uygulaması — mantık hataları, tutarsızlıklar ve iyileştirme önerileri.

---

## 1. Yetkilendirme ve Erişim Kontrolü

### 1.1 Developer paneli route koruması
- **Bulgu:** `/developer/*` route'ları sadece `auth` middleware ile korunuyor; `role:developer` middleware kullanılmıyor. Erişim kontrolü controller içinde `authDev()` ile yapılıyor.
- **Risk:** Tüm developer route'ları tek tek controller'da kontrol edilmezse sızıntı olabilir. Merkezi middleware kullanımı daha güvenli ve bakımı kolaydır.
- **Öneri:** Developer prefix'ine `role:developer` (veya özel bir `developer` middleware) ekleyin.

### 1.2 Pasif restoran ürün sayfası
- **Bulgu:** `QRController::publicProduct()` tenant'ın `is_active` durumunu kontrol etmiyor. `publicMenu()` ve `submitReview()` ise kontrol ediyor.
- **Risk:** Pasif yapılan bir restoranın menü ve değerlendirme formu kapalı olsa da, doğrudan `/menu/{tenantId}/product/{productId}` URL’i ile ürün sayfası açılabilir.
- **Öneri:** `publicProduct()` içinde `$tenant->is_active` kontrolü ekleyin; pasifse 503 veya bilgilendirici sayfa dönün.

---

## 2. Veri bütünlüğü ve tenant izolasyonu

### 2.1 Review silme — defense in depth
- **Bulgu:** `ReviewController::destroy()` önce `where('tenant_id', $tenantId)` ile kaydı buluyor, silerken sadece `where('id', $id)` kullanıyor.
- **Risk:** Düşük (zaten tenant’a ait olduğu doğrulanıyor); fakat silme sorgusunda da `tenant_id` koşulu olması savunma derinliği sağlar.
- **Öneri:** `DB::table('reviews')->where('id', $id)->where('tenant_id', $tenantId)->delete()` kullanın.

### 2.2 Inline update sonrası tekrar okuma
- **Bulgu:** `ProductController::inlineUpdate()` ve `CategoryController::inlineUpdate()` güncellemeden sonra yanıt için `DB::table(...)->find($id)` ile tekrar okuyor; tenant_id filtresi yok.
- **Risk:** Tek veritabanı ve tek tenant kullanımında pratikte ID çakışması beklenmez; yine de tutarlılık için tenant scope’lu okuma tercih edilebilir.
- **Öneri:** `->where('tenant_id', $tenantId)->first()` ile okuyup null kontrolü yapın; `$category` / `$updated` null ise uygun JSON/404 dönün.

---

## 3. Validasyon ve girdi kontrolü

### 3.1 Reorder endpoint’leri
- **Bulgu:** `ProductController::reorder()` ve `CategoryController::reorder()` `order` parametresini validate etmiyor. Gelen değer dizisi veya eleman türü kontrol edilmiyor.
- **Risk:** `order` string veya beklenmeyen yapıda gelirse hata veya yanlış güncelleme olabilir. Zaten `where('tenant_id', $tenantId)` olduğu için başka tenant’ın kaydı güncellenmez.
- **Öneri:** `order` için `array` ve `order.*` için `integer` (ve isteğe bağlı `exists:products,id` / `exists:categories,id` + tenant kontrolü) kuralı ekleyin.

### 3.2 Kategori store — parent_id self-reference
- **Bulgu:** `CategoryController::update()` içinde `parent_id` kategorinin kendisine eşit olamaz diye kontrol var; `store()` içinde yok.
- **Risk:** Store sırasında yeni kayıt henüz id almadığı için self-reference pratikte mümkün değil; mantıksal tutarlılık için update ile aynı kural store’da da belgelenebilir veya ileride “taşıma” senaryosu için düşünülebilir.
- **Öneri:** İsteğe bağlı: store’da da `parent_id != [mevcut üst kategorilerden birinin id’si]` gibi bir kural veya dokümantasyon eklenebilir.

### 3.3 E-posta tekil alanı
- **Bulgu:** Kayıtta `email` için `unique:users,email` kullanılıyor; iki farklı tenant’ta aynı e-posta ile kullanıcı açılamıyor.
- **Risk:** Çok-tenant uygulamalarda genelde tenant bazlı tekil e-posta istenir; mevcut davranış tek global hesap politikasına uygun.
- **Öneri:** Eğer tenant başına aynı e-postaya izin verilecekse: `unique:users,email,NULL,id,tenant_id,'.session('tenant_id')` benzeri bir kural düşünülebilir; aksi halde mevcut kural bilinçli tercih olarak bırakılabilir.

---

## 4. Dosya ve depolama

### 4.1 Tenant silindiğinde logo
- **Bulgu:** `DeveloperController::destroyTenant()` reviews, qr_visits, products, categories, users ve tenant kaydını siliyor; tenant’ın `logo` dosyası storage’dan silinmiyor.
- **Risk:** `storage/app/public/logos/` altında kullanılmayan dosyalar birikir.
- **Öneri:** Tenant silinmeden önce `$tenant->logo` varsa `Storage::disk('public')->delete($tenant->logo)` çağrısı ekleyin.

### 4.2 SVG logo ve XSS
- **Bulgu:** Logo için PNG ve SVG kabul ediliyor. SVG dosyaları içinde script/event handler olabilir; doğrudan `<img src="...">` ile kullanılıyorsa tarayıcı script’i çalıştırmaz, fakat ileride SVG inline veya farklı bağlamda render edilirse XSS riski oluşur.
- **Risk:** Şu anki kullanım (img src) ile düşük; gelecekteki kullanım için orta.
- **Öneri:** SVG’yi kabul ediyorsanız, yükleme sonrası SVG içeriğini sanitize eden bir katman veya sadece güvenilir elementlere izin veren bir kütüphane kullanın; veya risk kabul edilip dokümante edilsin.

---

## 5. Oturum ve kimlik

### 5.1 Impersonation sonrası session
- **Bulgu:** `DeveloperController::impersonate()` ile giriş yapılınca `session(['tenant_id' => $tenant->id])` set ediliyor; `Auth::login($user)` session’ı yeniliyor olabilir.
- **Risk:** Laravel’de `login()` session’ı regenerate etmez; `tenant_id` session’da kalır. Stop impersonate’te `tenant_id` siliniyor. Davranış tutarlı görünüyor.
- **Öneri:** İsteğe bağlı: impersonation başlangıç/bitişinde `session()->regenerate()` ile session fixation’a karşı ek güvenlik sağlanabilir.

### 5.2 Logout sırasında Auth::user()
- **Bulgu:** `AuthController::logout()` içinde `Auth::user()?->tenant_id` kullanılıyor; null-safe.
- **Risk:** Yok.
- **Öneri:** Değişiklik gerekmez.

---

## 6. Locale ve yönlendirme

### 6.1 Locale redirect parametresi
- **Bulgu:** `POST /locale` içinde `redirect` parametresi sadece `str_starts_with($redirect, '/')` ile kontrol ediliyor; protocol-relative veya host içeren URL’ler kabul edilmiyor (iyi).
- **Risk:** Çok düşük; open redirect engellenmiş.
- **Öneri:** İsteğe bağlı: sadece uygulama içi path’lere izin vermek için `Str::startsWith($redirect, ['/']) && !Str::contains($redirect, '//')` gibi ek kontrol eklenebilir.

---

## 7. Veritabanı ve migration

### 7.1 Silme sırası (destroyTenant)
- **Bulgu:** Sıra: reviews → qr_visits → products → categories → users → tenants. Products, category_id FK ile categories’e bağlı; önce products siliniyor, sonra categories. Users, tenant_id ile tenants’e bağlı; önce users sonra tenants. Cascade tanımları da uyumlu.
- **Risk:** Yok.
- **Öneri:** Değişiklik gerekmez.

### 7.2 users.tenant_id nullOnDelete
- **Bulgu:** Tenant silindiğinde ilgili users kayıtları siliniyor (destroyTenant); migration’da users.tenant_id için `nullOnDelete` tanımlı. Yani veritabanı seviyesinde tenant silinirse tenant_id null olur; uygulama ise kullanıcıları da siliyor.
- **Risk:** Migration ile uygulama davranışı uyumlu; tenant silindiğinde kullanıcılar zaten uygulama tarafından silindiği için nullOnDelete pratikte tetiklenmez. Tutarlılık için migration’da cascade tercih edilebilir veya mevcut hali “uygulama her zaman önce users’ı siler” olarak bırakılabilir.
- **Öneri:** İsteğe bağlı: migration’da users için `cascadeOnDelete` yapılabilir; kod zaten users’ı sildiği için davranış değişmez.

---

## 8. Hata mesajları ve kullanıcı deneyimi

### 8.1 Pasif restoran menü cevabı
- **Bulgu:** `publicMenu()` pasif tenant için `abort(503, 'Bu restoran şu anda hizmet vermiyor.')` kullanıyor; mesaj Türkçe sabit.
- **Risk:** Dil seçimi İngilizce iken bile mesaj Türkçe; tutarsızlık.
- **Öneri:** `abort(503, __('messages.tenant_not_available'))` gibi bir çeviri anahtarı kullanın.

### 8.2 Developer paneli mesajları
- **Bulgu:** DeveloperController içinde birçok başarı/hata mesajı Türkçe sabit string (“Restoran bilgileri güncellendi.”, “Bu kullanıcı silinemez.” vb.).
- **Risk:** Uygulama çok dilli ise developer arayüzü de dil dosyalarına taşınmalı.
- **Öneri:** Tüm kullanıcıya dönen mesajları `lang` dosyalarına taşıyın.

---

## 9. Özet tablo

| Madde | Alan | Önem | Özet |
|-------|------|------|------|
| 1.1 | Developer middleware | Orta | Developer route’larına `role:developer` (veya developer middleware) ekleyin |
| 1.2 | publicProduct is_active | Orta | Pasif tenant için ürün sayfasını kapatın veya 503 dönün |
| 2.1 | Review destroy | Düşük | Silme sorgusuna `tenant_id` koşulu ekleyin |
| 2.2 | Inline update read | Düşük | Güncelleme sonrası okumada tenant scope kullanın |
| 3.1 | Reorder validation | Düşük | `order` ve `order.*` için validasyon ekleyin |
| 3.2 | Category store parent | Çok düşük | İsteğe bağlı self-reference dokümantasyonu |
| 3.3 | Email unique | Bilinçli tercih | Çok tenant’ta aynı email istersen kuralı değiştirin |
| 4.1 | destroyTenant logo | Orta | Tenant silinirken logo dosyasını storage’dan silin |
| 4.2 | SVG XSS | Düşük/Orta | SVG sanitize veya risk dokümantasyonu |
| 5.1 | Impersonation session | Düşük | İsteğe bağlı session regenerate |
| 6.1 | Locale redirect | Çok düşük | İsteğe bağlı ek path kontrolü |
| 7.2 | users FK | İsteğe bağlı | Migration’da cascade tercihi |
| 8.1 | 503 mesajı | Düşük | Çeviri anahtarı kullanın |
| 8.2 | Developer mesajları | Düşük | Dil dosyalarına taşıyın |

---

## 10. Sonuç

Uygulama genel olarak tenant izolasyonu, auth ve kritik iş akışları açısından tutarlı. Tespit edilen noktalar çoğunlukla savunma derinliği, validasyon ve bakım kolaylığı ile ilgili. Öncelik verilmesi önerilenler:

1. **Developer route’larına** merkezi developer middleware eklenmesi  
2. **publicProduct** için pasif tenant kontrolü  
3. **destroyTenant** sırasında tenant logo dosyasının silinmesi  
4. **503 ve developer** mesajlarının dil dosyalarına alınması  
5. **Reorder** ve **review destroy** için küçük validasyon/scope iyileştirmeleri  

Bu rapor, sistem mühendisliği perspektifiyle mantık hataları ve iyileştirme alanlarını madde madde listelemektedir; uygulama kodunda değişiklik yapılmamıştır.
