<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            try {
                if (!Schema::hasTable('tenants')) {
                    return;
                }
                $tenant = DB::table('tenants')->first();
                $allUsers = Schema::hasTable('users') ? DB::table('users')->where('is_active', true)->get() : collect();

                $activeUserId = session('active_user_id');
                $currentUser = null;

                if ($activeUserId && $allUsers->isNotEmpty()) {
                    $currentUser = $allUsers->firstWhere('id', $activeUserId);
                }

                $userModules = ($currentUser && is_string($currentUser->enabled_modules))
                    ? json_decode($currentUser->enabled_modules, true)
                    : ($currentUser->enabled_modules ?? ['dashboard', 'definitions', 'invoicing', 'taxes', 'hr', 'hotels', 'pos', 'reports', 'jofotara', 'customizer']);

                if (empty($userModules) || !is_array($userModules)) {
                    $userModules = ['dashboard', 'definitions', 'invoicing', 'taxes', 'hr', 'hotels', 'pos', 'reports', 'jofotara', 'customizer'];
                }

                $userTools = ($currentUser && is_string($currentUser->enabled_tools))
                    ? json_decode($currentUser->enabled_tools, true)
                    : ($currentUser->enabled_tools ?? ['jofotara', 'taxes', 'sales_invoice', 'purchase_invoice', 'receipt_voucher', 'payment_voucher', 'journal_voucher', 'price_adjustment', 'salary_editor', 'hotel_guests', 'cheques', 'dispatches', 'phonebook', 'notepad', 'pos', 'reports']);

                if (empty($userTools) || !is_array($userTools)) {
                    $userTools = ['jofotara', 'taxes', 'sales_invoice', 'purchase_invoice', 'receipt_voucher', 'payment_voucher', 'journal_voucher', 'price_adjustment', 'salary_editor', 'hotel_guests', 'cheques', 'dispatches', 'phonebook', 'notepad', 'pos', 'reports'];
                }

                $view->with([
                    'tenant' => $tenant,
                    'currentUser' => $currentUser,
                    'allUsers' => $allUsers,
                    'userModules' => (array) $userModules,
                    'userTools' => (array) $userTools
                ]);
            } catch (\Throwable $e) {
                // Fail gracefully during maintenance or early bootstrap
            }
        });
    }
}
