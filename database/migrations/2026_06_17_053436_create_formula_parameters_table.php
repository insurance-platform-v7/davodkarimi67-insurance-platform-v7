<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formula_parameters', function (Blueprint $table) {
            $table->id();

            $table->foreignId('formula_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('key');
            $table->string('type')->default('number');
            $table->boolean('is_required')->default(true);
            $table->json('default_value')->nullable();

            $table->json('validation_rules')->nullable();
            $table->timestamps();

            $table->unique(['formula_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formula_parameters');
    }
};
