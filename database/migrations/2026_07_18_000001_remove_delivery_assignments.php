<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('delivery_tasks')->whereNotNull('staff_id')->update(['staff_id' => null]);
        DB::table('bookings')->where('status', 'assigned')->update(['status' => 'confirmed']);
    }

    public function down(): void
    {
        // Assignment data intentionally cannot be reconstructed.
    }
};
