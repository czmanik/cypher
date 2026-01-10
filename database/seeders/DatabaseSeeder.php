<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema; // Dulezite!

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Vypneme kontrolu cizich klicu, aby nezalezelo na poradi
        Schema::disableForeignKeyConstraints();

        $this->call([
            UsersTableSeeder::class,
            CategoriesTableSeeder::class,
            TablesTableSeeder::class,
            ProductsTableSeeder::class,
            MenuItemsTableSeeder::class,
            ChecklistTemplatesTableSeeder::class,
            ContentBlocksTableSeeder::class,
            PagesTableSeeder::class,
            OpeningHoursTableSeeder::class,
            
            // Transakcni data (zavisla na tech nahore)
            WorkShiftsTableSeeder::class,
            PlannedShiftsTableSeeder::class,
            EventsTableSeeder::class,
            EventClaimsTableSeeder::class,
            ReservationsTableSeeder::class,
            ShiftChecklistResultsTableSeeder::class,
            ShiftReportItemsTableSeeder::class,
        ]);

        // Zase ji zapneme
        Schema::enableForeignKeyConstraints();
    }
}