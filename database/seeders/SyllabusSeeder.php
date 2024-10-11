<?php

namespace Database\Seeders;

use App\Domain\Syllabus\Models\Syllabus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SyllabusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $syllabus = new Syllabus();
        $syllabus->semester = 1;
        $syllabus->start_date = '2024-09-09';
        $syllabus->end_date = '2025-01-01';
        $syllabus->save();
    }
}
