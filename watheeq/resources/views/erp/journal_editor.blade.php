@extends('layouts.app')

@section('title', 'سند قيد محاسبي مزدوج | وثيق ERP')

@section('content')
<div class="space-y-3 font-sans text-xs" dir="rtl">

    <form method="POST" action="{{ route('erp.journals.store') }}" id="watheeqJournalForm">
        @csrf

        <!-- Top Tool Command Ribbon -->
        <div class="bg-white p-2 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-2 no-print">
            <div class="flex items-center gap-1.5 flex-wrap">
                <button type="submit" id="btnSubmitJournal" class="px-4 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
                    ترحيل واعتماد القيد
                </button>
                <button type="button" onclick="window.print()" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    طباعة السند
                </button>
                <a href="{{ route('erp.journals.editor') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    جديد
                </a>
                <a href="{{ route('erp.accounts') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    دفتر القيود اليومية
                </a>
            </div>

            <div class="flex items-center gap-3">
                <span id="balanceBadge" class="px-2.5 py-1 bg-rose-500 text-white font-bold rounded text-[11px]">
                    غير متوازن
                </span>
                <span class="font-bold text-gray-700 text-sm">
                    سند قيد محاسبي مزدوج (Journal Entry)
                </span>
            </div>
        </div>

        <!-- Master Document Header Meta Box -->
        <div class="bg-[#F8F9FA] p-3.5 rounded-xl border border-gray-200 shadow-sm space-y-2.5">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-24 font-bold text-gray-600 text-left">رقم السند:</span>
                    <input type="text" name="entry_no" value="JV-{{ date('Y') }}-{{ sprintf('%04d', $nextEntryNo) }}" readonly class="flex-1 bg-white border border-gray-300 rounded px-2.5 py-1 font-mono font-bold text-gray-800">
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-16 font-bold text-gray-600 text-left">تاريخه:</span>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono text-gray-800">
                </div>

                <div class="md:col-span-6 flex items-center gap-2">
                    <span class="w-24 font-bold text-gray-600 text-left">البيان والشرح العام:</span>
                    <input type="text" name="narration" required placeholder="مثال: إثبات إيجار المقر، تسوية الرواتب، قيد تسوية بنكية..." class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-medium text-gray-800">
                </div>
            </div>
        </div>

        <!-- Master Lines Table -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs" id="jvTable">
                    <thead class="bg-[#1F2E4A] text-white font-bold text-[11px]">
                        <tr>
                            <th class="py-2.5 px-2 text-center w-8">#</th>
                            <th class="py-2.5 px-3 min-w-[220px]">اسم الحساب في الدليل</th>
                            <th class="py-2.5 px-3 min-w-[200px]">البيان / الشرح التفصيلي للسطر</th>
                            <th class="py-2.5 px-3 w-44">مركز التكلفة (المشروع)</th>
                            <th class="py-2.5 px-3 w-32 text-left font-mono">مدين (Debit)</th>
                            <th class="py-2.5 px-3 w-32 text-left font-mono">دائن (Credit)</th>
                            <th class="py-2.5 px-2 text-center w-10">حذف</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 font-medium" id="jvRows">
                        <!-- Line 1: Debit -->
                        <tr class="hover:bg-gray-50/80 transition" id="row_0">
                            <td class="py-2 px-2 text-center font-mono text-gray-400">1</td>
                            <td class="py-2 px-2">
                                <select name="lines[0][account_id]" required class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}" {{ $acc->code === '501' ? 'selected' : '' }}>[{{ $acc->code }}] {{ $acc->name }} ({{ $acc->type }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="py-2 px-2">
                                <input type="text" name="lines[0][description]" value="إثبات مصاريف تشغيلية" placeholder="شرح السطر..." class="w-full bg-white border border-gray-300 rounded px-2 py-1">
                            </td>
                            <td class="py-2 px-2">
                                <select name="lines[0][cost_center_id]" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-[11px]">
                                    <option value="">بدون مركز تكلفة</option>
                                    @foreach($costCenters as $cc)
                                        <option value="{{ $cc->id }}">{{ $cc->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="py-2 px-2">
                                <input type="number" step="0.01" name="lines[0][debit]" id="debit_0" value="500.00" min="0" oninput="calculateJvTotals()" required class="w-full bg-white border border-gray-300 rounded px-2 py-1 text-left font-mono font-bold text-emerald-800">
                            </td>
                            <td class="py-2 px-2">
                                <input type="number" step="0.01" name="lines[0][credit]" id="credit_0" value="0.00" min="0" oninput="calculateJvTotals()" required class="w-full bg-white border border-gray-300 rounded px-2 py-1 text-left font-mono text-gray-400">
                            </td>
                            <td class="py-2 px-2 text-center">
                                <button type="button" onclick="removeJvRow(0)" class="text-rose-600 hover:text-rose-800 font-bold px-1.5 py-0.5 rounded">×</button>
                            </td>
                        </tr>

                        <!-- Line 2: Credit -->
                        <tr class="hover:bg-gray-50/80 transition" id="row_1">
                            <td class="py-2 px-2 text-center font-mono text-gray-400">2</td>
                            <td class="py-2 px-2">
                                <select name="lines[1][account_id]" required class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}" {{ $acc->code === '101' ? 'selected' : '' }}>[{{ $acc->code }}] {{ $acc->name }} ({{ $acc->type }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="py-2 px-2">
                                <input type="text" name="lines[1][description]" value="صرف نقد من الصندوق الرئيسي" placeholder="شرح السطر..." class="w-full bg-white border border-gray-300 rounded px-2 py-1">
                            </td>
                            <td class="py-2 px-2">
                                <select name="lines[1][cost_center_id]" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-[11px]">
                                    <option value="">بدون مركز تكلفة</option>
                                    @foreach($costCenters as $cc)
                                        <option value="{{ $cc->id }}">{{ $cc->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="py-2 px-2">
                                <input type="number" step="0.01" name="lines[1][debit]" id="debit_1" value="0.00" min="0" oninput="calculateJvTotals()" required class="w-full bg-white border border-gray-300 rounded px-2 py-1 text-left font-mono text-gray-400">
                            </td>
                            <td class="py-2 px-2">
                                <input type="number" step="0.01" name="lines[1][credit]" id="credit_1" value="500.00" min="0" oninput="calculateJvTotals()" required class="w-full bg-white border border-gray-300 rounded px-2 py-1 text-left font-mono font-bold text-rose-800">
                            </td>
                            <td class="py-2 px-2 text-center">
                                <button type="button" onclick="removeJvRow(1)" class="text-rose-600 hover:text-rose-800 font-bold px-1.5 py-0.5 rounded">×</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-2 border-t border-gray-200 bg-gray-50 flex justify-between items-center no-print">
                <button type="button" onclick="addJvRow()" class="px-3 py-1 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded text-xs font-bold transition">
                    + إضافة طرف قيد جديد
                </button>
                <span class="text-xs text-gray-500 font-mono">عدد الأطراف: <span id="jvRowsCount" class="font-bold text-gray-800">2</span></span>
            </div>
        </div>

        <!-- Master Footer Totals Box -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs font-mono">
                <div class="bg-gray-50 p-3 rounded-xl border flex justify-between items-center">
                    <span class="font-bold text-gray-600">إجمالي المدين (Debit):</span>
                    <span class="font-black text-base text-emerald-800" id="totalDebitDisp">500.00 {{ $tenant->currency ?? 'JOD' }}</span>
                </div>
                <div class="bg-gray-50 p-3 rounded-xl border flex justify-between items-center">
                    <span class="font-bold text-gray-600">إجمالي الدائن (Credit):</span>
                    <span class="font-black text-base text-rose-800" id="totalCreditDisp">500.00 {{ $tenant->currency ?? 'JOD' }}</span>
                </div>
                <div class="bg-[#1F2E4A]/10 p-3 rounded-xl border border-[#1F2E4A]/30 flex justify-between items-center">
                    <span class="font-bold text-[#1F2E4A]">الفارق والتوازن (Diff):</span>
                    <span class="font-black text-base text-[#1F2E4A]" id="diffDisp">0.00 (متوازن تماماً)</span>
                </div>
            </div>
        </div>

    </form>

</div>

<script>
    let jvIndex = 2;
    const accountsData = @json($accounts);
    const costCentersData = @json($costCenters);

    function addJvRow() {
        const tbody = document.getElementById('jvRows');
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50/80 transition';
        tr.id = `row_${jvIndex}`;

        let accOptions = '';
        accountsData.forEach(a => {
            accOptions += `<option value="${a.id}">[${a.code}] ${a.name}</option>`;
        });

        let ccOptions = '<option value="">بدون مركز تكلفة</option>';
        costCentersData.forEach(c => {
            ccOptions += `<option value="${c.id}">${c.name}</option>`;
        });

        tr.innerHTML = `
            <td class="py-2 px-2 text-center font-mono text-gray-400">${jvIndex + 1}</td>
            <td class="py-2 px-2">
                <select name="lines[${jvIndex}][account_id]" required class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
                    ${accOptions}
                </select>
            </td>
            <td class="py-2 px-2">
                <input type="text" name="lines[${jvIndex}][description]" placeholder="شرح السطر..." class="w-full bg-white border border-gray-300 rounded px-2 py-1">
            </td>
            <td class="py-2 px-2">
                <select name="lines[${jvIndex}][cost_center_id]" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-[11px]">
                    ${ccOptions}
                </select>
            </td>
            <td class="py-2 px-2">
                <input type="number" step="0.01" name="lines[${jvIndex}][debit]" id="debit_${jvIndex}" value="0.00" min="0" oninput="calculateJvTotals()" required class="w-full bg-white border border-gray-300 rounded px-2 py-1 text-left font-mono font-bold text-emerald-800">
            </td>
            <td class="py-2 px-2">
                <input type="number" step="0.01" name="lines[${jvIndex}][credit]" id="credit_${jvIndex}" value="0.00" min="0" oninput="calculateJvTotals()" required class="w-full bg-white border border-gray-300 rounded px-2 py-1 text-left font-mono font-bold text-rose-800">
            </td>
            <td class="py-2 px-2 text-center">
                <button type="button" onclick="removeJvRow(${jvIndex})" class="text-rose-600 hover:text-rose-800 font-bold px-1.5 py-0.5 rounded">×</button>
            </td>
        `;

        tbody.appendChild(tr);
        jvIndex++;
        calculateJvTotals();
    }

    function removeJvRow(idx) {
        const row = document.getElementById(`row_${idx}`);
        if (row) {
            row.remove();
            calculateJvTotals();
        }
    }

    function calculateJvTotals() {
        let totalDebit = 0;
        let totalCredit = 0;
        const rows = document.querySelectorAll('#jvRows tr');
        document.getElementById('jvRowsCount').innerText = rows.length;

        rows.forEach(tr => {
            const id = tr.id.split('_')[1];
            const d = parseFloat(document.getElementById(`debit_${id}`)?.value || 0) || 0;
            const c = parseFloat(document.getElementById(`credit_${id}`)?.value || 0) || 0;
            totalDebit += d;
            totalCredit += c;
        });

        const currency = "{{ $tenant->currency ?? 'JOD' }}";
        document.getElementById('totalDebitDisp').innerText = `${totalDebit.toFixed(2)} ${currency}`;
        document.getElementById('totalCreditDisp').innerText = `${totalCredit.toFixed(2)} ${currency}`;

        const diff = Math.abs(totalDebit - totalCredit);
        const isBalanced = diff < 0.001 && totalDebit > 0;

        const badge = document.getElementById('balanceBadge');
        const submitBtn = document.getElementById('btnSubmitJournal');
        const diffDisp = document.getElementById('diffDisp');

        if (isBalanced) {
            badge.innerText = 'متوازن وجاهز للترحيل';
            badge.className = 'px-2.5 py-1 bg-emerald-600 text-white font-bold rounded text-[11px]';
            diffDisp.innerText = '0.00 (متوازن تماماً)';
            diffDisp.className = 'font-black text-base text-emerald-800';
            submitBtn.disabled = false;
            submitBtn.className = 'px-4 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition';
        } else {
            badge.innerText = `غير متوازن (فارق: ${diff.toFixed(2)})`;
            badge.className = 'px-2.5 py-1 bg-rose-600 text-white font-bold rounded text-[11px]';
            diffDisp.innerText = `فارق غير متوازن: ${diff.toFixed(2)} ${currency}`;
            diffDisp.className = 'font-black text-base text-rose-700';
            submitBtn.disabled = true;
            submitBtn.className = 'px-4 py-1.5 bg-gray-400 cursor-not-allowed text-white rounded font-bold shadow-sm';
        }
    }

    document.addEventListener('DOMContentLoaded', calculateJvTotals);
</script>
@endsection



