<?php

use App\Models\User;
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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->index()->unique()
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('department_id')->index();
            $table->string('full_name')->index();
            $table->string('short_name',100);
            $table->string('first_name',100)->nullable();
            $table->string('second_name',100)->nullable();
            $table->string('third_name',100)->nullable();
            $table->unsignedInteger('year_of_enter')->nullable();
            $table->unsignedBigInteger('h_employee_status')->index()->nullable();
            $table->unsignedBigInteger('h_employee_type')->index()->nullable();
            $table->date('birth_date')->nullable();
            $table->string('contract_number')->nullable();
            $table->string('decree_number')->nullable();
            $table->string('passport_series',9)->nullable();
            $table->unsignedBigInteger('passport_pinfl')->nullable();
            $table->date('passport_at')->nullable();
            $table->string('passport_where')->nullable();
            $table->date('contract_date')->nullable();
            $table->date('decree_date')->nullable();
            $table->json('tutorGroups')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
