<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add detailed fields to employees table matching Hesabate 3 Employee Card
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'gender')) {
                $table->enum('gender', ['male', 'female'])->default('male');
            }
            if (!Schema::hasColumn('employees', 'marital_status')) {
                $table->string('marital_status')->default('single'); // أعزب، متزوج
            }
            if (!Schema::hasColumn('employees', 'nationality')) {
                $table->string('nationality')->nullable()->default('أردني');
            }
            if (!Schema::hasColumn('employees', 'address')) {
                $table->string('address')->nullable();
            }
            if (!Schema::hasColumn('employees', 'birth_date')) {
                $table->date('birth_date')->nullable();
            }
            if (!Schema::hasColumn('employees', 'mobile')) {
                $table->string('mobile')->nullable();
            }
            if (!Schema::hasColumn('employees', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('employees', 'ref_person_1')) {
                $table->string('ref_person_1')->nullable(); // معرف 1
            }
            if (!Schema::hasColumn('employees', 'ref_person_2')) {
                $table->string('ref_person_2')->nullable(); // معرف 2
            }
            if (!Schema::hasColumn('employees', 'social_security_no')) {
                $table->string('social_security_no')->nullable(); // رقم الضمان الاجتماعي
            }
            if (!Schema::hasColumn('employees', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('employees', 'village')) {
                $table->string('village')->nullable();
            }
            if (!Schema::hasColumn('employees', 'grade')) {
                $table->string('grade')->nullable(); // الدرجة
            }
            if (!Schema::hasColumn('employees', 'is_resident')) {
                $table->boolean('is_resident')->default(true); // مقيم / غير مقيم
            }
            if (!Schema::hasColumn('employees', 'work_nature')) {
                $table->string('work_nature')->nullable()->default('مندوب'); // طبيعة العمل
            }
            if (!Schema::hasColumn('employees', 'education')) {
                $table->string('education')->nullable(); // التحصيل العلمي
            }
            if (!Schema::hasColumn('employees', 'stop_work_date')) {
                $table->date('stop_work_date')->nullable(); // تاريخ التوقف
            }
            if (!Schema::hasColumn('employees', 'notes')) {
                $table->text('notes')->nullable();
            }
            // Financial tab fields
            if (!Schema::hasColumn('employees', 'daily_salary')) {
                $table->decimal('daily_salary', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('employees', 'hourly_rate')) {
                $table->decimal('hourly_rate', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('employees', 'overtime_hourly_rate')) {
                $table->decimal('overtime_hourly_rate', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('employees', 'sales_commission_pct')) {
                $table->decimal('sales_commission_pct', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('employees', 'bank_name')) {
                $table->string('bank_name')->nullable();
            }
            if (!Schema::hasColumn('employees', 'bank_account_no')) {
                $table->string('bank_account_no')->nullable();
            }
            if (!Schema::hasColumn('employees', 'currency_code')) {
                $table->string('currency_code', 10)->default('JOD');
            }
            if (!Schema::hasColumn('employees', 'payment_method')) {
                $table->string('payment_method')->default('نقدي'); // نقدي، شيك، تحويل بنكي
            }
        });

        // 1. Employee Vouchers (سندات الموظفين: مدفوعات، خصم، مكافأة، مواصلات، عمل إضافي، سلفة، بدل سكن)
        if (!Schema::hasTable('employee_vouchers')) {
            Schema::create('employee_vouchers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->default(1);
                $table->string('voucher_type'); // payment, discount, bonus, transport, overtime, advance, housing, dues_settlement
                $table->string('voucher_no')->index();
                $table->string('manual_no')->nullable();
                $table->date('voucher_date');
                $table->unsignedBigInteger('employee_id')->nullable();
                $table->string('employee_name');
                $table->decimal('amount', 15, 2)->default(0);
                $table->string('currency_code', 10)->default('JOD');
                $table->decimal('exchange_rate', 12, 4)->default(1.0000);
                $table->decimal('amount_base_currency', 15, 2)->default(0);
                $table->string('cost_center')->nullable();
                $table->string('voucher_book')->nullable();
                $table->boolean('subject_to_income_tax')->default(false);
                $table->string('category')->nullable(); // نوع السند الفرعي
                $table->text('notes')->nullable();
                $table->string('status')->default('جديد'); // جديد، مرحل، ملغى
                $table->timestamps();
            });
        }

        // 2. Attendance & HR Settings (خصائص ضبط الدوام والإجازات)
        if (!Schema::hasTable('hr_settings')) {
            Schema::create('hr_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->default(1);
                $table->integer('total_allowed_vacation_days')->default(21); // عدد أيام العطل الكلية المسموح
                $table->integer('paid_sick_leave_days')->default(14); // عدد أيام العطل المرضية مدفوعة الأجر
                $table->integer('half_paid_sick_leave_days')->default(14); // عدد أيام العطل المرضية نصف مدفوعة الأجر
                $table->decimal('late_entry_multiplier', 5, 2)->default(1.00); // معامل ضرب تأخير الدخول
                $table->decimal('early_exit_multiplier', 5, 2)->default(1.00); // معامل ضرب الخروج المبكر
                $table->decimal('annual_resident_exemption', 15, 2)->default(9000); // الإعفاءات - مقيم سنوي
                $table->decimal('non_resident_exemption_pct', 5, 2)->default(0.00); // الإعفاءات - غير مقيم
                $table->decimal('days_per_month', 8, 4)->default(30.4375); // عدد الأيام في الشهر
                $table->boolean('count_leave_day_as_overtime')->default(false); // احتساب العمل يوم الإجازة ساعات إضافية
                $table->timestamps();
            });
        }

        // 3. Employee Service Termination (إنهاء خدمة موظف)
        if (!Schema::hasTable('employee_terminations')) {
            Schema::create('employee_terminations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->default(1);
                $table->string('termination_no')->index();
                $table->date('termination_date');
                $table->unsignedBigInteger('employee_id')->nullable();
                $table->string('employee_name');
                $table->string('reason')->nullable();
                $table->text('notes')->nullable();
                $table->string('status')->default('فعال');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_terminations');
        Schema::dropIfExists('hr_settings');
        Schema::dropIfExists('employee_vouchers');
    }
};
