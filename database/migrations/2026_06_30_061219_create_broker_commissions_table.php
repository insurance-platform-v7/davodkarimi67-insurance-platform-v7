<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broker_commissions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('broker_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('policy_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal(
                'premium',
                18,
                2
            );

            $table->decimal(
                'rate',
                5,
                2
            );

            $table->decimal(
                'commission_amount',
                18,
                2
            );

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'broker_commissions'
        );
    }
};
