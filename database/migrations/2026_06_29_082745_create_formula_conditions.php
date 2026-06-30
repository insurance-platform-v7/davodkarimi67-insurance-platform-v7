<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formula_conditions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('formula_version_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('formula_conditions')
                ->nullOnDelete();

            $table->string('operator')->nullable();

            $table->string('field')->nullable();

            $table->string('comparison')->nullable();

            $table->json('value')->nullable();

            $table->string('group_type')
                ->nullable();

            $table->unsignedInteger('priority')
                ->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'formula_conditions'
        );
    }
};
