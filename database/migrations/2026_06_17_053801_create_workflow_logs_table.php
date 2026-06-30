<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');

            $table->foreignId('from_state_id')
                ->nullable()
                ->constrained('workflow_states')
                ->nullOnDelete();

            $table->foreignId('to_state_id')
                ->nullable()
                ->constrained('workflow_states')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('action');
            $table->json('payload')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index(['tenant_id', 'entity_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_logs');
    }
};
