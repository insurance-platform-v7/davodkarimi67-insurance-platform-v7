<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('policy_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('claim_number')->unique();

            $table->string('status')->default('submitted');

            $table->decimal('requested_amount', 18, 2)->nullable();

            $table->decimal('approved_amount', 18, 2)->nullable();

            $table->text('description')->nullable();

            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
