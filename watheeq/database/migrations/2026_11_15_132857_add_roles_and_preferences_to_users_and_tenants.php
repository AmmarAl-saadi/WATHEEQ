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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('admin');
            }
            if (!Schema::hasColumn('users', 'role_name_ar')) {
                $table->string('role_name_ar')->nullable()->default('مدير النظام');
            }
            if (!Schema::hasColumn('users', 'enabled_modules')) {
                $table->text('enabled_modules')->nullable();
            }
            if (!Schema::hasColumn('users', 'enabled_tools')) {
                $table->text('enabled_tools')->nullable();
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('users', 'avatar_color')) {
                $table->string('avatar_color', 20)->default('#1F2E4A');
            }
        });

        Schema::table('tenants', function (Blueprint $table) {
            if (!Schema::hasColumn('tenants', 'system_name')) {
                $table->string('system_name')->default('وثيق');
            }
            if (!Schema::hasColumn('tenants', 'system_tagline')) {
                $table->string('system_tagline')->nullable()->default('منظومة وثيق لإدارة الأعمال');
            }
            if (!Schema::hasColumn('tenants', 'enabled_modules')) {
                $table->text('enabled_modules')->nullable();
            }
            if (!Schema::hasColumn('tenants', 'enabled_tools')) {
                $table->text('enabled_tools')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'role_name_ar', 'enabled_modules', 'enabled_tools', 'is_active', 'avatar_color']);
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['system_name', 'system_tagline', 'enabled_modules', 'enabled_tools']);
        });
    }
};
