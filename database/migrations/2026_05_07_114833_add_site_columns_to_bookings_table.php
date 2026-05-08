<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('origin_site_id')->nullable()->after('driver_id')->constrained('sites')->nullOnDelete();
            $table->foreignId('destination_site_id')->nullable()->after('origin_site_id')->constrained('sites')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('origin_site_id');
            $table->dropConstrainedForeignId('destination_site_id');
        });
    }
};
