<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reinsurance_allocations', function (Blueprint $table) {

            $table->id();

            $table->foreignId('policy_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('reinsurance_contract_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('premium', 18, 2);

            $table->decimal('retention', 18, 2);

            $table->decimal('ceded_amount', 18, 2);

            $table->decimal('reinsurer_share', 18, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'reinsurance_allocations'
        );
    }
};
