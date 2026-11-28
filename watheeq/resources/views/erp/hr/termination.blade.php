@extends('layouts.app')

@section('title', 'إنهاء خدمة موظف | حساباتي HR | وثيق ERP')

@section('content')
<div class="space-y-4 font-sans text-xs" dir="rtl">

    <form method="POST" action="{{ route('erp.hr.termination.store') }}">
        @csrf

        <!-- Ribbon (Hesabate 3_3_5i) -->
        <div class="bg-white p-2 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-2 no-print">
            <div class="flex items-center gap-1.5 flex-wrap">
                <button type="submit" class="px-5 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
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
                <a href="{{ route('erp.hr.termination') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    جديد
                </a>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    إلغاء
                </button>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm font-black text-gray-800">إنهاء خدمة موظف</span>
                <span class="bg-emerald-600 text-white text-[11px] px-2 py-0.5 rounded font-black">جديد</span>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <!-- Card Form (Hesabate 3_3_5i) -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">رقم السند:</label>
                        <input type="text" name="termination_no" value="{{ $nextNo ?? '1' }}" required class="w-full bg-gray-50 border border-gray-300 rounded px-2.5 py-1 text-center font-bold text-gray-800">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">رقم الموظف:</label>
                        <select name="employee_id" id="emp_term_select" onchange="updateTermName(this)" required class="w-full border border-gray-300 rounded px-2.5 py-1 font-bold bg-yellow-50">
                            <option value="">-- اختر موظف --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" data-name="{{ $emp->name }}">{{ $emp->emp_code }} - {{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">تاريخه:</label>
                        <input type="date" name="termination_date" value="{{ date('Y-m-d') }}" required class="w-full border border-gray-300 rounded px-2.5 py-1">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">الاسم:</label>
                        <input type="text" name="employee_name" id="employee_term_name" readonly class="w-full bg-gray-100 border border-gray-300 rounded px-2.5 py-1 font-bold text-gray-800">
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">الحالة:</label>
                        <select name="status" class="w-full border border-gray-300 rounded px-2.5 py-1 font-bold text-red-600">
                            <option value="إنهاء خدمة">إنهاء خدمة</option>
                            <option value="استقالة">استقالة</option>
                            <option value="فسخ عقد">فسخ عقد</option>
                            <option value="انتهاء مدة العقد">انتهاء مدة العقد</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-2 border-t border-gray-100">
                <label class="block text-gray-600 font-bold mb-1">ملاحظات وسبب إنهاء الخدمة / مستحقات مكافأة نهاية الخدمة:</label>
                <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded p-2 text-xs" placeholder="تسوية رصيد الإجازات ومكافأة نهاية الخدمة وفقاً لقانون العمل الأردني..."></textarea>
            </div>
        </div>
    </form>
</div>

<script>
function updateTermName(select) {
    const selectedOption = select.options[select.selectedIndex];
    document.getElementById('employee_term_name').value = selectedOption.getAttribute('data-name') || '';
}
</script>
@endsection
