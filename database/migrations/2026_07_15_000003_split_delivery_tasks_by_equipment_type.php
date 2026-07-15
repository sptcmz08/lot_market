<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_tasks', function (Blueprint $table) {
            $table->string('task_type', 30)->default('tent')->after('staff_id');
            $table->string('equipment_note', 255)->nullable()->after('task_type');
        });

        $tasks = DB::table('delivery_tasks')
            ->join('bookings', 'bookings.id', '=', 'delivery_tasks.booking_id')
            ->select('delivery_tasks.*', 'bookings.tent_size', 'bookings.counter_size')
            ->orderBy('delivery_tasks.id')
            ->get();

        foreach ($tasks as $task) {
            $primaryType = $task->tent_size ? 'tent' : ($task->counter_size ? 'counter' : 'other');
            DB::table('delivery_tasks')->where('id', $task->id)->update(['task_type' => $primaryType]);

            if ($task->tent_size && $task->counter_size) {
                DB::table('delivery_tasks')->insert([
                    'booking_id' => $task->booking_id,
                    'staff_id' => $task->staff_id,
                    'task_type' => 'counter',
                    'task_date' => $task->task_date,
                    'status' => $task->status,
                    'started_at' => $task->started_at,
                    'completed_at' => $task->completed_at,
                    'problem_note' => $task->problem_note,
                    'created_at' => $task->created_at,
                    'updated_at' => $task->updated_at,
                ]);
            }
        }

        Schema::table('delivery_tasks', function (Blueprint $table) {
            $table->unique(['booking_id', 'task_type'], 'delivery_tasks_booking_type_unique');
            $table->index('task_type');
        });
    }

    public function down(): void
    {
        DB::table('delivery_tasks')->where('task_type', '!=', 'tent')->delete();

        Schema::table('delivery_tasks', function (Blueprint $table) {
            $table->dropUnique('delivery_tasks_booking_type_unique');
            $table->dropIndex(['task_type']);
            $table->dropColumn(['task_type', 'equipment_note']);
        });
    }
};
