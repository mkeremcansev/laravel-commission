<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('commission_type_models', function (Blueprint $table) {
            $table->foreignIdFor(\Mkeremcansev\LaravelCommission\Models\CommissionType::class)->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('model_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_type_models');
    }
};
