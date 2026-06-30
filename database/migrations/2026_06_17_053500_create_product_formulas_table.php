<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_formulas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('insurance_product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('insurance_company_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('formula_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('formula_version_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('name');
            $table->string('version')->default('1.0.0');
            $table->json('formula_json')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'insurance_product_id', 'is_active'], 'pf_tenant_product_active_idx');
            $table->index(['insurance_company_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_formulas');
    }
};
