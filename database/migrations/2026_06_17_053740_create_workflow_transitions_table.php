<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_transitions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('entity_type');

            $table->foreignId('from_state_id')
                ->constrained('workflow_states')
                ->cascadeOnDelete();

            $table->foreignId('to_state_id')
                ->constrained('workflow_states')
                ->cascadeOnDelete();

            $table->string('action');
            $table->json('conditions')->nullable();
            $table->json('side_effects')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'entity_type', 'action'], 'workflow_transition_action_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_transitions');
    }
};
