<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AuthController extends Controller
{
    /**
     * Helper to verify if system has at least one super admin account initialized.
     */
    public static function isInitialized(): bool
    {
        if (!Schema::hasTable('users')) {
            return false;
        }
        return DB::table('users')->where('role', 'admin')->exists();
    }

    /**
     * First-time initialization & admin account setup wizard.
     */
    public function showSetup()
    {
        if (self::isInitialized()) {
            return redirect()->route('login');
        }

        $tenant = Schema::hasTable('tenants') ? DB::table('tenants')->first() : null;
        return view('auth.setup', compact('tenant'));
    }

    /**
     * Process initial admin account creation and system activation.
     */
    public function saveSetup(Request $request)
    {
        if (self::isInitialized()) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:150',
            'admin_name' => 'required|string|max:100',
            'admin_username' => 'required|string|max:50|alpha_dash|unique:users,username',
            'admin_email' => 'required|email|max:100|unique:users,email',
            'admin_password' => 'required|string|min:6|confirmed',
            'currency' => 'nullable|string|max:10'
        ], [
            'company_name.required' => 'يرجى إدخال اسم المنشأة أو الشركة.',
            'admin_name.required' => 'يرجى إدخال اسم مدير النظام الكامل.',
            'admin_username.required' => 'يرجى تحديد اسم المستخدم للمدير.',
            'admin_username.unique' => 'اسم المستخدم مستخدم مسبقاً، اختر اسماً آخر.',
            'admin_username.alpha_dash' => 'اسم المستخدم يجب أن يحتوي على أحرف وأرقام وشرطات فقط بدون مسافات.',
            'admin_email.required' => 'يرجى إدخال البريد الإلكتروني للمدير.',
            'admin_email.unique' => 'البريد الإلكتروني مسجل مسبقاً.',
            'admin_password.required' => 'يرجى تعيين كلمة المرور.',
            'admin_password.min' => 'كلمة المرور يجب أن لا تقل عن 6 خانات.',
            'admin_password.confirmed' => 'كلمتا المرور غير متطابقتين.'
        ]);

        // 1. Update or create Tenant Profile
        if (Schema::hasTable('tenants')) {
            DB::table('tenants')->updateOrInsert(
                ['id' => 1],
                [
                    'name' => $validated['company_name'],
                    'system_name' => 'وثيق',
                    'system_tagline' => 'منظومة وثيق لإدارة الأعمال | Watheeq ERP',
                    'currency' => $validated['currency'] ?: 'JOD',
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );
        }

        // 2. Create the Primary Super Admin User
        $adminId = DB::table('users')->insertGetId([
            'name' => $validated['admin_name'],
            'username' => strtolower(trim($validated['admin_username'])),
            'email' => strtolower(trim($validated['admin_email'])),
            'password' => Hash::make($validated['admin_password']),
            'role' => 'admin',
            'role_name_ar' => 'مدير النظام (Super Admin)',
            'enabled_modules' => json_encode([
                'dashboard', 'definitions', 'invoicing', 'hr', 'hotels', 'pos', 'reports', 'jofotara', 'customizer'
            ]),
            'enabled_tools' => json_encode([
                'jofotara', 'sales_invoice', 'purchase_invoice', 'receipt_voucher',
                'payment_voucher', 'journal_voucher', 'price_adjustment', 'salary_editor',
                'hotel_guests', 'cheques', 'dispatches', 'phonebook', 'notepad', 'pos', 'reports'
            ]),
            'is_active' => true,
            'avatar_color' => '#1F2E4A',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 3. Log in the newly created Admin into Session
        session(['active_user_id' => $adminId]);

        return redirect()->route('erp.dashboard')->with('success', 'تم تفعيل وتهيئة المنظومة وإنشاء حساب المدير بنجاح! أهلاً بك.');
    }

    /**
     * Show the Login screen.
     */
    public function showLogin()
    {
        if (!self::isInitialized()) {
            return redirect()->route('setup');
        }

        // If already logged in, redirect to ERP
        $activeUserId = session('active_user_id');
        if ($activeUserId) {
            $user = DB::table('users')->where('id', $activeUserId)->where('is_active', true)->first();
            if ($user) {
                return redirect()->route('erp.dashboard');
            }
        }

        $tenant = Schema::hasTable('tenants') ? DB::table('tenants')->first() : null;
        return view('auth.login', compact('tenant'));
    }

    /**
     * Process User Authentication.
     */
    public function login(Request $request)
    {
        if (!self::isInitialized()) {
            return redirect()->route('setup');
        }

        $validated = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string'
        ], [
            'login.required' => 'يرجى إدخال اسم المستخدم أو البريد الإلكتروني.',
            'password.required' => 'يرجى إدخال كلمة المرور.'
        ]);

        $loginInput = trim($validated['login']);

        // Look up by username or email
        $user = DB::table('users')
            ->where('username', $loginInput)
            ->orWhere('email', strtolower($loginInput))
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return back()->withInput($request->only('login'))->with('error', 'اسم المستخدم أو كلمة المرور غير صحيحة. يرجى التأكد وإعادة المحاولة.');
        }

        if (!$user->is_active) {
            return back()->withInput($request->only('login'))->with('error', 'هذا الحساب معطل حالياً من قبل الإدارة. يرجى مراجعة مدير النظام.');
        }

        // Save session
        session(['active_user_id' => $user->id]);

        return redirect()->intended(route('erp.dashboard'))->with('success', "مرحباً بك مجدداً، {$user->name}!");
    }

    /**
     * Log out of the system.
     */
    public function logout(Request $request)
    {
        session()->forget('active_user_id');
        session()->flush();
        session()->regenerate();

        return redirect()->route('login')->with('success', 'تم تسجيل الخروج بنجاح.');
    }
}

