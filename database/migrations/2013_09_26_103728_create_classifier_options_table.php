<?php

use App\Domain\Classifiers\Models\Classifier;
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
        Schema::create('classifier_options', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Classifier::class)
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classifier_options');
    }
};
