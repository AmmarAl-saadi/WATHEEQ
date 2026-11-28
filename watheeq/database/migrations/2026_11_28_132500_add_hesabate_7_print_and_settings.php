<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Table for Document Print Templates & Customization (14_6i.php)
        if (!Schema::hasTable('print_templates')) {
            Schema::create('print_templates', function (Blueprint $table) {
                $table->id();
                $table->string('document_type')->unique(); // e.g., 'sales_invoice', 'purchase_invoice', 'receipt_voucher', etc.
                $table->string('document_name_ar');
                
                // Header (ترويسة)
                $table->longText('header_html')->nullable();
                $table->string('header_color', 20)->default('#000000');
                
                // Header Options Matrix (رأس)
                $table->json('header_fields')->nullable();
                
                // Line Items Matrix (محتوى)
                $table->json('content_fields')->nullable();
                
                // Footer Options Matrix (تذييل)
                $table->json('footer_fields')->nullable();
                
                // Document Notes & Messaging (ملاحظات السند)
                $table->longText('notes_html')->nullable();
                $table->boolean('send_sms')->default(false);
                $table->text('sms_template')->nullable();
                $table->boolean('send_email')->default(false);
                $table->text('email_template')->nullable();
                
                $table->boolean('apply_header_to_all')->default(false);
                $table->boolean('apply_content_to_all')->default(false);
                $table->boolean('apply_footer_to_all')->default(false);
                
                $table->timestamps();
            });
        }

        // 2. Table for System Preferences & Configurations (14_3i.php)
        if (!Schema::hasTable('system_preferences')) {
            Schema::create('system_preferences', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        // 3. Table for Installment Vouchers (سند الأقساط - 6_5_1i.php)
        if (!Schema::hasTable('installment_vouchers')) {
            Schema::create('installment_vouchers', function (Blueprint $table) {
                $table->id();
                $table->string('voucher_no')->unique();
                $table->date('voucher_date');
                $table->foreignId('customer_id')->nullable()->constrained('contacts')->nullOnDelete();
                $table->string('customer_name')->nullable();
                $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
                $table->string('invoice_type')->default('debit'); // مدين / دائن
                $table->decimal('invoice_total', 15, 3)->default(0);
                $table->decimal('total_amount', 15, 3)->default(0);
                $table->string('recurrence_type')->default('monthly'); // التكرار شهري
                $table->string('manual_voucher_no')->nullable();
                $table->string('status')->default('جديد'); // جديد / مرحل
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 4. Table for Installment Voucher Rows
        if (!Schema::hasTable('installment_voucher_items')) {
            Schema::create('installment_voucher_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('installment_voucher_id')->constrained('installment_vouchers')->cascadeOnDelete();
                $table->integer('installment_no');
                $table->decimal('amount', 15, 3)->default(0);
                $table->date('due_date');
                $table->string('manual_no')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_voucher_items');
        Schema::dropIfExists('installment_vouchers');
        Schema::dropIfExists('system_preferences');
        Schema::dropIfExists('print_templates');
    }
};
