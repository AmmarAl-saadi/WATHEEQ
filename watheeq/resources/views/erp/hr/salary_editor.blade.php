@extends('layouts.app')

@section('title', 'تعديل راتب موظف | وثيق ERP')

@section('content')
<div class="space-y-3 font-sans text-xs" dir="rtl">

    <form method="POST" action="{{ route('erp.hr.salary_editor.store') }}" id="salaryEditorForm">
        @csrf

        <!-- Top Tool Command Ribbon -->
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
                <a href="{{ route('erp.hr.salary_editor') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    جديد
                </a>
                <a href="{{ route('erp.hr') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    إلغاء
                </a>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 bg-emerald-500 text-white font-bold rounded text-[11px]">
                    جديد
                </span>
                <span class="font-bold text-gray-800 text-sm">
                    تعديل راتب (+)
                </span>
            </div>
        </div>

        <!-- Master Document Header Meta Box -->
        <div class="bg-[#F8F9FA] p-3.5 rounded-xl border border-gray-200 shadow-sm space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">رقم السند:</span>
                    <input type="text" name="voucher_no" value="SAL-VCH-{{ sprintf('%04d', $nextNo) }}" readonly class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono font-bold text-gray-800 text-center">
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-16 font-bold text-gray-600 text-left">تاريخه:</span>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono text-gray-800 text-center">
                </div>

                <div class="md:col-span-6 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">رقم الموظف:</span>
                    <div class="flex-1 flex gap-1">
                        <select name="employee_name" id="employeeSelect" onchange="fillEmployeeData()" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->name }}" data-salary="{{ $emp->basic_salary ?? $emp->salary ?? 750 }}" data-dept="{{ $emp->department }}">{{ $emp->name }} ({{ $emp->job_title ?? $emp->position ?? 'موظف' }})</option>
                            @endforeach
                            @if($employees->isEmpty())
                                <option value="أحمد المحاسب" data-salary="750" data-dept="الإدارة المالية">أحمد المحاسب (محاسب عام)</option>
                                <option value="سارة المبيعات" data-salary="600" data-dept="المبيعات">سارة المبيعات (مسؤولة مبيعات)</option>
                            @endif
                        </select>
                        <button type="button" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 border rounded font-bold text-gray-700">بحث</button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-4 flex items-center gap-2">
                    <span class="w-24 font-bold text-gray-600 text-left">احتساب من تاريخ:</span>
                    <input type="date" value="{{ date('Y-m-01') }}" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono text-center">
                </div>
                <div class="md:col-span-4 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">القسم:</span>
                    <input type="text" name="department" id="deptInput" value="الإدارة المالية" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1">
                </div>
                <div class="md:col-span-4 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">التصنيف:</span>
                    <select class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold">
                        <option>معلومات إدارية + معلومات مالية</option>
                        <option>معلومات مالية فقط</option>
                        <option>معلومات إدارية فقط</option>
                    </select>
                </div>
            </div>

            <!-- Notes -->
            <div class="flex items-center gap-2 pt-1">
                <span class="w-20 font-bold text-gray-600 text-left">ملاحظات:</span>
                <input type="text" name="notes" placeholder="ملاحظات وتفاصيل تعديل الراتب أو الترقية..." class="flex-1 bg-white border border-gray-300 rounded px-2 py-1">
            </div>
        </div>

        <!-- Salary Breakdown Form Grid (مطابق بالمللي لشاشة تعديل راتب بوثيق) -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-4">
            <h4 class="font-black text-[#1F2E4A] border-b pb-2 text-sm">محددات الراتب، الساعات والعمولات:</h4>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Col 1 -->
                <div class="space-y-3 bg-[#F8F9FA] p-3 rounded-xl border border-gray-200">
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="font-bold text-gray-700">الراتب الشهري (USD):</label>
                            <button type="button" onclick="calcRates()" class="text-[10px] bg-[#1F2E4A] text-white px-2 py-0.5 rounded font-bold hover:bg-[#343A40]">
                                احتساب الراتب اليومي
                            </button>
                        </div>
                        <input type="number" step="0.001" name="monthly_salary" id="monthlySalaryInput" value="750.000" oninput="calcRates()" class="w-full bg-white border border-gray-300 rounded px-2.5 py-1 font-mono font-bold text-base text-center text-[#1F2E4A]">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">الراتب اليومي:</label>
                        <input type="text" name="daily_salary" id="dailySalaryInput" value="25.000" readonly class="w-full bg-gray-100 border border-gray-300 rounded px-2 py-1 font-mono text-center font-bold">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">راتب الساعة:</label>
                        <input type="text" name="hourly_salary" id="hourlySalaryInput" value="3.125" readonly class="w-full bg-gray-100 border border-gray-300 rounded px-2 py-1 font-mono text-center font-bold">
                    </div>
                </div>

                <!-- Col 2 -->
                <div class="space-y-3 bg-[#F8F9FA] p-3 rounded-xl border border-gray-200">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">راتب الساعة إضافي:</label>
                        <input type="number" step="0.001" name="overtime_rate" id="overtimeInput" value="4.688" class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-mono text-center font-bold">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">عمولة المبيعات (%):</label>
                        <input type="number" step="0.1" name="sales_commission" value="2.5" class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-mono text-center font-bold">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">عمولة التحصيل للشيكات (%):</label>
                        <input type="number" step="0.1" name="collection_commission" value="1.0" class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-mono text-center font-bold">
                    </div>
                </div>

                <!-- Col 3 -->
                <div class="space-y-3 bg-[#F8F9FA] p-3 rounded-xl border border-gray-200">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">إجازة أسبوعية 1:</label>
                        <select name="weekend_day_1" class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-bold">
                            <option value="الجمعة" selected>الجمعة</option>
                            <option value="السبت">السبت</option>
                            <option value="الأحد">الأحد</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">إجازة أسبوعية 2:</label>
                        <select name="weekend_day_2" class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-bold">
                            <option value="السبت" selected>السبت</option>
                            <option value="الجمعة">الجمعة</option>
                            <option value="بدون">بدون (يوم واحد فقط)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">قسيمة الراتب المعتمدة:</label>
                        <select class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-bold">
                            <option>آلي - احتساب تلقائي للضمان والاستقطاعات</option>
                            <option>يدوي - ثابت</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

    </form>

</div>

<script>
    function calcRates() {
        const monthly = parseFloat(document.getElementById('monthlySalaryInput').value) || 0;
        const daily = monthly / 30;
        const hourly = daily / 8;
        const overtime = hourly * 1.5;

        document.getElementById('dailySalaryInput').value = daily.toFixed(3);
        document.getElementById('hourlySalaryInput').value = hourly.toFixed(3);
        document.getElementById('overtimeInput').value = overtime.toFixed(3);
    }

    function fillEmployeeData() {
        const sel = document.getElementById('employeeSelect');
        const opt = sel.options[sel.selectedIndex];
        if (opt) {
            const sal = opt.getAttribute('data-salary');
            const dept = opt.getAttribute('data-dept');
            if (sal) document.getElementById('monthlySalaryInput').value = parseFloat(sal).toFixed(3);
            if (dept) document.getElementById('deptInput').value = dept;
            calcRates();
        }
    }
</script>
@endsection


