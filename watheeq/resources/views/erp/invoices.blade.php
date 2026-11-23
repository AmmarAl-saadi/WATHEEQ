@extends('layouts.app')

@section('title', 'فواتير المبيعات والمشتريات | وثيق ERP')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm no-print">
        <div>
            <h2 class="text-xl font-bold text-[#1F2E4A]">فواتير المبيعات والمشتريات الرسمية</h2>
            <p class="text-xs text-gray-500">إصدار الفواتير الضريبية، السداد، ودعم الطباعة المباشرة على A4 أو طابعات الرول الحرارية (80mm)</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('erp.jofotara.hub') }}" class="px-4 py-2 bg-[#343A40] hover:bg-black text-white text-xs font-bold rounded-lg shadow transition">
                الفوترة الوطنية (JoFotara)
            </a>
            <button onclick="document.getElementById('newInvoiceModal').classList.remove('hidden')" class="px-4 py-2 bg-[#1F2E4A] text-white text-xs font-bold rounded-lg shadow hover:bg-[#343A40]">
                + تحرير فاتورة جديدة
            </button>
        </div>
    </div>

    <!-- Invoices List Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden no-print">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-[#1F2E4A] text-white font-bold">
                    <tr>
                        <th class="py-3.5 px-4">رقم الفاتورة</th>
                        <th class="py-3.5 px-4">نوع المستند</th>
                        <th class="py-3.5 px-4">الطرف الثاني</th>
                        <th class="py-3.5 px-4">التاريخ</th>
                        <th class="py-3.5 px-4">وسيلة الدفع</th>
                        <th class="py-3.5 px-4">الإجمالي شامل الضريبة</th>
                        <th class="py-3.5 px-4">الحالة</th>
                        <th class="py-3.5 px-4 text-center">الفوترة الوطنية (ISTD)</th>
                        <th class="py-3.5 px-4 text-center">طباعة ومعاينة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @forelse($invoices as $inv)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-[#1F2E4A]">{{ $inv->invoice_no }}</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $inv->type === 'sale' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $inv->type === 'sale' ? 'فاتورة بيع' : 'فاتورة شراء' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-bold text-gray-800">{{ $inv->contact_name }}</td>
                        <td class="py-3.5 px-4 text-gray-500">{{ $inv->date }}</td>
                        <td class="py-3.5 px-4">
                            {{ $inv->payment_method === 'cash' ? 'نقداً (كاش)' : ($inv->payment_method === 'credit' ? 'آجل على الحساب' : 'شيك / بطاقة') }}
                        </td>
                        <td class="py-3.5 px-4 font-bold text-gray-900">{{ number_format($inv->grand_total, 2) }} {{ $tenant->currency }}</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $inv->status === 'paid' ? 'bg-[#1F2E4A]/10 text-[#1F2E4A]' : 'bg-amber-100 text-amber-800' }}">
                                {{ $inv->status === 'paid' ? 'مدفوعة' : 'غير مدفوعة' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($inv->type === 'sale')
                                @if(($inv->jofotara_status ?? '') === 'SUBMITTED')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#1F2E4A] text-white" title="{{ $inv->jofotara_uuid ?? '' }}">
                                        معتمدة (ISTD)
                                    </span>
                                @else
                                    <form action="{{ route('erp.invoices.jofotara.submit', $inv->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#1F2E4A]/10 hover:bg-[#1F2E4A] text-[#1F2E4A] hover:text-white border border-[#1F2E4A]/30 transition" title="إرسال الفاتورة للاعتماد في نظام الفوترة الوطني الأردني">
                                            اعتماد JoFotara
                                        </button>
                                    </form>
                                @endif
                            @else
                                <span class="text-gray-400 text-[10px]">-</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="inline-flex items-center gap-1.5">
                                <button 
                                    type="button" 
                                    onclick="openA4Preview({{ json_encode($inv) }})"
                                    class="px-2.5 py-1 bg-[#1F2E4A]/10 text-[#1F2E4A] hover:bg-[#1F2E4A] hover:text-white rounded-lg text-[11px] font-bold transition"
                                    title="طباعة على ورق قياسي A4 للشركات"
                                >
                                    A4
                                </button>
                                <button 
                                    type="button" 
                                    onclick="openThermalPreview({{ json_encode($inv) }})"
                                    class="px-2.5 py-1 bg-[#1F2E4A]/15 text-[#343A40] hover:bg-[#1F2E4A] hover:text-white rounded-lg text-[11px] font-bold transition"
                                    title="طباعة إيصال رول حراري 80mm للمحلات والكاشير"
                                >
                                    حراري (80mm)
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-6 text-center text-gray-400">لا توجد فواتير مسجلة في النظام</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL 1: A4 CORPORATE INVOICE PRINT PREVIEW -->
<div id="modalA4Invoice" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 overflow-y-auto no-print">
    <div class="bg-white rounded-2xl max-w-4xl w-full p-8 shadow-2xl space-y-5 my-8">
        <div id="printableA4Area" class="bg-white p-6 space-y-5">
            <div class="flex justify-between items-start pb-4 border-b-2 border-[#1F2E4A]">
                <div>
                    <h2 class="text-xl font-black text-[#1F2E4A]">{{ $tenant->name }}</h2>
                    <p class="text-xs text-gray-500 font-semibold">{{ $tenant->name_en }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $tenant->address }} - هاتف: {{ $tenant->phone }}</p>
                    <div class="flex gap-4 mt-1.5 text-xs font-bold text-[#1F2E4A]">
                        <span>الرقم الضريبي: {{ $tenant->tax_number }}</span>
                        <span>السجل التجاري: {{ $tenant->commercial_record }}</span>
                    </div>
                </div>
                <div class="text-left font-mono">
                    <span class="inline-block px-3 py-1 bg-[#1F2E4A] text-white text-xs font-bold rounded mb-1">فاتورة ضريبية رسمية (A4)</span>
                    <p id="a4InvNo" class="text-base font-black text-gray-800"></p>
                    <p id="a4InvDate" class="text-xs text-gray-400"></p>
                </div>
            </div>

            <div class="bg-[#CED4DA] p-3.5 rounded-xl flex justify-between items-center text-xs">
                <div>
                    <span class="text-gray-500 block font-semibold">صادرة إلى السيد / الشركة:</span>
                    <h4 id="a4ContactName" class="text-sm font-bold text-[#1F2E4A] mt-0.5"></h4>
                </div>
                <div class="text-left">
                    <span class="text-gray-500 block font-semibold">طريقة السداد المعتمدة:</span>
                    <p id="a4PayMethod" class="font-bold text-gray-800 text-sm"></p>
                </div>
            </div>

            <table class="w-full text-right text-xs">
                <thead class="bg-[#1F2E4A] text-white font-bold">
                    <tr>
                        <th class="py-2.5 px-3">#</th>
                        <th class="py-2.5 px-3">البيان / الصنف</th>
                        <th class="py-2.5 px-3 text-center">الوحدة</th>
                        <th class="py-2.5 px-3 text-center">الكمية</th>
                        <th class="py-2.5 px-3 text-center">سعر الوحدة</th>
                        <th class="py-2.5 px-3 text-left">المجموع الصافي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    <tr>
                        <td class="py-2.5 px-3">1</td>
                        <td class="py-2.5 px-3 font-bold text-gray-800">بند تجاري معتمد وفق أمر التوريد</td>
                        <td class="py-2.5 px-3 text-center">قطعة</td>
                        <td class="py-2.5 px-3 text-center font-bold">1</td>
                        <td class="py-2.5 px-3 text-center font-mono" id="a4ItemUnitPrice"></td>
                        <td class="py-2.5 px-3 text-left font-mono font-bold" id="a4ItemLineTotal"></td>
                    </tr>
                </tbody>
            </table>

            <div class="flex justify-between items-start pt-4 border-t border-gray-200 text-xs">
                <div class="space-y-2 max-w-sm">
                    <div class="text-[11px] text-gray-500 leading-relaxed">
                        <span class="font-bold text-gray-700 block mb-0.5">الشروط وسياسة الضمان:</span>
                        {{ $tenant->invoice_notes }}
                    </div>
                    <div class="p-2 border rounded-lg bg-gray-50 text-[10px] text-gray-600 font-mono">
                        ختم الفوترة الإلكترونية المعتمد - متوافق مع هيئة الضريبة الأردنية
                    </div>
                </div>

                <div class="w-60 bg-gray-50 p-3 rounded-xl border text-left space-y-1.5">
                    <div class="flex justify-between text-gray-600">
                        <span>المجموع الصافي:</span>
                        <span id="a4Subtotal" class="font-mono font-bold"></span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>ضريبة المبيعات ({{ $tenant->tax_rate }}%):</span>
                        <span id="a4Tax" class="font-mono font-bold"></span>
                    </div>
                    <div class="flex justify-between text-base font-black text-[#1F2E4A] pt-2 border-t">
                        <span>الصافي المطلوب:</span>
                        <span id="a4Grand" class="font-mono"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-4 border-t no-print">
            <button onclick="document.getElementById('modalA4Invoice').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-gray-500 bg-gray-100 rounded-lg">إغلاق</button>
            <button onclick="printA4()" class="px-6 py-2 text-xs font-bold text-white bg-[#1F2E4A] rounded-lg shadow">
                طباعة فورية على طابعة A4
            </button>
        </div>
    </div>
</div>

<!-- MODAL 2: THERMAL RECEIPT 80MM PRINT PREVIEW -->
<div id="modalThermalInvoice" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 overflow-y-auto no-print">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4 my-8">
        <div class="flex justify-between items-center pb-2 border-b no-print">
            <h3 class="text-xs font-bold text-[#1F2E4A]">معاينة إيصال الرول الحراري (Thermal 80mm)</h3>
            <button onclick="document.getElementById('modalThermalInvoice').classList.add('hidden')" class="text-gray-400 font-bold">&times;</button>
        </div>

        <div id="printableThermalArea" class="bg-white p-2 text-xs font-mono space-y-2 border border-dashed border-gray-400 rounded">
            <div class="text-center pb-2 border-b border-dashed border-gray-400">
                <h4 class="font-black text-sm text-gray-900">{{ $tenant->name }}</h4>
                <p class="text-[10px] text-gray-500">{{ $tenant->address }}</p>
                <p class="text-[10px] text-gray-600 font-bold">رقم ضريبي: {{ $tenant->tax_number }}</p>
                <span class="inline-block px-2 py-0.5 bg-gray-100 text-[9px] font-bold rounded mt-1">فاتورة مبيعات كاشير مبسطة</span>
            </div>

            <div class="text-[10px] space-y-0.5 text-gray-700 pb-1 border-b border-dashed border-gray-300">
                <div class="flex justify-between"><span>رقم الفاتورة:</span><span id="thInvNo" class="font-bold"></span></div>
                <div class="flex justify-between"><span>التاريخ:</span><span id="thDate"></span></div>
                <div class="flex justify-between"><span>العميل:</span><span id="thContact"></span></div>
                <div class="flex justify-between"><span>وسيلة السداد:</span><span id="thPayMethod"></span></div>
            </div>

            <table class="w-full text-right text-[10px] py-1 border-b border-dashed border-gray-400">
                <thead>
                    <tr class="border-b border-gray-300">
                        <th class="py-1">الصنف</th>
                        <th class="py-1 text-center">الكمية</th>
                        <th class="py-1 text-left">المبلغ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-1 font-bold">بند مبيعات رسمي</td>
                        <td class="py-1 text-center">1</td>
                        <td class="py-1 text-left font-bold" id="thItemTotal"></td>
                    </tr>
                </tbody>
            </table>

            <div class="space-y-1 text-[11px] pt-1">
                <div class="flex justify-between"><span>الصافي:</span><span id="thSubtotal"></span></div>
                <div class="flex justify-between"><span>الضريبة ({{ $tenant->tax_rate }}%):</span><span id="thTax"></span></div>
                <div class="flex justify-between text-sm font-black pt-1 border-t border-gray-800">
                    <span>الإجمالي المستحق:</span>
                    <span id="thGrand"></span>
                </div>
            </div>

            <div class="text-center text-[9px] text-gray-400 pt-2 border-t border-dashed border-gray-400">
                شكراً لزيارتكم - البضاعة المباعة تخضع للشروط المعتمدة
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-2 no-print">
            <button onclick="document.getElementById('modalThermalInvoice').classList.add('hidden')" class="px-3 py-1.5 text-xs font-bold text-gray-500 bg-gray-100 rounded-lg">إلغاء</button>
            <button onclick="printThermal()" class="px-5 py-1.5 text-xs font-bold text-white bg-[#1F2E4A] rounded-lg shadow">
                طباعة على الطابعة الحرارية
            </button>
        </div>
    </div>
</div>

<!-- MODAL 3: NEW INVOICE CREATION -->
<div id="newInvoiceModal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 overflow-y-auto no-print">
    <div class="bg-white rounded-2xl max-w-4xl w-full p-6 shadow-2xl space-y-4 my-8">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-base font-bold text-[#1F2E4A]">تحرير فاتورة تجارية رسمية</h3>
            <button onclick="document.getElementById('newInvoiceModal').classList.add('hidden')" class="text-gray-400 text-xl font-bold">&times;</button>
        </div>

        <form action="{{ route('erp.invoices.store') }}" method="POST" id="mainInvoiceForm" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div>
                    <label class="block font-semibold mb-1">الطرف الثاني (العميل / المورد)</label>
                    <select name="contact_id" class="w-full p-2 border rounded-lg bg-gray-50" required>
                        @foreach($contacts as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->type }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1">نوع المستند</label>
                    <select name="type" class="w-full p-2 border rounded-lg bg-gray-50">
                        <option value="sale">فاتورة مبيعات (ضريبية)</option>
                        <option value="purchase">فاتورة مشتريات (توريد)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1">طريقة السداد</label>
                    <select name="payment_method" class="w-full p-2 border rounded-lg bg-gray-50">
                        <option value="cash">نقداً (الصندوق الرئيسي)</option>
                        <option value="credit">آجل على الحساب</option>
                        <option value="card">بطاقة بنكية إلكترونية</option>
                        <option value="cheque">شيك بنكي</option>
                    </select>
                </div>
            </div>

            <div class="border rounded-xl p-3 bg-gray-50/50 space-y-3">
                <div class="flex justify-between items-center">
                    <h4 class="text-xs font-bold text-[#1F2E4A]">بنود وأصناف الفاتورة</h4>
                    <button type="button" onclick="addInvoiceRow()" class="text-xs text-[#1F2E4A] font-bold hover:underline">
                        + إضافة سطر صنف جديد
                    </button>
                </div>

                <div id="invoiceRowsContainer" class="space-y-2"></div>

                <div class="flex justify-end pt-2 text-xs">
                    <div class="w-64 bg-white p-3 rounded-lg border space-y-1">
                        <div class="flex justify-between text-gray-600">
                            <span>المجموع الصافي:</span>
                            <span id="invFormSubtotalDisplay" class="font-bold">0.00 {{ $tenant->currency }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>ضريبة المبيعات ({{ $tenant->tax_rate }}%):</span>
                            <span id="invFormTaxDisplay" class="font-bold">0.00 {{ $tenant->currency }}</span>
                        </div>
                        <div class="flex justify-between text-sm font-black text-[#1F2E4A] pt-2 border-t">
                            <span>المجموع النهائي:</span>
                            <span id="invFormGrandDisplay">0.00 {{ $tenant->currency }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="subtotal" id="invHiddenSubtotal" value="0">
            <input type="hidden" name="tax_total" id="invHiddenTax" value="0">
            <input type="hidden" name="grand_total" id="invHiddenGrand" value="0">

            <div>
                <label class="block text-xs font-semibold mb-1">ملاحظات وشروط الفاتورة</label>
                <textarea name="notes" rows="2" class="w-full text-xs p-2 border rounded-lg bg-gray-50" placeholder="شروط إضافية تظهر على الفاتورة المطبوعة..."></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('newInvoiceModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-gray-500 bg-gray-100 rounded-lg">إلغاء</button>
                <button type="submit" class="px-6 py-2 text-xs font-bold text-white bg-[#1F2E4A] rounded-lg shadow">حفظ وترحيل الفاتورة</button>
            </div>
        </form>
    </div>
</div>

<script>
    const availableProducts = @json($products);
    const taxRate = {{ $tenant->tax_rate ?? 16 }};
    const currency = '{{ $tenant->currency ?? "JOD" }}';
    let rowIndex = 0;

    function openA4Preview(inv) {
        document.getElementById('a4InvNo').innerText = inv.invoice_no;
        document.getElementById('a4InvDate').innerText = 'التاريخ: ' + inv.date;
        document.getElementById('a4ContactName').innerText = inv.contact_name;
        document.getElementById('a4PayMethod').innerText = inv.payment_method === 'cash' ? 'نقداً (كاش)' : 'آجل على الحساب';
        document.getElementById('a4Subtotal').innerText = parseFloat(inv.subtotal).toFixed(2) + ' ' + currency;
        document.getElementById('a4Tax').innerText = parseFloat(inv.tax_total).toFixed(2) + ' ' + currency;
        document.getElementById('a4Grand').innerText = parseFloat(inv.grand_total).toFixed(2) + ' ' + currency;
        document.getElementById('a4ItemUnitPrice').innerText = parseFloat(inv.subtotal).toFixed(2);
        document.getElementById('a4ItemLineTotal').innerText = parseFloat(inv.subtotal).toFixed(2);

        document.getElementById('modalA4Invoice').classList.remove('hidden');
    }

    function openThermalPreview(inv) {
        document.getElementById('thInvNo').innerText = inv.invoice_no;
        document.getElementById('thDate').innerText = inv.date;
        document.getElementById('thContact').innerText = inv.contact_name;
        document.getElementById('thPayMethod').innerText = inv.payment_method;
        document.getElementById('thSubtotal').innerText = parseFloat(inv.subtotal).toFixed(2) + ' ' + currency;
        document.getElementById('thTax').innerText = parseFloat(inv.tax_total).toFixed(2) + ' ' + currency;
        document.getElementById('thGrand').innerText = parseFloat(inv.grand_total).toFixed(2) + ' ' + currency;
        document.getElementById('thItemTotal').innerText = parseFloat(inv.subtotal).toFixed(2);

        document.getElementById('modalThermalInvoice').classList.remove('hidden');
    }

    function printA4() {
        watheeqPrint('printableA4Area', 'a4');
    }

    function printThermal() {
        watheeqPrint('printableThermalArea', 'thermal');
    }

    function addInvoiceRow() {
        const container = document.getElementById('invoiceRowsContainer');
        const div = document.createElement('div');
        div.className = 'grid grid-cols-12 gap-2 items-center bg-white p-2 rounded-lg border text-xs';
        div.id = `invRow_${rowIndex}`;

        let options = '';
        availableProducts.forEach(p => {
            options += `<option value="${p.id}" data-price="${p.sale_price}" data-unit="${p.unit}">${p.name} (${p.sale_price} ${currency})</option>`;
        });

        div.innerHTML = `
            <div class="col-span-5">
                <select name="items[${rowIndex}][product_id]" onchange="updateRowPrice(${rowIndex}, this)" class="w-full p-1.5 border rounded">
                    ${options}
                </select>
                <input type="hidden" name="items[${rowIndex}][name]" id="rowName_${rowIndex}" value="${availableProducts[0]?.name || ''}">
                <input type="hidden" name="items[${rowIndex}][unit]" id="rowUnit_${rowIndex}" value="${availableProducts[0]?.unit || 'قطعة'}">
            </div>
            <div class="col-span-2">
                <input type="number" min="1" value="1" name="items[${rowIndex}][qty]" id="rowQty_${rowIndex}" oninput="calcInvoiceTotals()" class="w-full p-1.5 border rounded text-center font-mono">
            </div>
            <div class="col-span-2">
                <input type="number" step="0.01" value="${availableProducts[0]?.sale_price || 0}" name="items[${rowIndex}][price]" id="rowPrice_${rowIndex}" oninput="calcInvoiceTotals()" class="w-full p-1.5 border rounded text-center font-mono">
            </div>
            <div class="col-span-2 text-center font-bold font-mono text-[#1F2E4A]" id="rowTotal_${rowIndex}">
                ${availableProducts[0]?.sale_price || 0}
            </div>
            <div class="col-span-1 text-center">
                <button type="button" onclick="removeInvoiceRow(${rowIndex})" class="text-red-500 font-bold hover:text-red-700">&times;</button>
            </div>
        `;

        container.appendChild(div);
        rowIndex++;
        calcInvoiceTotals();
    }

    function removeInvoiceRow(idx) {
        const row = document.getElementById(`invRow_${idx}`);
        if (row) {
            row.remove();
            calcInvoiceTotals();
        }
    }

    function updateRowPrice(idx, select) {
        const option = select.options[select.selectedIndex];
        const price = option.dataset.price;
        const unit = option.dataset.unit;
        document.getElementById(`rowPrice_${idx}`).value = price;
        document.getElementById(`rowName_${idx}`).value = option.text;
        document.getElementById(`rowUnit_${idx}`).value = unit;
        calcInvoiceTotals();
    }

    function calcInvoiceTotals() {
        let subtotal = 0;
        for (let i = 0; i < rowIndex; i++) {
            const row = document.getElementById(`invRow_${i}`);
            if (row) {
                const qty = parseFloat(document.getElementById(`rowQty_${i}`).value) || 0;
                const price = parseFloat(document.getElementById(`rowPrice_${i}`).value) || 0;
                const lineTot = qty * price;
                document.getElementById(`rowTotal_${i}`).innerText = lineTot.toFixed(2);
                subtotal += lineTot;
            }
        }

        const tax = (subtotal * taxRate) / 100;
        const grand = subtotal + tax;

        document.getElementById('invFormSubtotalDisplay').innerText = subtotal.toFixed(2) + ' ' + currency;
        document.getElementById('invFormTaxDisplay').innerText = tax.toFixed(2) + ' ' + currency;
        document.getElementById('invFormGrandDisplay').innerText = grand.toFixed(2) + ' ' + currency;

        document.getElementById('invHiddenSubtotal').value = subtotal.toFixed(2);
        document.getElementById('invHiddenTax').value = tax.toFixed(2);
        document.getElementById('invHiddenGrand').value = grand.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', () => {
        addInvoiceRow();
    });
</script>
@endsection

