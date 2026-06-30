<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formula_execution_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('formula_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('formula_version_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('quote_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('quote_offer_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->json('input_data')->nullable();
            $table->json('output_data')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->boolean('successful')->default(true);
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['formula_id', 'successful']);
            $table->index(['quote_id', 'successful']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formula_execution_logs');
    }
};
