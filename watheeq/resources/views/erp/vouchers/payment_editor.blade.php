@extends('layouts.app')

@section('title', 'سند صرف ودفع | وثيق ERP')

@section('content')
<div class="space-y-3 font-sans text-xs" dir="rtl">

    <form method="POST" action="{{ route('erp.vouchers.payment_editor.store') }}" id="paymentVoucherForm">
        @csrf

        <!-- Top Tool Command Ribbon (مطابق لمحطة صرف وثيق) -->
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
                <button type="button" onclick="window.print()" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    طباعة
                </button>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    ترحيل
                </button>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    إلغاء
                </button>
                <a href="{{ route('erp.vouchers.payment_editor') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    جديد
                </a>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    مرفقات
                </a>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 bg-emerald-500 text-white font-bold rounded text-[11px]">
                    جديد
                </span>
                <span class="font-bold text-gray-800 text-sm">
                    سند صرف (+)
                </span>
            </div>
        </div>

        <!-- Master Document Header Meta Box -->
        <div class="bg-[#F8F9FA] p-3 rounded-xl border border-gray-200 shadow-sm space-y-2">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">رقم السند:</span>
                    <input type="text" name="voucher_no" value="VCH-PAY-{{ sprintf('%04d', $nextVoucherNo) }}" readonly class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono font-bold text-gray-800 text-center">
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-16 font-bold text-gray-600 text-left">تاريخه:</span>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono text-gray-800 text-center">
                </div>

                <div class="md:col-span-6 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">مصروف:</span>
                    <div class="flex-1 flex gap-1">
                        <select name="expense_account" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
                            <option value="مصروفات عمومية وإدارية">مصروفات عمومية وإدارية (501)</option>
                            <option value="مصروف إيجار المقر">مصروف إيجار المقر (502)</option>
                            <option value="مصروف كهرباء ومياه">مصروف كهرباء ومياه (503)</option>
                            <option value="مصروف رواتب وأجور">مصروف رواتب وأجور (504)</option>
                            <option value="مصروف شحن ونقل">مصروف شحن ونقل (505)</option>
                            <option value="دفعات للموردين">دفعات للموردين (ذمم دائنة)</option>
                        </select>
                        <button type="button" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 border rounded font-bold text-gray-700">بحث</button>
                        <button type="button" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 border rounded font-bold text-gray-700">+</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 1: نقدي (Cash) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-[#1F2E4A] text-white px-3 py-1.5 font-bold flex justify-between items-center text-xs">
                <span>1. نقدي (Cash Payment)</span>
                <span class="text-[10px] text-gray-300">الصندوق الرئيسي</span>
            </div>
            <div class="p-3 grid grid-cols-1 md:grid-cols-4 gap-3 items-center">
                <div>
                    <label class="block font-bold text-gray-600 mb-1">العملة:</label>
                    <select name="currency" class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-bold">
                        <option value="USD">USD - دولار أمريكي</option>
                        <option value="JOD">JOD - دينار أردني</option>
                        <option value="ILS">ILS - شيكل</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-600 mb-1">المبلغ:</label>
                    <input type="number" step="0.001" name="cash_amount" id="cashAmountInput" value="0.000" oninput="calculateTotals()" class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-mono font-bold text-gray-800 text-center">
                </div>
                <div>
                    <label class="block font-bold text-gray-600 mb-1">سعر الصرف:</label>
                    <input type="number" step="0.0001" name="exchange_rate" id="exchangeRateInput" value="1.0000" oninput="calculateTotals()" class="w-full bg-gray-50 border border-gray-300 rounded px-2 py-1 font-mono text-gray-700 text-center">
                </div>
                <div>
                    <label class="block font-bold text-gray-600 mb-1">بالعملة الرئيسية:</label>
                    <input type="text" id="baseCashAmount" value="0.000" readonly class="w-full bg-gray-100 border border-gray-300 rounded px-2 py-1 font-mono font-bold text-gray-800 text-center">
                </div>
            </div>
        </div>

        <!-- Section 2: بطاقات الائتمان (Credit Cards) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-gray-700 text-white px-3 py-1.5 font-bold flex justify-between items-center text-xs">
                <span>2. بطاقات الائتمان (Credit Cards)</span>
                <span class="text-[10px] text-gray-300">Visa / MasterCard</span>
            </div>
            <div class="p-3 grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block font-bold text-gray-600 mb-1">نوع البطاقة:</label>
                    <input type="text" placeholder="Visa / MasterCard" class="w-full bg-white border border-gray-300 rounded px-2 py-1">
                </div>
                <div>
                    <label class="block font-bold text-gray-600 mb-1">رقم العملية / المرجع:</label>
                    <input type="text" placeholder="Ref-00000" class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-gray-600 mb-1">المبلغ:</label>
                    <input type="number" step="0.001" value="0.000" class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-mono text-center">
                </div>
                <div>
                    <label class="block font-bold text-gray-600 mb-1">تاريخ العملية:</label>
                    <input type="date" value="{{ date('Y-m-d') }}" class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-mono text-center">
                </div>
            </div>
        </div>

        <!-- Section 3: الدفع بالشيكات (Cheques Payment) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-[#1F2E4A] text-white px-3 py-1.5 font-bold flex justify-between items-center text-xs">
                <span>3. الدفع بالشيكات الصادرة (Cheques)</span>
                <button type="button" onclick="addChequeRow()" class="px-2 py-0.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[10px] font-bold">
                    + إضافة شيك
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead class="bg-gray-100 text-gray-700 border-b border-gray-300 text-[11px]">
                        <tr>
                            <th class="p-2 border-l w-8 text-center">#</th>
                            <th class="p-2 border-l">رقم الشيك</th>
                            <th class="p-2 border-l">البنك المسحوب عليه</th>
                            <th class="p-2 border-l">رقم الحساب</th>
                            <th class="p-2 border-l">المبلغ</th>
                            <th class="p-2 border-l">العملة</th>
                            <th class="p-2 border-l">تاريخ الاستحقاق</th>
                            <th class="p-2 border-l">البيان</th>
                            <th class="p-2 text-center w-12">حذف</th>
                        </tr>
                    </thead>
                    <tbody id="chequesTableBody">
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-1.5 border-l text-center font-bold">1</td>
                            <td class="p-1.5 border-l"><input type="text" name="cheques[0][no]" placeholder="رقم الشيك" class="w-full border rounded px-1.5 py-0.5 font-mono"></td>
                            <td class="p-1.5 border-l"><input type="text" name="cheques[0][bank]" placeholder="اسم البنك" class="w-full border rounded px-1.5 py-0.5"></td>
                            <td class="p-1.5 border-l"><input type="text" name="cheques[0][acc]" placeholder="رقم الحساب" class="w-full border rounded px-1.5 py-0.5 font-mono"></td>
                            <td class="p-1.5 border-l"><input type="number" step="0.001" name="cheques[0][amount]" value="0.000" oninput="calculateTotals()" class="w-full border rounded px-1.5 py-0.5 font-mono text-center cheque-amount"></td>
                            <td class="p-1.5 border-l"><span class="font-bold text-gray-600">USD</span></td>
                            <td class="p-1.5 border-l"><input type="date" name="cheques[0][due]" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="w-full border rounded px-1.5 py-0.5 font-mono text-center"></td>
                            <td class="p-1.5 border-l"><input type="text" name="cheques[0][notes]" placeholder="شرح الدفعة" class="w-full border rounded px-1.5 py-0.5"></td>
                            <td class="p-1.5 text-center"><button type="button" onclick="this.closest('tr').remove(); calculateTotals();" class="text-red-500 hover:text-red-700 font-bold">✕</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 4: تجيير الشيكات (Endorsed Cheques) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="bg-gray-700 text-white px-3 py-1.5 font-bold flex justify-between items-center text-xs">
                <span>4. تجيير الشيكات الواردة (Endorsed Cheques Portfolio)</span>
                <span class="text-[10px] text-gray-300">تحويل شيك عميل لمورد</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead class="bg-gray-100 text-gray-700 border-b border-gray-300 text-[11px]">
                        <tr>
                            <th class="p-2 border-l w-8 text-center">#</th>
                            <th class="p-2 border-l">رقم الشيك</th>
                            <th class="p-2 border-l">البنك</th>
                            <th class="p-2 border-l">المبلغ</th>
                            <th class="p-2 border-l">تاريخ الاستحقاق</th>
                            <th class="p-2 text-center w-16">إفراغ الحقول</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-1.5 border-l text-center font-bold">1</td>
                            <td class="p-1.5 border-l"><input type="text" placeholder="اختر من الشيكات الواردة..." class="w-full border rounded px-1.5 py-0.5 font-mono"></td>
                            <td class="p-1.5 border-l"><input type="text" readonly class="w-full bg-gray-50 border rounded px-1.5 py-0.5"></td>
                            <td class="p-1.5 border-l"><input type="text" readonly value="0.000" class="w-full bg-gray-50 border rounded px-1.5 py-0.5 font-mono text-center"></td>
                            <td class="p-1.5 border-l"><input type="text" readonly class="w-full bg-gray-50 border rounded px-1.5 py-0.5 font-mono text-center"></td>
                            <td class="p-1.5 text-center"><button type="button" class="text-xs text-gray-500 hover:text-red-600">مسح</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Master Footer Totals Box -->
        <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-sm grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            <div class="md:col-span-8 flex flex-col gap-1.5">
                <label class="font-bold text-gray-700">ملاحظات السند:</label>
                <textarea name="notes" rows="2" placeholder="أدخل أي ملاحظات إدارية أو محاسبية هنا..." class="w-full border border-gray-300 rounded p-2 text-xs"></textarea>
            </div>
            <div class="md:col-span-4 bg-[#F8F9FA] p-3 rounded-xl border border-gray-200 space-y-2">
                <div class="flex justify-between items-center text-xs">
                    <span class="font-bold text-gray-600">مجموع دولار أمريكي:</span>
                    <span id="grandTotalUsd" class="font-mono font-black text-sm text-[#1F2E4A]">0.000 USD</span>
                </div>
                <div class="flex justify-between items-center text-xs border-t pt-1.5">
                    <span class="font-bold text-gray-800">بالعملة الرئيسية (JOD):</span>
                    <span id="grandTotalBase" class="font-mono font-black text-base text-emerald-600">0.000 JOD</span>
                </div>
            </div>
        </div>

    </form>

</div>

<script>
    function calculateTotals() {
        const cash = parseFloat(document.getElementById('cashAmountInput').value) || 0;
        const rate = parseFloat(document.getElementById('exchangeRateInput').value) || 1;
        const baseCash = cash * rate;
        document.getElementById('baseCashAmount').value = baseCash.toFixed(3);

        let chequeSum = 0;
        document.querySelectorAll('.cheque-amount').forEach(el => {
            chequeSum += parseFloat(el.value) || 0;
        });

        const totalUsd = cash + chequeSum;
        const totalBase = totalUsd * rate;

        document.getElementById('grandTotalUsd').innerText = totalUsd.toFixed(3) + ' USD';
        document.getElementById('grandTotalBase').innerText = totalBase.toFixed(3) + ' JOD';
    }

    let chequeRowIdx = 1;
    function addChequeRow() {
        const tbody = document.getElementById('chequesTableBody');
        const tr = document.createElement('tr');
        tr.className = 'border-b hover:bg-gray-50';
        tr.innerHTML = `
            <td class="p-1.5 border-l text-center font-bold">${tbody.children.length + 1}</td>
            <td class="p-1.5 border-l"><input type="text" name="cheques[${chequeRowIdx}][no]" placeholder="رقم الشيك" class="w-full border rounded px-1.5 py-0.5 font-mono"></td>
            <td class="p-1.5 border-l"><input type="text" name="cheques[${chequeRowIdx}][bank]" placeholder="اسم البنك" class="w-full border rounded px-1.5 py-0.5"></td>
            <td class="p-1.5 border-l"><input type="text" name="cheques[${chequeRowIdx}][acc]" placeholder="رقم الحساب" class="w-full border rounded px-1.5 py-0.5 font-mono"></td>
            <td class="p-1.5 border-l"><input type="number" step="0.001" name="cheques[${chequeRowIdx}][amount]" value="0.000" oninput="calculateTotals()" class="w-full border rounded px-1.5 py-0.5 font-mono text-center cheque-amount"></td>
            <td class="p-1.5 border-l"><span class="font-bold text-gray-600">USD</span></td>
            <td class="p-1.5 border-l"><input type="date" name="cheques[${chequeRowIdx}][due]" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="w-full border rounded px-1.5 py-0.5 font-mono text-center"></td>
            <td class="p-1.5 border-l"><input type="text" name="cheques[${chequeRowIdx}][notes]" placeholder="شرح الدفعة" class="w-full border rounded px-1.5 py-0.5"></td>
            <td class="p-1.5 text-center"><button type="button" onclick="this.closest('tr').remove(); calculateTotals();" class="text-red-500 hover:text-red-700 font-bold">✕</button></td>
        `;
        tbody.appendChild(tr);
        chequeRowIdx++;
    }
</script>
@endsection



