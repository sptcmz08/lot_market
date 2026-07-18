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
            $table->json('tent_items')->nullable()->after('tent_quantity');
            $table->json('counter_items')->nullable()->after('counter_quantity');
        });

        DB::table('bookings')->orderBy('id')->each(function ($booking) {
            $updates = [];

            if ($booking->tent_size) {
                $updates['tent_items'] = json_encode([[
                    'size' => $booking->tent_size,
                    'color' => $booking->tent_color,
                    'quantity' => (int) ($booking->tent_quantity ?: 1),
                ]], JSON_UNESCAPED_UNICODE);
            }

            if ($booking->counter_size) {
                $updates['counter_items'] = json_encode([[
                    'size' => $booking->counter_size,
                    'color' => $booking->counter_color,
                    'quantity' => (int) ($booking->counter_quantity ?: 1),
                ]], JSON_UNESCAPED_UNICODE);
            }

            if ($updates) {
                DB::table('bookings')->where('id', $booking->id)->update($updates);
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['tent_items', 'counter_items']);
        });
    }
};
