<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('tent_size', 50)->nullable()->change();
            $table->string('tent_color', 50)->nullable()->after('tent_size');
            $table->string('counter_color', 50)->nullable()->after('counter_size');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['tent_color', 'counter_color']);
            $table->string('tent_size', 50)->nullable(false)->change();
        });
    }
};
