<!DOCTYPE html>
<html lang="ar" dir="rtl" class="font-sans">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'منظومة وثيق لإدارة الأعمال | Watheeq ERP')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Offline Barcode & QR Scanner Engine (Zero Dependencies) -->
    <script src="/js/html5-qrcode.min.js"></script>
    <script src="/js/watheeq-scanner.js"></script>
    <!-- Google Fonts: Official Almarai + Plus Jakarta Sans Architecture -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                fontFamily: {
                    sans: ['"Plus Jakarta Sans"', '"Almarai"', 'sans-serif'],
                    mono: ['"Plus Jakarta Sans"', '"Almarai"', 'sans-serif']
                },
                extend: {
                    colors: {
                        ssc: {
                            green: '{{ $tenant->primary_color ?? "#1F2E4A" }}',
                            gold: '{{ $tenant->accent_color ?? "#1F2E4A" }}',
                            surface: '#CED4DA'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Map weights 500 and 600 directly to Almarai Bold to guarantee identical typeface everywhere */
        @font-face {
            font-family: 'Almarai';
            font-style: normal;
            font-weight: 500;
            font-display: swap;
            src: url(https://fonts.gstatic.com/s/almarai/v19/tssoApxBaigK_hnnS-aghng.ttf) format('truetype');
        }
        @font-face {
            font-family: 'Almarai';
            font-style: normal;
            font-weight: 600;
            font-display: swap;
            src: url(https://fonts.gstatic.com/s/almarai/v19/tssoApxBaigK_hnnS-aghng.ttf) format('truetype');
        }

        /* ── Universal Executive Typography: 100% Pure Almarai for Arabic + Plus Jakarta Sans for Latin/Numbers ── */
        *, *::before, *::after,
        html, body,
        h1, h2, h3, h4, h5, h6,
        p, span, a, label, div,
        button, input, select, textarea, optgroup, option,
        table, th, td, tr, thead, tbody, aside, header, nav, main, footer,
        .font-sans {
            font-family: 'Plus Jakarta Sans', 'Almarai', sans-serif !important;
            letter-spacing: normal !important;
            word-spacing: normal !important;
        }

        /* Guarantee that all bold/medium elements render with true Almarai 700 bold weight matching header */
        .font-medium, .font-semibold, .font-bold, .font-extrabold, .font-black,
        b, strong, h1, h2, h3, h4, h5, h6, th,
        nav a, button, .btn {
            font-weight: 700 !important;
        }

        /* ── Tabular Numbers Matching FaceAttend ── */
        .font-mono, td.font-mono, span.font-mono {
            font-family: 'Plus Jakarta Sans', 'Almarai', sans-serif !important;
            font-feature-settings: "tnum" 1;
            font-variant-numeric: tabular-nums;
        }

        body {
            background-color: #F8F9FA;
            line-height: 1.6;
            letter-spacing: normal !important;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }

        /* ══════════════════════════════════════════════════════════════════
           WATHEEQ UNIVERSAL ENTERPRISE PRINT ENGINE
           Supports full A4 reports, tax declarations, ledgers & 80mm thermal
           ══════════════════════════════════════════════════════════════════ */
        @media print {
            /* 1. Global Reset: Crisp black-on-white paper layout */
            html, body {
                background: #ffffff !important;
                background-color: #ffffff !important;
                color: #000000 !important;
                font-family: 'Almarai', 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
                font-size: 10pt !important;
                line-height: 1.4 !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: auto !important;
                min-height: 0 !important;
                overflow: visible !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* 2. Strip Screen-Only Chrome & Navigation */
            .no-print,
            header,
            nav,
            aside,
            footer,
            button,
            .btn,
            input[type="button"],
            input[type="submit"],
            .modal-backdrop,
            #userSwitchMenu,
            #scannerModal,
            #cameraScannerModal,
            #testQrModal,
            #testStockQrModal {
                display: none !important;
                visibility: hidden !important;
            }

            /* 3. Expand Main Workspace to 100% Paper Width */
            main,
            .max-w-7xl,
            .max-w-4xl,
            .max-w-xl,
            .max-w-2xl,
            .space-y-6,
            .space-y-4 {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                overflow: visible !important;
            }

            /* 4. Multi-Page Table & Accounting Ledger Formatting */
            table {
                width: 100% !important;
                border-collapse: collapse !important;
                page-break-inside: auto !important;
            }
            thead {
                display: table-header-group !important;
            }
            tr {
                page-break-inside: avoid !important;
                page-break-after: auto !important;
            }
            th, td {
                border: 1px solid #cbd5e1 !important;
                padding: 5px 8px !important;
                color: #000000 !important;
                font-size: 9.5pt !important;
            }
            th {
                background-color: #f1f5f9 !important;
                color: #0f172a !important;
                font-weight: 700 !important;
            }

            /* Remove shadows, flatten rounded corners for crisp ink */
            * {
                box-shadow: none !important;
                text-shadow: none !important;
            }
            .rounded-2xl, .rounded-xl, .rounded-lg, .rounded {
                border-radius: 4px !important;
            }
            .border, .border-2, .border-b, .border-t {
                border-color: #cbd5e1 !important;
            }

            /* 5. Isolated Print Target Mode (Triggered by watheeqPrint) */
            body.watheeq-printing-active > *:not(#watheeqPrintHost) {
                display: none !important;
                visibility: hidden !important;
            }
            body.watheeq-printing-active #watheeqPrintHost {
                display: block !important;
                visibility: visible !important;
                position: static !important;
                margin: 0 auto !important;
                padding: 0 !important;
                background: #ffffff !important;
                width: 100% !important;
            }
            body.watheeq-printing-active #watheeqPrintHost * {
                visibility: visible !important;
            }
            /* Hide any internal action buttons inside cloned content */
            body.watheeq-printing-active #watheeqPrintHost button,
            body.watheeq-printing-active #watheeqPrintHost .no-print {
                display: none !important;
            }

            /* 80mm POS Thermal Receipt Styling */
            body.watheeq-print-thermal #watheeqPrintHost {
                width: 76mm !important;
                max-width: 76mm !important;
                margin: 0 auto !important;
                font-family: 'Plus Jakarta Sans', 'Almarai', monospace !important;
                font-size: 9.5pt !important;
                line-height: 1.35 !important;
            }
            body.watheeq-print-thermal #watheeqPrintHost table {
                border: none !important;
            }
            body.watheeq-print-thermal #watheeqPrintHost th,
            body.watheeq-print-thermal #watheeqPrintHost td {
                border-top: 1px dashed #64748b !important;
                border-bottom: 1px dashed #64748b !important;
                border-left: none !important;
                border-right: none !important;
                padding: 3px 0 !important;
                background: transparent !important;
            }

            /* Barcode / QR Label Sticker Styling */
            body.watheeq-print-label #watheeqPrintHost {
                width: 65mm !important;
                max-width: 65mm !important;
                margin: 0 auto !important;
                text-align: center !important;
            }

            /* 6. Standard Print Page Margins */
            @page {
                size: auto;
                margin: 8mm 10mm 8mm 10mm;
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col text-gray-800 font-sans">

    <!-- Top Utility Ribbon in Legal Eminence Palette (Clean, Uncluttered) -->
    <header class="w-full text-white shadow-md z-30 sticky top-0 no-print" style="background-color: {{ $tenant->primary_color ?? '#1F2E4A' }};">
        <div class="bg-[#18243A] text-white text-xs py-1.5 px-4 sm:px-8 flex justify-between items-center border-b border-[#2A3B5C]/60">
            <div class="flex items-center gap-3 text-[11px] text-[#CED4DA]">
                <span class="font-medium text-white/90">منظومة وثيق السحابية لإدارة الأعمال</span>
                <span class="text-white/30">•</span>
                <span>{{ date('Y-m-d') }}</span>
            </div>
            <div class="flex items-center gap-3 text-[11px]">
                <span class="text-[#CED4DA]">العملة: <strong class="text-white font-mono">{{ $tenant->currency ?? 'JOD' }}</strong> ({{ $tenant->tax_rate ?? 16 }}% ض.م)</span>
                <span class="text-white/30">|</span>
                <span class="text-[#CED4DA]">الرقم الضريبي: <strong class="text-white font-mono">{{ $tenant->tax_number ?? '1004829103' }}</strong></span>
            </div>
        </div>

        <!-- Corporate Brand Header (Pure Executive Typography, Zero Icons) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-8 py-2.5 flex justify-between items-center">
            <!-- Brand Identity: Watheeq | وثيق -->
            <a href="{{ route('erp.dashboard') }}" class="block">
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-bold tracking-tight text-white">وثيق | Watheeq</h1>
                    <span class="bg-white/15 text-[10px] text-white font-bold px-2 py-0.5 rounded border border-white/20 font-mono">ERP Suite</span>
                </div>
                <p class="text-xs text-[#CED4DA] font-medium">{{ $tenant->name ?? 'وثيق لإدارة الأعمال' }}</p>
            </a>

            <!-- User Account Profile Dropdown -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- User Profile Dropdown -->
                <div class="relative">
                    <button type="button" onclick="document.getElementById('userSwitchMenu').classList.toggle('hidden')" class="flex items-center gap-2 bg-black/25 hover:bg-black/35 text-white px-3.5 py-1.5 rounded-lg border border-white/15 transition text-xs">
                        <div class="text-right">
                            <span class="block font-bold text-xs leading-none text-white">{{ $currentUser->name ?? 'المدير العام' }}</span>
                            <span class="block text-[10px] text-[#CED4DA] leading-tight mt-0.5">{{ $currentUser->role_name_ar ?? 'مدير النظام' }}</span>
                        </div>
                        <span class="text-[9px] text-white/70">▼</span>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="userSwitchMenu" class="hidden absolute left-0 mt-2 w-64 bg-white text-gray-800 rounded-xl shadow-2xl border border-gray-200 py-2 z-50 text-xs">
                        @if(($currentUser->role ?? '') === 'admin')
                            <div class="px-3.5 py-2 border-b border-gray-100 flex justify-between items-center bg-gray-50/70">
                                <span class="font-bold text-gray-700">تبديل الحساب (للمشرف فقط)</span>
                                <span class="text-[10px] text-gray-400">تخصيص الواجهة</span>
                            </div>
                            <div class="max-h-60 overflow-y-auto divide-y divide-gray-50">
                                @foreach($allUsers as $u)
                                    <a href="{{ route('erp.users.switch', $u->id) }}" class="flex items-center justify-between px-3.5 py-2.5 hover:bg-gray-100 transition {{ ($currentUser->id ?? null) == $u->id ? 'bg-blue-50 font-bold' : '' }}">
                                        <div>
                                            <div class="text-gray-900 font-bold">{{ $u->name }}</div>
                                            <div class="text-[10px] text-gray-500">{{ $u->role_name_ar }}</div>
                                        </div>
                                        @if(($currentUser->id ?? null) == $u->id)
                                            <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-1.5 py-0.5 rounded">الحالي</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="px-3.5 py-2 border-b border-gray-100 bg-gray-50/70">
                                <div class="font-bold text-gray-800">{{ $currentUser->name }}</div>
                                <div class="text-[10px] text-gray-500">{{ $currentUser->role_name_ar }}</div>
                            </div>
                        @endif
                        <div class="border-t border-gray-100 p-2 bg-gray-50 space-y-1.5">
                            @if(($currentUser->role ?? '') === 'admin')
                                <a href="{{ route('erp.admin') }}" class="block w-full py-1.5 px-3 bg-[#1F2E4A] hover:bg-[#343A40] text-white font-bold rounded-lg text-xs transition text-center shadow-sm">
                                    لوحة الإدارة وتخصيص الحسابات
                                </a>
                            @endif
                            <a href="{{ route('logout') }}" class="block w-full py-1.5 px-3 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-lg text-xs transition text-center border border-rose-200">
                                تسجيل الخروج من النظام
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Admin Panel Direct Shortcut -->
                @if(($currentUser->role ?? '') === 'admin')
                    <a href="{{ route('erp.admin') }}" class="hidden md:flex items-center px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition">
                        لوحة الإدارة
                    </a>
                @endif

                <!-- Logout Direct Button -->
                <a href="{{ route('logout') }}" class="flex items-center px-2.5 py-1.5 rounded-lg bg-white/10 hover:bg-rose-600 text-white font-bold text-xs border border-white/20 hover:border-rose-500 transition" title="تسجيل الخروج">
                    خروج
                </a>
            </div>
        </div>
    </header>

    <!-- Master Navigation Mega-Menu (Dynamically Filtered by Current User Interface Permissions) -->
    <nav class="bg-white border-b border-[#E6E7E9] shadow-sm sticky top-[76px] z-20 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            <div class="flex items-center space-x-1 space-x-reverse overflow-x-auto py-2 no-scrollbar text-xs md:text-sm font-semibold">
                @if(in_array('dashboard', $userModules))
                    <a href="{{ route('erp.dashboard') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('erp.dashboard') ? 'bg-[#1F2E4A] text-white shadow' : 'text-gray-700 hover:bg-gray-100' }}">
                        الواجهة الرئيسية
                    </a>
                @endif

                @if(in_array('definitions', $userModules))
                    <a href="{{ route('erp.definitions') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('erp.definitions') || request()->routeIs('erp.price_lists') || request()->routeIs('erp.price_adjustment.editor') ? 'bg-[#1F2E4A] text-white shadow' : 'text-gray-700 hover:bg-gray-100' }}">
                        تعريفات
                    </a>
                @endif

                @if(in_array('invoicing', $userModules))
                    <a href="{{ route('erp.invoicing.hub') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('erp.invoicing.hub') || request()->routeIs('erp.invoices*') || request()->routeIs('erp.vouchers*') || request()->routeIs('erp.journals*') || request()->routeIs('erp.dispatches') || request()->routeIs('erp.stock_count') ? 'bg-[#1F2E4A] text-white shadow' : 'text-gray-700 hover:bg-gray-100' }}">
                        الفواتير والسندات
                    </a>
                @endif

                @if(in_array('taxes', $userModules))
                    <a href="{{ route('erp.taxes') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('erp.taxes*') ? 'bg-[#1F2E4A] text-white shadow' : 'text-gray-700 hover:bg-gray-100' }}">
                        الضرائب والإقرار
                    </a>
                @endif

                @if(in_array('hr', $userModules))
                    <a href="{{ route('erp.hr') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('erp.hr*') ? 'bg-[#1F2E4A] text-white shadow' : 'text-gray-700 hover:bg-gray-100' }}">
                        الموارد البشرية (HR)
                    </a>
                @endif

                @if(in_array('hotels', $userModules))
                    <a href="{{ route('erp.hotels') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('erp.hotels*') ? 'bg-[#1F2E4A] text-white shadow' : 'text-gray-700 hover:bg-gray-100' }}">
                        إدارة الفنادق
                    </a>
                @endif

                @if(in_array('pos', $userModules))
                    <a href="{{ route('erp.pos.hub') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('erp.pos*') ? 'bg-[#1F2E4A] text-white shadow' : 'text-gray-700 hover:bg-gray-100' }}">
                        نقاط البيع (POS)
                    </a>
                @endif

                @if(in_array('reports', $userModules))
                    <a href="{{ route('erp.reports') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('erp.reports*') ? 'bg-[#1F2E4A] text-white shadow' : 'text-gray-700 hover:bg-gray-100' }}">
                        التقارير المالية
                    </a>
                @endif

                @if(in_array('jofotara', $userModules))
                    <a href="{{ route('erp.jofotara.hub') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('erp.jofotara*') ? 'bg-[#1F2E4A] text-white shadow' : 'text-gray-700 hover:bg-gray-100' }}">
                        الفوترة الوطنية
                    </a>
                @endif

                <div class="mr-auto">
                    <a href="{{ route('erp.admin') }}" class="px-3 py-1.5 rounded-lg font-bold border border-[#1F2E4A]/30 transition {{ request()->routeIs('erp.admin') || request()->routeIs('erp.customizer') ? 'bg-[#1F2E4A] text-white' : 'bg-[#1F2E4A]/10 text-[#343A40] hover:bg-[#1F2E4A]/20' }}">
                        تخصيص الواجهة
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Master Layout Body with Clean Minimalist Sidebar -->
    <div class="max-w-7xl w-full mx-auto px-4 sm:px-8 py-5 flex-1 flex flex-col lg:flex-row gap-6">
        <!-- Main Workspace Canvas -->
        <main class="flex-1 min-w-0">
            @if(session('success'))
                <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold no-print shadow-sm flex justify-between items-center">
                    <span>{{ session('success') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-bold no-print shadow-sm flex justify-between items-center">
                    <span>{{ session('error') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold">✕</button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Right Quick Actions & Tools Sidebar (Dynamically Filtered by Current User Preferences) -->
        <aside class="w-full lg:w-56 space-y-4 no-print flex-shrink-0">
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-3">
                <div class="border-b pb-2 flex justify-between items-center">
                    <span class="text-xs font-bold text-[#1F2E4A]">الأدوات السريعة</span>
                    <span class="text-[10px] text-[#1F2E4A] font-bold">وثيق ERP</span>
                </div>
                <div class="space-y-1.5 text-xs text-gray-700 font-medium">
                    @if(in_array('jofotara', $userTools))
                        <a href="{{ route('erp.jofotara.hub') }}" class="block p-1.5 rounded font-bold text-[#1F2E4A] bg-[#1F2E4A]/5 hover:bg-[#CED4DA] transition flex justify-between items-center">
                            <span>الفوترة الوطنية (JoFotara)</span>
                            <span class="text-[9px] bg-[#1F2E4A] text-white px-1.5 py-0.2 rounded font-mono">ISTD</span>
                        </a>
                    @endif
                    @if(in_array('taxes', $userTools))
                        <a href="{{ route('erp.taxes') }}" class="block p-1.5 rounded font-bold text-[#1F2E4A] hover:bg-[#CED4DA] transition flex justify-between items-center">
                            <span>الضرائب والإقرار</span>
                            <span class="text-[9px] bg-emerald-700 text-white px-1.5 py-0.2 rounded font-mono">16%</span>
                        </a>
                    @endif
                    @if(in_array('sales_invoice', $userTools))
                        <a href="{{ route('erp.invoices.editor', ['type' => 'sale']) }}" class="block p-1.5 rounded hover:bg-[#CED4DA] hover:text-[#1F2E4A] transition">فاتورة مبيعات</a>
                    @endif
                    @if(in_array('purchase_invoice', $userTools))
                        <a href="{{ route('erp.invoices.editor', ['type' => 'purchase']) }}" class="block p-1.5 rounded hover:bg-[#CED4DA] hover:text-[#1F2E4A] transition">فاتورة مشتريات</a>
                    @endif
                    @if(in_array('receipt_voucher', $userTools))
                        <a href="{{ route('erp.vouchers.editor', ['type' => 'receipt']) }}" class="block p-1.5 rounded hover:bg-[#CED4DA] hover:text-[#1F2E4A] transition">سند قبض مالي</a>
                    @endif
                    @if(in_array('payment_voucher', $userTools))
                        <a href="{{ route('erp.vouchers.payment_editor') }}" class="block p-1.5 rounded hover:bg-[#CED4DA] hover:text-[#1F2E4A] transition">سند صرف ودفع</a>
                    @endif
                    @if(in_array('journal_voucher', $userTools))
                        <a href="{{ route('erp.journals.editor') }}" class="block p-1.5 rounded hover:bg-[#CED4DA] hover:text-[#1F2E4A] transition">سند قيد يومية</a>
                    @endif
                    @if(in_array('price_adjustment', $userTools))
                        <a href="{{ route('erp.price_adjustment.editor') }}" class="block p-1.5 rounded hover:bg-[#CED4DA] hover:text-[#1F2E4A] transition">تعديل أسعار البيع</a>
                    @endif
                    @if(in_array('salary_editor', $userTools))
                        <a href="{{ route('erp.hr.salary_editor') }}" class="block p-1.5 rounded hover:bg-[#CED4DA] hover:text-[#1F2E4A] transition">تعديل راتب موظف</a>
                    @endif
                    @if(in_array('hotel_guests', $userTools))
                        <a href="{{ route('erp.hotels.guests') }}" class="block p-1.5 rounded hover:bg-[#CED4DA] hover:text-[#1F2E4A] transition">سجل النزلاء (فنادق)</a>
                    @endif
                    @if(in_array('cheques', $userTools))
                        <a href="{{ route('erp.cheques') }}" class="block p-1.5 rounded hover:bg-[#CED4DA] hover:text-[#1F2E4A] transition">حافظة الشيكات</a>
                    @endif
                    @if(in_array('dispatches', $userTools))
                        <a href="{{ route('erp.dispatches') }}" class="block p-1.5 rounded hover:bg-[#CED4DA] hover:text-[#1F2E4A] transition">إرساليات البضاعة</a>
                    @endif
                    @if(in_array('phonebook', $userTools))
                        <a href="{{ route('erp.phonebook') }}" class="block p-1.5 rounded hover:bg-[#CED4DA] hover:text-[#1F2E4A] transition">دليل الهاتف</a>
                    @endif
                    @if(in_array('notepad', $userTools))
                        <a href="{{ route('erp.notepad') }}" class="block p-1.5 rounded hover:bg-[#CED4DA] hover:text-[#1F2E4A] transition">المفكرة والمواعيد</a>
                    @endif
                    @if(in_array('pos', $userTools))
                        <a href="{{ route('erp.pos') }}" class="block p-1.5 rounded hover:bg-[#CED4DA] hover:text-[#1F2E4A] transition">نقطة البيع (POS)</a>
                    @endif
                    @if(in_array('reports', $userTools))
                        <a href="{{ route('erp.reports') }}" class="block p-1.5 rounded hover:bg-[#CED4DA] hover:text-[#1F2E4A] transition">التقارير والميزان</a>
                    @endif
                    <a href="{{ route('erp.admin') }}" class="block p-1.5 rounded hover:bg-[#CED4DA] hover:text-[#1F2E4A] transition font-bold text-[#1F2E4A]">تخصيص الواجهة</a>
                </div>
            </div>

            <div class="bg-[#1F2E4A] text-white p-4 rounded-xl shadow-sm space-y-2 border border-[#1F2E4A]/40">
                <span class="text-xs font-bold text-[#CED4DA] block">منظومة وثيق لإدارة الأعمال</span>
                <p class="text-[11px] text-gray-200 leading-relaxed">
                    حلول سحابية ومحاسبية متكاملة معتمدة للامتثال الضريبي والفوترة الإلكترونية (ISTD).
                </p>
                <div class="pt-2 border-t border-white/10 text-[10px] text-[#CED4DA] font-mono">
                    Watheeq ERP Suite v2.0
                </div>
            </div>
        </aside>
    </div>

    <!-- Footer in Corporate Palette -->
    <footer class="bg-[#1F2E4A] text-white/80 py-5 border-t-4 border-[#1F2E4A] mt-auto no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs">
            <div>
                <p class="font-semibold text-white">منظومة وثيق لإدارة الأعمال (Watheeq ERP) © {{ date('Y') }}</p>
                <p class="text-[11px] text-[#CED4DA]">نظام محاسبي وإداري متكامل معتمد للامتثال الضريبي والفوترة الوطنية الأردنية</p>
            </div>
            <div class="flex items-center gap-4 text-xs font-medium">
                <span>طباعة مزدوجة (A4 و Thermal 80mm)</span>
                <span>•</span>
                <span>ربط فوري بنظام الفوترة الوطني JoFotara</span>
                <span>•</span>
                <span>ربط تكاملي كامل للوحدات المالية</span>
            </div>
        </div>
    </footer>

    <!-- Dedicated Host for Isolated Document / Receipt / Voucher Printing -->
    <div id="watheeqPrintHost" class="hidden"></div>

    <script>
        /**
         * Watheeq Universal Enterprise Print Function
         * Clones any target element (modal, invoice, receipt, voucher) into an isolated
         * top-level host before printing, avoiding any modal backdrop or overflow issues.
         */
        window.watheeqPrint = function(target, mode = 'a4') {
            var el = typeof target === 'string' ? document.getElementById(target) : target;
            if (!el) {
                window.print();
                return;
            }

            var printHost = document.getElementById('watheeqPrintHost');
            if (!printHost) {
                printHost = document.createElement('div');
                printHost.id = 'watheeqPrintHost';
                document.body.appendChild(printHost);
            }

            // Clone target node cleanly and unhide
            printHost.innerHTML = '';
            var clone = el.cloneNode(true);
            clone.classList.remove('hidden');
            clone.style.display = 'block';
            printHost.appendChild(clone);
            printHost.classList.remove('hidden');

            // Apply mode classes to body
            document.body.classList.remove('watheeq-print-thermal', 'watheeq-print-label', 'watheeq-print-a4');
            document.body.classList.add('watheeq-printing-active');
            if (mode === 'thermal') {
                document.body.classList.add('watheeq-print-thermal');
            } else if (mode === 'label') {
                document.body.classList.add('watheeq-print-label');
            } else {
                document.body.classList.add('watheeq-print-a4');
            }

            // Cleanup handler - fires strictly when print dialog closes or completes
            var cleanup = function() {
                document.body.classList.remove('watheeq-printing-active', 'watheeq-print-thermal', 'watheeq-print-label', 'watheeq-print-a4');
                if (printHost) {
                    printHost.classList.add('hidden');
                    printHost.innerHTML = '';
                }
                window.removeEventListener('afterprint', cleanup);
            };
            window.addEventListener('afterprint', cleanup, { once: true });

            // Reflow frame check before triggering print dialog
            requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                    window.print();
                });
            });
        };
    </script>
</body>
</html>
