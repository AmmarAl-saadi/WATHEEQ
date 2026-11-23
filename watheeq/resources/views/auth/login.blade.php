<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | منظومة وثيق لإدارة الأعمال (Watheeq ERP)</title>
    
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
<body class="flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white border border-[#CED4DA] rounded-3xl p-8 sm:p-10 shadow-xl space-y-6">
        
        <!-- Header & Branding (Pure Typography - No Icons) -->
        <div class="text-center space-y-2 pb-2 border-b border-gray-100">
            <div class="inline-block px-3 py-1 bg-[#1F2E4A]/10 border border-[#1F2E4A]/20 rounded-full text-xs font-bold text-[#1F2E4A] mb-1">
                بوابة الدخول الموحدة للنظام
            </div>
            <h1 class="text-2xl font-black text-[#1F2E4A] tracking-tight">
                وثيق | Watheeq
            </h1>
            <p class="text-xs font-bold text-gray-500">
                {{ $tenant->system_tagline ?? 'منظومة وثيق السحابية لإدارة الأعمال | Watheeq ERP' }}
            </p>
            @if($tenant && $tenant->name)
                <div class="text-[11px] font-bold text-gray-700 bg-gray-50 py-1 px-3 rounded-lg border border-gray-200">
                    {{ $tenant->name }}
                </div>
            @endif
        </div>

        <!-- Feedback Alerts -->
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-2.5 rounded-xl text-xs font-bold text-center">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-2.5 rounded-xl text-xs font-bold text-center space-y-1">
                <div>{{ session('error') ?? $errors->first() }}</div>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
            @csrf

            <!-- Username or Email -->
            <div class="space-y-1.5">
                <label for="loginInput" class="block text-xs font-bold text-gray-700">
                    اسم المستخدم أو البريد الإلكتروني:
                </label>
                <input 
                    type="text" 
                    id="loginInput"
                    name="login" 
                    value="{{ old('login') }}" 
                    required 
                    autofocus
                    placeholder="مثال: admin أو cashier"
                    class="w-full px-3.5 py-2.5 text-xs bg-white border border-[#CED4DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1F2E4A] focus:border-transparent font-sans font-bold transition shadow-sm"
                >
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <div class="flex justify-between items-center">
                    <label for="passwordInput" class="block text-xs font-bold text-gray-700">
                        كلمة المرور:
                    </label>
                </div>
                <input 
                    type="password" 
                    id="passwordInput"
                    name="password" 
                    required 
                    placeholder="••••••••"
                    class="w-full px-3.5 py-2.5 text-xs bg-white border border-[#CED4DA] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1F2E4A] focus:border-transparent font-num font-bold transition shadow-sm"
                >
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-gray-600 font-bold select-none">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#1F2E4A] focus:ring-[#1F2E4A] border-gray-300">
                    <span>تذكر تسجيل الدخول على هذا الجهاز</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                class="w-full py-3 px-4 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-black shadow-md hover:shadow-lg transition transform active:scale-[0.99]"
            >
                تسجيل الدخول للمنظومة
            </button>
        </form>

        <!-- Footer Notice -->
        <div class="text-center pt-2 border-t border-gray-100 space-y-1">
            <p class="text-[11px] text-gray-400 font-medium">
                وثيق ERP — نظام محاسبي وإداري متكامل ومرخص
            </p>
        </div>

    </div>

</body>
</html>

