<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // JÍDLO
            ['name' => 'Burgery', 'type' => 'menu', 'sort' => 10],
            ['name' => 'Přílohy & Omáčky', 'type' => 'menu', 'sort' => 15],
            
            // NEALKO
            ['name' => 'Káva', 'type' => 'menu', 'sort' => 20],
            ['name' => 'Čaj', 'type' => 'menu', 'sort' => 25],
            ['name' => 'Limonády', 'type' => 'menu', 'sort' => 30],
            ['name' => 'Nealko koktejly', 'type' => 'menu', 'sort' => 35],
            
            // ALKO
            ['name' => 'Pivo', 'type' => 'menu', 'sort' => 40],
            ['name' => 'Víno', 'type' => 'menu', 'sort' => 50],
            ['name' => 'Koktejly', 'type' => 'menu', 'sort' => 60],
            ['name' => 'Destiláty', 'type' => 'menu', 'sort' => 70],

            // E-SHOP (Merch atd.)
            ['name' => 'Merch', 'type' => 'eshop', 'sort' => 80],
            ['name' => 'Zrnková káva', 'type' => 'eshop', 'sort' => 90],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']), // Automaticky vytvoří "nealko-koktejly"
                'type' => $cat['type'],
                'sort_order' => $cat['sort'],
                'is_visible' => true,
            ]);
        }
    }
}