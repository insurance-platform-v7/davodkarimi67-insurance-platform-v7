<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_assessments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('claim_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('risk_score',5,2)->default(0);

            $table->boolean('fraud_suspected')->default(false);

            $table->text('notes')->nullable();

            $table->json('factors')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'claim_assessments'
        );
    }
};
