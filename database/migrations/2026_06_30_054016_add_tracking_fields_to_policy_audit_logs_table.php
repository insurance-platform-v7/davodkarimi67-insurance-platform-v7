<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('policy_audit_logs', function (Blueprint $table) {

            $table->string('correlation_id')
                ->nullable()
                ->after('payload');

            $table->string('trace_id')
                ->nullable()
                ->after('correlation_id');

            $table->string('source')
                ->nullable()
                ->after('trace_id');
        });
    }

    public function down(): void
    {
        Schema::table('policy_audit_logs', function (Blueprint $table) {

            $table->dropColumn([
                'correlation_id',
                'trace_id',
                'source',
            ]);
        });
    }
};
