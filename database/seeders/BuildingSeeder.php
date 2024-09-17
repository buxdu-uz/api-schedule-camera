<?php

namespace Database\Seeders;

use App\Services\BuildingService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    public function __construct(protected BuildingService $buildingService)
    {
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "Starting Migrate Hemis Buildings" . PHP_EOL;
        $this->buildingService->hemisMigration();
    }
}
