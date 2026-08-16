<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reinsurance_contracts', function (Blueprint $table) {
            $table->foreignId('tenant_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();

            $table->index([
                'tenant_id',
                'active',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('reinsurance_contracts', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropIndex([
                'reinsurance_contracts_tenant_id_active_index',
            ]);
            $table->dropColumn('tenant_id');
        });
    }
};
