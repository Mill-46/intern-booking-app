<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->date('service_date')->index();
            $table->string('service_type');
            $table->string('workshop_name');
            $table->decimal('cost', 14, 2)->default(0);
            $table->string('status')->default('scheduled')->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'service_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_services');
    }
};
