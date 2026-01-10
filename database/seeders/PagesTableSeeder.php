<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('pages')->delete();
        
        \DB::table('pages')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'O nás',
                'slug' => 'o-nas',
                'content' => '[{"type":"text_image","data":{"title":"nadpis","text":"<p>a tady n\\u011bjak\\u00fd hezk\\u00fd text<\\/p>","image":"pages-content\\/01KEMFQ47Z8FX3MSZRDK7DCKTR.jpg","layout":"left"}},{"type":"quote","data":{"text":"Spolu jsme \\u017di\\u017ekov <3","author":"t\\u00fdm cypher"}}]',
                'is_active' => 1,
                'created_at' => '2026-01-10 16:14:17',
                'updated_at' => '2026-01-10 18:36:57',
            ),
        ));
        
        
    }
}