<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('policy_audit_logs', function (Blueprint $table) {
            $table->foreignId('tenant_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();

            $table->index([
                'tenant_id',
                'entity_type',
                'entity_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('policy_audit_logs', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);

            $table->dropIndex(
                'policy_audit_logs_tenant_id_entity_type_entity_id_index'
            );

            $table->dropColumn('tenant_id');
        });
    }
};
