<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('code', 10); // JOD, USD, EUR, SAR, AED
            $table->string('name_ar'); // دينار أردني، دولار أمريكي
            $table->string('name_en')->nullable();
            $table->string('symbol', 10)->default('د.أ');
            $table->decimal('exchange_rate', 12, 6)->default(1.000000); // سعر الصرف مقابل العملة الأساسية
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
