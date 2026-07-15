<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('front_store_collected_amount', 10, 2)->nullable()->after('collect_front_store');
            $table->dateTime('front_store_collected_at')->nullable()->after('front_store_collected_amount');
            $table->foreignId('front_store_collected_by')
                ->nullable()
                ->after('front_store_collected_at')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('front_store_collected_by');
            $table->dropColumn(['front_store_collected_amount', 'front_store_collected_at']);
        });
    }
};
