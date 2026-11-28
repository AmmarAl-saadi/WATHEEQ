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
        // 1. Extend Invoices for Hesabate standards (payment methods, tax inclusive, currency rate)
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'is_tax_inclusive')) {
                    $table->boolean('is_tax_inclusive')->default(false)->after('discount_total');
                }
                if (!Schema::hasColumn('invoices', 'warehouse_id')) {
                    $table->unsignedBigInteger('warehouse_id')->nullable()->default(1)->after('contact_name');
                }
                if (!Schema::hasColumn('invoices', 'exchange_rate')) {
                    $table->decimal('exchange_rate', 10, 4)->default(1.0000)->after('grand_total');
                }
                if (!Schema::hasColumn('invoices', 'currency_code')) {
                    $table->string('currency_code', 10)->default('JOD')->after('exchange_rate');
                }
                if (!Schema::hasColumn('invoices', 'settlement_type')) {
                    $table->string('settlement_type', 50)->default('credit')->after('payment_method'); // cash, credit, cheque, split
                }
            });
        }

        // 2. Extend Invoice Items (batch_number, expiry_date, unit_name, discount_percent)
        if (Schema::hasTable('invoice_items')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                if (!Schema::hasColumn('invoice_items', 'batch_no')) {
                    $table->string('batch_no', 100)->nullable()->after('product_name');
                }
                if (!Schema::hasColumn('invoice_items', 'expiry_date')) {
                    $table->date('expiry_date')->nullable()->after('batch_no');
                }
                if (!Schema::hasColumn('invoice_items', 'unit_name')) {
                    $table->string('unit_name', 50)->default('قطعة')->after('quantity');
                }
                if (!Schema::hasColumn('invoice_items', 'warehouse_id')) {
                    $table->unsignedBigInteger('warehouse_id')->nullable()->default(1)->after('unit_name');
                }
            });
        }

        // 3. Extend Products (tabs, barcode, cost price, expiry, serials)
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'name_en')) {
                    $table->string('name_en', 255)->nullable()->after('name');
                }
                if (!Schema::hasColumn('products', 'print_name')) {
                    $table->string('print_name', 255)->nullable()->after('name_en');
                }
                if (!Schema::hasColumn('products', 'has_serial')) {
                    $table->boolean('has_serial')->default(false)->after('barcode');
                }
                if (!Schema::hasColumn('products', 'min_price')) {
                    $table->decimal('min_price', 15, 2)->default(0)->after('sale_price');
                }
                if (!Schema::hasColumn('products', 'max_price')) {
                    $table->decimal('max_price', 15, 2)->default(0)->after('min_price');
                }
                if (!Schema::hasColumn('products', 'wholesale_price')) {
                    $table->decimal('wholesale_price', 15, 2)->default(0)->after('max_price');
                }
                if (!Schema::hasColumn('products', 'unit_name')) {
                    $table->string('unit_name', 50)->default('قطعة')->after('category');
                }
            });
        }

        // 4. Multi-payment voucher details (Cash + Multi-cheques)
        if (!Schema::hasTable('voucher_cheques')) {
            Schema::create('voucher_cheques', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('voucher_id');
                $table->string('cheque_no', 100);
                $table->string('bank_name', 150);
                $table->string('account_no', 100)->nullable();
                $table->date('due_date');
                $table->string('currency', 10)->default('JOD');
                $table->decimal('exchange_rate', 10, 4)->default(1.0000);
                $table->decimal('amount', 15, 2);
                $table->decimal('amount_base', 15, 2);
                $table->string('status', 50)->default('pending'); // pending, collected, endorsed, returned
                $table->string('statement', 255)->nullable();
                $table->timestamps();
            });
        }

        // 5. Internal Stock Transfers Table
        if (!Schema::hasTable('stock_transfers')) {
            Schema::create('stock_transfers', function (Blueprint $table) {
                $table->id();
                $table->string('transfer_no', 100)->unique();
                $table->date('transfer_date');
                $table->string('from_warehouse', 100)->default('المستودع الرئيسي');
                $table->string('to_warehouse', 100)->default('مستودع المعرض');
                $table->unsignedBigInteger('cost_center_id')->nullable();
                $table->string('manual_ref', 100)->nullable();
                $table->text('notes')->nullable();
                $table->string('status', 50)->default('completed');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('stock_transfer_items')) {
            Schema::create('stock_transfer_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('stock_transfer_id');
                $table->unsignedBigInteger('product_id')->nullable();
                $table->string('product_name', 255);
                $table->decimal('quantity', 15, 2)->default(1);
                $table->string('unit_name', 50)->default('قطعة');
                $table->date('expiry_date')->nullable();
                $table->timestamps();
            });
        }

        // 6. Production & Manufacturing Vouchers
        if (!Schema::hasTable('manufacturing_vouchers')) {
            Schema::create('manufacturing_vouchers', function (Blueprint $table) {
                $table->id();
                $table->string('voucher_no', 100)->unique();
                $table->date('voucher_date');
                $table->string('manual_ref', 100)->nullable();
                $table->string('target_warehouse', 100)->default('المستودع الرئيسي');
                $table->unsignedBigInteger('cost_center_id')->nullable();
                
                // Finished Output Product
                $table->unsignedBigInteger('output_product_id')->nullable();
                $table->string('output_product_name', 255);
                $table->decimal('output_quantity', 15, 2)->default(1);
                $table->string('output_unit', 50)->default('قطعة');
                $table->date('output_expiry')->nullable();
                $table->string('output_batch', 100)->nullable();
                $table->decimal('unit_cost', 15, 2)->default(0);
                $table->decimal('total_cost', 15, 2)->default(0);
                
                $table->text('notes')->nullable();
                $table->string('status', 50)->default('completed');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('manufacturing_items')) {
            Schema::create('manufacturing_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('manufacturing_voucher_id');
                $table->string('type', 20)->default('raw_material'); // raw_material or expense
                $table->unsignedBigInteger('product_id')->nullable();
                $table->string('name', 255);
                $table->string('warehouse', 100)->default('المستودع الرئيسي');
                $table->decimal('quantity', 15, 2)->default(1);
                $table->string('unit_name', 50)->default('قطعة');
                $table->decimal('unit_cost', 15, 2)->default(0);
                $table->decimal('total_cost', 15, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_items');
        Schema::dropIfExists('manufacturing_vouchers');
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('voucher_cheques');
    }
};
