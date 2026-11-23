<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Promotions / Campaigns (حملات العروض والخصومات)
        Schema::create('pos_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['general', 'points', 'cards_prices', 'period_discount']); 
            // الحملة الشاملة، حملة نقاط، حملة بطاقات وأسعار، خصم خلال فترة
            $table->decimal('discount_rate', 5, 2)->default(0.00); // نسبة الخصم %
            $table->integer('points_multiplier')->default(1);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['active', 'scheduled', 'expired'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. POS Tables / Dining Areas (طاولات وصالات نقاط البيع)
        Schema::create('pos_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('table_number')->index();
            $table->string('section')->default('الصالة الرئيسية'); // الصالة الرئيسية، التراس، العائلات
            $table->integer('capacity')->default(4);
            $table->enum('status', ['available', 'occupied', 'reserved'])->default('available');
            $table->string('current_waiter')->nullable();
            $table->decimal('current_order_total', 15, 2)->default(0.00);
            $table->timestamps();
        });

        // 3. POS Shifts & Cash Drawer Custody (تسليم العهدة وورديات الكاشير)
        Schema::create('pos_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('shift_no')->index();
            $table->string('cashier_name');
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->decimal('opening_cash', 15, 2)->default(100.00);
            $table->decimal('cash_sales', 15, 2)->default(0.00);
            $table->decimal('card_sales', 15, 2)->default(0.00);
            $table->decimal('expected_cash', 15, 2)->default(100.00);
            $table->decimal('actual_cash', 15, 2)->nullable();
            $table->decimal('difference', 15, 2)->default(0.00);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_shifts');
        Schema::dropIfExists('pos_tables');
        Schema::dropIfExists('pos_campaigns');
    }
};
