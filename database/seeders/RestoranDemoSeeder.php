<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class RestoranDemoSeeder extends Seeder
{
    protected int $tenantId = 1;

    /**
     * Tenant id=1 için 5 kategori (1 alt kategori dahil) ve 8-10 ürün ekler.
     * Kategori ve ürün fotoğrafları picsum.photos üzerinden indirilir.
     */
    public function run(): void
    {
        if (!DB::table('tenants')->where('id', $this->tenantId)->exists()) {
            $this->command->warn("Tenant id={$this->tenantId} bulunamadı. Önce tenant oluşturun.");
            return;
        }

        $this->command->info('Tenant 1 için demo kategori ve ürünler ekleniyor...');

        DB::table('products')->where('tenant_id', $this->tenantId)->delete();
        DB::table('categories')->where('tenant_id', $this->tenantId)->delete();

        $baseDirCategories = "tenants/{$this->tenantId}/categories";
        $baseDirProducts   = "tenants/{$this->tenantId}/products";
        Storage::disk('public')->makeDirectory($baseDirCategories);
        Storage::disk('public')->makeDirectory($baseDirProducts);

        $categories = [
            ['name' => 'İçecekler',   'parent_id' => null, 'sort_order' => 1],
            ['name' => 'Yemekler',    'parent_id' => null, 'sort_order' => 2],
            ['name' => 'Tatlılar',    'parent_id' => null, 'sort_order' => 3],
            ['name' => 'Salatalar',   'parent_id' => null, 'sort_order' => 4],
        ];

        $categoryIds = [];
        foreach ($categories as $idx => $cat) {
            $imagePath = $this->downloadImage("{$baseDirCategories}/cat_" . ($idx + 1) . '.jpg', 400, 400);
            $categoryIds[] = DB::table('categories')->insertGetId([
                'tenant_id'  => $this->tenantId,
                'parent_id'  => null,
                'name'       => $cat['name'],
                'image'      => $imagePath,
                'sort_order' => $cat['sort_order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $subCatImage = $this->downloadImage("{$baseDirCategories}/cat_5.jpg", 400, 400);
        $subCategoryId = DB::table('categories')->insertGetId([
            'tenant_id'  => $this->tenantId,
            'parent_id'  => $categoryIds[0],
            'name'       => 'Sıcak İçecekler',
            'image'      => $subCatImage,
            'sort_order' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $idIcecekler = $categoryIds[0];
        $idYemekler  = $categoryIds[1];
        $idTatlilar  = $categoryIds[2];
        $idSalatalar = $categoryIds[3];

        $products = [
            ['name' => 'Türk Kahvesi',   'category_id' => $subCategoryId, 'price' => 35.00,  'description' => 'Geleneksel Türk kahvesi, orta şekerli.'],
            ['name' => 'Çay',            'category_id' => $subCategoryId, 'price' => 15.00,  'description' => 'Taze demleme çay, ince belli.'],
            ['name' => 'Latte',          'category_id' => $idIcecekler,   'price' => 55.00,  'description' => 'Çift shot espresso, süt köpüğü.'],
            ['name' => 'Limonata',       'category_id' => $idIcecekler,   'price' => 45.00,  'description' => 'Ev yapımı limonata, nane.'],
            ['name' => 'Köfte',          'category_id' => $idYemekler,    'price' => 120.00, 'description' => 'El yapımı köfte, pilav ve salata ile.'],
            ['name' => 'Tavuk Şiş',      'category_id' => $idYemekler,    'price' => 95.00,  'description' => 'Izgara tavuk şiş, bulgur pilavı.'],
            ['name' => 'Künefe',         'category_id' => $idTatlilar,    'price' => 85.00,  'description' => 'Sıcak künefe, antep fıstığı ve kaymak.'],
            ['name' => 'Baklava',        'category_id' => $idTatlilar,    'price' => 75.00,  'description' => 'Cevizli baklava, 5 dilim.'],
            ['name' => 'Çoban Salata',   'category_id' => $idSalatalar,   'price' => 45.00,  'description' => 'Domates, salatalık, soğan, biber.'],
            ['name' => 'Mevsim Salata',  'category_id' => $idSalatalar,   'price' => 55.00,  'description' => 'Roka, ceviz, nar, balzamik.'],
        ];

        foreach ($products as $i => $p) {
            $imgPath = $this->downloadImage("{$baseDirProducts}/prod_" . ($i + 1) . '.jpg', 600, 600);
            DB::table('products')->insert([
                'tenant_id'   => $this->tenantId,
                'category_id' => $p['category_id'],
                'name'        => $p['name'],
                'description' => $p['description'] ?? null,
                'price'       => $p['price'],
                'image'       => $imgPath,
                'sort_order'  => $i + 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $this->command->info('Tamamlandı: 5 kategori (1 alt kategori) ve 10 ürün eklendi.');
    }

    private function downloadImage(string $path, int $w, int $h): ?string
    {
        $url = "https://picsum.photos/{$w}/{$h}?" . uniqid();
        try {
            $response = Http::timeout(15)->get($url);
            if ($response->successful()) {
                Storage::disk('public')->put($path, $response->body());
                return $path;
            }
        } catch (\Throwable $e) {
            $this->command->warn("Görsel indirilemedi: {$path} - " . $e->getMessage());
        }
        return null;
    }
}