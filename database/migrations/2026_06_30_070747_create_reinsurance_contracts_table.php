<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reinsurance_contracts', function (Blueprint $table) {

            $table->id();

            $table->string('name');

            $table->string('type')
                ->default('quota_share');

            $table->decimal(
                'retention_limit',
                18,
                2
            );

            $table->decimal(
                'cession_rate',
                5,
                2
            );

            $table->boolean('active')
                ->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'reinsurance_contracts'
        );
    }
};
