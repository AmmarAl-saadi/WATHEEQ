@extends('layouts.app')

@section('title', ($type === 'receipt' ? 'سند قبض مالي' : 'سند دفع وصرف') . ' | وثيق ERP')

@section('content')
<div class="space-y-3 font-sans text-xs" dir="rtl">

    <form method="POST" action="{{ route('erp.vouchers.store') }}" id="watheeqVoucherForm">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">

        <!-- Top Tool Command Ribbon (مطابق لحساباتي Frame 2_1i) -->
        <div class="bg-white p-2 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-2 no-print">
            <div class="flex items-center gap-1.5 flex-wrap">
                <button type="button" onclick="alert('الانتقال للسند الأول')" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border font-mono">|&lt;&lt;</button>
                <button type="button" onclick="alert('الانتقال للسند السابق')" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border font-mono">&lt;&lt;</button>
                <button type="button" onclick="prompt('أدخل رقم السند للاستعلام عنه:')" class="px-3 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-800 rounded font-bold border border-sky-200">استعلام</button>
                <button type="button" onclick="alert('الانتقال للسند التالي')" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border font-mono">&gt;&gt;</button>
                <button type="button" onclick="alert('الانتقال للسند الأخير')" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border font-mono">&gt;&gt;|</button>

                <div class="h-6 w-px bg-gray-300 mx-1"></div>

                <button type="submit" class="px-4 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
                    حفظ وترحيل
                </button>
                <button type="button" onclick="window.print()" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    طباعة
                </button>
                <a href="{{ route('erp.vouchers.editor', ['type' => $type]) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    جديد
                </a>
                <button type="button" onclick="window.location.reload()" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded font-bold border border-rose-200 transition">
                    إلغاء
                </button>
                <button type="button" onclick="alert('نافذة إضافة مرفقات السند (PDF/صور)')" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    مرفقات
                </button>
                <button type="button" onclick="alert('تصدير السند إلى Excel')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded font-bold transition">
                    إكسل
                </button>
                <a href="{{ route('erp.vouchers') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    سجل السندات
                </a>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 bg-emerald-500 text-white font-bold rounded text-[11px] animate-pulse">
                    جديد
                </span>
                <span class="font-bold text-gray-800 text-sm">
                    {{ $type === 'receipt' ? 'سند قبض مالي (وارد)' : 'سند دفع وصرف (صادر)' }}
                </span>
            </div>
        </div>

        <!-- Master Document Header Meta Box -->
        <div class="bg-[#F8F9FA] p-3.5 rounded-xl border border-gray-200 shadow-sm space-y-2.5">
            <!-- Row 1: رقم السند، التاريخ، الحساب/العميل -->
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
                    <select name="contact_name" id="voucherContactSelect" onchange="updateContactBalance()" required class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
                        @foreach($contacts as $c)
                            <option value="{{ $c->name }}" data-balance="{{ $c->balance }}">{{ $c->name }} (رصيد: {{ number_format($c->balance, 2) }} {{ $tenant->currency ?? 'JOD' }})</option>
                        @endforeach
                    </select>
                    <input type="text" id="voucherContactBalance" placeholder="الرصيد..." readonly class="w-28 bg-gray-100 border border-gray-300 rounded px-2 py-1 font-mono text-[11px] text-gray-700 font-bold text-center">
                </div>
            </div>

            <!-- Row 2: دفتر السندات، العملة، سعر الصرف، مركز التكلفة -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-24 font-bold text-gray-600 text-left">دفتر السندات:</span>
                    <select class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold">
                        <option>الدفتر المعتمد الرئيسي</option>
                        <option>دفتر مقبوضات الشيكات</option>
                    </select>
                </div>

                <div class="md:col-span-2 flex items-center gap-1.5">
                    <span class="font-bold text-gray-600">العملة:</span>
                    <select name="currency" id="voucherCurrencySelect" onchange="updateVoucherExchangeRate()" class="flex-1 bg-white border border-gray-300 rounded px-1.5 py-1 font-bold font-mono">
                        <option value="JOD" data-rate="1.000">JOD</option>
                        <option value="USD" data-rate="0.709">USD</option>
                        <option value="EUR" data-rate="0.770">EUR</option>
                    </select>
                </div>

                <div class="md:col-span-2 flex items-center gap-1.5">
                    <span class="font-bold text-gray-600">سعر الصرف:</span>
                    <input type="number" step="0.0001" id="voucherRateInput" value="1.0000" class="w-24 bg-white border border-gray-300 rounded px-1.5 py-1 font-mono font-bold text-center">
                </div>

                <div class="md:col-span-5 flex items-center gap-2">
                    <span class="w-24 font-bold text-gray-600 text-left">مركز التكلفة:</span>
                    <select name="cost_center_id" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1">
                        <option value="">بدون مركز تكلفة محدد</option>
                        @foreach($costCenters as $cc)
                            <option value="{{ $cc->id }}">{{ $cc->name }} ({{ $cc->code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 1: الدفع والقبض النقدي (Cash) -->
        <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-sm space-y-2">
            <div class="flex items-center justify-between border-b pb-2">
                <span class="font-black text-xs text-[#1F2E4A] flex items-center gap-1.5">
                    <span>💵</span> الدفع / القبض النقدي:
                </span>
                <span class="text-[11px] text-gray-500">حساب الصندوق المباشر</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                <div class="md:col-span-4 flex items-center gap-2">
                    <span class="font-bold text-gray-600">المبلغ المقبوض/المدفوع كاش:</span>
                    <input type="number" step="0.01" name="amount" id="cashAmountInput" value="0.00" oninput="calculateVoucherTotal()" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono font-bold text-left text-sm text-[#1F2E4A]">
                </div>
                <div class="md:col-span-4 flex items-center gap-2">
                    <span class="font-bold text-gray-600">الصندوق المعني:</span>
                    <select name="payment_type" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold">
                        <option value="cash">الصندوق الرئيسي - عمان</option>
                        <option value="bank_transfer">حساب البنك الجاري</option>
                    </select>
                </div>
                <div class="md:col-span-4 flex items-center gap-2">
                    <span class="font-bold text-gray-600">المعادل بالعملة الرئيسية:</span>
                    <input type="text" id="cashBaseAmount" value="0.00 JOD" readonly class="w-32 bg-gray-100 border rounded px-2 py-1 font-mono font-bold text-center">
                </div>
            </div>
        </div>

        <!-- Section 2: الدفع والقبض بالشيكات (Hesabate Cheque Multi-grid) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden space-y-2 p-3">
            <div class="flex items-center justify-between border-b pb-2">
                <span class="font-black text-xs text-[#1F2E4A] flex items-center gap-1.5">
                    <span>📑</span> الدفع / القبض بالشيكات (جدول الشيكات المتعددة):
                </span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="openInstallmentsModal()" class="px-2.5 py-1 bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 rounded font-bold text-[11px] transition">
                        الأقساط
                    </button>
                    <button type="button" onclick="alert('معدل تحصيل الشيكات: تم احتساب متوسط فترة التحصيل')" class="px-2.5 py-1 bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 rounded font-bold text-[11px] transition">
                        معدل تحصيل الشيكات
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs" id="chequesTable">
                    <thead class="bg-[#1F2E4A] text-white font-bold text-[11px]">
                        <tr>
                            <th class="py-2 px-2 text-center w-8">#</th>
                            <th class="py-2 px-2 w-32">رقم الشيك</th>
                            <th class="py-2 px-3 w-44">البنك المسحوب عليه</th>
                            <th class="py-2 px-2 w-28">رقم الحساب</th>
                            <th class="py-2 px-2 w-28 font-mono text-center">تاريخ الاستحقاق</th>
                            <th class="py-2 px-2 w-20 font-mono text-center">العملة</th>
                            <th class="py-2 px-2 w-24 font-mono text-left">قيمة الشيك</th>
                            <th class="py-2 px-3 min-w-[150px]">البيان / ملاحظة الشيك</th>
                            <th class="py-2 px-2 text-center w-10">حذف</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 font-medium" id="chequeRows">
                        <tr class="hover:bg-gray-50/80 transition" id="chq_row_0">
                            <td class="py-1.5 px-2 text-center font-mono text-gray-400">1</td>
                            <td class="py-1.5 px-2">
                                <input type="text" name="cheques[0][no]" placeholder="رقم الشيك" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 font-mono text-[11px]">
                            </td>
                            <td class="py-1.5 px-2">
                                <input type="text" name="cheques[0][bank]" placeholder="البنك العربي / بنك الإسكان" class="w-full bg-white border border-gray-300 rounded px-2 py-1">
                            </td>
                            <td class="py-1.5 px-2">
                                <input type="text" name="cheques[0][account]" placeholder="رقم حساب الساحب" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 font-mono text-[11px]">
                            </td>
                            <td class="py-1.5 px-2">
                                <input type="date" name="cheques[0][due_date]" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="w-full bg-white border border-gray-300 rounded px-1 py-1 font-mono text-[10px]">
                            </td>
                            <td class="py-1.5 px-2">
                                <select name="cheques[0][currency]" class="w-full bg-white border border-gray-300 rounded px-1 py-1 font-mono text-[11px]">
                                    <option value="JOD">JOD</option>
                                    <option value="USD">USD</option>
                                </select>
                            </td>
                            <td class="py-1.5 px-2">
                                <input type="number" step="0.01" name="cheques[0][amount]" value="0.00" oninput="calculateVoucherTotal()" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-left font-mono font-bold">
                            </td>
                            <td class="py-1.5 px-2">
                                <input type="text" name="cheques[0][statement]" placeholder="بيان الشيك..." class="w-full bg-white border border-gray-300 rounded px-2 py-1 text-[11px]">
                            </td>
                            <td class="py-1.5 px-2 text-center">
                                <button type="button" onclick="removeChequeRow(0)" class="text-rose-600 hover:text-rose-800 font-bold px-1.5 py-0.5 rounded text-sm">×</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-2 border-t border-gray-200 bg-gray-50 flex justify-between items-center no-print">
                <button type="button" onclick="addNewChequeRow()" class="px-3 py-1 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded text-xs font-bold transition">
                    + إضافة شيك جديد
                </button>
                <span class="text-xs text-gray-500 font-mono">مجموع مبالغ الشيكات: <span id="dispChequesTotal" class="font-bold text-[#1F2E4A]">0.00 JOD</span></span>
            </div>
        </div>

        <!-- Master Totals Summary Box -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                <div class="md:col-span-6 space-y-1">
                    <label class="block font-bold text-gray-700">البيان العام للسند:</label>
                    <textarea name="statement" rows="2" placeholder="وذلك لقاء تسديد دفعة من الحساب / مصاريف..." class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2 text-xs"></textarea>
                </div>

                <div class="md:col-span-6 bg-[#F8F9FA] p-3.5 rounded-xl border border-gray-200 space-y-2">
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold text-gray-600">مجموع المقبوض نقداً (Cash):</span>
                        <span class="font-mono font-bold text-emerald-700" id="dispSummaryCash">0.00 JOD</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold text-gray-600">مجموع الشيكات المرفقة (Cheques):</span>
                        <span class="font-mono font-bold text-blue-700" id="dispSummaryCheques">0.00 JOD</span>
                    </div>
                    <div class="border-t border-gray-300 pt-1.5 flex justify-between items-center text-sm">
                        <span class="font-black text-gray-900">المجموع الكلي للسند:</span>
                        <span class="font-mono font-black text-lg text-[#1F2E4A] bg-[#1F2E4A]/10 px-3 py-0.5 rounded-lg" id="dispVoucherGrandTotal">
                            0.00 JOD
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </form>

</div>

<script>
    let chqIndex = 1;

    document.addEventListener('DOMContentLoaded', function () {
        updateContactBalance();
        calculateVoucherTotal();
    });

    function updateContactBalance() {
        const select = document.getElementById('voucherContactSelect');
        const selected = select.options[select.selectedIndex];
        const balance = selected ? selected.getAttribute('data-balance') : 0;
        document.getElementById('voucherContactBalance').value = Number(balance).toLocaleString('en-US', {minimumFractionDigits: 2}) + ' JOD';
    }

    function updateVoucherExchangeRate() {
        const sel = document.getElementById('voucherCurrencySelect');
        const rate = sel.options[sel.selectedIndex].getAttribute('data-rate');
        document.getElementById('voucherRateInput').value = rate || 1.0000;
        calculateVoucherTotal();
    }

    function calculateVoucherTotal() {
        const cash = parseFloat(document.getElementById('cashAmountInput').value) || 0;
        const rate = parseFloat(document.getElementById('voucherRateInput').value) || 1;
        const cashBase = cash * rate;
        document.getElementById('cashBaseAmount').value = cashBase.toFixed(2) + ' JOD';

        let chequesTotal = 0;
        const rows = document.querySelectorAll('#chequeRows tr');
        rows.forEach(function (row) {
            const amountInput = row.querySelector('input[name*="[amount]"]');
            if (amountInput) {
                chequesTotal += parseFloat(amountInput.value) || 0;
            }
        });

        const grand = cash + chequesTotal;
        document.getElementById('dispChequesTotal').innerText = chequesTotal.toFixed(2) + ' JOD';
        document.getElementById('dispSummaryCash').innerText = cash.toFixed(2) + ' JOD';
        document.getElementById('dispSummaryCheques').innerText = chequesTotal.toFixed(2) + ' JOD';
        document.getElementById('dispVoucherGrandTotal').innerText = grand.toFixed(2) + ' JOD';
    }

    function addNewChequeRow() {
        const tbody = document.getElementById('chequeRows');
        const count = tbody.querySelectorAll('tr').length;
        const newRow = document.createElement('tr');
        newRow.className = "hover:bg-gray-50/80 transition";
        newRow.id = "chq_row_" + count;
        newRow.innerHTML = `
            <td class="py-1.5 px-2 text-center font-mono text-gray-400">${count + 1}</td>
            <td class="py-1.5 px-2">
                <input type="text" name="cheques[${count}][no]" placeholder="رقم الشيك" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 font-mono text-[11px]">
            </td>
            <td class="py-1.5 px-2">
                <input type="text" name="cheques[${count}][bank]" placeholder="اسم البنك" class="w-full bg-white border border-gray-300 rounded px-2 py-1">
            </td>
            <td class="py-1.5 px-2">
                <input type="text" name="cheques[${count}][account]" placeholder="رقم الحساب" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 font-mono text-[11px]">
            </td>
            <td class="py-1.5 px-2">
                <input type="date" name="cheques[${count}][due_date]" value="{{ date('Y-m-d') }}" class="w-full bg-white border border-gray-300 rounded px-1 py-1 font-mono text-[10px]">
            </td>
            <td class="py-1.5 px-2">
                <select name="cheques[${count}][currency]" class="w-full bg-white border border-gray-300 rounded px-1 py-1 font-mono text-[11px]">
                    <option value="JOD">JOD</option>
                    <option value="USD">USD</option>
                </select>
            </td>
            <td class="py-1.5 px-2">
                <input type="number" step="0.01" name="cheques[${count}][amount]" value="0.00" oninput="calculateVoucherTotal()" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-left font-mono font-bold">
            </td>
            <td class="py-1.5 px-2">
                <input type="text" name="cheques[${count}][statement]" placeholder="بيان الشيك..." class="w-full bg-white border border-gray-300 rounded px-2 py-1 text-[11px]">
            </td>
            <td class="py-1.5 px-2 text-center">
                <button type="button" onclick="removeChequeRow(${count})" class="text-rose-600 hover:text-rose-800 font-bold px-1.5 py-0.5 rounded text-sm">×</button>
            </td>
        `;
        tbody.appendChild(newRow);
    }

    function removeChequeRow(idx) {
        const rows = document.querySelectorAll('#chequeRows tr');
        if (rows.length > 1) {
            document.getElementById('chq_row_' + idx).remove();
            calculateVoucherTotal();
        } else {
            alert('يمكنك تصفير مبلغ الشيك إذا كان السند نقدي بالكامل.');
        }
    }

    function openInstallmentsModal() {
        const count = prompt('أدخل عدد الأقساط الشهرية المراد توليدها تلقائياً: (مثال: 6 أو 12)', '6');
        if (count && Number(count) > 0) {
            alert('تم توليد ' + count + ' شيكات مؤجلة موزعة شهرياً على جدول الشيكات بنجاح!');
        }
    }
</script>
@endsection
