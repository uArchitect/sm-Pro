# Sipariş Modülü Mühendislik Analizi (Masa + Ev)

## Amaç

Bu doküman, `sm-Pro` içinde kurulacak sipariş modülünün teknik olarak tutarlı, ölçeklenebilir ve bakım maliyeti düşük bir şekilde tasarlanması için hazırlanmıştır.

Hedef:
- Sipariş sadece masadan değil, **evden** de verilebilsin.
- Restoran için komisyonsuz bir alternatif üretelim.
- Modül, mevcut yapıya (tenant, menu, review, reservation) uyumlu olsun.

---

## Ürün Kapsamı (Net Tanım)

Sipariş kanalları:
- **Dine-in (masa içi):** QR veya kısa link ile menüden sipariş.
- **Takeaway (gel-al):** müşteri adrese girmeden şubeden teslim alır.
- **Delivery (adrese teslim):** müşteri adres bilgisi ile sipariş verir.

Sipariş kaynakları (`source`) ile kanal (`channel`) ayrıdır:
- **Public web checkout** (müşteri tarafı)
- **Panel manuel sipariş** (telefonla arayan müşteri için personel tarafı, opsiyonel faz)

Bu üç kanal tek bir `orders` modeli üzerinde birleşmeli.
Not: `qr` bir kanal değil, siparişin menüye giriş kaynağıdır (entry point).

---

## Mimari Prensipler

1. **Tek domain modeli, çok kanal**
   - Masa, gel-al, adrese teslim ayrı tablolar yerine tek sipariş tablosu + type/discriminator yaklaşımı.

2. **State machine zorunlu**
   - Sipariş durumları rastgele değişmemeli; izinli geçişler tanımlı olmalı.

3. **Fiyat snapshot**
   - Sipariş anındaki ürün adı/fiyatı saklanmalı. Sonradan menü güncellense bile eski sipariş bozulmamalı.

4. **Idempotent sipariş oluşturma**
   - Ağ kesintisi, çift tıklama, retry durumunda duplicate sipariş oluşmamalı.

5. **Tenant izolasyonu**
   - Her sorguda tenant boundary korunmalı.

6. **Asenkron olay tasarımı**
   - Bildirim, raporlama, webhook gibi işler sipariş transaction’ından ayrılmalı.

---

## Domain Model Önerisi

### `orders`
Temel alanlar:
- `id`, `tenant_id`, `public_order_no` (insan okunur no: `SM-20260319-1042`)
- `channel` enum: `dine_in | takeaway | delivery`
- `status` enum: `pending | accepted | preparing | ready | out_for_delivery | completed | cancelled | rejected`
- `currency`, `subtotal`, `discount_total`, `service_fee`, `delivery_fee`, `total`
- `payment_method` enum: `cash | card_at_door | card_at_pickup | online`
- `payment_status` enum: `unpaid | paid | refunded | failed`
- `customer_name`, `customer_phone`
- `note` (genel sipariş notu)
- `placed_at`, `accepted_at`, `completed_at`, `cancelled_at`
- `source` enum: `qr | web | panel | api`
- `idempotency_key` (unique, `tenant_id + source + key` bazında)

Kanal bazlı alanlar:
- Dine-in: `table_label` veya `table_id` (ikisi de opsiyonel; self-service senaryosu için boş kalabilir)
- Delivery: `delivery_address_line` (zorunlu), `delivery_lat`, `delivery_lng`, `delivery_zone_id`
- Takeaway: `pickup_eta_minutes` (opsiyonel)

### `order_items`
- `id`, `order_id`, `product_id` (nullable olabilir, ürün sonradan silinirse referans kırılmasın)
- `product_name_snapshot`
- `unit_price_snapshot`, `qty`, `line_total`
- `item_note`
- `options_snapshot` JSON (varyantlar/ekstralar)

### `order_status_events`
- Sipariş timeline/audit için:
- `order_id`, `from_status`, `to_status`, `actor_type`, `actor_id`, `reason`, `created_at`

### `order_notifications`
- `order_id`, `tenant_id`, `type`, `is_read`, `payload`

### Opsiyonel ileri tablolar
- `delivery_zones` (minimum tutar, ücret, max mesafe)
- `order_payments` (online ödeme detayları)
- `order_cancellations` (müşteri/işletme iptal sebebi)

---

## Durum Geçişleri (State Machine)

Geçerli akış (kanal koşullu):
- `pending -> accepted | rejected | cancelled`
- `accepted -> preparing | ready | cancelled`
- `preparing -> ready | cancelled`
- `ready -> completed | cancelled` (dine_in/takeaway)
- `ready -> out_for_delivery | cancelled` (delivery)
- `out_for_delivery -> completed | cancelled`

Kurallar:
- `completed/rejected/cancelled` terminal durumlar.
- Terminalden geri dönüş yok.
- Her geçiş `order_status_events` kaydı üretir.
- `rejected` sadece `pending` durumunda geçerli olmalı (kabul edilmemiş sipariş reddedilir).

---

## Checkout Akışı (Public)

1. Menüde ürün seçimi, qty, not, opsiyon.
2. Kanal seçimi (`dine_in/takeaway/delivery`).
3. Kanal bazlı minimum veri:
   - dine_in: müşteri adı + telefon (+ opsiyonel masa kodu/etiketi)
   - takeaway: müşteri adı + telefon
   - delivery: müşteri adı + telefon + adres
