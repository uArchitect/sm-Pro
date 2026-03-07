<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoRestaurantSeeder extends Seeder
{
    /**
     * Demo menü için "Fake RESTORANT" tenant'ı, 5 kategori ve her birinde 6 ürün oluşturur.
     * Landing sayfasındaki "Test sayfasına bak" ile bu menü gösterilir.
     */
    public function run(): void
    {
        $existing = DB::table('tenants')->where('restoran_adi', 'Fake RESTORANT')->first();
        if ($existing) {
            $this->command->info('Demo restoran zaten mevcut (tenant_id: ' . $existing->id . '). /demo ile görüntüleyebilirsiniz.');
            return;
        }

        $now = now();

        $tenantId = DB::table('tenants')->insertGetId([
            'is_active' => true,
            'firma_adi' => 'Fake RESTORANT',
            'restoran_adi' => 'Fake RESTORANT',
            'restoran_adresi' => 'Demo Adres Sokak No:1, İstanbul',
            'restoran_telefonu' => '+90 212 000 00 00',
            'logo' => null,
            'ordering_enabled' => false,
            'instagram' => null,
            'facebook' => null,
            'twitter' => null,
            'whatsapp' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $categories = [
            ['name' => 'Ana Yemekler', 'sort_order' => 1],
            ['name' => 'İçecekler', 'sort_order' => 2],
            ['name' => 'Salatalar', 'sort_order' => 3],
            ['name' => 'Tatlılar', 'sort_order' => 4],
            ['name' => 'Çorbalar', 'sort_order' => 5],
        ];

        $categoryProducts = [
            'Ana Yemekler' => [
                ['name' => 'Izgara Köfte', 'description' => 'Pilav ve salata ile', 'price' => 120.00],
                ['name' => 'Tavuk Şiş', 'description' => 'Bulgur pilavı ve közlenmiş biberle', 'price' => 95.00],
                ['name' => 'Lahmacun', 'description' => 'Maydanoz, sumak ve limon ile', 'price' => 45.00],
                ['name' => 'Adana Kebap', 'description' => 'Pide ve mevsim salata ile', 'price' => 135.00],
                ['name' => 'Mantı', 'description' => 'Yoğurt ve sarımsaklı sos', 'price' => 75.00],
                ['name' => 'Etli Nohut', 'description' => 'Pilav ve turşu ile', 'price' => 85.00],
            ],
            'İçecekler' => [
                ['name' => 'Ayran', 'description' => 'Ev yapımı soğuk ayran', 'price' => 25.00],
                ['name' => 'Limonata', 'description' => 'Ev yapımı, naneli', 'price' => 45.00],
                ['name' => 'Türk Kahvesi', 'description' => 'Geleneksel orta şekerli', 'price' => 35.00],
                ['name' => 'Çay', 'description' => 'Demlik çay, ince belli', 'price' => 15.00],
                ['name' => 'Soda', 'description' => 'Limonlu soda', 'price' => 20.00],
                ['name' => 'Taze Sıkılmış Portakal Suyu', 'description' => '250 ml', 'price' => 55.00],
            ],
            'Salatalar' => [
                ['name' => 'Çoban Salata', 'description' => 'Domates, salatalık, biber, soğan', 'price' => 40.00],
                ['name' => 'Mevsim Salata', 'description' => 'Yeşil salata, nar ekşili', 'price' => 45.00],
                ['name' => 'Sezar Salata', 'description' => 'Tavuklu, parmesan, kruton', 'price' => 75.00],
                ['name' => 'Gavurdağı Salata', 'description' => 'Cevizli, nar ekşili', 'price' => 55.00],
                ['name' => 'Cacık', 'description' => 'Yoğurt, salatalık, sarımsak', 'price' => 35.00],
                ['name' => 'Haydari', 'description' => 'Yoğurt, dereotu, zeytinyağı', 'price' => 38.00],
            ],
            'Tatlılar' => [
                ['name' => 'Künefe', 'description' => 'Antep fıstıklı, sıcak', 'price' => 85.00],
                ['name' => 'Baklava', 'description' => 'Cevizli, 3 dilim', 'price' => 95.00],
                ['name' => 'Sütlaç', 'description' => 'Fırın sütlaç', 'price' => 45.00],
                ['name' => 'Revani', 'description' => 'Şerbetli, cevizli', 'price' => 42.00],
                ['name' => 'Kazandibi', 'description' => 'Tavuk göğsü kazandibi', 'price' => 55.00],
                ['name' => 'Dondurma', 'description' => '2 top, mevsim meyveli', 'price' => 40.00],
            ],
            'Çorbalar' => [
                ['name' => 'Mercimek Çorbası', 'description' => 'Geleneksel kırmızı mercimek', 'price' => 35.00],
                ['name' => 'Tarhana Çorbası', 'description' => 'Ev yapımı tarhana', 'price' => 38.00],
                ['name' => 'Ezogelin Çorbası', 'description' => 'Bulgur ve domatesli', 'price' => 40.00],
                ['name' => 'İşkembe Çorbası', 'description' => 'Sarımsaklı, limonlu', 'price' => 55.00],
                ['name' => 'Yayla Çorbası', 'description' => 'Yoğurtlu, naneli', 'price' => 38.00],
                ['name' => 'Tavuk Suyu Çorba', 'description' => 'Şehriyeli', 'price' => 42.00],
            ],
        ];

        foreach ($categories as $order => $cat) {
            $categoryId = DB::table('categories')->insertGetId([
                'tenant_id' => $tenantId,
                'parent_id' => null,
                'name' => $cat['name'],
                'image' => null,
                'sort_order' => $cat['sort_order'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $products = $categoryProducts[$cat['name']] ?? [];
            foreach ($products as $i => $p) {
                DB::table('products')->insert([
                    'tenant_id' => $tenantId,
                    'category_id' => $categoryId,
                    'name' => $p['name'],
                    'description' => $p['description'] ?? null,
                    'price' => $p['price'],
                    'image' => null,
                    'sort_order' => $i + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $this->command->info('Demo restoran oluşturuldu: Fake RESTORANT (tenant_id: ' . $tenantId . '). /demo ile görüntüleyebilirsiniz.');
    }
}
