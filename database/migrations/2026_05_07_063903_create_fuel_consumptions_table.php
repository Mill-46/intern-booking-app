<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->restrictOnDelete();
            $table->decimal('fuel_used', 10, 2);
            $table->timestamp('recorded_at')->index();
            $table->timestamps();

            $table->index(['vehicle_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_consumptions');
    }
};
