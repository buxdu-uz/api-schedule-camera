<?php

use App\Domain\Classifiers\Models\ClassifierOption;
use App\Domain\Departments\Models\Department;
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
        Schema::create('specialities', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Department::class)->index()->nullable()
                ->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->string('code');
            $table->string('name');
            $table->foreignIdFor(ClassifierOption::class,'h_locality_type');
            $table->foreignIdFor(ClassifierOption::class,'h_education_type');
            $table->string('bachelor_specialty')->nullable();
            $table->string('master_specialty')->nullable();
            $table->string('doctorate_specialty')->nullable();
            $table->string('ordinature_specialty')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialities');
    }
};
