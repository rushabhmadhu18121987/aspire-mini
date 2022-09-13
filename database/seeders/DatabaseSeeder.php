<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        
        //Created Admin user for Loan Approval
        \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@practicalexam.com',
            'password' => bcrypt('Practical@Test#2022'),
            'created_at' => time(),
            'user_type' => 1
        ]);
    }
}