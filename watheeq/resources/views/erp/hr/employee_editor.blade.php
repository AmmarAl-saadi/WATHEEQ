@extends('layouts.app')

@section('title', 'بطاقة الموظف | حساباتي HR | وثيق ERP')

@section('content')
<div class="space-y-4 font-sans text-xs" dir="rtl">

    <form method="POST" action="{{ route('erp.hr.employee_editor.store') }}" id="employeeForm">
        @csrf
        <input type="hidden" name="id" value="{{ $employee->id ?? '' }}">

        <!-- Top Navigation & Action Ribbon (مطابق تماماً لهيدر حساباتي Frame 3_3i) -->
        <div class="bg-white p-2 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-2 no-print">
            <div class="flex items-center gap-1.5 flex-wrap">
                <button type="submit" class="px-4 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
                    حفظ
                </button>
                <button type="button" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    &gt;|
                </button>
                <button type="button" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    &gt;&gt;
                </button>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    استعلام
                </button>
                <button type="button" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    &lt;&lt;
                </button>
                <button type="button" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    |&lt;
                </button>
                <a href="{{ route('erp.hr.employee_editor') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    جديد
                </a>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    جديد مشابه
                </button>
                <button type="button" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded font-bold border border-red-200 transition">
                    حذف
                </button>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    مرفقات
                </button>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-gray-500 font-semibold">شاشة بطاقة الموظف المعتمدة</span>
                <span class="bg-[#1F2E4A] text-white text-[11px] px-2.5 py-0.5 rounded font-black">حساباتي HR</span>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <!-- Card Header Info (الرقم، الاسم، اسم الحساب) -->
        <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            <div class="md:col-span-2 flex items-center gap-1.5">
                <label class="text-gray-600 font-bold whitespace-nowrap">الرقم:</label>
                <input type="text" name="emp_code" value="{{ $employee->emp_code ?? ($nextCode ?? '1') }}" required class="w-full bg-gray-50 border border-gray-300 rounded px-2.5 py-1 text-center font-bold text-gray-800 focus:bg-white focus:border-[#1F2E4A] outline-none">
            </div>
            <div class="md:col-span-5 flex items-center gap-1.5">
                <label class="text-gray-600 font-bold whitespace-nowrap">الاسم:</label>
                <input type="text" name="name" value="{{ $employee->name ?? '' }}" placeholder="اسم الموظف الرباعي" required class="w-full bg-yellow-50 border border-gray-300 rounded px-2.5 py-1 font-bold text-gray-900 focus:bg-white focus:border-[#1F2E4A] outline-none">
            </div>
            <div class="md:col-span-5 flex items-center gap-1.5">
                <label class="text-gray-600 font-bold whitespace-nowrap">اسم الحساب:</label>
                <div class="relative w-full flex">
                    <input type="text" name="account_name" value="{{ $employee->account_name ?? '0' }}" class="w-full bg-gray-50 border border-gray-300 rounded-r px-2.5 py-1 text-gray-800 outline-none">
                    <button type="button" class="bg-gray-200 hover:bg-gray-300 px-2.5 rounded-l border border-r-0 border-gray-300 text-gray-600">🔍</button>
                </div>
            </div>
        </div>

        <!-- 12-Tab Header Ribbon (Hesabate Tabs) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center gap-0.5 overflow-x-auto border-b border-gray-200 bg-gray-50 p-1 text-[11px] font-bold">
                <button type="button" onclick="switchTab('personal')" id="tab-btn-personal" class="tab-btn px-3 py-1.5 rounded-t text-white bg-[#1F2E4A]">المعلومات الشخصية</button>
                <button type="button" onclick="switchTab('financial')" id="tab-btn-financial" class="tab-btn px-3 py-1.5 rounded-t text-gray-600 hover:bg-gray-200">معلومات مالية</button>
                <button type="button" onclick="switchTab('attendance')" id="tab-btn-attendance" class="tab-btn px-3 py-1.5 rounded-t text-gray-600 hover:bg-gray-200">ضبط الدوام</button>
                <button type="button" onclick="switchTab('deductions')" id="tab-btn-deductions" class="tab-btn px-3 py-1.5 rounded-t text-gray-600 hover:bg-gray-200">الإقتطاعات</button>
                <button type="button" onclick="switchTab('discounts')" id="tab-btn-discounts" class="tab-btn px-3 py-1.5 rounded-t text-gray-600 hover:bg-gray-200">الخصومات</button>
                <button type="button" onclick="switchTab('bonuses')" id="tab-btn-bonuses" class="tab-btn px-3 py-1.5 rounded-t text-gray-600 hover:bg-gray-200">المكافآت</button>
                <button type="button" onclick="switchTab('family')" id="tab-btn-family" class="tab-btn px-3 py-1.5 rounded-t text-gray-600 hover:bg-gray-200">الزوج/ة-الأطفال</button>
                <button type="button" onclick="switchTab('contracts')" id="tab-btn-contracts" class="tab-btn px-3 py-1.5 rounded-t text-gray-600 hover:bg-gray-200">عقود العمل</button>
                <button type="button" onclick="switchTab('assets')" id="tab-btn-assets" class="tab-btn px-3 py-1.5 rounded-t text-gray-600 hover:bg-gray-200">العهدة الشخصية</button>
                <button type="button" onclick="switchTab('evaluations')" id="tab-btn-evaluations" class="tab-btn px-3 py-1.5 rounded-t text-gray-600 hover:bg-gray-200">التقييمات</button>
                <button type="button" onclick="switchTab('penalties')" id="tab-btn-penalties" class="tab-btn px-3 py-1.5 rounded-t text-gray-600 hover:bg-gray-200">الجزاءات-المخالفات</button>
                <button type="button" onclick="switchTab('insurance')" id="tab-btn-insurance" class="tab-btn px-3 py-1.5 rounded-t text-gray-600 hover:bg-gray-200">التأمين</button>
                <button type="button" onclick="switchTab('alerts')" id="tab-btn-alerts" class="tab-btn px-3 py-1.5 rounded-t text-gray-600 hover:bg-gray-200">تنبيهات</button>
            </div>

            <!-- Tab 1: المعلومات الشخصية -->
            <div id="tab-personal" class="tab-pane p-4 space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Column 1 -->
                    <div class="space-y-2">
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">رقم الهوية / الرقم الوطني:</label>
                            <input type="text" name="national_id" value="{{ $employee->national_id ?? '' }}" class="w-full border border-gray-300 rounded px-2.5 py-1">
                        </div>
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">تاريخ الميلاد:</label>
                            <input type="date" name="birth_date" value="{{ $employee->birth_date ?? '1990-01-01' }}" class="w-full border border-gray-300 rounded px-2.5 py-1">
                        </div>
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">رقم الضمان الاجتماعي:</label>
                            <input type="text" name="social_security_no" value="{{ $employee->social_security_no ?? '' }}" class="w-full border border-gray-300 rounded px-2.5 py-1">
                        </div>
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">المسمى الوظيفي:</label>
                            <input type="text" name="job_title" value="{{ $employee->job_title ?? '' }}" required class="w-full border border-gray-300 rounded px-2.5 py-1">
                        </div>
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">القسم:</label>
                            <select name="department" class="w-full border border-gray-300 rounded px-2 py-1">
                                <option value="المالية والإدارة">المالية والإدارة</option>
                                <option value="المبيعات">المبيعات</option>
                                <option value="المستودعات">المستودعات</option>
                                <option value="الإنتاج">الإنتاج</option>
                                <option value="خدمة العملاء">خدمة العملاء</option>
                            </select>
                        </div>
                    </div>

                    <!-- Column 2 -->
                    <div class="space-y-2">
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">الجنس:</label>
                            <div class="flex items-center gap-4 py-1">
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="gender" value="male" checked> ذكر
                                </label>
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="gender" value="female"> أنثى
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">بدأ العمل من تاريخ:</label>
                            <input type="date" name="hire_date" value="{{ $employee->hire_date ?? date('Y-m-d') }}" class="w-full border border-gray-300 rounded px-2.5 py-1">
                        </div>
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">البريد الإلكتروني:</label>
                            <input type="email" name="email" value="{{ $employee->email ?? '' }}" class="w-full border border-gray-300 rounded px-2.5 py-1">
                        </div>
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">المدينة:</label>
                            <input type="text" name="city" value="{{ $employee->city ?? 'عمان' }}" class="w-full border border-gray-300 rounded px-2.5 py-1">
                        </div>
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">الحالة:</label>
                            <select name="status" class="w-full border border-gray-300 rounded px-2 py-1 font-bold text-emerald-700">
                                <option value="active">فعال</option>
                                <option value="on_leave">إجازة</option>
                                <option value="terminated">إنهاء خدمة</option>
                            </select>
                        </div>
                    </div>

                    <!-- Column 3 -->
                    <div class="space-y-2">
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">الحالة الاجتماعية:</label>
                            <div class="flex items-center gap-4 py-1">
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="marital_status" value="single" checked> أعزب
                                </label>
                                <label class="flex items-center gap-1 cursor-pointer">
                                    <input type="radio" name="marital_status" value="married"> متزوج
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">الجنسية:</label>
                            <input type="text" name="nationality" value="{{ $employee->nationality ?? 'أردني' }}" class="w-full border border-gray-300 rounded px-2.5 py-1">
                        </div>
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">مقيم / غير مقيم (ضريبياً):</label>
                            <select name="is_resident" class="w-full border border-gray-300 rounded px-2 py-1">
                                <option value="1">مقيم (خاضع للإعفاءات الأردنية 9,000 د.أ)</option>
                                <option value="0">غير مقيم</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">طبيعة العمل:</label>
                            <select name="work_nature" class="w-full border border-gray-300 rounded px-2 py-1">
                                <option value="مندوب">مندوب</option>
                                <option value="إداري">إداري</option>
                                <option value="فني">فني</option>
                                <option value="محاسب">محاسب</option>
                                <option value="عامل">عامل</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">رقم الموبايل:</label>
                            <input type="text" name="mobile" value="{{ $employee->mobile ?? '' }}" class="w-full border border-gray-300 rounded px-2.5 py-1">
                        </div>
                    </div>

                    <!-- Column 4 (Photo & Extra notes) -->
                    <div class="space-y-2 flex flex-col items-center justify-start border border-gray-100 rounded-xl p-3 bg-gray-50/50">
                        <div class="w-32 h-36 bg-gray-200 border-2 border-dashed border-gray-300 rounded-xl flex flex-col items-center justify-center text-gray-400">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span class="text-[10px] mt-1 font-semibold">صورة الموظف</span>
                        </div>
                        <button type="button" class="px-4 py-1 bg-white hover:bg-gray-100 border border-gray-300 rounded font-bold text-gray-700 shadow-sm transition">
                            استعراض...
                        </button>
                        <div class="w-full mt-2">
                            <label class="block text-gray-600 font-semibold mb-0.5">ملاحظات عامة:</label>
                            <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded p-1.5 text-xs">{{ $employee->notes ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: معلومات مالية (Hesabate Financial Info) -->
            <div id="tab-financial" class="tab-pane hidden p-4 space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-gray-200">
                    <span class="font-bold text-gray-800">بيانات الأجور والرواتب والعمولات والضمان</span>
                    <button type="button" onclick="calcDailySalary()" class="px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded font-bold hover:bg-blue-100">
                        احتساب الراتب اليومي تلقائياً (÷ 30.4375)
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">الراتب الشهري الأساسي:</label>
                            <div class="flex">
                                <input type="number" step="0.01" name="basic_salary" id="basic_salary" value="{{ $employee->basic_salary ?? '0' }}" required class="w-full border border-gray-300 rounded-r px-2.5 py-1 font-bold text-gray-900 bg-yellow-50 focus:bg-white">
                                <select name="currency_code" class="bg-gray-100 border border-l-0 border-gray-300 rounded-l px-2 font-bold text-gray-700">
                                    <option value="JOD">JOD</option>
                                    <option value="USD">USD</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">الراتب اليومي:</label>
                            <input type="number" step="0.01" name="daily_salary" id="daily_salary" value="{{ $employee->daily_salary ?? '0' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">راتب الساعة:</label>
                            <input type="number" step="0.01" name="hourly_rate" id="hourly_rate" value="{{ $employee->hourly_rate ?? '0' }}" class="w-full border border-gray-300 rounded px-2.5 py-1 bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">راتب الساعة إضافي:</label>
                            <input type="number" step="0.01" name="overtime_hourly_rate" id="overtime_hourly_rate" value="{{ $employee->overtime_hourly_rate ?? '0' }}" class="w-full border border-gray-300 rounded px-2.5 py-1">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">طريقة الدفع:</label>
                            <select name="payment_method" class="w-full border border-gray-300 rounded px-2 py-1">
                                <option value="نقدي">نقدي</option>
                                <option value="شيك">شيك</option>
                                <option value="تحويل بنكي">تحويل بنكي</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">البنك المحول إليه:</label>
                            <input type="text" name="bank_name" value="{{ $employee->bank_name ?? '' }}" placeholder="مثل: البنك العربي، بنك الإسكان" class="w-full border border-gray-300 rounded px-2.5 py-1">
                        </div>
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">رقم الحساب / IBAN:</label>
                            <input type="text" name="bank_account_no" value="{{ $employee->bank_account_no ?? '' }}" class="w-full border border-gray-300 rounded px-2.5 py-1">
                        </div>
                        <div>
                            <label class="block text-gray-600 font-semibold mb-0.5">نسبة عمولة المبيعات %:</label>
                            <input type="number" step="0.1" name="sales_commission_pct" value="{{ $employee->sales_commission_pct ?? '0' }}" class="w-full border border-gray-300 rounded px-2.5 py-1">
                        </div>
                    </div>

                    <div class="space-y-2 bg-emerald-50/50 p-3 rounded-xl border border-emerald-100">
                        <span class="font-bold text-emerald-900 block border-b border-emerald-200 pb-1">الضمان الاجتماعي وضريبة الدخل الأردنية</span>
                        <div class="text-[11px] space-y-1 text-gray-700 pt-1">
                            <div class="flex justify-between">
                                <span>اقتطاع الموظف للضمان:</span>
                                <span class="font-bold text-gray-900">7.5%</span>
                            </div>
                            <div class="flex justify-between">
                                <span>مساهمة المنشأة في الضمان:</span>
                                <span class="font-bold text-emerald-700">14.25%</span>
                            </div>
                            <div class="flex justify-between">
                                <span>الإعفاء السنوي للأردني المقيم:</span>
                                <span class="font-bold text-blue-700">9,000 د.أ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: الإقتطاعات والخصومات -->
            <div id="tab-deductions" class="tab-pane hidden p-4 space-y-3">
                <table class="w-full border-collapse border border-gray-200 text-right">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 font-bold">
                            <th class="p-2 border border-gray-200">النوع</th>
                            <th class="p-2 border border-gray-200">القيمة</th>
                            <th class="p-2 border border-gray-200">نسبة %</th>
                            <th class="p-2 border border-gray-200">خاضع لضريبة الدخل</th>
                            <th class="p-2 border border-gray-200">تاريخ الاستحقاق</th>
                            <th class="p-2 border border-gray-200">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-2 border border-gray-200"><input type="text" value="ضمان اجتماعي 7.5%" class="w-full bg-transparent border-0 outline-none"></td>
                            <td class="p-2 border border-gray-200"><input type="number" step="0.01" value="0.00" class="w-24 text-center border rounded px-1"></td>
                            <td class="p-2 border border-gray-200"><input type="number" step="0.1" value="7.5" class="w-16 text-center border rounded px-1"></td>
                            <td class="p-2 border border-gray-200 text-center"><input type="checkbox" checked></td>
                            <td class="p-2 border border-gray-200"><input type="date" value="{{ date('Y-m-d') }}" class="border rounded px-1"></td>
                            <td class="p-2 border border-gray-200"><input type="text" placeholder="اقتطاع إلزامي" class="w-full border rounded px-1"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Tab 4: العهدة الشخصية (Personal Assets) -->
            <div id="tab-assets" class="tab-pane hidden p-4 space-y-3">
                <table class="w-full border-collapse border border-gray-200 text-right">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 font-bold">
                            <th class="p-2 border border-gray-200">العهدة / الأصل المسلم</th>
                            <th class="p-2 border border-gray-200">العدد</th>
                            <th class="p-2 border border-gray-200">الرقم التسلسلي / الموديل</th>
                            <th class="p-2 border border-gray-200">ملاحظات والتسليم</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-2 border border-gray-200"><input type="text" placeholder="لابتوب عمل / جهاز POS / سيارة" class="w-full border rounded px-2 py-1"></td>
                            <td class="p-2 border border-gray-200"><input type="number" value="1" class="w-16 text-center border rounded px-1 py-1"></td>
                            <td class="p-2 border border-gray-200"><input type="text" placeholder="S/N" class="w-full border rounded px-2 py-1"></td>
                            <td class="p-2 border border-gray-200"><input type="text" placeholder="بحالة ممتازة" class="w-full border rounded px-2 py-1"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Tab 5: التأمين الصحي -->
            <div id="tab-insurance" class="tab-pane hidden p-4 space-y-3">
                <table class="w-full border-collapse border border-gray-200 text-right">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 font-bold">
                            <th class="p-2 border border-gray-200">شركة التأمين</th>
                            <th class="p-2 border border-gray-200">من تاريخ</th>
                            <th class="p-2 border border-gray-200">إلى تاريخ</th>
                            <th class="p-2 border border-gray-200">رقم البطاقة</th>
                            <th class="p-2 border border-gray-200">عدد الزوجات</th>
                            <th class="p-2 border border-gray-200">عدد الأولاد</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-2 border border-gray-200"><input type="text" placeholder="اسم شركة التأمين" class="w-full border rounded px-2 py-1"></td>
                            <td class="p-2 border border-gray-200"><input type="date" value="{{ date('Y-m-d') }}" class="border rounded px-1 py-1"></td>
                            <td class="p-2 border border-gray-200"><input type="date" value="{{ date('Y-m-d', strtotime('+1 year')) }}" class="border rounded px-1 py-1"></td>
                            <td class="p-2 border border-gray-200"><input type="text" placeholder="000-000" class="w-full border rounded px-2 py-1"></td>
                            <td class="p-2 border border-gray-200"><input type="number" value="0" class="w-16 text-center border rounded px-1 py-1"></td>
                            <td class="p-2 border border-gray-200"><input type="number" value="0" class="w-16 text-center border rounded px-1 py-1"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

<script>
function switchTab(tabId) {
    document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('bg-[#1F2E4A]', 'text-white');
        el.classList.add('text-gray-600');
    });

    const activePane = document.getElementById('tab-' + tabId);
    const activeBtn = document.getElementById('tab-btn-' + tabId);
    if(activePane) activePane.classList.remove('hidden');
    if(activeBtn) {
        activeBtn.classList.add('bg-[#1F2E4A]', 'text-white');
        activeBtn.classList.remove('text-gray-600');
    }
}

function calcDailySalary() {
    const basic = parseFloat(document.getElementById('basic_salary').value) || 0;
    const daysInMonth = 30.4375; // معيار حساباتي والقانون الأردني
    const daily = basic > 0 ? (basic / daysInMonth).toFixed(2) : 0;
    const hourly = daily > 0 ? (daily / 8).toFixed(2) : 0;
    const overtime = hourly > 0 ? (hourly * 1.25).toFixed(2) : 0;

    document.getElementById('daily_salary').value = daily;
    document.getElementById('hourly_rate').value = hourly;
    document.getElementById('overtime_hourly_rate').value = overtime;
}
</script>
@endsection
