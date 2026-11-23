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
        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'tax_period')) {
                $table->string('tax_period')->nullable()->default('شهري (Monthly)');
            }
            if (!Schema::hasColumn('tenants', 'commercial_register')) {
                $table->string('commercial_register')->nullable()->default('29381-CR');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['tax_period', 'commercial_register']);
        });
    }
};
