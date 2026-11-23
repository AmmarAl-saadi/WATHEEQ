<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تهيئة وتفعيل النظام لأول مرة | وثيق ERP</title>
    
    <!-- Professional Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1F2E4A',
                        slateDark: '#343A40',
                        lightBg: '#F8F9FA',
                        borderColor: '#CED4DA'
                    },
                    fontFamily: {
                        sans: ['Almarai', 'sans-serif'],
                        mono: ['Plus Jakarta Sans', 'monospace']
                    }
                }
            }
        }
    </script>
    <style>
        * {
            letter-spacing: normal !important;
            word-spacing: normal !important;
        }
        body {
            font-family: 'Almarai', sans-serif;
            background: radial-gradient(circle at 50% 0%, #FFFFFF 0%, #F8F9FA 55%, #EDF2F7 100%);
            min-height: 100vh;
        }
        .font-num {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="flex items-center justify-center p-4 py-8">

    <div class="w-full max-w-xl bg-white border border-[#CED4DA] rounded-3xl p-8 sm:p-10 shadow-xl space-y-6">
        
        <!-- Header & Branding (Pure Typography - No Icons) -->
        <div class="text-center space-y-2 pb-3 border-b border-gray-100">
            <div class="inline-block px-3 py-1 bg-[#1F2E4A]/10 border border-[#1F2E4A]/20 rounded-full text-xs font-bold text-[#1F2E4A] mb-1">
                معالج التهيئة والتشغيل لأول مرة
            </div>
            <h1 class="text-2xl font-black text-[#1F2E4A] tracking-tight">
                تهيئة منظومة وثيق | Watheeq ERP
            </h1>
            <p class="text-xs text-gray-500 font-bold">
                قم بتعيين بيانات المنشأة وحساب المدير العام للبدء باستخدام النظام
            </p>
        </div>

        <!-- Feedback Alerts -->
        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs font-bold space-y-1">
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <!-- Setup Form -->
        <form method="POST" action="{{ route('setup.save') }}" class="space-y-5">
            @csrf

            <!-- Section 1: بيانات المنشأة / الشركة -->
            <div class="space-y-3 bg-[#F8F9FA] p-4 rounded-2xl border border-gray-200">
                <h3 class="text-xs font-black text-[#1F2E4A] border-b pb-2">
                    1. بيانات المنشأة والنشاط التجاري
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2 space-y-1">
                        <label class="block text-xs font-bold text-gray-700">
                            اسم المنشأة أو الشركة:
                        </label>
                        <input 
                            type="text" 
                            name="company_name" 
                            value="{{ old('company_name', $tenant->name ?? 'وثيق لإدارة الأعمال') }}" 
                            required 
                            placeholder="مثال: شركة وثيق للتجارة والخدمات"
                            class="w-full px-3 py-2 text-xs bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1F2E4A] font-bold"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">
                            العملة الأساسية:
                        </label>
                        <select 
                            name="currency" 
                            class="w-full px-3 py-2 text-xs bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1F2E4A] font-bold"
                        >
                            <option value="JOD" selected>دينار أردني (JOD)</option>
                            <option value="SAR">ريال سعودي (SAR)</option>
                            <option value="AED">درهم إماراتي (AED)</option>
                            <option value="USD">دولار أمريكي (USD)</option>
                            <option value="EUR">يورو (EUR)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 2: بيانات حساب المدير العام (Super Admin) -->
            <div class="space-y-3 bg-[#F8F9FA] p-4 rounded-2xl border border-gray-200">
                <h3 class="text-xs font-black text-[#1F2E4A] border-b pb-2">
                    2. بيانات حساب مدير النظام (Super Admin)
                </h3>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">
                        الاسم الكامل للمدير:
                    </label>
                    <input 
                        type="text" 
                        name="admin_name" 
                        value="{{ old('admin_name', 'المدير العام') }}" 
                        required 
                        placeholder="مثال: أحمد عبد الله"
                        class="w-full px-3 py-2 text-xs bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1F2E4A] font-bold"
                    >
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">
                            اسم المستخدم للدخول (Username):
                        </label>
                        <input 
                            type="text" 
                            name="admin_username" 
                            value="{{ old('admin_username', 'admin') }}" 
                            required 
                            placeholder="admin"
                            class="w-full px-3 py-2 text-xs bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1F2E4A] font-num font-bold text-left"
                            dir="ltr"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">
                            البريد الإلكتروني:
                        </label>
                        <input 
                            type="email" 
                            name="admin_email" 
                            value="{{ old('admin_email', 'admin@watheeq.jo') }}" 
                            required 
                            placeholder="admin@company.com"
                            class="w-full px-3 py-2 text-xs bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1F2E4A] font-num font-bold text-left"
                            dir="ltr"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">
                            كلمة المرور:
                        </label>
                        <input 
                            type="password" 
                            name="admin_password" 
                            required 
                            placeholder="6 خانات على الأقل"
                            class="w-full px-3 py-2 text-xs bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1F2E4A] font-num font-bold"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">
                            تأكيد كلمة المرور:
                        </label>
                        <input 
                            type="password" 
                            name="admin_password_confirmation" 
                            required 
                            placeholder="إعادة إدخال كلمة المرور"
                            class="w-full px-3 py-2 text-xs bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1F2E4A] font-num font-bold"
                        >
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                class="w-full py-3 px-4 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-black shadow-md hover:shadow-lg transition transform active:scale-[0.99]"
            >
                حفظ الإعدادات وتفعيل النظام والبدء فوراً
            </button>
        </form>

        <div class="text-center pt-2 border-t border-gray-100">
            <p class="text-[11px] text-gray-400 font-medium">
                بإمكانك لاحقاً إنشاء وإدارة عدد غير محدود من الحسابات وتخصيص صلاحياتهم من لوحة الإدارة
            </p>
        </div>

    </div>

</body>
</html>

