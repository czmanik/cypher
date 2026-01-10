<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 2,
                'name' => 'admin',
                'email' => 'david.biksa@gmail.com',
                'email_verified_at' => NULL,
                'password' => '$2y$12$l6myouPYzqxybdCOP4901uFd8TBakqU5kb0GJENfpLhTYMzQKlt7S',
                'remember_token' => 'ybJh9f1AkA8jhaETC3VDep7EgnpZsthy4JOWYrmGNdes625oMg91ketkRqR7',
                'created_at' => '2026-01-07 14:14:43',
                'updated_at' => '2026-01-07 14:14:43',
                'pin_code' => NULL,
                'hourly_rate' => NULL,
                'salary_type' => 'hourly',
                'is_active' => 1,
                'phone' => NULL,
                'is_manager' => 0,
                'employee_type' => NULL,
            ),
            1 => 
            array (
                'id' => 3,
                'name' => 'Radek Moutayrek',
                'email' => 'info@heavytamper.com',
                'email_verified_at' => NULL,
                'password' => '$2y$12$QJAQ5Jq8j9IhVFJmJcwNeeL0rdPSidy4j4vqMA5/MncaQeaZBuuoy',
                'remember_token' => NULL,
                'created_at' => '2026-01-09 14:31:53',
                'updated_at' => '2026-01-09 15:04:50',
                'pin_code' => '0000',
                'hourly_rate' => 1500,
                'salary_type' => 'fixed',
                'is_active' => 1,
                'phone' => '777777777',
                'is_manager' => 0,
                'employee_type' => 'manager',
            ),
            2 => 
            array (
                'id' => 4,
                'name' => 'David Biksadsky',
                'email' => 'cz.notm@gmail.com',
                'email_verified_at' => NULL,
                'password' => '$2y$12$VIzNMHbwvZ/hqOQ8SPQ3yeMbDQ8ZK2YeS8xNMS8vWmEZ8LBIF3jaC',
                'remember_token' => NULL,
                'created_at' => '2026-01-09 15:31:34',
                'updated_at' => '2026-01-09 15:31:34',
                'pin_code' => '0000',
                'hourly_rate' => 1500,
                'salary_type' => 'fixed',
                'is_active' => 1,
                'phone' => NULL,
                'is_manager' => 0,
                'employee_type' => 'manager',
            ),
            3 => 
            array (
                'id' => 5,
                'name' => 'Matěj',
                'email' => 'test@test.cz',
                'email_verified_at' => NULL,
                'password' => '$2y$12$5sGZDPoY5H4/1x1Nxw.MK.9ZHDvyP1V7Zber5Z3jdk3nqaxQ/YRam',
                'remember_token' => NULL,
                'created_at' => '2026-01-09 15:32:57',
                'updated_at' => '2026-01-09 15:32:57',
                'pin_code' => '0000',
                'hourly_rate' => 180,
                'salary_type' => 'hourly',
                'is_active' => 1,
                'phone' => NULL,
                'is_manager' => 1,
                'employee_type' => 'kitchen',
            ),
        ));
        
        
    }
}