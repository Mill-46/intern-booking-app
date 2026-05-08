<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_usages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
            $table->foreignId('origin_site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->foreignId('destination_site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->dateTime('started_at');
            $table->dateTime('ended_at');
            $table->unsignedInteger('odometer_start');
            $table->unsignedInteger('odometer_end');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_usages');
    }
};
