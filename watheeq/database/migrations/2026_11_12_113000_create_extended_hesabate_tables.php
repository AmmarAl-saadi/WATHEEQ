<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Phonebook Directory (دليل الهاتف)
        Schema::create('phonebook', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('category')->default('عميل'); // عميل، مورد، جهة حكومية، بنك، شريك
            $table->string('phone');
            $table->string('mobile')->nullable();
            $table->string('company')->nullable();
            $table->string('city')->default('عمان');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Notepad / Daily Reminders (المفكرة والملاحظات)
        Schema::create('notepad_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('priority', ['urgent', 'high', 'normal', 'low'])->default('normal');
            $table->date('due_date')->nullable();
            $table->text('content');
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });

        // 3. Cheque Management & Life Cycle (حافظة الشيكات البنكية)
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('cheque_no')->index();
            $table->enum('type', ['received', 'issued']); // شيك وارد برسم التحصيل أو شيك صادر
            $table->string('bank_name');
            $table->string('branch')->nullable();
            $table->string('drawer_name'); // الساحب
            $table->string('beneficiary'); // المستفيد
            $table->decimal('amount', 15, 2);
            $table->date('due_date');
            $table->enum('status', ['under_collection', 'deposited', 'cleared', 'bounced', 'returned'])->default('under_collection');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Logistics & Delivery Notes (الإرساليات والطلبيات)
        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('dispatch_no')->index();
            $table->enum('type', ['delivery_out', 'receipt_in', 'branch_transfer', 'sales_order']); // إرسالية مبيعات، إرسالية استلام، تحويل فروع، طلبية
            $table->string('party_name'); // الطرف الآخر
            $table->date('date');
            $table->string('driver_name')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->string('source_warehouse')->default('المستودع الرئيسي - عمان');
            $table->string('dest_warehouse')->nullable();
            $table->enum('status', ['pending', 'in_transit', 'delivered', 'received'])->default('delivered');
            $table->decimal('total_qty', 15, 2)->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. General Expenses & Services (المصاريف وسندات الصرف)
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('expense_no')->index();
            $table->string('category'); // إيجار، كهرباء ومياه، ضيافة، قرطاسية، اتصالات، صيانة
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->string('paid_to'); // المدفوع له
            $table->string('payment_method')->default('نقد من الصندوق الرئيسي');
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->nullOnDelete();
            $table->text('description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('dispatches');
        Schema::dropIfExists('cheques');
        Schema::dropIfExists('notepad_notes');
        Schema::dropIfExists('phonebook');
    }
};
