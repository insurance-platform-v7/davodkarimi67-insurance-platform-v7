<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_offers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('quote_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('insurance_company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('formula_version_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->decimal('premium', 18, 2);
            $table->decimal('present_value', 18, 2)->default(0);
            $table->decimal('profit', 18, 2)->default(0);
            $table->unsignedInteger('rank')->nullable();
            $table->json('breakdown')->nullable();
            $table->json('meta')->nullable();
            $table->string('status')->default('available');
            $table->timestamps();

            $table->index(['quote_id', 'rank']);
            $table->index(['tenant_id', 'quote_id']);
            $table->index(['insurance_company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_offers');
    }
};
