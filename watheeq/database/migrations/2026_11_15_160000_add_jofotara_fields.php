<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('jofotara_client_id')->nullable();
            $table->string('jofotara_secret_key')->nullable();
            $table->string('jofotara_activity_no')->default('1');
            $table->string('jofotara_environment')->default('sandbox'); // sandbox or production
            $table->boolean('jofotara_auto_sync')->default(false);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('jofotara_uuid')->nullable()->index();
            $table->string('jofotara_status')->default('PENDING')->index(); // PENDING, SUBMITTED, REJECTED, NOT_SUBMITTED
            $table->text('jofotara_qr')->nullable();
            $table->text('jofotara_message')->nullable();
            $table->timestamp('jofotara_submitted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'jofotara_client_id',
                'jofotara_secret_key',
                'jofotara_activity_no',
                'jofotara_environment',
                'jofotara_auto_sync'
            ]);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'jofotara_uuid',
                'jofotara_status',
                'jofotara_qr',
                'jofotara_message',
                'jofotara_submitted_at'
            ]);
        });
    }
};

