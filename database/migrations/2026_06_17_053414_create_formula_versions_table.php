<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formula_versions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('formula_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('version');
            $table->json('formula_json');
            $table->boolean('is_active')->default(false);
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();

            $table->unique(['formula_id', 'version']);
            $table->index(['formula_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formula_versions');
    }
};
