@extends('layouts.app')

@section('title', 'خصائص ضبط الدوام والإجازات | حساباتي HR | وثيق ERP')

@section('content')
<div class="space-y-4 font-sans text-xs" dir="rtl">

    <form method="POST" action="{{ route('erp.hr.settings.store') }}">
        @csrf

        <!-- Ribbon (Hesabate 3_3_12i) -->
        <div class="bg-white p-2 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-2 no-print">
            <div class="flex items-center gap-1.5 flex-wrap">
                <button type="submit" class="px-5 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
                    حفظ
                </button>
                <a href="{{ route('erp.hr') }}" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    رجوع للموارد البشرية
                </a>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm font-black text-gray-800">خصائص ضبط الدوام والإجازات والضرائب</span>
                <span class="bg-[#1F2E4A] text-white text-[11px] px-2 py-0.5 rounded font-black">حساباتي HR</span>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <!-- Settings Grid (طابق شاشة حساباتي HR 3_3_12i بالضبط) -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-4">
            <h3 class="font-bold text-gray-900 border-b border-gray-100 pb-2 text-sm">المحددات والمعايير العامة لحساب ساعات الدوام والإجازات</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Column 1 -->
                <div class="space-y-4 bg-gray-50/50 p-4 rounded-xl border border-gray-200">
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">معامل ضرب تأخير الدخول:</label>
                        <input type="number" step="0.1" name="late_entry_multiplier" value="{{ $settings->late_entry_multiplier ?? '1.0' }}" class="w-full bg-white border border-gray-300 rounded px-3 py-1.5 font-bold text-center">
                        <span class="text-[11px] text-gray-500 block mt-1">يُضرب في عدد ساعات التأخير عند الخصم</span>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-1">عدد أيام العطل المرضية مدفوعة الأجر:</label>
                        <input type="number" name="paid_sick_leave_days" value="{{ $settings->paid_sick_leave_days ?? '14' }}" class="w-full bg-white border border-gray-300 rounded px-3 py-1.5 font-bold text-center">
                        <span class="text-[11px] text-gray-500 block mt-1">معيار قانون العمل الأردني: 14 يوم سنوياً</span>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-1">الإعفاءات - مقيم - سنوي (د.أ):</label>
                        <input type="number" step="0.01" name="annual_resident_exemption" value="{{ $settings->annual_resident_exemption ?? '9000.00' }}" class="w-full bg-yellow-50 border border-gray-300 rounded px-3 py-1.5 font-bold text-center text-emerald-900">
                        <span class="text-[11px] text-gray-500 block mt-1">قانون ضريبة الدخل الأردني (9,000 للمكلف + 9,000 للمعالين)</span>
                    </div>
                </div>

                <!-- Column 2 -->
                <div class="space-y-4 bg-gray-50/50 p-4 rounded-xl border border-gray-200">
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">معامل ضرب الخروج المبكر:</label>
                        <input type="number" step="0.1" name="early_exit_multiplier" value="{{ $settings->early_exit_multiplier ?? '1.0' }}" class="w-full bg-white border border-gray-300 rounded px-3 py-1.5 font-bold text-center">
                        <span class="text-[11px] text-gray-500 block mt-1">يُضرب في عدد ساعات المغادرة المبكرة</span>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-1">عدد أيام العطل المرضية نصف مدفوعة الأجر:</label>
                        <input type="number" name="half_paid_sick_leave_days" value="{{ $settings->half_paid_sick_leave_days ?? '14' }}" class="w-full bg-white border border-gray-300 rounded px-3 py-1.5 font-bold text-center">
                        <span class="text-[11px] text-gray-500 block mt-1">14 يوماً إضافية بنصف أجر حسب القانون الأردني</span>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-1">الإعفاءات - غير مقيم %:</label>
                        <input type="number" step="0.1" name="non_resident_exemption_pct" value="{{ $settings->non_resident_exemption_pct ?? '0' }}" class="w-full bg-white border border-gray-300 rounded px-3 py-1.5 font-bold text-center">
                        <span class="text-[11px] text-gray-500 block mt-1">غير المقيم لا يستفيد من الإعفاء الشخصي السنوي</span>
                    </div>
                </div>

                <!-- Column 3 -->
                <div class="space-y-4 bg-gray-50/50 p-4 rounded-xl border border-gray-200">
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">عدد أيام العطل الكلية المسموح:</label>
                        <input type="number" name="total_allowed_vacation_days" value="{{ $settings->total_allowed_vacation_days ?? '21' }}" class="w-full bg-white border border-gray-300 rounded px-3 py-1.5 font-bold text-center">
                        <span class="text-[11px] text-gray-500 block mt-1">رصيد الإجازات السنوية المعتمد</span>
                    </div>

                    <div>
                        <label class="flex items-center gap-2 text-gray-800 font-bold cursor-pointer pt-2">
                            <input type="checkbox" name="count_leave_day_as_overtime" value="1" {{ ($settings->count_leave_day_as_overtime ?? false) ? 'checked' : '' }} class="w-4 h-4 text-[#1F2E4A] rounded">
                            <span>احتساب العمل يوم الإجازة ساعات إضافية</span>
                        </label>
                        <span class="text-[11px] text-gray-500 block mt-1">يحتسب بمعامل 1.5 حسب قانون العمل الأردني</span>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-1">عدد الأيام - الشهر:</label>
                        <input type="number" step="0.0001" name="days_per_month" value="{{ $settings->days_per_month ?? '30.4375' }}" class="w-full bg-white border border-gray-300 rounded px-3 py-1.5 font-bold text-center text-[#1F2E4A]">
                        <span class="text-[11px] text-gray-500 block mt-1">معيار حساباتي الدقيق: 365.25 ÷ 12 = 30.4375 يوم</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
