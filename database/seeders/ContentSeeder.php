<?php

namespace Database\Seeders;

use App\Models\ContentBlock;
use App\Models\OpeningHour;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Otevírací doba (Pondělí - Neděle)
        // Přednastavíme: 08:00 - 24:00 (Bar), 11:00 - 22:00 (Kuchyně)
        for ($i = 1; $i <= 7; $i++) {
            OpeningHour::create([
                'day_of_week' => $i,
                'bar_open' => '08:00',
                'bar_close' => '23:59',
                'kitchen_open' => '11:00',
                'kitchen_close' => '22:00',
                'is_closed' => false,
            ]);
        }

        // 2. Základní textové bloky
        $blocks = [
            [
                'key' => 'homepage_hero',
                'title' => 'Vítejte v Cypher93',
                'content' => 'Káva, umění a večerní underground na Žižkově.',
            ],
            [
                'key' => 'about_us',
                'title' => 'O nás',
                'content' => '<p>Jsme kavárna s galerií, která se večer mění v bar...</p>',
            ],
            [
                'key' => 'footer_contact',
                'title' => 'Kde nás najdete',
                'content' => 'Koněvova ulice, Praha 3...',
            ],
        ];

        foreach ($blocks as $block) {
            ContentBlock::create($block);
        }
    }
}