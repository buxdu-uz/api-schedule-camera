<?php

use App\Domain\Departments\Models\Department;
use App\Domain\Specialities\Models\Speciality;
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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->foreignIdFor(Department::class)
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Speciality::class)->nullable()
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('h_language');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');

    }
};
