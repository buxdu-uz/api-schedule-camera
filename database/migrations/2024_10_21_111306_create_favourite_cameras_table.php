<?php

use App\Domain\Cameras\Models\Camera;
use App\Domain\Favourites\Models\Favourite;
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
        Schema::create('favourite_cameras', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Favourite::class)->index()
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(Camera::class)->index()
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favourite_cameras');
    }
};
