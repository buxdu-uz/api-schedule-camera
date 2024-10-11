<?php

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
            $table->foreignIdFor(SubjectGroup::class)
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Room::class)
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('date');
            $table->time('start_at');
            $table->time('end_at');
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
