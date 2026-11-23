@extends('layouts.app')

@section('title', 'القبض والصرف وإدارة محفظة الشيكات | وثيق ERP')

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm no-print">
        <div>
            <h2 class="text-xl font-bold text-[#1F2E4A]">سندات القبض والصرف وإدارة محفظة الشيكات</h2>
            <p class="text-xs text-gray-500">توثيق المقبوضات النقدية والبنكية والشيكات برسم التحصيل والمدفوعات</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button onclick="document.getElementById('newVoucherModal').classList.remove('hidden')" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#243b2a] text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5 whitespace-nowrap">
                <span>+</span> تحرير سند مالي جديد
            </button>
            <button onclick="window.print()" class="px-3 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1">
                <span></span> طباعة اليومية
            </button>
        </div>
    </div>

    <!-- Summary Metrics -->
    @php
        $totalReceipts = $vouchers->where('type', 'receipt')->sum('amount');
        $totalPayments = $vouchers->where('type', 'payment')->sum('amount');
        $chequesCount = $vouchers->where('payment_type', 'cheque')->count();
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 no-print">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-500">إجمالي المقبوضات الواردة (Receipts)</span>
            <h3 class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($totalReceipts, 2) }} <span class="text-xs font-bold text-[#1F2E4A]">{{ $tenant->currency }}</span></h3>
            <p class="text-[10px] text-gray-400 mt-1">أموال مستلمة نقدية وبنكية وشيكات</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-500">إجمالي المدفوعات الصادرة (Payments)</span>
            <h3 class="text-2xl font-black text-red-600 mt-1">{{ number_format($totalPayments, 2) }} <span class="text-xs font-bold text-[#1F2E4A]">{{ $tenant->currency }}</span></h3>
            <p class="text-[10px] text-gray-400 mt-1">دفعات موردين ومصاريف تشغيلية</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-500">محفظة الشيكات النشطة</span>
            <h3 class="text-2xl font-black text-[#1F2E4A] mt-1">{{ $chequesCount }} <span class="text-xs font-bold text-gray-400">شيك بنكي</span></h3>
            <p class="text-[10px] text-gray-400 mt-1">برسم التحصيل في البنوك</p>
        </div>
    </div>

    <!-- Vouchers Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3 no-print">
            <input 
                type="text" 
                id="searchVoucherTable" 
                placeholder="بحث برقم السند، الطرف المستلم، أو البيان..." 
                oninput="filterVoucherTable()" 
                class="w-full sm:w-80 text-xs p-2.5 border rounded-lg bg-gray-50 focus:outline-none focus:ring-1 focus:ring-[#1F2E4A]"
            >
            <div class="flex gap-2">
                <button type="button" onclick="filterVoucherType('all')" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-[#1F2E4A] text-white">الكل</button>
                <button type="button" onclick="filterVoucherType('receipt')" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">سندات قبض</button>
                <button type="button" onclick="filterVoucherType('payment')" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">سندات صرف</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs" id="voucherTable">
                <thead class="bg-[#1F2E4A] text-white font-bold">
                    <tr>
                        <th class="py-3 px-4">رقم السند</th>
                        <th class="py-3 px-4">النوع</th>
                        <th class="py-3 px-4">وصلنا من / صُرف إلى</th>
                        <th class="py-3 px-4">التاريخ</th>
                        <th class="py-3 px-4">وسيلة الدفع</th>
                        <th class="py-3 px-4">القيمة المالية</th>
                        <th class="py-3 px-4">البيان والشرح</th>
                        <th class="py-3 px-4 text-center no-print">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @foreach($vouchers as $v)
                    <tr class="hover:bg-gray-50 transition voucher-row" data-type="{{ $v->type }}">
                        <td class="py-3.5 px-4 font-mono font-bold text-[#1F2E4A]">{{ $v->voucher_no }}</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $v->type === 'receipt' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                {{ $v->type === 'receipt' ? 'سند قبض' : 'سند صرف' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-bold text-gray-800">{{ $v->contact_name }}</td>
                        <td class="py-3.5 px-4 text-gray-500">{{ $v->date }}</td>
                        <td class="py-3.5 px-4">
                            @if($v->payment_type === 'cash')
                                <span class="px-2 py-0.5 bg-gray-100 rounded text-gray-700 font-semibold">نقداً (كاش)</span>
                            @elseif($v->payment_type === 'cheque')
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded font-bold">شيك #{{ $v->cheque_no }} ({{ $v->bank_name ?? 'البنك العربي' }})</span>
                            @else
                                <span class="px-2 py-0.5 bg-purple-100 text-purple-800 rounded font-bold">حوالة بنكية</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 font-mono font-black text-sm text-gray-900">
                            {{ number_format($v->amount, 2) }} {{ $v->currency }}
                        </td>
                        <td class="py-3.5 px-4 text-gray-600 max-w-xs truncate">{{ $v->statement }}</td>
                        <td class="py-3.5 px-4 text-center no-print">
                            <button type="button" onclick="openVoucherPrint({{ json_encode($v) }})" class="px-2.5 py-1 bg-gray-100 hover:bg-[#1F2E4A] hover:text-white rounded text-[11px] font-bold transition">
                                 طباعة
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: New Voucher Form -->
<div id="newVoucherModal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 overflow-y-auto no-print">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 my-8">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-base font-bold text-[#1F2E4A]">تحرير سند قبض أو صرف جديد</h3>
            <button onclick="document.getElementById('newVoucherModal').classList.add('hidden')" class="text-gray-400 text-xl font-bold">&times;</button>
        </div>

        <form action="{{ route('erp.vouchers.store') }}" method="POST" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                    <label class="block font-semibold mb-1">نوع السند</label>
                    <select name="type" class="w-full p-2.5 border rounded-lg bg-gray-50">
                        <option value="receipt">سند قبض (استلام أموال / شيك)</option>
                        <option value="payment">سند صرف (دفع أموال / مصاريف)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1">وصلنا من / صُرف إلى</label>
                    <input type="text" name="contact_name" required placeholder="اسم العميل أو المستفيد" class="w-full p-2.5 border rounded-lg bg-gray-50">
                </div>
                <div>
                    <label class="block font-semibold mb-1">المبلغ المالي</label>
                    <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full p-2.5 border rounded-lg bg-gray-50 font-mono font-bold text-[#1F2E4A]">
                </div>
                <div>
                    <label class="block font-semibold mb-1">طريقة القبض / الصرف</label>
                    <select name="payment_type" id="vPaymentType" onchange="toggleChequeFields(this.value)" class="w-full p-2.5 border rounded-lg bg-gray-50">
                        <option value="cash">نقداً (الصندوق العام)</option>
                        <option value="cheque">شيك بنكي مؤجل / حال</option>
                        <option value="bank_transfer">حوالة بنكية مباشرة</option>
                    </select>
                </div>
            </div>

            <!-- Conditional Cheque Fields -->
            <div id="chequeFieldsSection" class="hidden p-3 bg-blue-50 border border-blue-200 rounded-xl space-y-2 text-xs">
                <span class="font-bold text-blue-900 block">بيانات الشيك البنكي:</span>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-[11px] mb-1">اسم البنك</label>
                        <input type="text" name="bank_name" value="البنك العربي" class="w-full p-2 border rounded bg-white">
                    </div>
                    <div>
                        <label class="block text-[11px] mb-1">رقم الشيك</label>
                        <input type="text" name="cheque_no" placeholder="8849201" class="w-full p-2 border rounded bg-white font-mono">
                    </div>
                    <div>
                        <label class="block text-[11px] mb-1">تاريخ الاستحقاق</label>
                        <input type="date" name="cheque_due_date" value="{{ date('Y-m-d') }}" class="w-full p-2 border rounded bg-white font-mono">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1">وذلك لقاء / البيان والشرح</label>
                <textarea name="statement" required rows="2" placeholder="مثال: دفعة تحت حساب فاتورة رقم..." class="w-full text-xs p-2.5 border rounded-lg bg-gray-50"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="document.getElementById('newVoucherModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-gray-500 bg-gray-100 rounded-lg">إلغاء</button>
                <button type="submit" class="px-6 py-2 text-xs font-bold text-white bg-[#1F2E4A] rounded-lg shadow">ترحيل السند المالي</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Printable Voucher Preview -->
<div id="voucherPrintModal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 overflow-y-auto no-print">
    <div id="printableVoucherArea" class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4 my-8">
        <div class="flex justify-between items-start pb-3 border-b-2 border-[#1F2E4A]">
            <div>
                <h3 class="text-base font-black text-[#1F2E4A]">{{ $tenant->name }}</h3>
                <p class="text-[11px] text-gray-500">{{ $tenant->address }}</p>
                <span id="vpTypeBadge" class="inline-block px-2 py-0.5 rounded text-[10px] font-bold mt-1"></span>
            </div>
            <div class="text-left font-mono">
                <p id="vpNo" class="text-sm font-black text-gray-800"></p>
                <p id="vpDate" class="text-xs text-gray-400"></p>
            </div>
        </div>

        <div class="space-y-2 text-xs">
            <div class="p-2.5 bg-gray-50 rounded flex justify-between">
                <span class="text-gray-500 font-semibold" id="vpLabelContact"></span>
                <strong id="vpContact" class="text-gray-900"></strong>
            </div>
            <div class="p-2.5 bg-gray-50 rounded flex justify-between">
                <span class="text-gray-500 font-semibold">المبلغ المالي:</span>
                <span id="vpAmount" class="font-mono font-black text-sm text-[#1F2E4A]"></span>
            </div>
            <div class="p-2.5 bg-gray-50 rounded flex justify-between">
                <span class="text-gray-500 font-semibold">طريقة القبض / الصرف:</span>
                <span id="vpMethod" class="font-bold"></span>
            </div>
            <div class="p-2.5 bg-gray-50 rounded">
                <span class="text-gray-500 font-semibold block mb-1">وذلك لقاء / البيان:</span>
                <p id="vpStatement" class="text-gray-800 leading-relaxed"></p>
            </div>
        </div>

        <div class="flex justify-between items-center pt-6 border-t text-xs text-center text-gray-500 font-semibold">
            <div>
                <p class="mb-4">توقيع أمين الصندوق / المحاسب</p>
                <p class="border-t border-dashed w-32 mx-auto"></p>
            </div>
            <div>
                <p class="mb-4">توقيع المستلم / العميل</p>
                <p class="border-t border-dashed w-32 mx-auto"></p>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t no-print">
            <button type="button" onclick="document.getElementById('voucherPrintModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-gray-500 bg-gray-100 rounded-lg">إغلاق</button>
            <button type="button" onclick="watheeqPrint('printableVoucherArea', 'a4')" class="px-5 py-2 text-xs font-bold text-white bg-[#1F2E4A] rounded-lg shadow">طباعة السند</button>
        </div>
    </div>
</div>

<script>
    function toggleChequeFields(val) {
        const sec = document.getElementById('chequeFieldsSection');
        if (val === 'cheque') {
            sec.classList.remove('hidden');
        } else {
            sec.classList.add('hidden');
        }
    }

    function filterVoucherTable() {
        const query = document.getElementById('searchVoucherTable').value.toLowerCase();
        const rows = document.querySelectorAll('.voucher-row');
        rows.forEach(r => {
            const text = r.innerText.toLowerCase();
            r.style.display = text.includes(query) ? '' : 'none';
        });
    }

    function filterVoucherType(type) {
        const rows = document.querySelectorAll('.voucher-row');
        rows.forEach(r => {
            if (type === 'all' || r.dataset.type === type) {
                r.style.display = '';
            } else {
                r.style.display = 'none';
            }
        });
    }

    function openVoucherPrint(v) {
        document.getElementById('vpNo').innerText = v.voucher_no;
        document.getElementById('vpDate').innerText = 'التاريخ: ' + v.date;
        document.getElementById('vpContact').innerText = v.contact_name;
        document.getElementById('vpLabelContact').innerText = v.type === 'receipt' ? 'وصلنا من السيد / الشركة:' : 'صُرف إلى السيد / الشركة:';
        document.getElementById('vpAmount').innerText = parseFloat(v.amount).toFixed(2) + ' ' + v.currency;
        document.getElementById('vpMethod').innerText = v.payment_type === 'cash' ? 'نقداً (كاش)' : (v.payment_type === 'cheque' ? ('شيك رقم ' + (v.cheque_no || '—')) : 'حوالة بنكية');
        document.getElementById('vpStatement').innerText = v.statement;

        const badge = document.getElementById('vpTypeBadge');
        if (v.type === 'receipt') {
            badge.innerText = 'سند قبض مالي معتمد';
            badge.className = 'inline-block px-2 py-0.5 rounded text-[10px] font-bold mt-1 bg-emerald-100 text-emerald-800';
        } else {
            badge.innerText = 'سند صرف مالي معتمد';
            badge.className = 'inline-block px-2 py-0.5 rounded text-[10px] font-bold mt-1 bg-red-100 text-red-800';
        }

        document.getElementById('voucherPrintModal').classList.remove('hidden');
    }
</script>
@endsection


