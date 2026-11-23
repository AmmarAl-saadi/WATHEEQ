@extends('layouts.app')

@section('title', 'شجرة الحسابات وسندات القيد المحاسبية | وثيق ERP')

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm no-print">
        <div>
            <h2 class="text-xl font-bold text-[#1F2E4A]">دليل شجرة الحسابات وسندات القيد (General Ledger & Journal Entries)</h2>
            <p class="text-xs text-gray-500">نظام القيد المزدوج، مراكز التكلفة، وأرصدة الحسابات المالية المعتمدة</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="document.getElementById('newJournalModal').classList.remove('hidden')" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5 whitespace-nowrap">
                <span></span> تحرير سند قيد يدوي
            </button>
            <button onclick="document.getElementById('newAccountModal').classList.remove('hidden')" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#243b2a] text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5 whitespace-nowrap">
                <span>+</span> إضافة حساب فرعي
            </button>
        </div>
    </div>

    <!-- Ledger Overview Tabs -->
    <div class="flex gap-2 border-b border-gray-200 pb-2 no-print">
        <button type="button" onclick="switchAccountView('chart')" id="btnTabChart" class="px-4 py-1.5 text-xs font-bold rounded-lg bg-[#1F2E4A] text-white transition">
            دليل شجرة الحسابات (COA)
        </button>
        <button type="button" onclick="switchAccountView('journal')" id="btnTabJournal" class="px-4 py-1.5 text-xs font-bold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition flex items-center gap-1">
            سندات القيد المحاسبية اليومية (Journal Entries)
            <span class="px-1.5 py-0.2 bg-[#1F2E4A] text-white rounded-full text-[10px]">{{ count($journalEntries ?? []) }}</span>
        </button>
    </div>

    <!-- Section 1: Chart of Accounts View -->
    <div id="chartView" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-[#1F2E4A] text-white font-bold">
                    <tr>
                        <th class="py-3 px-4">رقم / كود الحساب</th>
                        <th class="py-3 px-4">اسم الحساب في الدليل</th>
                        <th class="py-3 px-4">التبويب المحاسبي</th>
                        <th class="py-3 px-4">طبيعة الحساب</th>
                        <th class="py-3 px-4">العملة</th>
                        <th class="py-3 px-4 text-left">الرصيد المالي الحالي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @foreach($accounts as $acc)
                    @php
                        $isDebitNature = in_array($acc->type, ['asset', 'expense']);
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-[#1F2E4A]">{{ $acc->code }}</td>
                        <td class="py-3.5 px-4 font-bold text-gray-800 flex items-center gap-2">
                            <span></span> {{ $acc->name }}
                        </td>
                        <td class="py-3.5 px-4">
                            @if($acc->type === 'asset')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">أصول (موجودات)</span>
                            @elseif($acc->type === 'liability')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800">خصوم (التزامات)</span>
                            @elseif($acc->type === 'equity')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">حقوق ملكية ورأس مال</span>
                            @elseif($acc->type === 'revenue')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">إيرادات ومبيعات</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">مصاريف ونفقات</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 font-bold {{ $isDebitNature ? 'text-emerald-700' : 'text-red-700' }}">
                            {{ $isDebitNature ? 'مدين (Debit)' : 'دائن (Credit)' }}
                        </td>
                        <td class="py-3.5 px-4 font-mono text-gray-500">{{ $acc->currency }}</td>
                        <td class="py-3.5 px-4 text-left font-mono font-black text-sm text-gray-900">
                            {{ number_format($acc->balance, 2) }} {{ $acc->currency }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Journal Entries View (سندات القيد) -->
    <div id="journalView" class="hidden bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-[#1F2E4A] text-white font-bold">
                    <tr>
                        <th class="py-3 px-4">رقم القيد</th>
                        <th class="py-3 px-4">التاريخ</th>
                        <th class="py-3 px-4">البيان والشرح العام للقيد</th>
                        <th class="py-3 px-4 text-center">إجمالي المدين</th>
                        <th class="py-3 px-4 text-center">إجمالي الدائن</th>
                        <th class="py-3 px-4 text-center">حالة التوازن</th>
                        <th class="py-3 px-4 text-center no-print">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @forelse($journalEntries ?? [] as $jv)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-[#1F2E4A]">{{ $jv->entry_no }}</td>
                        <td class="py-3.5 px-4 text-gray-500">{{ $jv->date }}</td>
                        <td class="py-3.5 px-4 font-bold text-gray-800 max-w-sm">{{ $jv->narration }}</td>
                        <td class="py-3.5 px-4 text-center font-mono font-bold text-[#1F2E4A]">{{ number_format($jv->total_debit, 2) }}</td>
                        <td class="py-3.5 px-4 text-center font-mono font-bold text-[#1F2E4A]">{{ number_format($jv->total_credit, 2) }}</td>
                        <td class="py-3.5 px-4 text-center">
                            @if(abs($jv->total_debit - $jv->total_credit) < 0.01)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">✓ قيد متوازن</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">غير متوازن</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center no-print">
                            <button onclick="viewJournalEntryModal({{ json_encode($jv) }})" class="px-2.5 py-1 bg-gray-100 hover:bg-[#1F2E4A] hover:text-white rounded text-[11px] font-bold transition">
                                 تفاصيل القيد
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-gray-400">لا توجد قيود يومية مسجلة</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal 1: New Account Form -->
<div id="newAccountModal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 overflow-y-auto no-print">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 my-8">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-base font-bold text-[#1F2E4A]">إضافة حساب مالي جديد في الدليل</h3>
            <button onclick="document.getElementById('newAccountModal').classList.add('hidden')" class="text-gray-400 font-bold text-xl">&times;</button>
        </div>

        <form action="{{ route('erp.accounts.store') }}" method="POST" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                    <label class="block font-semibold mb-1">رمز / كود الحساب</label>
                    <input type="text" name="code" required placeholder="مثال: 505" class="w-full p-2.5 border rounded-lg bg-gray-50 font-mono">
                </div>
                <div>
                    <label class="block font-semibold mb-1">التبويب المحاسبي الرئيسي</label>
                    <select name="type" class="w-full p-2.5 border rounded-lg bg-gray-50">
                        <option value="asset">أصول وموجودات (1)</option>
                        <option value="liability">خصوم والتزامات (2)</option>
                        <option value="equity">حقوق ملكية ورأس مال (3)</option>
                        <option value="revenue">إيرادات ومبيعات (4)</option>
                        <option value="expense">مصاريف وتكاليف (5)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1">اسم الحساب الكامل</label>
                <input type="text" name="name" required placeholder="مثال: مصاريف الدعاية والإعلان، بنك الإسكان..." class="w-full text-xs p-2.5 border rounded-lg bg-gray-50">
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1">الرصيد الافتتاحي</label>
                <input type="number" step="0.01" name="balance" value="0.00" class="w-full text-xs p-2.5 border rounded-lg bg-gray-50 font-mono font-bold text-[#1F2E4A]">
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="document.getElementById('newAccountModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-gray-500 bg-gray-100 rounded-lg">إلغاء</button>
                <button type="submit" class="px-6 py-2 text-xs font-bold text-white bg-[#1F2E4A] rounded-lg shadow">حفظ في الدليل المحاسبي</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Create Double-Entry Balanced Journal Entry (سند قيد مزدوج) -->
<div id="newJournalModal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 overflow-y-auto no-print">
    <div class="bg-white rounded-2xl max-w-4xl w-full p-6 shadow-2xl space-y-4 my-8">
        <div class="flex justify-between items-center pb-3 border-b">
            <div>
                <h3 class="text-base font-bold text-[#1F2E4A]">تحرير سند قيد محاسبي مزدوج (Journal Entry)</h3>
                <p class="text-[11px] text-gray-400">يشترط تساوي إجمالي الطرف المدين (Debit) مع الطرف الدائن (Credit) لترحيل القيد</p>
            </div>
            <button onclick="document.getElementById('newJournalModal').classList.add('hidden')" class="text-gray-400 font-bold text-xl">&times;</button>
        </div>

        <form action="{{ route('erp.journals.store') }}" method="POST" id="journalEntryForm" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div>
                    <label class="block font-semibold mb-1">تاريخ القيد</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full p-2 border rounded-lg bg-gray-50 font-mono">
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-semibold mb-1">البيان والشرح العام للقيد المحاسبي</label>
                    <input type="text" name="narration" required placeholder="مثال: تسوية مصاريف الشهر، قيد تسوية مخصص..." class="w-full p-2 border rounded-lg bg-gray-50">
                </div>
            </div>

            <!-- Dynamic Lines Table -->
            <div class="border rounded-xl p-3 bg-gray-50/50 space-y-3">
                <div class="flex justify-between items-center">
                    <h4 class="text-xs font-bold text-[#1F2E4A]">أطراف القيد المحاسبي</h4>
                    <button type="button" onclick="addJournalLine()" class="text-xs text-[#1F2E4A] font-bold hover:underline">
                        + إضافة سطر محاسبي جديد
                    </button>
                </div>

                <div id="journalLinesContainer" class="space-y-2"></div>

                <!-- Totals & Balance Verification Strip -->
                <div class="flex flex-col sm:flex-row justify-between items-center bg-white p-3 rounded-lg border text-xs gap-3">
                    <div id="journalBalanceStatus" class="font-bold flex items-center gap-1.5 text-emerald-700">
                        <span>✓</span> القيد متوازن ومطابق لمعايير المحاسبة
                    </div>
                    <div class="flex gap-4 font-mono font-bold">
                        <div>إجمالي المدين: <span id="jvTotalDebitDisplay" class="text-[#1F2E4A]">0.00</span></div>
                        <div>إجمالي الدائن: <span id="jvTotalCreditDisplay" class="text-[#1F2E4A]">0.00</span></div>
                        <div>الفرق: <span id="jvDiffDisplay" class="text-gray-900">0.00</span></div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t">
                <button type="button" onclick="document.getElementById('newJournalModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-gray-500 bg-gray-100 rounded-lg">إلغاء</button>
                <button type="submit" id="btnSubmitJournal" class="px-6 py-2 text-xs font-bold text-white bg-[#1F2E4A] rounded-lg shadow">ترحيل واعتماد سند القيد</button>
            </div>
        </form>
    </div>
</div>

<script>
    const availableAccounts = @json($accounts);
    let jvLineIndex = 0;

    function switchAccountView(view) {
        if (view === 'chart') {
            document.getElementById('chartView').classList.remove('hidden');
            document.getElementById('journalView').classList.add('hidden');
            document.getElementById('btnTabChart').className = 'px-4 py-1.5 text-xs font-bold rounded-lg bg-[#1F2E4A] text-white transition';
            document.getElementById('btnTabJournal').className = 'px-4 py-1.5 text-xs font-bold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition flex items-center gap-1';
        } else {
            document.getElementById('chartView').classList.add('hidden');
            document.getElementById('journalView').classList.remove('hidden');
            document.getElementById('btnTabChart').className = 'px-4 py-1.5 text-xs font-bold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition';
            document.getElementById('btnTabJournal').className = 'px-4 py-1.5 text-xs font-bold rounded-lg bg-[#1F2E4A] text-white transition flex items-center gap-1';
        }
    }

    function addJournalLine(defaultDebit = 0, defaultCredit = 0) {
        const container = document.getElementById('journalLinesContainer');
        const div = document.createElement('div');
        div.className = 'grid grid-cols-12 gap-2 items-center bg-white p-2 rounded-lg border text-xs';
        div.id = `jvLine_${jvLineIndex}`;

        let options = '';
        availableAccounts.forEach(a => {
            options += `<option value="${a.id}">[${a.code}] ${a.name} (${a.type})</option>`;
        });

        div.innerHTML = `
            <div class="col-span-5">
                <select name="lines[${jvLineIndex}][account_id]" class="w-full p-1.5 border rounded">
                    ${options}
                </select>
            </div>
            <div class="col-span-2">
                <input type="number" step="0.01" value="${defaultDebit}" name="lines[${jvLineIndex}][debit]" id="jvDebit_${jvLineIndex}" oninput="calcJournalTotals()" placeholder="مدين Debit" class="w-full p-1.5 border rounded text-center font-mono">
            </div>
            <div class="col-span-2">
                <input type="number" step="0.01" value="${defaultCredit}" name="lines[${jvLineIndex}][credit]" id="jvCredit_${jvLineIndex}" oninput="calcJournalTotals()" placeholder="دائن Credit" class="w-full p-1.5 border rounded text-center font-mono">
            </div>
            <div class="col-span-2">
                <input type="text" name="lines[${jvLineIndex}][description]" placeholder="شرح السطر..." class="w-full p-1.5 border rounded">
            </div>
            <div class="col-span-1 text-center">
                <button type="button" onclick="removeJournalLine(${jvLineIndex})" class="text-red-500 font-bold hover:text-red-700">&times;</button>
            </div>
        `;

        container.appendChild(div);
        jvLineIndex++;
        calcJournalTotals();
    }

    function removeJournalLine(idx) {
        const line = document.getElementById(`jvLine_${idx}`);
        if (line) {
            line.remove();
            calcJournalTotals();
        }
    }

    function calcJournalTotals() {
        let totalDebit = 0;
        let totalCredit = 0;

        for (let i = 0; i < jvLineIndex; i++) {
            const deb = document.getElementById(`jvDebit_${i}`);
            const cred = document.getElementById(`jvCredit_${i}`);
            if (deb && cred) {
                totalDebit += parseFloat(deb.value) || 0;
                totalCredit += parseFloat(cred.value) || 0;
            }
        }

        const diff = Math.abs(totalDebit - totalCredit);
        document.getElementById('jvTotalDebitDisplay').innerText = totalDebit.toFixed(2);
        document.getElementById('jvTotalCreditDisplay').innerText = totalCredit.toFixed(2);
        document.getElementById('jvDiffDisplay').innerText = diff.toFixed(2);

        const status = document.getElementById('journalBalanceStatus');
        const submitBtn = document.getElementById('btnSubmitJournal');

        if (diff < 0.01 && (totalDebit > 0 || totalCredit > 0)) {
            status.innerHTML = '<span>✓</span> القيد متوازن ومطابق لمعايير المحاسبة';
            status.className = 'font-bold flex items-center gap-1.5 text-emerald-700';
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-40');
        } else {
            status.innerHTML = '<span></span> القيد غير متوازن: يجب أن يتساوى المدين مع الدائن';
            status.className = 'font-bold flex items-center gap-1.5 text-red-600';
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-40');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        addJournalLine(100, 0);
        addJournalLine(0, 100);
    });
</script>
@endsection


