<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SyllabusSeeder::class,
            ClassifierSeeder::class,
            SubjectSeeder::class,
            DepartmentSeeder::class,
            SpecialitySeeder::class,
            GroupSeeder::class,
            BuildingSeeder::class,
            UserSeeder::class
        ]);
    }
}
