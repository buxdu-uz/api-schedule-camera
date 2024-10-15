<?php

use App\Domain\Buildings\Models\Building;
use App\Domain\Rooms\Models\Room;
use App\Domain\SubjectGroups\Models\SubjectGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('generation_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')
                ->index()
                ->comment('biriktirgan o\'qituvchi')
                ->constrained('users') // Explicitly specify the table name here
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignIdFor(SubjectGroup::class)
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('date');
            $table->integer('pair');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generation_schedules');
    }
};
