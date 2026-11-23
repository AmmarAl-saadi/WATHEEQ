@extends('layouts.app')

@section('title', ($type === 'receipt' ? 'سند قبض مالي' : 'سند دفع وصرف') . ' | وثيق ERP')

@section('content')
<div class="space-y-3 font-sans text-xs" dir="rtl">

    <form method="POST" action="{{ route('erp.vouchers.store') }}" id="watheeqVoucherForm">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">

        <!-- Top Tool Command Ribbon -->
        <div class="bg-white p-2 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-2 no-print">
            <div class="flex items-center gap-1.5 flex-wrap">
                <button type="submit" class="px-4 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
                    حفظ وترحيل
                </button>
                <button type="button" onclick="window.print()" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    طباعة السند
                </button>
                <a href="{{ route('erp.vouchers.editor', ['type' => $type]) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    جديد
                </a>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    إلغاء
                </button>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    مرفقات
                </button>
                <a href="{{ route('erp.vouchers') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    سجل السندات
                </a>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 bg-emerald-500 text-white font-bold rounded text-[11px]">
                    جديد
                </span>
                <span class="font-bold text-gray-700 text-sm">
                    {{ $type === 'receipt' ? 'سند قبض مالي (وارد)' : 'سند دفع وصرف (صادر)' }}
                </span>
            </div>
        </div>

        <!-- Master Document Header Meta Box -->
        <div class="bg-[#F8F9FA] p-3.5 rounded-xl border border-gray-200 shadow-sm space-y-2.5">
            <!-- Row 1: رقم السند، التاريخ، الحساب/العميل المقبوض منه أو المدفوع له -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-24 font-bold text-gray-600 text-left">رقم السند:</span>
                    <input type="text" name="voucher_no" value="VCH-{{ date('Ymd') }}-{{ sprintf('%03d', $nextVoucherNo) }}" readonly class="flex-1 bg-white border border-gray-300 rounded px-2.5 py-1 font-mono font-bold text-gray-800">
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-16 font-bold text-gray-600 text-left">تاريخه:</span>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono text-gray-800">
                </div>

                <div class="md:col-span-6 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">{{ $type === 'receipt' ? 'وصلنا من:' : 'ادفعوا إلى:' }}</span>
                    <select name="contact_name" required class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
                        @foreach($contacts as $c)
                            <option value="{{ $c->name }}">{{ $c->name }} (رصيد: {{ number_format($c->balance, 2) }} {{ $tenant->currency ?? 'JOD' }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Row 2: المبلغ، العملة، طريقة القبض/الدفع -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-24 font-bold text-gray-600 text-left">المبلغ بالأرقام:</span>
                    <input type="number" step="0.01" name="amount" value="100.00" required class="flex-1 bg-white border border-gray-300 rounded px-2.5 py-1 font-mono font-black text-sm text-[#1F2E4A]">
                </div>

                <div class="md:col-span-2 flex items-center gap-1.5">
                    <span class="font-bold text-gray-600">العملة:</span>
                    <select name="currency" class="flex-1 bg-white border border-gray-300 rounded px-1.5 py-1 font-bold font-mono">
                        <option value="JOD">JOD</option>
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                    </select>
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="font-bold text-gray-600">طريقة التسديد:</span>
                    <select name="payment_type" id="paymentTypeSelect" onchange="toggleChequeFields()" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold">
                        <option value="cash">نقداً (الصندوق الرئيسي)</option>
                        <option value="cheque">شيك بنكي برسم التحصيل</option>
                        <option value="bank_transfer">تحويل بنكي / إيداع</option>
                    </select>
                </div>

                <div class="md:col-span-4 flex items-center gap-2">
                    <span class="w-24 font-bold text-gray-600 text-left">مركز التكلفة:</span>
                    <select name="cost_center_id" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1">
                        <option value="">بدون مركز تكلفة محدد</option>
                        @foreach($costCenters as $cc)
                            <option value="{{ $cc->id }}">{{ $cc->name }} ({{ $cc->code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Row 3: بيانات الشيك (تظهر عند اختيار شيك) -->
            <div id="chequeFieldsRow" class="hidden grid grid-cols-1 md:grid-cols-12 gap-2 items-center bg-[#1F2E4A]/10 p-2.5 rounded-lg border border-[#1F2E4A]/30">
                <div class="md:col-span-4 flex items-center gap-2">
                    <span class="font-bold text-gray-700">رقم الشيك:</span>
                    <input type="text" name="cheque_no" placeholder="مثال: 8849201" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono">
                </div>
                <div class="md:col-span-4 flex items-center gap-2">
                    <span class="font-bold text-gray-700">اسم البنك:</span>
                    <input type="text" name="bank_name" placeholder="مثال: البنك العربي" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1">
                </div>
                <div class="md:col-span-4 flex items-center gap-2">
                    <span class="font-bold text-gray-700">تاريخ الاستحقاق:</span>
                    <input type="date" name="cheque_due_date" value="{{ date('Y-m-d', strtotime('+15 days')) }}" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono">
                </div>
            </div>

            <!-- Row 4: البيان والشرح -->
            <div class="flex items-center gap-2 pt-1">
                <span class="w-24 font-bold text-gray-600 text-left">وذلك عن (البيان):</span>
                <input type="text" name="statement" required placeholder="دفعة عن حساب / سداد فاتورة مبيعات رقم..." class="flex-1 bg-white border border-gray-300 rounded px-2.5 py-1.5 font-medium text-gray-800">
            </div>
        </div>

    </form>

</div>

<script>
    function toggleChequeFields() {
        const val = document.getElementById('paymentTypeSelect').value;
        const row = document.getElementById('chequeFieldsRow');
        if (val === 'cheque') {
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    }
</script>
@endsection



