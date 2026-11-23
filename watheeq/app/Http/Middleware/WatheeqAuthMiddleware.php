<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;
use Symfony\Component\HttpFoundation\Response;

class WatheeqAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = trim($request->path(), '/');

        // Allow static assets, favicon, livewire, or debug routes if any
        if ($request->is('static/*') || $request->is('favicon.*') || $request->is('up')) {
            return $next($request);
        }

        // 1. Check if the system has at least one super admin initialized
        $isInitialized = AuthController::isInitialized();
        if (!$isInitialized) {
            if (!$request->is('setup*')) {
                return redirect()->route('setup');
            }
            return $next($request);
        }

        // 2. If already initialized, visiting /setup redirects to /login
        if ($request->is('setup*')) {
            return redirect()->route('login');
        }

        // 3. Allow public unauthenticated routes (login page, login submission, hardware scanner lookup)
        if ($request->is('login*') || $request->is('api/products/lookup*')) {
            return $next($request);
        }

        // 4. Verify Active User Session
        $activeUserId = session('active_user_id');
        if (!$activeUserId) {
            return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول للوصول إلى منظومة وثيق.');
        }

        $currentUser = DB::table('users')->where('id', $activeUserId)->first();
        if (!$currentUser || !$currentUser->is_active) {
            session()->forget('active_user_id');
            return redirect()->route('login')->with('error', 'جلسة الدخول منتهية أو تم تعطيل هذا الحساب. يرجى تسجيل الدخول مجدداً.');
        }

        // 5. Restrict Admin Panel to Super Admin role
        if ($request->is('admin*') || $request->is('customizer*')) {
            if ($currentUser->role !== 'admin') {
                return redirect()->route('erp.dashboard')->with('error', 'عذراً، لوحة الإدارة وتخصيص الحسابات مخصصة لمدير النظام فقط.');
            }
        }

        return $next($request);
    }
}
