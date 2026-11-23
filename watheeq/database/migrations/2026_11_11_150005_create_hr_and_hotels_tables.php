<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Employees / HR (بطاقات الموظفين)
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('emp_code')->index();
            $table->string('name');
            $table->string('job_title');
            $table->string('department')->default('المالية والإدارة');
            $table->string('national_id')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('basic_salary', 15, 2);
            $table->decimal('allowances', 15, 2)->default(0.00); // بدلات (سكن، مواصلات)
            $table->decimal('deductions', 15, 2)->default(0.00); // اقتطاعات وضمان اجتماعي
            $table->decimal('net_salary', 15, 2); // صافي الراتب المستحق
            $table->date('hire_date');
            $table->enum('status', ['active', 'on_leave', 'terminated'])->default('active');
            $table->timestamps();
        });

        // 2. Hotel Rooms (الغرف الفندقية)
        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('room_number')->index();
            $table->string('room_type'); // فردية، زوجية، جناح ملكي
            $table->integer('floor')->default(1);
            $table->decimal('nightly_rate', 15, 2);
            $table->enum('status', ['available', 'occupied', 'cleaning', 'maintenance'])->default('available');
            $table->string('current_guest')->nullable();
            $table->timestamps();
        });

        // 3. Hotel Bookings (حجوزات النزلاء)
        Schema::create('hotel_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('hotel_rooms')->cascadeOnDelete();
            $table->string('booking_no')->index();
            $table->string('guest_name');
            $table->string('guest_phone')->nullable();
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('nights')->default(1);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0.00);
            $table->enum('status', ['confirmed', 'checked_in', 'checked_out', 'cancelled'])->default('confirmed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_bookings');
        Schema::dropIfExists('hotel_rooms');
        Schema::dropIfExists('employees');
    }
};
