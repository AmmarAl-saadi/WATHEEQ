<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Fiscal Years (السنوات والفترات المالية)
        Schema::create('fiscal_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('title'); // e.g. "السنة المالية 2026"
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();
        });

        // 2. Fixed Assets (الأصول الثابتة والإهلاك)
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('asset_code')->index();
            $table->string('name');
            $table->date('purchase_date');
            $table->decimal('purchase_cost', 15, 2);
            $table->decimal('salvage_value', 15, 2)->default(0.00); // قيمة الخردة
            $table->decimal('depreciation_rate', 5, 2); // نسبة الإهلاك السنوي %
            $table->decimal('accumulated_depreciation', 15, 2)->default(0.00); // مجمع الإهلاك
            $table->decimal('book_value', 15, 2); // القيمة الدفترية الحالية
            $table->string('location')->default('المقر الرئيسي');
            $table->timestamps();
        });

        // 3. Bank Reconciliation Statements (مطابقة كشوف الحسابات البنكية)
        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->date('statement_date');
            $table->decimal('bank_statement_balance', 15, 2); // رصيد كشف البنك الفعلي
            $table->decimal('book_balance', 15, 2); // رصيد الدفاتر في النظام
            $table->decimal('difference', 15, 2)->default(0.00); // الفارق إن وجد
            $table->enum('status', ['balanced', 'pending'])->default('balanced');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Tax Declaration Reports (الإقرارات الضريبية الشهرية والربع سنوية)
        Schema::create('tax_declarations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('period'); // e.g. "الربع الرابع 2026"
            $table->decimal('taxable_sales', 15, 2)->default(0.00); // المبيعات الخاضعة
            $table->decimal('output_tax', 15, 2)->default(0.00); // ضريبة المخرجات
            $table->decimal('taxable_purchases', 15, 2)->default(0.00); // المشتريات الخاضعة
            $table->decimal('input_tax', 15, 2)->default(0.00); // ضريبة المدخلات
            $table->decimal('net_tax_payable', 15, 2)->default(0.00); // صافي الضريبة المستحقة للدفع
            $table->enum('status', ['draft', 'submitted', 'paid'])->default('submitted');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_declarations');
        Schema::dropIfExists('bank_reconciliations');
        Schema::dropIfExists('fixed_assets');
        Schema::dropIfExists('fiscal_years');
    }
};
