<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'root', 'title' => 'Суперадмін', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'director', 'title' => 'Керівник', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'advocate', 'title' => 'Адвокат', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'lawyer', 'title' => 'Юрист', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
