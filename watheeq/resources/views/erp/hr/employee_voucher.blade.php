@extends('layouts.app')

@section('title', 'سندات الموظفين المالية | حساباتي HR | وثيق ERP')

@section('content')
<div class="space-y-4 font-sans text-xs" dir="rtl">

    @php
        $voucherTitles = [
            'payment' => 'سند المدفوعات للموظف',
            'discount' => 'سند خصم للموظف',
            'bonus' => 'سند مكافأة للموظف',
            'transport' => 'سند مواصلات',
            'overtime' => 'سند عمل إضافي',
            'advance' => 'سند سلفة',
            'housing' => 'سند بدل سكن',
            'dues_settlement' => 'سند تسديد مستحقات'
        ];
        $currentTitle = $voucherTitles[$type] ?? 'سند مالي للموظف';
    @endphp

    <form method="POST" action="{{ route('erp.hr.employee_voucher.store') }}" id="voucherForm">
        @csrf
        <input type="hidden" name="voucher_type" value="{{ $type }}">

        <!-- Navigation Ribbon (Hesabate 12_4i Style) -->
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
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    طباعة
                </button>
                <button type="button" class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded font-bold border border-blue-200 transition">
                    ترحيل
                </button>
                <a href="{{ route('erp.hr.employee_voucher', ['type' => $type]) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    جديد
                </a>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    مرفقات
                </button>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm font-black text-gray-800">{{ $currentTitle }}</span>
                <span class="bg-emerald-600 text-white text-[11px] px-2 py-0.5 rounded font-black">جديد</span>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <!-- Main Voucher Card (طابق تماماً شاشات Hesabate 12_4i, 12_5i, 12_6i, 12_9i) -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Col 1: رقم وتاريخ -->
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">رقم السند:</label>
                        <input type="text" name="voucher_no" value="{{ $nextVoucherNo ?? '1' }}" required class="w-full bg-gray-50 border border-gray-300 rounded px-2.5 py-1 text-center font-bold text-gray-800">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">سند يدوي:</label>
                        <input type="text" name="manual_no" class="w-full border border-gray-300 rounded px-2.5 py-1">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">تاريخه:</label>
                        <input type="date" name="voucher_date" value="{{ date('Y-m-d') }}" required class="w-full border border-gray-300 rounded px-2.5 py-1">
                    </div>
                </div>

                <!-- Col 2: الموظف ودفتر السندات -->
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">الموظف:</label>
                        <div class="relative w-full flex">
                            <select name="employee_id" id="employee_select" onchange="updateEmpName(this)" required class="w-full border border-gray-300 rounded-r px-2 py-1 font-bold text-gray-900 bg-yellow-50">
                                <option value="">-- اختر موظف --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" data-name="{{ $emp->name }}">{{ $emp->emp_code }} - {{ $emp->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="bg-gray-200 hover:bg-gray-300 px-2 rounded-l border border-r-0 border-gray-300">🔍</button>
                        </div>
                    </div>
                    <input type="hidden" name="employee_name" id="employee_name_hidden">
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">دفتر السندات:</label>
                        <select name="voucher_book" class="w-full border border-gray-300 rounded px-2 py-1">
                            <option value="الرئيسي">الدفتر الرئيسي</option>
                            <option value="فرعي">دفتر فرعي</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">مركز التكلفة:</label>
                        <input type="text" name="cost_center" placeholder="عام" class="w-full border border-gray-300 rounded px-2.5 py-1">
                    </div>
                </div>

                <!-- Col 3: المبالغ والعملة -->
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">المبلغ:</label>
                        <input type="number" step="0.01" name="amount" id="amount" value="0.00" oninput="calcVoucherTotal()" required class="w-full border border-gray-300 rounded px-2.5 py-1 font-black text-gray-900 bg-yellow-50 text-left">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">العملة:</label>
                        <div class="flex w-full">
                            <select name="currency_code" id="currency_code" onchange="calcVoucherTotal()" class="w-1/2 border border-gray-300 rounded-r px-2 py-1 font-bold">
                                <option value="JOD" data-rate="1.000">JOD</option>
                                <option value="USD" data-rate="0.709">USD</option>
                            </select>
                            <input type="number" step="0.0001" name="exchange_rate" id="exchange_rate" value="1.000" oninput="calcVoucherTotal()" class="w-1/2 border border-r-0 border-gray-300 rounded-l px-2 py-1 text-center bg-gray-50">
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">بالرئيسية:</label>
                        <input type="text" id="amount_base_currency" readonly class="w-full bg-gray-100 border border-gray-300 rounded px-2.5 py-1 font-bold text-gray-700 text-left">
                    </div>
                </div>

                <!-- Col 4: خاضع للضريبة وحالة السند -->
                <div class="space-y-2 bg-gray-50 p-2.5 rounded-xl border border-gray-200">
                    <label class="flex items-center gap-2 font-bold text-gray-700 cursor-pointer">
                        <input type="checkbox" name="subject_to_income_tax" value="1" class="w-4 h-4 text-[#1F2E4A] rounded">
                        <span>خاضع لضريبة الدخل الأردنية</span>
                    </label>
                    <div class="text-[11px] text-gray-500 pt-1">
                        وفقاً لنظام حساباتي ومعايير ضريبة الدخل ISTD: المكافآت والعمل الإضافي قد يخضع للاقتطاع الشهري.
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="pt-2 border-t border-gray-100">
                <label class="block text-gray-600 font-bold mb-1">ملاحظات وبيان السند:</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded p-2 text-xs" placeholder="اكتب تفاصيل السند والغرض منه..."></textarea>
            </div>
        </div>

        <!-- Quick Switcher to Other HR Vouchers -->
        <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between flex-wrap gap-2">
            <span class="font-bold text-gray-700">التنقل السريع بين سندات حساباتي HR:</span>
            <div class="flex items-center gap-1.5 flex-wrap">
                <a href="{{ route('erp.hr.employee_voucher', ['type' => 'payment']) }}" class="px-2.5 py-1 rounded border {{ $type == 'payment' ? 'bg-[#1F2E4A] text-white' : 'bg-gray-50 hover:bg-gray-100 text-gray-700' }}">سند المدفوعات</a>
                <a href="{{ route('erp.hr.employee_voucher', ['type' => 'discount']) }}" class="px-2.5 py-1 rounded border {{ $type == 'discount' ? 'bg-[#1F2E4A] text-white' : 'bg-gray-50 hover:bg-gray-100 text-gray-700' }}">سند خصم</a>
                <a href="{{ route('erp.hr.employee_voucher', ['type' => 'bonus']) }}" class="px-2.5 py-1 rounded border {{ $type == 'bonus' ? 'bg-[#1F2E4A] text-white' : 'bg-gray-50 hover:bg-gray-100 text-gray-700' }}">سند مكافأة</a>
                <a href="{{ route('erp.hr.employee_voucher', ['type' => 'transport']) }}" class="px-2.5 py-1 rounded border {{ $type == 'transport' ? 'bg-[#1F2E4A] text-white' : 'bg-gray-50 hover:bg-gray-100 text-gray-700' }}">سند مواصلات</a>
                <a href="{{ route('erp.hr.employee_voucher', ['type' => 'overtime']) }}" class="px-2.5 py-1 rounded border {{ $type == 'overtime' ? 'bg-[#1F2E4A] text-white' : 'bg-gray-50 hover:bg-gray-100 text-gray-700' }}">سند عمل إضافي</a>
                <a href="{{ route('erp.hr.employee_voucher', ['type' => 'advance']) }}" class="px-2.5 py-1 rounded border {{ $type == 'advance' ? 'bg-[#1F2E4A] text-white' : 'bg-gray-50 hover:bg-gray-100 text-gray-700' }}">سند سلفة</a>
                <a href="{{ route('erp.hr.employee_voucher', ['type' => 'housing']) }}" class="px-2.5 py-1 rounded border {{ $type == 'housing' ? 'bg-[#1F2E4A] text-white' : 'bg-gray-50 hover:bg-gray-100 text-gray-700' }}">سند بدل سكن</a>
                <a href="{{ route('erp.hr.employee_voucher', ['type' => 'dues_settlement']) }}" class="px-2.5 py-1 rounded border {{ $type == 'dues_settlement' ? 'bg-[#1F2E4A] text-white' : 'bg-gray-50 hover:bg-gray-100 text-gray-700' }}">تسديد مستحقات</a>
            </div>
        </div>
    </form>
</div>

<script>
function updateEmpName(select) {
    const selectedOption = select.options[select.selectedIndex];
    document.getElementById('employee_name_hidden').value = selectedOption.getAttribute('data-name') || '';
}

function calcVoucherTotal() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const rate = parseFloat(document.getElementById('exchange_rate').value) || 1.0;
    const baseTotal = (amount * rate).toFixed(2);
    document.getElementById('amount_base_currency').value = baseTotal;
}

window.addEventListener('DOMContentLoaded', () => {
    calcVoucherTotal();
    const sel = document.getElementById('employee_select');
    if(sel) updateEmpName(sel);
});
</script>
@endsection
