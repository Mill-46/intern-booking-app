<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->restrictOnDelete();
            $table->foreignId('driver_id')->constrained()->restrictOnDelete();
            $table->dateTime('start_at')->index();
            $table->dateTime('end_at')->index();
            $table->string('destination');
            $table->text('purpose');
            $table->string('status')->default('draft')->index();
            $table->timestamps();

            $table->index(['status', 'start_at', 'end_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
