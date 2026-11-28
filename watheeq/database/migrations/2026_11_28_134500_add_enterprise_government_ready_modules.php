<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Projects, Tenders & BOQ (Section 10 & Visual Appendix Page 53)
        if (!Schema::hasTable('projects')) {
            Schema::create('projects', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->foreignId('customer_id')->nullable()->constrained('contacts')->nullOnDelete();
                $table->decimal('contract_value', 15, 3)->default(0); // قيمة العقد بالدينار الأردني
                $table->decimal('expected_cost', 15, 3)->default(0);  // التكلفة المتوقعة
                $table->decimal('actual_cost', 15, 3)->default(0);    // التكلفة الفعلية
                $table->decimal('billed_amount', 15, 3)->default(0);  // المبالغ المفوترة
                $table->decimal('cash_collected', 15, 3)->default(0); // النقد المحصل
                $table->decimal('retention_rate', 5, 2)->default(10.00); // نسبة المحتجزات %
                $table->decimal('retention_amount', 15, 3)->default(0);
                $table->integer('completion_percentage')->default(0);  // نسبة الإنجاز %
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->enum('status', ['tender', 'approved', 'in_progress', 'on_hold', 'completed', 'cancelled'])->default('in_progress');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('project_boq_items')) {
            Schema::create('project_boq_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
                $table->string('item_code')->nullable();
                $table->string('description');
                $table->string('unit')->default('متر');
                $table->decimal('quantity', 15, 3)->default(1);
                $table->decimal('estimated_unit_cost', 15, 3)->default(0);
                $table->decimal('selling_unit_price', 15, 3)->default(0);
                $table->decimal('total_estimated_cost', 15, 3)->default(0);
                $table->decimal('total_selling_price', 15, 3)->default(0);
                $table->decimal('actual_consumed_cost', 15, 3)->default(0);
                $table->integer('progress_percentage')->default(0);
                $table->timestamps();
            });
        }

        // 2. Three-Way Matching Procurement (Section 06)
        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->id();
                $table->string('po_no')->unique();
                $table->date('po_date');
                $table->foreignId('supplier_id')->nullable()->constrained('contacts')->nullOnDelete();
                $table->decimal('total_amount', 15, 3)->default(0);
                $table->decimal('tax_amount', 15, 3)->default(0);
                $table->enum('status', ['draft', 'approved', 'partially_received', 'received', 'billed', 'closed'])->default('approved');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('goods_receipt_notes')) {
            Schema::create('goods_receipt_notes', function (Blueprint $table) {
                $table->id();
                $table->string('grn_no')->unique();
                $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
                $table->foreignId('supplier_id')->nullable()->constrained('contacts')->nullOnDelete();
                $table->date('receipt_date');
                $table->string('delivery_note_no')->nullable();
                $table->string('warehouse_name')->default('المستودع الرئيسي');
                $table->decimal('total_received_value', 15, 3)->default(0);
                $table->enum('matching_status', ['pending', 'matched', 'variance_detected', 'approved_with_exception'])->default('matched');
                $table->text('inspection_notes')->nullable();
                $table->timestamps();
            });
        }

        // 3. Biometric Attendance Log (Section 09 - ZKTeco & Biometric Importer)
        if (!Schema::hasTable('biometric_attendance_logs')) {
            Schema::create('biometric_attendance_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
                $table->string('device_user_id'); // رقم الموظف على جهاز البصمة
                $table->dateTime('punch_time');
                $table->enum('punch_type', ['check_in', 'check_out', 'break_out', 'break_in'])->default('check_in');
                $table->string('device_ip')->nullable();
                $table->enum('processing_status', ['unprocessed', 'processed', 'duplicate', 'error'])->default('unprocessed');
                $table->timestamps();
            });
        }

        // 4. Fixed Assets Auto-Depreciation History (Section 11)
        if (!Schema::hasTable('asset_depreciation_runs')) {
            Schema::create('asset_depreciation_runs', function (Blueprint $table) {
                $table->id();
                $table->string('run_no')->unique();
                $table->string('period_name'); // e.g., '2026-11'
                $table->date('run_date');
                $table->decimal('total_depreciation', 15, 3)->default(0);
                $table->integer('assets_count')->default(0);
                $table->string('journal_entry_no')->nullable();
                $table->string('status')->default('posted');
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_depreciation_runs');
        Schema::dropIfExists('biometric_attendance_logs');
        Schema::dropIfExists('goods_receipt_notes');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('project_boq_items');
        Schema::dropIfExists('projects');
    }
};
