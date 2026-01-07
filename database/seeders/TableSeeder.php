<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        // 2x Stůl pro 5 osob
        Table::create(['name' => 'Stůl 1 (Velký u zdi)', 'capacity' => 5, 'location' => 'indoor']);
        Table::create(['name' => 'Stůl 2 (Velký střed)', 'capacity' => 5, 'location' => 'indoor']);

        // 3x Stůl pro 3 osoby
        Table::create(['name' => 'Stůl 3 (Výloha)', 'capacity' => 3, 'location' => 'indoor']);
        Table::create(['name' => 'Stůl 4 (Bar)', 'capacity' => 3, 'location' => 'indoor']);
        Table::create(['name' => 'Stůl 5 (Kout)', 'capacity' => 3, 'location' => 'indoor']);
        
        // Zahrádka (zatím neaktivní, připravená na jaro)
        Table::create(['name' => 'Zahrádka 1', 'capacity' => 4, 'location' => 'garden', 'is_active' => false]);
    }
}