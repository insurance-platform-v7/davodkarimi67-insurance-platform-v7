<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quote_offers', function (Blueprint $table): void {
            $table->foreignId('company_product_id')
                ->after('quote_id')
                ->constrained('company_product')
                ->cascadeOnDelete();

            $table->unique(
                ['quote_id', 'company_product_id'],
                'quote_offer_quote_company_product_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('quote_offers', function (Blueprint $table): void {
            $table->dropUnique('quote_offer_quote_company_product_unique');
            $table->dropConstrainedForeignId('company_product_id');
        });
    }
};
