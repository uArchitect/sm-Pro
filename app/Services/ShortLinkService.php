<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ShortLinkService
{
    public function shorten(string $url): ?string
    {
        try {
            $response = Http::connectTimeout(1)->timeout(2)->get('https://is.gd/create.php', [
                'format' => 'simple',
                'url'    => $url,
            ]);

            if ($response->successful()) {
                $short = trim($response->body());

                // is.gd hata durumunda "Error: ..." ile başlayan plain-text döndürür
                if ($short && !str_starts_with($short, 'Error:')) {
                    return $short;
                }
            }
        } catch (\Throwable) {
            // timeout, network error — null döndür
        }

        return null;
    }
}
