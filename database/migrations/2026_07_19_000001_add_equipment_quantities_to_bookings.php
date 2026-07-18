<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedSmallInteger('tent_quantity')->nullable()->after('tent_color');
            $table->unsignedSmallInteger('counter_quantity')->nullable()->after('counter_color');
        });

        DB::table('bookings')->whereNotNull('tent_size')->update(['tent_quantity' => 1]);
        DB::table('bookings')->whereNotNull('counter_size')->update(['counter_quantity' => 1]);
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['tent_quantity', 'counter_quantity']);
        });
    }
};
