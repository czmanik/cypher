<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EventsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('events')->delete();
        
        \DB::table('events')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'Výstava: Martin Beck & kalendář rádia Beat 2026',
                'slug' => 'vystava-martin-beck-kalendar-radia-beat-2026',
                'perex' => 'Jako první výstavu u nás vám chceme ukázat originální analogové fotografie z kalendáře rádia Beat pro letošní rok. Zachycují legendy českého rocku a jednu světoou legendu.',
                'description' => NULL,
                'start_at' => '2026-01-01 10:00:00',
                'image_path' => NULL,
                'is_published' => 1,
                'created_at' => '2026-01-08 17:01:28',
                'updated_at' => '2026-01-08 17:01:28',
                'end_at' => '2026-01-30 22:00:00',
                'category' => 'kultura',
                'is_commercial' => 0,
                'capacity_limit' => NULL,
                'required_fields' => NULL,
                'offline_consumed_count' => 0,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => 'Burger víkend #01',
                'slug' => 'burger-vikend-01',
                'perex' => 'Burger víkend - přijď ochutnat naše burgery s klubovou slevou. Burger v menus hranolky a salátem za 250,-',
                'description' => NULL,
                'start_at' => '2026-01-10 12:00:00',
                'image_path' => NULL,
                'is_published' => 1,
                'created_at' => '2026-01-08 21:48:08',
                'updated_at' => '2026-01-10 18:03:00',
                'end_at' => '2026-01-12 18:00:00',
                'category' => 'gastro',
                'is_commercial' => 1,
                'capacity_limit' => 30,
                'required_fields' => '[]',
                'offline_consumed_count' => 0,
            ),
            2 => 
            array (
                'id' => 3,
                'title' => 'Burger víkend #O2',
                'slug' => 'burger-vikend-o2',
                'perex' => 'Burger víkend - přijď ochutnat naše burgery s klubovou slevou. Burger v menu s hranolky a salátem za 250,-',
                'description' => NULL,
                'start_at' => '2026-01-24 10:00:00',
                'image_path' => NULL,
                'is_published' => 1,
                'created_at' => '2026-01-08 22:06:33',
                'updated_at' => '2026-01-10 18:02:33',
                'end_at' => '2026-01-26 18:00:00',
                'category' => 'gastro',
                'is_commercial' => 1,
                'capacity_limit' => 30,
                'required_fields' => '[]',
                'offline_consumed_count' => 0,
            ),
            3 => 
            array (
                'id' => 4,
                'title' => 'Double expresso týden #01',
                'slug' => 'double-expresso-tyden-01',
                'perex' => 'Je libo pořádný nakopávák? Dvojité presso za cenu jednoho pressa. Stačí říct!',
                'description' => NULL,
                'start_at' => '2026-01-12 12:00:00',
                'image_path' => NULL,
                'is_published' => 1,
                'created_at' => '2026-01-08 22:08:44',
                'updated_at' => '2026-01-10 17:50:11',
                'end_at' => '2026-01-16 18:00:00',
                'category' => 'piti',
                'is_commercial' => 1,
                'capacity_limit' => 100,
                'required_fields' => '["phone"]',
                'offline_consumed_count' => 0,
            ),
            4 => 
            array (
                'id' => 5,
                'title' => 'Výstava: Martin Beck & Divadlo Járy Cimmermana',
                'slug' => 'vystava-martin-beck-divadlo-jary-cimmermana',
                'perex' => 'Náš dvorní fotograf a legendy Pražského Žižkova: divadlo Járy CImmermana. Tohle je víc než ochutnávka, tohle je vaše pozvá nka do zákulisí...',
                'description' => NULL,
                'start_at' => '2026-04-01 12:00:00',
                'image_path' => NULL,
                'is_published' => 1,
                'created_at' => '2026-01-08 22:12:34',
                'updated_at' => '2026-01-08 22:12:34',
                'end_at' => '2026-04-30 18:00:00',
                'category' => 'kultura',
                'is_commercial' => 0,
                'capacity_limit' => NULL,
                'required_fields' => NULL,
                'offline_consumed_count' => 0,
            ),
            5 => 
            array (
                'id' => 6,
                'title' => 'Výstava: Jan Křikava - Praha mýma očima',
                'slug' => 'vystava-jan-krikava-praha-myma-ocima',
                'perex' => 'Jan Kříkava je výrazným fotografem ze Žižkova. S radostí vám představujeme práci našeho souseda, fotografa a vítěze několika fotografických soutěží.',
                'description' => NULL,
                'start_at' => '2026-02-01 12:00:00',
                'image_path' => NULL,
                'is_published' => 1,
                'created_at' => '2026-01-08 22:15:26',
                'updated_at' => '2026-01-08 22:15:26',
                'end_at' => '2026-02-28 18:00:00',
                'category' => 'kultura',
                'is_commercial' => 0,
                'capacity_limit' => NULL,
                'required_fields' => NULL,
                'offline_consumed_count' => 0,
            ),
            6 => 
            array (
                'id' => 7,
                'title' => 'Výstava: Živel house & 30 let kultuře',
                'slug' => 'vystava-zivel-house-30-let-kulture',
                'perex' => 'Živel house: tištěný časopis jehož tvorba sahá přes čas i hranice. Parta lidí sledující a komentující kulturu nejen viziální ale i textovou. Přijďte si nejen prohlédnout jejich plakáty, ale i časopisy které komentují a provázejí českou kulturu již 30 let.',
                'description' => NULL,
                'start_at' => '2026-03-01 12:00:00',
                'image_path' => NULL,
                'is_published' => 1,
                'created_at' => '2026-01-08 22:18:32',
                'updated_at' => '2026-01-08 22:18:32',
                'end_at' => '2026-03-31 18:00:00',
                'category' => 'kultura',
                'is_commercial' => 0,
                'capacity_limit' => NULL,
                'required_fields' => NULL,
                'offline_consumed_count' => 0,
            ),
            7 => 
            array (
                'id' => 8,
                'title' => 'Žižkovská noc 2026',
                'slug' => 'zizkovska-noc-2026',
                'perex' => 'Spolu jsme Žižkov',
                'description' => NULL,
                'start_at' => '2026-03-20 12:00:00',
                'image_path' => NULL,
                'is_published' => 1,
                'created_at' => '2026-01-09 21:34:29',
                'updated_at' => '2026-01-09 21:34:29',
                'end_at' => '2026-03-22 22:00:00',
                'category' => 'kultura',
                'is_commercial' => 0,
                'capacity_limit' => NULL,
                'required_fields' => NULL,
                'offline_consumed_count' => 0,
            ),
            8 => 
            array (
                'id' => 9,
                'title' => 'Flat white #100 event',
                'slug' => 'flat-white-100-event',
                'perex' => 'Ochutnej kávu přes naše speciální sítko s bio nebo klasickým mlékem. Prvních 100 káv je za 50% = 1€ / 25,- Kč Spolu jsme Žižkov',
                'description' => '<p>dlouhý popis, který <strong>tlustý</strong> a <em>skloněný.</em></p>',
                'start_at' => '2026-01-01 00:00:00',
                'image_path' => NULL,
                'is_published' => 1,
                'created_at' => '2026-01-09 21:46:03',
                'updated_at' => '2026-01-10 18:35:20',
                'end_at' => '2026-01-31 23:59:59',
                'category' => 'piti',
                'is_commercial' => 1,
                'capacity_limit' => 100,
                'required_fields' => '["phone"]',
                'offline_consumed_count' => 0,
            ),
            9 => 
            array (
                'id' => 10,
                'title' => 'Welcome drink: Slivovice reserved #1000',
                'slug' => 'welcome-drink-slivovice-reserved-1000',
                'perex' => 'Slivovice od nás jako welcome drink? No jo, to se tu děje od prvního dne kdy jsme otevřeli: 26.11.2025 a rozdali jsme mezi vás asi tak tisíc slivovic.',
                'description' => NULL,
                'start_at' => '2025-11-26 08:00:00',
                'image_path' => NULL,
                'is_published' => 1,
                'created_at' => '2026-01-09 22:00:19',
                'updated_at' => '2026-01-10 18:31:46',
                'end_at' => '2026-01-26 22:00:00',
                'category' => 'piti',
                'is_commercial' => 1,
                'capacity_limit' => 1000,
                'required_fields' => '[]',
                'offline_consumed_count' => 890,
            ),
        ));
        
        
    }
}