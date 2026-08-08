<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_product', function (Blueprint $table) {
            $table->foreignId('tenant_id')
                ->nullable()
                ->after('id')
                ->constrained('tenants')
                ->cascadeOnDelete();
        });

        DB::statement('
            UPDATE company_product cp
            SET tenant_id = ic.tenant_id
            FROM insurance_companies ic
            WHERE ic.id = cp.insurance_company_id
        ');

        Schema::table('company_product', function (Blueprint $table) {
            $table->index(
                ['tenant_id', 'insurance_company_id', 'insurance_product_id'],
                'company_product_tenant_idx'
            );

            $table->dropUnique('company_product_unique');

            $table->unique(
                ['tenant_id', 'insurance_company_id', 'insurance_product_id'],
                'company_product_tenant_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('company_product', function (Blueprint $table) {
            $table->dropUnique('company_product_tenant_unique');
            $table->dropIndex('company_product_tenant_idx');
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
    }
};
