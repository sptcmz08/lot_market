<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 150);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->nullable()->constrained('zones')->nullOnDelete();
            $table->string('lot_code', 50)->unique();
            $table->string('display_name', 100)->nullable();
            $table->string('svg_element_id', 100)->nullable();
            $table->decimal('position_x', 10, 2)->nullable();
            $table->decimal('position_y', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 50)->unique();
            $table->date('use_date');
            $table->string('shop_name', 150);
            $table->string('customer_phone', 30);
            $table->string('tent_size', 50);
            $table->string('counter_size', 50)->nullable();
            $table->enum('status', [
                'pending_admin',
                'confirmed',
                'assigned',
                'installing',
                'completed',
                'cancelled',
                'problem'
            ])->default('pending_admin');
            $table->text('admin_note')->nullable();
            $table->text('customer_note')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('confirmed_at')->nullable();
            $table->timestamps();

            $table->index('use_date');
            $table->index('customer_phone');
            $table->index('status');
        });

        Schema::create('booking_lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('lot_id')->constrained('lots')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['booking_id', 'lot_id']);
        });

        Schema::create('delivery_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('task_date');
            $table->enum('status', ['waiting', 'started', 'photo_uploaded', 'completed', 'problem'])->default('waiting');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->text('problem_note')->nullable();
            $table->timestamps();

            $table->index('task_date');
            $table->index(['staff_id', 'status']);
        });

        Schema::create('delivery_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_task_id')->constrained('delivery_tasks')->cascadeOnDelete();
            $table->enum('photo_type', ['lot_number', 'before', 'after', 'problem']);
            $table->string('image_path', 255);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->dateTime('taken_at')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->text('ocr_text')->nullable();
            $table->string('ocr_status', 50)->nullable();
            $table->decimal('ocr_confidence', 5, 2)->nullable();
            $table->timestamps();

            $table->index('photo_type');
        });

        Schema::create('status_logs', function (Blueprint $table) {
            $table->id();
            $table->string('loggable_type', 100);
            $table->unsignedBigInteger('loggable_id');
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['loggable_type', 'loggable_id']);
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key', 100)->unique();
            $table->text('setting_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('status_logs');
        Schema::dropIfExists('delivery_photos');
        Schema::dropIfExists('delivery_tasks');
        Schema::dropIfExists('booking_lots');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('lots');
        Schema::dropIfExists('zones');
    }
};
