<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
        });

        // Populate usernames for seeded users
        $defaults = [
            1 => 'admin',
            2 => 'accountant',
            3 => 'cashier',
            4 => 'hr',
            5 => 'hotel'
        ];

        foreach (DB::table('users')->get() as $u) {
            $username = $defaults[$u->id] ?? (explode('@', $u->email)[0] ?: 'user_' . $u->id);
            DB::table('users')->where('id', $u->id)->update(['username' => $username]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
