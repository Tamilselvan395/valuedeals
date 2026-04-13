<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('free_shipping_threshold', 10, 2)->default(99);
            $table->decimal('default_shipping_rate', 10, 2)->default(15);
            $table->string('currency_code', 10)->default('AED');
            $table->string('currency_symbol', 10)->default('AED');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
