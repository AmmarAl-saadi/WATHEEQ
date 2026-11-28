@extends('layouts.app')

@section('title', 'سند إنتاج وتصنيع | وثيق ERP')

@section('content')
<div class="space-y-3 font-sans text-xs" dir="rtl">

    <form method="POST" action="{{ route('erp.manufacturing.store') }}" id="watheeqProductionForm">
        @csrf

        <!-- Top Tool Command Ribbon (مطابق لسند الإنتاج والتصنيع Frame 3_14_1i) -->
        <div class="bg-white p-2 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-2 no-print">
            <div class="flex items-center gap-1.5 flex-wrap">
                <button type="button" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border font-mono">|&lt;&lt;</button>
                <button type="button" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border font-mono">&lt;&lt;</button>
                <button type="button" class="px-3 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-800 rounded font-bold border border-sky-200">استعلام</button>
                <button type="button" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border font-mono">&gt;&gt;</button>
                <button type="button" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border font-mono">&gt;&gt;|</button>

                <div class="h-6 w-px bg-gray-300 mx-1"></div>

                <button type="submit" class="px-4 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
                    حفظ وترحيل
                </button>
                <button type="button" onclick="window.print()" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    طباعة
                </button>
                <a href="{{ route('erp.manufacturing') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    جديد
                </a>
                <button type="button" onclick="window.location.reload()" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded font-bold border border-rose-200 transition">
                    إلغاء
                </button>
                <button type="button" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded font-bold transition">
                    إكسل
                </button>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 bg-emerald-500 text-white font-bold rounded text-[11px] animate-pulse">
                    جديد
                </span>
                <span class="font-bold text-gray-800 text-sm">
                    سند إنتاج وتصنيع المواد
                </span>
            </div>
        </div>

        <!-- Master Document Header Meta Box -->
        <div class="bg-[#F8F9FA] p-3.5 rounded-xl border border-gray-200 shadow-sm space-y-2.5">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-24 font-bold text-gray-600 text-left">رقم السند:</span>
                    <input type="text" name="voucher_no" value="MFG-{{ date('Ymd') }}-{{ sprintf('%03d', $nextMfgNo ?? 1) }}" readonly class="flex-1 bg-white border border-gray-300 rounded px-2.5 py-1 font-mono font-bold text-gray-800">
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-16 font-bold text-gray-600 text-left">تاريخه:</span>
                    <input type="date" name="voucher_date" value="{{ date('Y-m-d') }}" required class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono text-gray-800">
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">مخزن التوريد:</span>
                    <select name="target_warehouse" required class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
                        <option value="المستودع الرئيسي - عمان">المستودع الرئيسي - عمان</option>
                        <option value="مستودع الإنتاج التام">مستودع الإنتاج التام</option>
                    </select>
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">مركز التكلفة:</span>
                    <select name="cost_center_id" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1">
                        <option value="">خط الإنتاج العام</option>
                        @foreach($costCenters as $cc)
                            <option value="{{ $cc->id }}">{{ $cc->name }} ({{ $cc->code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Section 1: المادة المصنعة الناتجة (Output Finished Good) -->
        <div class="bg-emerald-50/60 p-3.5 rounded-xl border border-emerald-200 space-y-2.5">
            <span class="font-black text-xs text-emerald-950 flex items-center gap-1.5">
                <span>🏭</span> المادة المصنعة الناتجة (التي سيتم إيداعها بالمخزن كمنتج تام):
            </span>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-4 flex items-center gap-2">
                    <span class="w-20 font-bold text-emerald-900 text-left">اسم المادة:</span>
                    <input type="text" name="output_product_name" placeholder="مثال: كرتونة منظف معبأ 1 لتر" required class="flex-1 bg-white border border-emerald-300 rounded px-2 py-1 font-bold text-gray-800">
                </div>
                <div class="md:col-span-2 flex items-center gap-2">
                    <span class="w-16 font-bold text-emerald-900 text-left">الكمية:</span>
                    <input type="number" step="1" name="output_quantity" id="outputQty" value="100" oninput="calculateProductionCost()" min="1" required class="flex-1 bg-white border border-emerald-300 rounded px-2 py-1 text-center font-mono font-black text-emerald-800">
                </div>
                <div class="md:col-span-2 flex items-center gap-2">
                    <span class="w-16 font-bold text-emerald-900 text-left">الوحدة:</span>
                    <select name="output_unit" class="flex-1 bg-white border border-emerald-300 rounded px-1.5 py-1 text-xs">
                        <option value="صندوق">صندوق</option>
                        <option value="كرتونة">كرتونة</option>
                        <option value="قطعة">قطعة</option>
                    </select>
                </div>
                <div class="md:col-span-2 flex items-center gap-2">
                    <span class="font-bold text-emerald-900">الصلاحية:</span>
                    <input type="date" name="output_expiry" value="2030-01-01" class="flex-1 bg-white border border-emerald-300 rounded px-1 py-1 font-mono text-[10px]">
                </div>
                <div class="md:col-span-2 flex items-center gap-2">
                    <span class="font-bold text-emerald-900">Batch:</span>
                    <input type="text" name="output_batch" placeholder="BATCH-2026-A" class="flex-1 bg-white border border-emerald-300 rounded px-1.5 py-1 font-mono text-[11px]">
                </div>
            </div>
        </div>

        <!-- Section 2: المواد الأولية المستهلكة (Raw Materials) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden space-y-2 p-3">
            <div class="flex items-center justify-between border-b pb-2">
                <span class="font-black text-xs text-[#1F2E4A] flex items-center gap-1.5">
                    <span>📦</span> المواد الخام والمكونات المستهلكة (التي سيتم خصمها من المخازن):
                </span>
                <span class="text-[11px] text-gray-500 font-mono">خصم مخزني فوري</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs" id="rawMaterialsTable">
                    <thead class="bg-[#1F2E4A] text-white font-bold text-[11px]">
                        <tr>
                            <th class="py-2 px-2 text-center w-8">#</th>
                            <th class="py-2 px-3 min-w-[200px]">المادة الأولية / الخام</th>
                            <th class="py-2 px-2 w-36">المخزن المسحوب منه</th>
                            <th class="py-2 px-2 w-24">الوحدة</th>
                            <th class="py-2 px-2 w-24 text-center">الكمية المستهلكة</th>
                            <th class="py-2 px-2 w-24 font-mono text-left">تكلفة الوحدة</th>
                            <th class="py-2 px-3 w-28 font-mono text-left">إجمالي التكلفة</th>
                            <th class="py-2 px-2 text-center w-10">حذف</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 font-medium" id="rawMaterialRows">
                        <tr class="hover:bg-gray-50/80 transition" id="raw_0">
                            <td class="py-1.5 px-2 text-center font-mono text-gray-400">1</td>
                            <td class="py-1.5 px-2">
                                <input type="text" name="raw[0][name]" value="مادة خام بلاستيكية أولية" required class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
                            </td>
                            <td class="py-1.5 px-2">
                                <select name="raw[0][warehouse]" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-[11px]">
                                    <option>مستودع المواد الخام</option>
                                    <option>المستودع الرئيسي</option>
                                </select>
                            </td>
                            <td class="py-1.5 px-2">
                                <select name="raw[0][unit]" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-[11px]">
                                    <option value="كيلو">كيلو</option>
                                    <option value="قطعة">قطعة</option>
                                    <option value="لتر">لتر</option>
                                </select>
                            </td>
                            <td class="py-1.5 px-2">
                                <input type="number" step="0.1" name="raw[0][qty]" value="50" oninput="calculateProductionCost()" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-center font-mono font-bold">
                            </td>
                            <td class="py-1.5 px-2">
                                <input type="number" step="0.01" name="raw[0][price]" value="2.50" oninput="calculateProductionCost()" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-left font-mono font-bold">
                            </td>
                            <td class="py-1.5 px-3 text-left font-mono font-bold text-gray-800" id="raw_total_0">
                                125.00
                            </td>
                            <td class="py-1.5 px-2 text-center">
                                <button type="button" onclick="removeRawRow(0)" class="text-rose-600 hover:text-rose-800 font-bold px-1.5 py-0.5 rounded text-sm">×</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-2 border-t border-gray-200 bg-gray-50 flex justify-between items-center no-print">
                <button type="button" onclick="addRawRow()" class="px-3 py-1 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded text-xs font-bold transition">
                    + إضافة مادة خام مستهلكة
                </button>
                <span class="text-xs text-gray-500 font-mono">مجموع تكلفة المواد الأولية: <span id="dispRawTotal" class="font-bold text-[#1F2E4A]">125.00 JOD</span></span>
            </div>
        </div>

        <!-- Section 3: مصاريف التشغيل والعمالة الإضافية (Direct Expenses) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-3.5 space-y-2.5">
            <div class="flex items-center justify-between border-b pb-2">
                <span class="font-black text-xs text-[#1F2E4A] flex items-center gap-1.5">
                    <span>⚡</span> مصاريف التشغيل، الكهرباء، وأجور العمالة المحملة على الإنتاج:
                </span>
                <span class="text-[11px] text-gray-500">حسابات التكاليف المباشرة</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                <div class="md:col-span-4 flex items-center gap-2">
                    <span class="w-28 font-bold text-gray-600 text-left">أجور عمالة الإنتاج:</span>
                    <input type="number" step="0.01" id="laborExpense" value="25.00" oninput="calculateProductionCost()" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono font-bold text-left">
                </div>
                <div class="md:col-span-4 flex items-center gap-2">
                    <span class="w-28 font-bold text-gray-600 text-left">طاقة وكهرباء تصنيع:</span>
                    <input type="number" step="0.01" id="energyExpense" value="10.00" oninput="calculateProductionCost()" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono font-bold text-left">
                </div>
                <div class="md:col-span-4 flex items-center gap-2">
                    <span class="w-28 font-bold text-gray-600 text-left">تغليف ومصاريف أخرى:</span>
                    <input type="number" step="0.01" id="otherExpense" value="15.00" oninput="calculateProductionCost()" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono font-bold text-left">
                </div>
            </div>
        </div>

        <!-- Master Totals Summary Box -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                <div class="md:col-span-6 space-y-1">
                    <label class="block font-bold text-gray-700">ملاحظات خط الإنتاج:</label>
                    <textarea name="notes" rows="2" placeholder="أمر تشغيل رقم ORD-092 تم بنجاح بدون هدر..." class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2 text-xs"></textarea>
                </div>

                <div class="md:col-span-6 bg-[#F8F9FA] p-3.5 rounded-xl border border-gray-200 space-y-2">
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold text-gray-600">إجمالي تكلفة أمر الإنتاج:</span>
                        <input type="hidden" name="total_cost" id="inputTotalCost" value="0.00">
                        <span class="font-mono font-bold text-emerald-700 text-base" id="dispTotalProductionCost">0.00 JOD</span>
                    </div>
                    <div class="border-t border-gray-300 pt-1.5 flex justify-between items-center text-sm">
                        <span class="font-black text-gray-900">تكلفة الوحدة الواحدة المصنعة:</span>
                        <input type="hidden" name="unit_cost" id="inputUnitCost" value="0.00">
                        <span class="font-mono font-black text-lg text-[#1F2E4A] bg-[#1F2E4A]/10 px-3 py-0.5 rounded-lg" id="dispUnitCost">
                            0.00 JOD / وحدة
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </form>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        calculateProductionCost();
    });

    function calculateProductionCost() {
        let rawTotal = 0;
        const rows = document.querySelectorAll('#rawMaterialRows tr');
        rows.forEach(function (row, idx) {
            const qty = parseFloat(row.querySelector('input[name*="[qty]"]').value) || 0;
            const price = parseFloat(row.querySelector('input[name*="[price]"]').value) || 0;
            const rowTotal = qty * price;
            rawTotal += rowTotal;
            const totalCell = row.querySelector('td[id^="raw_total_"]');
            if (totalCell) {
                totalCell.innerText = rowTotal.toFixed(2);
            }
        });

        const labor = parseFloat(document.getElementById('laborExpense').value) || 0;
        const energy = parseFloat(document.getElementById('energyExpense').value) || 0;
        const other = parseFloat(document.getElementById('otherExpense').value) || 0;
        const totalExpenses = labor + energy + other;

        const grandCost = rawTotal + totalExpenses;
        const outputQty = parseFloat(document.getElementById('outputQty').value) || 1;
        const unitCost = grandCost / outputQty;

        document.getElementById('dispRawTotal').innerText = rawTotal.toFixed(2) + ' JOD';
        document.getElementById('dispTotalProductionCost').innerText = grandCost.toFixed(2) + ' JOD';
        document.getElementById('inputTotalCost').value = grandCost.toFixed(2);

        document.getElementById('dispUnitCost').innerText = unitCost.toFixed(2) + ' JOD / وحدة';
        document.getElementById('inputUnitCost').value = unitCost.toFixed(2);
    }

    function addRawRow() {
        const tbody = document.getElementById('rawMaterialRows');
        const count = tbody.querySelectorAll('tr').length;
        const newRow = document.createElement('tr');
        newRow.className = "hover:bg-gray-50/80 transition";
        newRow.id = "raw_" + count;
        newRow.innerHTML = `
            <td class="py-1.5 px-2 text-center font-mono text-gray-400">${count + 1}</td>
            <td class="py-1.5 px-2">
                <input type="text" name="raw[${count}][name]" placeholder="اسم المادة الخام" required class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
            </td>
            <td class="py-1.5 px-2">
                <select name="raw[${count}][warehouse]" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-[11px]">
                    <option>مستودع المواد الخام</option>
                    <option>المستودع الرئيسي</option>
                </select>
            </td>
            <td class="py-1.5 px-2">
                <select name="raw[${count}][unit]" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-[11px]">
                    <option value="كيلو">كيلو</option>
                    <option value="قطعة">قطعة</option>
                    <option value="لتر">لتر</option>
                </select>
            </td>
            <td class="py-1.5 px-2">
                <input type="number" step="0.1" name="raw[${count}][qty]" value="10" oninput="calculateProductionCost()" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-center font-mono font-bold">
            </td>
            <td class="py-1.5 px-2">
                <input type="number" step="0.01" name="raw[${count}][price]" value="1.00" oninput="calculateProductionCost()" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-left font-mono font-bold">
            </td>
            <td class="py-1.5 px-3 text-left font-mono font-bold text-gray-800" id="raw_total_${count}">
                10.00
            </td>
            <td class="py-1.5 px-2 text-center">
                <button type="button" onclick="removeRawRow(${count})" class="text-rose-600 hover:text-rose-800 font-bold px-1.5 py-0.5 rounded text-sm">×</button>
            </td>
        `;
        tbody.appendChild(newRow);
        calculateProductionCost();
    }

    function removeRawRow(idx) {
        const rows = document.querySelectorAll('#rawMaterialRows tr');
        if (rows.length > 1) {
            document.getElementById('raw_' + idx).remove();
            calculateProductionCost();
        } else {
            alert('يجب الإبقاء على مادة خام واحدة على الأقل في سند التصنيع.');
        }
    }
</script>
@endsection
