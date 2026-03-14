<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SeedTestData extends Command
{
    protected $signature   = 'seed:test';
    protected $description = 'Test tenant, kategoriler ve ürünler oluştur (fake fotoğraflarla)';

    public function handle(): int
    {
        $this->info('🌱  Test verileri oluşturuluyor...');

        // ── 1. Tenant ──────────────────────────────────────────────────────────
        $tenantId = DB::table('tenants')->insertGetId([
            'firma_adi'    => 'Test Bistro',
            'restoran_adi' => 'Test Bistro',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
        $this->line("  ✓ Tenant oluşturuldu  → #$tenantId");

        // ── 2. Owner kullanıcı ─────────────────────────────────────────────────
        DB::table('users')->insert([
            'tenant_id'  => $tenantId,
            'name'       => 'Test Kullanıcı',
            'email'      => 'test@testbistro.com',
            'password'   => Hash::make('test123456'),
            'role'       => 'owner',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->line("  ✓ Kullanıcı: test@testbistro.com / test123456");

        // ── 3. Kategoriler ─────────────────────────────────────────────────────
        // Ana kategoriler
        $catAnaImg = $this->fakeImg("tenants/{$tenantId}/categories", 1, 'food');
        $catAnaId  = DB::table('categories')->insertGetId([
            'tenant_id'  => $tenantId,
            'parent_id'  => null,
            'name'       => 'Ana Yemekler',
            'image'      => $catAnaImg,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $catIcImg  = $this->fakeImg("tenants/{$tenantId}/categories", 2, 'drinks');
        $catIcId   = DB::table('categories')->insertGetId([
            'tenant_id'  => $tenantId,
            'parent_id'  => null,
            'name'       => 'İçecekler',
            'image'      => $catIcImg,
            'sort_order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Alt kategori → Ana Yemekler'in altında
        $catAltImg  = $this->fakeImg("tenants/{$tenantId}/categories", 3, 'dessert');
        $catAltId   = DB::table('categories')->insertGetId([
            'tenant_id'  => $tenantId,
            'parent_id'  => $catAnaId,
            'name'       => 'Izgara Çeşitleri',
            'image'      => $catAltImg,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->line("  ✓ Kategoriler: Ana Yemekler → Izgara Çeşitleri (alt), İçecekler");

        // ── 4. Ürünler ─────────────────────────────────────────────────────────
        $this->seedProducts($tenantId, $catAnaId,  'Ana Yemekler',  [
            ['Mercimek Çorbası',     42.00, 'Günlük taze hazırlanan kırmızı mercimek çorbası.'],
            ['Tavuk Şiş',            89.90, 'Marine edilmiş tavuk, közlenmiş biber ile.'],
            ['Karışık Izgara',      145.00, 'Dana, tavuk ve köfteden oluşan karışık tabak.'],
            ['Fırın Makarna',        65.00, 'Beyaz soslu, kaşarlı fırın makarnası.'],
            ['Sebze Güveç',          55.00, 'Mevsim sebzeleriyle hazırlanan sağlıklı güveç.'],
        ], [10, 11, 12, 13, 14]);

        $this->seedProducts($tenantId, $catIcId, 'İçecekler', [
            ['Ayran',                15.00, 'Ev yapımı sade ayran.'],
            ['Türk Çayı',            10.00, 'Demlikten taze demlenen Türk çayı.'],
            ['Limonata',             25.00, 'Taze sıkılmış limon ve nane ile.'],
            ['Cola',                 20.00, '330ml soğuk kutu kola.'],
            ['Şalgam Suyu',          18.00, 'Acı veya sade şalgam suyu.'],
        ], [20, 21, 22, 23, 24]);

        $this->seedProducts($tenantId, $catAltId, 'Izgara Çeşitleri', [
            ['Adana Kebap',         95.00, 'El yapımı acılı Adana kebabı.'],
            ['Urfa Kebap',          95.00, 'Baharatlı Urfa usulü kebap.'],
            ['Kuzu Şiş',           120.00, 'Taze kuzu etinden hazırlanan şiş kebap.'],
            ['Piliç Kanat',         75.00, 'Izgara tavuk kanatları, sarımsaklı sos ile.'],
            ['Köfte',               70.00, 'Kömür ateşinde pişirilmiş ızgara köfte.'],
        ], [30, 31, 32, 33, 34]);

        $this->info("\n✅  Tamamlandı! Toplam: 1 tenant, 1 kullanıcı, 3 kategori, 15 ürün\n");
        $this->table(['Alan', 'Değer'], [
            ['Tenant ID', $tenantId],
            ['Giriş E-posta', 'test@testbistro.com'],
            ['Şifre', 'test123456'],
            ['Menü URL', url('/menu/' . $tenantId)],
        ]);

        return self::SUCCESS;
    }

    private function seedProducts(int $tenantId, int $catId, string $catName, array $products, array $imgSeeds): void
    {
        foreach ($products as $i => [$name, $price, $desc]) {
            $img = $this->fakeImg("tenants/{$tenantId}/products", $imgSeeds[$i], 'food');
            DB::table('products')->insert([
                'tenant_id'   => $tenantId,
                'category_id' => $catId,
                'name'        => $name,
                'description' => $desc,
                'price'       => $price,
                'image'       => $img,
                'sort_order'  => $i + 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
        $this->line("  ✓ $catName: " . count($products) . " ürün eklendi");
    }

    /**
     * picsum.photos'tan rastgele fotoğraf indir, storage'a kaydet.
     * Başarısız olursa null döner (fotoğrafsız da çalışır).
     */
    private function fakeImg(string $dir, int $seed, string $category = 'food'): ?string
    {
        try {
            $url  = "https://picsum.photos/seed/{$seed}/400/400";
            $ctx  = stream_context_create(['http' => ['timeout' => 8]]);
            $data = @file_get_contents($url, false, $ctx);

            if (!$data) return null;

            Storage::disk('uploads')->makeDirectory($dir);
            $path = "{$dir}/{$seed}.jpg";
            Storage::disk('uploads')->put($path, $data);
            return $path;
        } catch (\Throwable) {
            return null;
        }
    }
}
