<?php

// File: database/migrations/xxxx_xx_xx_create_policies_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quote_offer_id')->constrained('quote_offers')->cascadeOnDelete();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('policy_number')->unique();
            $table->decimal('premium', 16, 2);
            $table->string('status')->default('quote_created');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
