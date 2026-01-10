<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EventClaimsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('event_claims')->delete();
        
        \DB::table('event_claims')->insert(array (
            0 => 
            array (
                'id' => 1,
                'event_id' => 4,
                'email' => '',
                'phone' => '777777777',
                'instagram' => '',
                'claim_token' => 'uIWkD1pl15iaNM7FX13wgzMQVlayPMgn',
                'redeemed_at' => NULL,
                'created_at' => '2026-01-10 17:53:07',
                'updated_at' => '2026-01-10 17:53:07',
            ),
            1 => 
            array (
                'id' => 2,
                'event_id' => 4,
                'email' => '',
                'phone' => '777777777',
                'instagram' => '',
                'claim_token' => 'QAWeiIgO5JUszlBvtMCXnh8IAdtEJFPd',
                'redeemed_at' => NULL,
                'created_at' => '2026-01-10 17:54:54',
                'updated_at' => '2026-01-10 17:54:54',
            ),
            2 => 
            array (
                'id' => 3,
                'event_id' => 2,
                'email' => '',
                'phone' => '',
                'instagram' => '',
                'claim_token' => '6HjQ8z36EVgcWFXMT2hvMKmmMndaFSqH',
                'redeemed_at' => NULL,
                'created_at' => '2026-01-10 18:22:21',
                'updated_at' => '2026-01-10 18:22:21',
            ),
            3 => 
            array (
                'id' => 4,
                'event_id' => 10,
                'email' => '',
                'phone' => '',
                'instagram' => '',
                'claim_token' => 'LAAcwTOLbhChrdHNNYuWbT9LgP8COYxO',
                'redeemed_at' => NULL,
                'created_at' => '2026-01-10 18:29:54',
                'updated_at' => '2026-01-10 18:29:54',
            ),
            4 => 
            array (
                'id' => 5,
                'event_id' => 10,
                'email' => '',
                'phone' => '',
                'instagram' => '',
                'claim_token' => 'w8NRgpuzIQVi4uAiZfYKtOKiJu0ysTOv',
                'redeemed_at' => NULL,
                'created_at' => '2026-01-10 19:42:57',
                'updated_at' => '2026-01-10 19:42:57',
            ),
        ));
        
        
    }
}