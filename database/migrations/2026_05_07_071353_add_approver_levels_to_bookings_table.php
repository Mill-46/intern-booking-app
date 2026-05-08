<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('approver_l1_id')
                ->nullable()
                ->after('driver_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('approver_l2_id')
                ->nullable()
                ->after('approver_l1_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approver_l1_id');
            $table->dropConstrainedForeignId('approver_l2_id');
        });
    }
};
