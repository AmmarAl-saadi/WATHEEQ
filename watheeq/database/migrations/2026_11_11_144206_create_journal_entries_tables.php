<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Cost Centers (مراكز التكلفة)
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('code')->index();
            $table->string('name');
            $table->timestamps();
        });

        // 2. Journal Entries Headers (سندات القيد المحاسبية)
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('entry_no')->index();
            $table->date('date')->index();
            $table->text('narration')->nullable(); // البيان العام للقيد
            $table->decimal('total_debit', 15, 2)->default(0.00);
            $table->decimal('total_credit', 15, 2)->default(0.00);
            $table->enum('status', ['draft', 'posted'])->default('posted');
            $table->string('created_by')->default('مدير النظام');
            $table->timestamps();
            $table->index(['tenant_id', 'date']);
        });

        // 3. Journal Entry Lines (أسطر القيد مدين ودائن)
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->nullOnDelete();
            $table->decimal('debit', 15, 2)->default(0.00);
            $table->decimal('credit', 15, 2)->default(0.00);
            $table->string('description')->nullable(); // بيان السطر
            $table->timestamps();
            $table->index('account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('cost_centers');
    }
};
