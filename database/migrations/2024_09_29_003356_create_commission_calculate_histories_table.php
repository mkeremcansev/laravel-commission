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
        Schema::create('commission_calculate_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\Mkeremcansev\LaravelCommission\Models\Commission::class);
            $table->morphs('model');
            $table->uuid('group_id');
            $table->string('column');
            $table->unsignedBigInteger('original_amount');
            $table->unsignedBigInteger('calculated_amount');
            $table->bigInteger('commission_amount');
            $table->tinyInteger('status');
            $table->tinyInteger('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_calculate_histories');
    }
};
