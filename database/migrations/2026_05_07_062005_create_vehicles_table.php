<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('registration_no')->unique();
            $table->string('vehicle_type');
            $table->string('brand');
            $table->string('model');
            $table->decimal('fuel_capacity', 8, 2)->nullable();
            $table->unsignedInteger('mileage')->default(0);
            $table->string('status')->default('available')->index();
            $table->string('owner')->default('company');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
