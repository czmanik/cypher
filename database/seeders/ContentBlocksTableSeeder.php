<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ContentBlocksTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('content_blocks')->delete();
        
        \DB::table('content_blocks')->insert(array (
            0 => 
            array (
                'id' => 1,
                'key' => 'homepage_hero',
                'title' => 'Vítejte v Cypher93',
                'content' => 'Káva, umění a večerní underground na Žižkově.',
                'image_path' => NULL,
                'created_at' => '2026-01-07 14:42:14',
                'updated_at' => '2026-01-07 14:42:14',
            ),
            1 => 
            array (
                'id' => 2,
                'key' => 'about_us',
                'title' => 'O nás',
                'content' => '<p>Jsme kavárna s galerií, která se večer mění v bar... Jsme parta lidí nadšená do svoji práce. Počínaje výrobou vlastních sítek do kávovarů a do výčepního zařízení, přes lásku ke kuchyni a pořádným burgerům, až po naše nadšení k pivu a péči o něj. Sdružujeme fotografy a umělecké duše v rámci projektu naší Cypher galerie. Přijďte se sami přesvědčit co, jak a proč děláme.</p>',
                'image_path' => NULL,
                'created_at' => '2026-01-07 14:42:14',
                'updated_at' => '2026-01-10 15:44:56',
            ),
            2 => 
            array (
                'id' => 3,
                'key' => 'footer_contact',
                'title' => 'Kde nás najdete',
                'content' => 'Koněvova ulice, Praha 3...',
                'image_path' => NULL,
                'created_at' => '2026-01-07 14:42:14',
                'updated_at' => '2026-01-07 14:42:14',
            ),
        ));
        
        
    }
}