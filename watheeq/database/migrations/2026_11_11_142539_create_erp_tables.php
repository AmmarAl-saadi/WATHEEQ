<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tenants / Companies table
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('tax_number')->nullable()->index();
            $table->string('commercial_record')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('currency', 10)->default('JOD');
            $table->decimal('tax_rate', 5, 2)->default(16.00);
            $table->string('primary_color', 20)->default('#35573E');
            $table->string('accent_color', 20)->default('#B59341');
            $table->string('print_format', 20)->default('A4');
            $table->text('invoice_notes')->nullable();
            $table->json('dictionary')->nullable();
            $table->json('custom_fields')->nullable();
            $table->timestamps();
        });

        // 2. Chart of Accounts
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('code')->index();
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->string('currency', 10)->default('JOD');
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->timestamps();
            $table->index(['tenant_id', 'code']);
        });

        // 3. Products / Items
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('code')->index();
            $table->string('barcode')->nullable()->index();
            $table->string('name');
            $table->string('category')->default('عام');
            $table->string('unit')->default('قطعة');
            $table->decimal('cost_price', 15, 2)->default(0.00);
            $table->decimal('sale_price', 15, 2)->default(0.00);
            $table->integer('quantity')->default(0);
            $table->integer('min_quantity')->default(5);
            $table->string('warehouse')->default('المستودع الرئيسي');
            $table->json('custom_data')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'barcode']);
        });

        // 4. Contacts (Customers & Suppliers)
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('code')->index();
            $table->string('name');
            $table->enum('type', ['customer', 'supplier', 'both'])->default('customer');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('tax_id')->nullable();
            $table->decimal('credit_limit', 15, 2)->default(5000.00);
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->string('address')->nullable();
            $table->json('custom_data')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'type']);
        });

        // 5. Invoices
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_no')->index();
            $table->enum('type', ['sale', 'purchase', 'sale_return', 'purchase_return'])->default('sale');
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->string('contact_name');
            $table->date('date')->index();
            $table->date('due_date')->nullable();
            $table->enum('status', ['paid', 'partial', 'unpaid'])->default('paid');
            $table->enum('payment_method', ['cash', 'credit', 'cheque', 'card', 'mixed'])->default('cash');
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('tax_total', 15, 2)->default(0.00);
            $table->decimal('discount_total', 15, 2)->default(0.00);
            $table->decimal('grand_total', 15, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->json('custom_data')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'date']);
        });

        // 6. Invoice Line Items
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('item_name');
            $table->string('unit')->default('قطعة');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0.00);
            $table->decimal('discount', 15, 2)->default(0.00);
            $table->decimal('tax', 15, 2)->default(0.00);
            $table->decimal('total', 15, 2)->default(0.00);
            $table->timestamps();
            $table->index('invoice_id');
        });

        // 7. Vouchers (Receipts, Payments & Checks)
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('voucher_no')->index();
            $table->enum('type', ['receipt', 'payment'])->default('receipt');
            $table->date('date')->index();
            $table->string('contact_name');
            $table->decimal('amount', 15, 2)->default(0.00);
            $table->string('currency', 10)->default('JOD');
            $table->enum('payment_type', ['cash', 'cheque', 'bank_transfer'])->default('cash');
            $table->string('bank_name')->nullable();
            $table->string('cheque_no')->nullable()->index();
            $table->date('cheque_due_date')->nullable();
            $table->enum('cheque_status', ['under_collection', 'deposited', 'cleared', 'bounced'])->nullable();
            $table->text('statement');
            $table->timestamps();
            $table->index(['tenant_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('products');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('tenants');
    }
};
