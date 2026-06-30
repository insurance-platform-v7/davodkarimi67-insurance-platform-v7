<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('insurance_company_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('insurance_product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('code');
            $table->integer('priority')->default(100);
            $table->json('conditions')->nullable();
            $table->json('actions')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'code']);
            $table->index(['insurance_product_id', 'is_active']);
            $table->index(['insurance_company_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};
