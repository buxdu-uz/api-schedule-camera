<?php

use App\Domain\Groups\Models\Group;
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
        Schema::create('group_subject_group', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Group::class)->index()
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignIdFor(SubjectGroup::class)->index()
                ->constrained()->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_subject_groups');
    }
};
