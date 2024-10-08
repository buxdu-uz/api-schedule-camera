<?php

use App\Domain\Classifiers\Models\ClassifierOption;
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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string("code");
            $table->text('name');
            $table->boolean('active');
            $table->foreignIdFor(ClassifierOption::class,'h_subject_block')->nullable();
            $table->foreignIdFor(ClassifierOption::class,'h_education_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
