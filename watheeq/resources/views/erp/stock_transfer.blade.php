@extends('layouts.app')

@section('title', 'إرسالية داخلية ومناقلة بين المخازن | وثيق ERP')

@section('content')
<div class="space-y-3 font-sans text-xs" dir="rtl">

    <form method="POST" action="{{ route('erp.transfers.store') }}" id="watheeqTransferForm">
        @csrf

        <!-- Top Tool Command Ribbon (مطابق لحساباتي Frame 6_1i) -->
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
                <a href="{{ route('erp.transfers') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    جديد
                </a>
                <button type="button" onclick="window.location.reload()" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded font-bold border border-rose-200 transition">
                    إلغاء
                </button>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    مرفقات
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
                    إرسالية مناقلة داخلية بين المخازن
                </span>
            </div>
        </div>

        <!-- Master Meta Box -->
        <div class="bg-[#F8F9FA] p-3.5 rounded-xl border border-gray-200 shadow-sm space-y-2.5">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-24 font-bold text-gray-600 text-left">رقم السند:</span>
                    <input type="text" name="transfer_no" value="TRF-{{ date('Ymd') }}-{{ sprintf('%03d', $nextTransferNo ?? 1) }}" readonly class="flex-1 bg-white border border-gray-300 rounded px-2.5 py-1 font-mono font-bold text-gray-800">
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-16 font-bold text-gray-600 text-left">تاريخه:</span>
                    <input type="date" name="transfer_date" value="{{ date('Y-m-d') }}" required class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono text-gray-800">
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">من مخزن:</span>
                    <select name="from_warehouse" required class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
                        <option value="المستودع الرئيسي - عمان">المستودع الرئيسي - عمان</option>
                        <option value="مستودع فرع إربد">مستودع فرع إربد</option>
                    </select>
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">إلى مخزن:</span>
                    <select name="to_warehouse" required class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
                        <option value="مستودع المعرض / صالة العرض">مستودع المعرض / صالة العرض</option>
                        <option value="مستودع فرع الزرقاء">مستودع فرع الزرقاء</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-4 flex items-center gap-2">
                    <span class="w-24 font-bold text-gray-600 text-left">مركز التكلفة:</span>
                    <select name="cost_center_id" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1">
                        <option value="">بدون مركز تكلفة محدد</option>
                        @foreach($costCenters as $cc)
                            <option value="{{ $cc->id }}">{{ $cc->name }} ({{ $cc->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-4 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">سند يدوي:</span>
                    <input type="text" name="manual_ref" placeholder="رقم السند اليدوي إن وجد..." class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono">
                </div>

                <div class="md:col-span-4 flex items-center gap-2">
                    <span class="w-24 font-bold text-gray-600 text-left">سائق النقل:</span>
                    <input type="text" placeholder="اسم السائق أو مندوب التحويل..." class="flex-1 bg-white border border-gray-300 rounded px-2 py-1">
                </div>
            </div>
        </div>

        <!-- Master Items Table -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs" id="transferTable">
                    <thead class="bg-[#1F2E4A] text-white font-bold text-[11px]">
                        <tr>
                            <th class="py-2.5 px-2 text-center w-8">#</th>
                            <th class="py-2.5 px-2 w-32">رقم المادة</th>
                            <th class="py-2.5 px-3 min-w-[220px]">اسم الصنف / المادة المنقولة</th>
                            <th class="py-2.5 px-2 w-28">الوحدة</th>
                            <th class="py-2.5 px-2 w-24 text-center">الكمية المنقولة</th>
                            <th class="py-2.5 px-2 w-28 text-center font-mono">تاريخ الصلاحية</th>
                            <th class="py-2.5 px-3 min-w-[150px]">ملاحظات المادة</th>
                            <th class="py-2.5 px-2 text-center w-10">حذف</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 font-medium" id="transferRows">
                        <tr class="hover:bg-gray-50/80 transition" id="trf_row_0">
                            <td class="py-1.5 px-2 text-center font-mono text-gray-400">1</td>
                            <td class="py-1.5 px-2">
                                <input type="text" name="items[0][code]" placeholder="رقم المادة" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 font-mono text-[11px]">
                            </td>
                            <td class="py-1.5 px-2">
                                <input type="text" name="items[0][name]" placeholder="اسم الصنف" required class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
                            </td>
                            <td class="py-1.5 px-2">
                                <select name="items[0][unit]" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-[11px]">
                                    <option value="قطعة">قطعة</option>
                                    <option value="صندوق">صندوق</option>
                                    <option value="كيلو">كيلو</option>
                                    <option value="متر">متر</option>
                                </select>
                            </td>
                            <td class="py-1.5 px-2">
                                <input type="number" step="1" name="items[0][qty]" value="1" min="1" required class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-center font-mono font-bold">
                            </td>
                            <td class="py-1.5 px-2">
                                <input type="date" name="items[0][expiry_date]" value="2030-01-01" class="w-full bg-white border border-gray-300 rounded px-1 py-1 font-mono text-[10px]">
                            </td>
                            <td class="py-1.5 px-2">
                                <input type="text" name="items[0][notes]" placeholder="ملاحظات..." class="w-full bg-white border border-gray-300 rounded px-2 py-1 text-[11px]">
                            </td>
                            <td class="py-1.5 px-2 text-center">
                                <button type="button" onclick="removeTransferRow(0)" class="text-rose-600 hover:text-rose-800 font-bold px-1.5 py-0.5 rounded text-sm">×</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-2 border-t border-gray-200 bg-gray-50 flex justify-between items-center no-print">
                <button type="button" onclick="addTransferRow()" class="px-3 py-1 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded text-xs font-bold transition">
                    + إضافة مادة للمناقلة
                </button>
                <span class="text-xs text-gray-500 font-mono">عدد البنود المنقولة: <span id="transferCount" class="font-bold text-gray-800">1</span></span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm space-y-2">
            <label class="block font-bold text-gray-700">بيان وأسباب المناقلة:</label>
            <textarea name="notes" rows="2" placeholder="وذلك لتزويد فرع المعرض بالبضائع المطلوبة..." class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2 text-xs"></textarea>
        </div>

    </form>

</div>

<script>
    function addTransferRow() {
        const tbody = document.getElementById('transferRows');
        const count = tbody.querySelectorAll('tr').length;
        const newRow = document.createElement('tr');
        newRow.className = "hover:bg-gray-50/80 transition";
        newRow.id = "trf_row_" + count;
        newRow.innerHTML = `
            <td class="py-1.5 px-2 text-center font-mono text-gray-400">${count + 1}</td>
            <td class="py-1.5 px-2">
                <input type="text" name="items[${count}][code]" placeholder="رقم المادة" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 font-mono text-[11px]">
            </td>
            <td class="py-1.5 px-2">
                <input type="text" name="items[${count}][name]" placeholder="اسم الصنف" required class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
            </td>
            <td class="py-1.5 px-2">
                <select name="items[${count}][unit]" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-[11px]">
                    <option value="قطعة">قطعة</option>
                    <option value="صندوق">صندوق</option>
                    <option value="كيلو">كيلو</option>
                </select>
            </td>
            <td class="py-1.5 px-2">
                <input type="number" step="1" name="items[${count}][qty]" value="1" min="1" required class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-center font-mono font-bold">
            </td>
            <td class="py-1.5 px-2">
                <input type="date" name="items[${count}][expiry_date]" value="2030-01-01" class="w-full bg-white border border-gray-300 rounded px-1 py-1 font-mono text-[10px]">
            </td>
            <td class="py-1.5 px-2">
                <input type="text" name="items[${count}][notes]" placeholder="ملاحظات..." class="w-full bg-white border border-gray-300 rounded px-2 py-1 text-[11px]">
            </td>
            <td class="py-1.5 px-2 text-center">
                <button type="button" onclick="removeTransferRow(${count})" class="text-rose-600 hover:text-rose-800 font-bold px-1.5 py-0.5 rounded text-sm">×</button>
            </td>
        `;
        tbody.appendChild(newRow);
        document.getElementById('transferCount').innerText = tbody.querySelectorAll('tr').length;
    }

    function removeTransferRow(idx) {
        const rows = document.querySelectorAll('#transferRows tr');
        if (rows.length > 1) {
            document.getElementById('trf_row_' + idx).remove();
            document.getElementById('transferCount').innerText = document.querySelectorAll('#transferRows tr').length;
        } else {
            alert('يجب الإبقاء على مادة واحدة على الأقل في الإرسالية.');
        }
    }
</script>
@endsection
