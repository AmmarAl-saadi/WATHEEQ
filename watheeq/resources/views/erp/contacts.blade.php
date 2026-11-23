@extends('layouts.app')

@section('title', 'العملاء والذمم المدينة والدائنة | وثيق ERP')

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm no-print">
        <div>
            <h2 class="text-xl font-bold text-[#1F2E4A]">إدارة العملاء والموردين (الذمم المدينة والدائنة)</h2>
            <p class="text-xs text-gray-500">كشوفات الحسابات، الأرصدة القائمة، السقوف الائتمانية والبيانات الضريبية</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button onclick="document.getElementById('newContactModal').classList.remove('hidden')" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#243b2a] text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5 whitespace-nowrap">
                <span>+</span> إضافة عميل / مورد جديد
            </button>
            <button onclick="window.print()" class="px-3 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1">
                <span></span> طباعة كشف الذمم
            </button>
        </div>
    </div>

    <!-- Summary Metrics -->
    @php
        $totalCustomerDebt = $contacts->whereIn('type', ['customer', 'both'])->sum('balance');
        $totalSupplierDebt = $contacts->whereIn('type', ['supplier', 'both'])->sum('balance');
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 no-print">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-500">إجمالي الذمم المدينة (مستحقة لدى العملاء)</span>
            <h3 class="text-2xl font-black text-[#1F2E4A] mt-1">{{ number_format($totalCustomerDebt, 2) }} <span class="text-xs font-bold text-[#1F2E4A]">{{ $tenant->currency }}</span></h3>
            <p class="text-[10px] text-gray-400 mt-1">حقوق مالية مستحقة للمنشأة</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-500">إجمالي الذمم الدائنة (مطلوبة للموردين)</span>
            <h3 class="text-2xl font-black text-red-600 mt-1">{{ number_format($totalSupplierDebt, 2) }} <span class="text-xs font-bold text-[#1F2E4A]">{{ $tenant->currency }}</span></h3>
            <p class="text-[10px] text-gray-400 mt-1">التزامات واجبة السداد</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-500">صافي الموقف المالي للذمم</span>
            <h3 class="text-2xl font-black text-gray-900 mt-1">{{ number_format($totalCustomerDebt - $totalSupplierDebt, 2) }} <span class="text-xs font-bold text-[#1F2E4A]">{{ $tenant->currency }}</span></h3>
            <p class="text-[10px] text-gray-400 mt-1">الفارق بين المستحقات والالتزامات</p>
        </div>
    </div>

    <!-- Contacts Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3 no-print">
            <input 
                type="text" 
                id="searchContactTable" 
                placeholder="بحث بالاسم، رقم الهاتف، أو الرقم الضريبي..." 
                oninput="filterContactTable()" 
                class="w-full sm:w-80 text-xs p-2.5 border rounded-lg bg-gray-50 focus:outline-none focus:ring-1 focus:ring-[#1F2E4A]"
            >
            <div class="flex gap-2">
                <button type="button" onclick="filterType('all')" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-[#1F2E4A] text-white">الكل</button>
                <button type="button" onclick="filterType('customer')" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">العملاء</button>
                <button type="button" onclick="filterType('supplier')" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">الموردين</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs" id="contactTable">
                <thead class="bg-[#1F2E4A] text-white font-bold">
                    <tr>
                        <th class="py-3 px-4">كود الحساب</th>
                        <th class="py-3 px-4">الاسم التجاري / العميل</th>
                        <th class="py-3 px-4">التصنيف</th>
                        <th class="py-3 px-4">الهاتف والبريد</th>
                        <th class="py-3 px-4">الرقم الضريبي</th>
                        <th class="py-3 px-4">السقف الائتماني</th>
                        <th class="py-3 px-4 text-left">الرصيد القائم</th>
                        <th class="py-3 px-4 text-center no-print">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @foreach($contacts as $c)
                    <tr class="hover:bg-gray-50 transition contact-row" data-type="{{ $c->type }}">
                        <td class="py-3.5 px-4 font-mono font-bold text-[#1F2E4A]">{{ $c->code }}</td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-gray-800 block">{{ $c->name }}</span>
                            <span class="text-[11px] text-gray-400">{{ $c->address ?? 'لا يوجد عنوان' }}</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $c->type === 'customer' ? 'bg-blue-100 text-blue-800' : ($c->type === 'supplier' ? 'bg-purple-100 text-purple-800' : 'bg-emerald-100 text-emerald-800') }}">
                                {{ $c->type === 'customer' ? 'عميل مدين' : ($c->type === 'supplier' ? 'مورد دائن' : 'عميل ومورد') }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-mono">
                            <span class="block text-gray-700">{{ $c->phone }}</span>
                            <span class="text-[10px] text-gray-400">{{ $c->email ?? '' }}</span>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-gray-500">{{ $c->tax_id ?? 'غير مسجل' }}</td>
                        <td class="py-3.5 px-4 font-mono text-gray-600">{{ number_format($c->credit_limit, 2) }} {{ $tenant->currency }}</td>
                        <td class="py-3.5 px-4 text-left font-mono font-black text-sm {{ $c->balance > 0 ? 'text-[#1F2E4A]' : 'text-gray-700' }}">
                            {{ number_format($c->balance, 2) }} {{ $tenant->currency }}
                        </td>
                        <td class="py-3.5 px-4 text-center no-print">
                            <button type="button" onclick="openStatementModal({{ json_encode($c) }})" class="px-2.5 py-1 bg-gray-100 hover:bg-[#1F2E4A] hover:text-white rounded text-[11px] font-bold transition">
                                 كشف حساب
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: New Contact Form -->
<div id="newContactModal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 overflow-y-auto no-print">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 my-8">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-base font-bold text-[#1F2E4A]">تسجيل بطاقة عميل أو مورد جديد</h3>
            <button onclick="document.getElementById('newContactModal').classList.add('hidden')" class="text-gray-400 text-xl font-bold">&times;</button>
        </div>

        <form action="{{ route('erp.contacts.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold mb-1">الاسم الكامل / الاسم التجاري للشركة</label>
                <input type="text" name="name" required placeholder="مثال: شركة القدس للتجهيزات الفنية" class="w-full text-xs p-2.5 border rounded-lg bg-gray-50">
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                    <label class="block font-semibold mb-1">نوع الجهة</label>
                    <select name="type" class="w-full p-2.5 border rounded-lg bg-gray-50">
                        <option value="customer">عميل (مبيعات / ذمم مدينة)</option>
                        <option value="supplier">مورد (مشتريات / ذمم دائنة)</option>
                        <option value="both">عميل ومورد معاً</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1">رقم الهاتف / الموبايل</label>
                    <input type="text" name="phone" required placeholder="079XXXXXXX" class="w-full p-2.5 border rounded-lg bg-gray-50 font-mono">
                </div>
                <div>
                    <label class="block font-semibold mb-1">الرقم الضريبي للمنشأة</label>
                    <input type="text" name="tax_id" placeholder="الرقم الوطني / الضريبي" class="w-full p-2.5 border rounded-lg bg-gray-50 font-mono">
                </div>
                <div>
                    <label class="block font-semibold mb-1">السقف الائتماني المالي</label>
                    <input type="number" step="0.01" name="credit_limit" value="5000.00" class="w-full p-2.5 border rounded-lg bg-gray-50 font-mono">
                </div>
                <div>
                    <label class="block font-semibold mb-1">الرصيد الافتتاحي القائم</label>
                    <input type="number" step="0.01" name="balance" value="0.00" class="w-full p-2.5 border rounded-lg bg-gray-50 font-mono font-bold text-[#1F2E4A]">
                </div>
                <div>
                    <label class="block font-semibold mb-1">البريد الإلكتروني</label>
                    <input type="email" name="email" placeholder="account@company.jo" class="w-full p-2.5 border rounded-lg bg-gray-50 font-mono">
                </div>
                <div class="col-span-2">
                    <label class="block font-semibold mb-1">العنوان بالتفصيل</label>
                    <input type="text" name="address" placeholder="المدينة، الشارع، المجمع..." class="w-full p-2.5 border rounded-lg bg-gray-50">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="document.getElementById('newContactModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-gray-500 bg-gray-100 rounded-lg">إلغاء</button>
                <button type="submit" class="px-6 py-2 text-xs font-bold text-white bg-[#1F2E4A] rounded-lg shadow">حفظ البيانات</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Account Statement Preview (كشف حساب تفصيلي) -->
<div id="statementModal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 overflow-y-auto no-print">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4 my-8">
        <div class="flex justify-between items-start pb-3 border-b">
            <div>
                <h3 class="text-sm font-bold text-[#1F2E4A]">كشف حساب مالي تفصيلي</h3>
                <p id="stmtContactName" class="text-xs font-bold text-gray-800 mt-0.5"></p>
                <span id="stmtCode" class="text-[10px] font-mono text-gray-400"></span>
            </div>
            <button onclick="document.getElementById('statementModal').classList.add('hidden')" class="text-gray-400 font-bold">&times;</button>
        </div>

        <div class="p-3 bg-gray-50 rounded-lg flex justify-between items-center text-xs">
            <div><span>الرصيد النهائي المستحق:</span> <strong id="stmtBalance" class="text-sm font-black text-[#1F2E4A] font-mono mr-2"></strong></div>
            <button onclick="window.print()" class="px-3 py-1 bg-[#1F2E4A] text-white text-[11px] font-bold rounded"> طباعة كشف الحساب</button>
        </div>
    </div>
</div>

<script>
    function filterContactTable() {
        const query = document.getElementById('searchContactTable').value.toLowerCase();
        const rows = document.querySelectorAll('.contact-row');
        rows.forEach(r => {
            const text = r.innerText.toLowerCase();
            r.style.display = text.includes(query) ? '' : 'none';
        });
    }

    function filterType(type) {
        const rows = document.querySelectorAll('.contact-row');
        rows.forEach(r => {
            if (type === 'all' || r.dataset.type === type || r.dataset.type === 'both') {
                r.style.display = '';
            } else {
                r.style.display = 'none';
            }
        });
    }

    function openStatementModal(contact) {
        document.getElementById('stmtContactName').innerText = contact.name;
        document.getElementById('stmtCode').innerText = 'كود الحساب: ' + contact.code + ' • هاتف: ' + contact.phone;
        document.getElementById('stmtBalance').innerText = parseFloat(contact.balance).toFixed(2) + ' {{ $tenant->currency }}';
        document.getElementById('statementModal').classList.remove('hidden');
    }
</script>
@endsection


