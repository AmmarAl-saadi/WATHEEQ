<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('hotel_guests')) {
            Schema::create('hotel_guests', function (Blueprint $table) {
                $table->id();
                $table->string('guest_no')->unique();
                $table->string('name');
                $table->string('national_id')->nullable();
                $table->date('birth_date')->nullable();
                $table->string('address')->nullable();
                $table->string('phone')->nullable();
                $table->boolean('is_blacklisted')->default(false);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('salary_adjustments')) {
            Schema::create('salary_adjustments', function (Blueprint $table) {
                $table->id();
                $table->string('voucher_no')->unique();
                $table->date('date');
                $table->string('employee_code')->nullable();
                $table->string('employee_name');
                $table->decimal('monthly_salary', 12, 3)->default(0);
                $table->decimal('daily_salary', 12, 3)->default(0);
                $table->decimal('hourly_salary', 12, 3)->default(0);
                $table->decimal('overtime_rate', 12, 3)->default(0);
                $table->decimal('sales_commission', 5, 2)->default(0);
                $table->decimal('collection_commission', 5, 2)->default(0);
                $table->string('department')->nullable();
                $table->string('currency')->default('USD');
                $table->string('weekend_day_1')->nullable();
                $table->string('weekend_day_2')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('price_adjustment_vouchers')) {
            Schema::create('price_adjustment_vouchers', function (Blueprint $table) {
                $table->id();
                $table->string('voucher_no')->unique();
                $table->date('from_date');
                $table->date('to_date');
                $table->string('category')->nullable();
                $table->string('brand')->nullable();
                $table->string('model')->nullable();
                $table->string('manufacturer')->nullable();
                $table->string('status')->default('جديد');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('price_adjustment_vouchers');
        Schema::dropIfExists('salary_adjustments');
        Schema::dropIfExists('hotel_guests');
    }
};

