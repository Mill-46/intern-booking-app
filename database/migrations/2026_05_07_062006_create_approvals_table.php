<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approver_id')->constrained('users')->restrictOnDelete();
            $table->unsignedTinyInteger('level');
            $table->string('status')->default('pending')->index();
            $table->text('comment')->nullable();
            $table->timestamp('acted_at')->nullable();
            $table->timestamps();

            $table->unique(['booking_id', 'level']);
            $table->index(['booking_id', 'level', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
