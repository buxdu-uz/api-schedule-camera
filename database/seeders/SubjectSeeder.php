<?php

namespace Database\Seeders;

use App\Services\SubjectService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function __construct(protected SubjectService $subjectService)
    {
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "Starting Migrate Hemis Subject metas" . PHP_EOL;
        $this->subjectService->hemisMigration();
    }
}
