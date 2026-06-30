<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_product', function (Blueprint $table) {
            $table->id();

            $table->foreignId('insurance_company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('insurance_product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable();
            $table->timestamps();

            $table->unique(['insurance_company_id', 'insurance_product_id'], 'company_product_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_product');
    }
};