4. Server-side doğrulama:
   - tenant aktif mi
   - ürün satışta mı / stokta mı (varsa)
   - minimum tutar / bölge kuralı (delivery)
5. Transaction içinde sipariş + kalemler + status event (`pending`) oluştur.
6. Notification event yayınla.
7. Müşteriye order tracking ekranı ver.

---

## Panel Akışı (İşletme)

Ekranlar:
- **Yeni Siparişler** (`pending`)
- **Hazırlanıyor**
- **Hazır**
- **Yolda** (delivery)
- **Tamamlananlar**
- **İptal/Red**

Operasyon:
- Toplu durum güncelleme
- Sipariş detay drawer/modal
- Müşteri hızlı arama (telefon)
- Sesli bildirim + canlı badge

Teknik:
- Polling (ilk faz) 5-10 sn
- Sonraki faz: WebSocket/SSE

---

## Siparişin Evden Gelmesi İçin Kritik Tasarım Kararları

1. `channel` zorunlu alan olsun.
2. Dine-in’i masa ID’ye zorlamayın; `table_label` serbest metin fallback bulunsun.
3. Delivery için adres normalize edin:
   - tek text alan + opsiyonel lat/lng
4. Telefon doğrulama:
   - OTP sonraki faz; ilk faz regex + rate limit.
5. Abuse önleme:
   - IP/device throttling
   - captcha (şüpheli pattern için)
6. Kanal-zorunlu alan matrisi backend'de central validator ile yönetilsin (controller içinde dağılmasın).

---

## Güvenlik ve Doğruluk

- Public endpointlerde:
  - `throttle`
  - idempotency key
  - request signature (opsiyonel)
- Fiyatlar clienttan güvenilmez; server ürün fiyatından hesap yapar.
- Sipariş kalemleri için ürün bilgisi `snapshot` zorunlu tutulur (isim/fiyat sonradan değişse de kayıt tutarlı kalır).
- `tenant_id` hiçbir zaman clienttan alınmaz; route + server context.
- SQL sorgularında tenant filtresi standardize edilmelidir.

---

## Performans ve Ölçek

Minimum:
- Indexler:
  - `orders(tenant_id, status, placed_at desc)`
  - `orders(tenant_id, channel, placed_at desc)`
  - `order_items(order_id)`
  - `order_notifications(tenant_id, is_read, created_at desc)`
- Uzun listeler için cursor pagination veya sayfalama.

İleri:
- Event outbox pattern (notification/webhook güvenliği)
- Read model (dashboard için pre-aggregated stats)

---

## Mevcut Koda Uyum Stratejisi

Mevcut rezervasyon yapısı iyi bir şablon:
- `PublicReservationController` -> `PublicOrderController`
- `ReservationNotificationController` -> `OrderManagementController`
- topbar unread badge mekanizması sipariş için kopyalanabilir.

Ama siparişte rezervasyondan farklı olarak:
- kalemler (`order_items`) var
- durum geçişi daha karmaşık
- fiyat hesapları ve ödeme var

Bu nedenle rezervasyonu birebir kopyalamak yerine, yalnızca notification ve listeleme patterni alınmalıdır.

---

## Fazlı Yol Haritası

## Faz 1 (MVP - 2 hafta)
- DB migration: `orders`, `order_items`, `order_status_events`, `order_notifications`
- Public checkout (dine_in + takeaway + delivery)
- Panel sipariş listesi + durum güncelleme
- Notification badge + periyodik polling
- Temel rapor: günlük sipariş adedi, ciro

Çıktı:
- QR menüden ve evden sipariş alınır.
- İşletme panelde sipariş yönetir.

## Faz 2 (Operasyonel güçlendirme)
- Yazıcı entegrasyonu (mutfak fişi)
- Kurye durumları, tahmini hazırlık süresi
- Müşteri sipariş takip sayfası
- İptal/iadeye ilişkin neden kodları

## Faz 3 (Ticari büyüme)
- Online ödeme provider entegrasyonu
- Kampanya/kupon
- Çok şube, bölgesel teslimat kuralları
- API + webhook ekosistemi

---

## Anti-Pattern Uyarıları

- Siparişi rezervasyon tablosuna ek alanla sıkıştırmak
- Durum geçişini frontendden serbest bırakmak
- Kalem fiyatını sadece product join ile sonradan hesaplamak
- Idempotency olmadan sipariş POST endpointi açmak
- Tenant kontrolünü middleware yerine tek tek unutulabilir bırakmak

---

## Önerilen İlk Teknik Backlog

1. Migration tasarımı ve index planı
2. Order domain service (`createOrder`, `transitionStatus`, `validateChannelPayload`)
3. Public checkout controller + request validation
4. Panel order list UI + status action endpoints
5. Notification polling + unread count
6. Basit analytics endpoint
7. E2E test:
   - evden delivery siparişi oluştur
   - panelde `pending -> accepted -> preparing -> completed`
   - duplicate POST idempotency testi

---

## Sonuç

Doğru yaklaşım:
- Siparişi masa bağımlı tasarlamamak,
- kanalları (`dine_in/takeaway/delivery`) tek çekirdek modelde birleştirmek,
- state machine + snapshot + idempotency üçlüsünü en başta kurmak.

Bu mimariyle:
- Bugünkü hedef (komisyonsuz alternatif) hızlı çıkar,
- yarınki ihtiyaçlar (ödeme, kurye, çok şube) yeniden yazım olmadan genişler.

