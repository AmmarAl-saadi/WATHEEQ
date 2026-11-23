@extends('layouts.app')

@section('title', ($type === 'purchase' ? 'فاتورة مشتريات' : 'فاتورة مبيعات') . ' | وثيق ERP')

@section('content')
<div class="space-y-3 font-sans text-xs" dir="rtl">

    <form method="POST" action="{{ route('erp.invoices.store') }}" id="watheeqInvoiceForm">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">

        <!-- Top Tool Command Ribbon (مطابق للشريط العلوي بوثيق) -->
        <div class="bg-white p-2 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-2 no-print">
            <div class="flex items-center gap-1.5 flex-wrap">
                <button type="submit" class="px-4 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
                    حفظ
                </button>
                <button type="button" onclick="window.print()" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    طباعة
                </button>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    ترحيل
                </button>
                <a href="{{ route('erp.invoices.editor', ['type' => $type]) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    جديد
                </a>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    إلغاء
                </a>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    مرفقات
                </button>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    إكسل
                </button>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    طباعة الباركود
                </button>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    طباعة بيانات خاصة
                </button>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 bg-emerald-500 text-white font-bold rounded text-[11px]">
                    جديد
                </span>
                <span class="font-bold text-gray-700 text-sm">
                    {{ $type === 'purchase' ? 'فاتورة مشتريات' : 'فاتورة مبيعات ضريبية' }}
                </span>
            </div>
        </div>

        <!-- Master Document Header Meta Box (مطابق لترويسة الفاتورة بالصورة) -->
        <div class="bg-[#F8F9FA] p-3.5 rounded-xl border border-gray-200 shadow-sm space-y-2.5">
            <!-- Row 1: رقم الفاتورة، التاريخ، العميل/المورد -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-24 font-bold text-gray-600 text-left">رقم الفاتورة:</span>
                    <input type="text" name="invoice_no" value="INV-{{ date('Ymd') }}-{{ sprintf('%03d', $nextInvoiceNo) }}" readonly class="flex-1 bg-white border border-gray-300 rounded px-2.5 py-1 font-mono font-bold text-gray-800">
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-16 font-bold text-gray-600 text-left">تاريخها:</span>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono text-gray-800">
                </div>

                <div class="md:col-span-6 flex items-center gap-2">
                    <span class="w-16 font-bold text-gray-600 text-left">{{ $type === 'purchase' ? 'المورد:' : 'العميل:' }}</span>
                    <select name="contact_id" required class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
                        @foreach($contacts as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} (رصيد: {{ number_format($c->balance, 2) }} {{ $tenant->currency ?? 'JOD' }})</option>
                        @endforeach
                    </select>
                    <input type="text" placeholder="الرصيد القائم..." readonly class="w-24 bg-gray-100 border border-gray-300 rounded px-2 py-1 font-mono text-[11px] text-gray-600">
                </div>
            </div>

            <!-- Row 2: دفتر السندات، العملة، سعر الصرف، سند يدوي، طريقة السداد -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-24 font-bold text-gray-600 text-left">دفتر السندات:</span>
                    <select class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold">
                        <option>الدفتر الرئيسي المعتمد</option>
                        <option>دفتر المشتريات الخارجية</option>
                    </select>
                </div>

                <div class="md:col-span-2 flex items-center gap-1.5">
                    <span class="font-bold text-gray-600">العملة:</span>
                    <select name="currency" class="flex-1 bg-white border border-gray-300 rounded px-1.5 py-1 font-bold font-mono">
                        <option value="JOD">JOD</option>
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                        <option value="ILS">ILS</option>
                    </select>
                </div>

                <div class="md:col-span-2 flex items-center gap-1.5">
                    <span class="font-bold text-gray-600">سعر الصرف:</span>
                    <input type="number" step="0.001" value="1.000" class="w-20 bg-white border border-gray-300 rounded px-1.5 py-1 font-mono">
                </div>

                <div class="md:col-span-2 flex items-center gap-2">
                    <span class="font-bold text-gray-600">سند يدوي:</span>
                    <input type="text" placeholder="رقم دفتري" class="w-24 bg-white border border-gray-300 rounded px-2 py-1 font-mono">
                </div>

                <div class="md:col-span-3 flex items-center gap-3">
                    <label class="flex items-center gap-1 text-gray-700 font-bold cursor-pointer">
                        <input type="radio" name="payment_method" value="cash" checked> فاتورة نقدية
                    </label>
                    <label class="flex items-center gap-1 text-gray-700 font-bold cursor-pointer">
                        <input type="radio" name="payment_method" value="credit"> ذمم آجل
                    </label>
                    <label class="flex items-center gap-1 text-gray-500 cursor-pointer">
                        <input type="checkbox"> مسدد
                    </label>
                </div>
            </div>

            <!-- Row 3: مركز التكلفة، المندوب، الحالة، والتصنيف الضريبي -->
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
                    <span class="w-16 font-bold text-gray-600 text-left">المندوب:</span>
                    <input type="text" placeholder="اسم المندوب أو البائع" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1">
                </div>

                <div class="md:col-span-4 flex items-center gap-3">
                    <span class="font-bold text-gray-600">التصنيف الضريبي:</span>
                    <select class="bg-white border border-gray-300 rounded px-2 py-1 font-bold">
                        <option>تجارة خاضعة للضريبة العامة 16%</option>
                        <option>معفاة ضريبياً</option>
                        <option>صفرية</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Hardware Barcode & QR Scanner Bar -->
        <div class="bg-blue-50/70 p-3 rounded-xl border border-blue-200/80 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 no-print">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-bold text-[#1F2E4A]">مسح رمز QR أو الباركود لإضافة المادة تلقائياً إلى الفاتورة:</span>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto flex-1 sm:max-w-md">
                <input 
                    type="text" 
                    id="invoiceBarcodeInput" 
                    placeholder="امسح الباركود / QR بجهاز القارئ (Scanner) أو اكتب واضغط Enter..."
                    class="flex-1 px-3 py-1.5 text-xs bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1F2E4A] font-mono font-bold"
                >
                <button type="button" onclick="openInvoiceCameraModal()" class="px-2.5 py-1.5 bg-white hover:bg-gray-100 text-gray-700 border border-gray-300 text-[11px] font-bold rounded-lg whitespace-nowrap transition">
                    كاميرا
                </button>
                <button type="button" onclick="document.getElementById('testInvoiceQrModal').classList.remove('hidden')" class="px-2.5 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white text-[11px] font-bold rounded-lg whitespace-nowrap transition shadow-sm">
                    أكواد للتجربة
                </button>
            </div>
        </div>

        <!-- Master Line Items Table (جدول المواد والأصناف المطابق لوثيق) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs" id="itemsTable">
                    <thead class="bg-[#1F2E4A] text-white font-bold text-[11px]">
                        <tr>
                            <th class="py-2.5 px-2 text-center w-8">#</th>
                            <th class="py-2.5 px-3 w-32">رقم المادة (كود)</th>
                            <th class="py-2.5 px-3 min-w-[200px]">اسم الصنف / البيان</th>
                            <th class="py-2.5 px-3 w-36">المستودع</th>
                            <th class="py-2.5 px-2 w-24">الوحدة</th>
                            <th class="py-2.5 px-2 w-20 text-center">الكمية</th>
                            <th class="py-2.5 px-2 w-28 text-center font-mono">تاريخ الصلاحية</th>
                            <th class="py-2.5 px-2 w-24 text-center font-mono">Batch</th>
                            <th class="py-2.5 px-2 w-24 text-left font-mono">السعر الفردي</th>
                            <th class="py-2.5 px-2 w-20 text-left font-mono">الخصم %</th>
                            <th class="py-2.5 px-3 w-28 text-left font-mono">المجموع</th>
                            <th class="py-2.5 px-2 text-center w-10">حذف</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 font-medium" id="invoiceRows">
                        <!-- Dynamic Item Row 1 -->
                        <tr class="hover:bg-gray-50/80 transition" id="row_0">
                            <td class="py-2 px-2 text-center font-mono text-gray-400">1</td>
                            <td class="py-2 px-2">
                                <input type="text" name="items[0][code]" value="" placeholder="كود الصنف / الباركود" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 font-mono text-[11px]">
                            </td>
                            <td class="py-2 px-2">
                                <input type="text" name="items[0][name]" value="" placeholder="اسم المادة (امسح الباركود للتعبئة الفورية)" required class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
                            </td>
                            <td class="py-2 px-2">
                                <select class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-[11px]">
                                    <option>المستودع الرئيسي - عمان</option>
                                    <option>مستودع فرع إربد</option>
                                </select>
                            </td>
                            <td class="py-2 px-2">
                                <select name="items[0][unit]" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-[11px]">
                                    <option value="قطعة">قطعة</option>
                                    <option value="صندوق">صندوق</option>
                                    <option value="متر">متر</option>
                                </select>
                            </td>
                            <td class="py-2 px-2">
                                <input type="number" step="1" name="items[0][qty]" id="qty_0" value="1" min="1" oninput="calculateTotals()" required class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-center font-mono font-bold">
                            </td>
                            <td class="py-2 px-2">
                                <input type="date" value="2030-01-01" class="w-full bg-white border border-gray-300 rounded px-1 py-1 font-mono text-[10px]">
                            </td>
                            <td class="py-2 px-2">
                                <input type="text" placeholder="B-001" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 font-mono text-[11px]">
                            </td>
                            <td class="py-2 px-2">
                                <input type="number" step="0.01" name="items[0][price]" id="price_0" value="0.00" min="0" oninput="calculateTotals()" required class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-left font-mono font-bold">
                            </td>
                            <td class="py-2 px-2">
                                <input type="number" step="0.5" id="disc_0" value="0.0" min="0" max="100" oninput="calculateTotals()" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-left font-mono">
                            </td>
                            <td class="py-2 px-3 text-left font-mono font-black text-[#1F2E4A] bg-[#1F2E4A]/5" id="total_0">
                                0.00
                            </td>
                            <td class="py-2 px-2 text-center">
                                <button type="button" onclick="removeRow(0)" class="text-rose-600 hover:text-rose-800 font-bold px-1.5 py-0.5 rounded">×</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-2 border-t border-gray-200 bg-gray-50 flex justify-between items-center no-print">
                <button type="button" onclick="addNewRow()" class="px-3 py-1 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded text-xs font-bold transition">
                    + إضافة سطر مادة جديد
                </button>
                <span class="text-xs text-gray-500 font-mono">عدد المواد: <span id="itemsCount" class="font-bold text-gray-800">1</span></span>
            </div>
        </div>

        <!-- Master Footer Totals Box (مطابق للأسفل بالصورة مع حقول الضريبة والخصم) -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <!-- Column 1: ملاحظات وبيان الفاتورة -->
                <div class="md:col-span-2 space-y-2">
                    <label class="block font-bold text-gray-700">ملاحظات وشروط الفاتورة:</label>
                    <textarea name="notes" rows="3" placeholder="اكتب أي ملاحظات أو شروط تسليم وضمان..." class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2 text-xs"></textarea>
                </div>

                <!-- Column 2 & 3: احتساب المجاميع والضريبة (16%) والخصم -->
                <div class="md:col-span-2 space-y-1.5 bg-gray-50 p-3.5 rounded-xl border border-gray-200">
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold text-gray-600">المجموع قبل الضريبة (Subtotal):</span>
                        <input type="hidden" name="subtotal" id="inputSubtotal" value="0.00">
                        <span class="font-mono font-bold text-gray-900" id="dispSubtotal">0.00 {{ $tenant->currency ?? 'JOD' }}</span>
                    </div>

                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold text-gray-600">قيمة الضريبة المضافة (16% VAT):</span>
                        <input type="hidden" name="tax_total" id="inputTax" value="0.00">
                        <span class="font-mono font-bold text-[#1F2E4A]" id="dispTax">0.00 {{ $tenant->currency ?? 'JOD' }}</span>
                    </div>

                    <div class="border-t border-gray-300 pt-2 flex justify-between items-center text-sm">
                        <span class="font-black text-gray-900">المجموع النهائي بعد الضريبة (Grand Total):</span>
                        <input type="hidden" name="grand_total" id="inputGrandTotal" value="0.00">
                        <span class="font-mono font-black text-lg text-[#1F2E4A] bg-[#1F2E4A]/10 px-3 py-1 rounded-lg" id="dispGrandTotal">
                            0.00 {{ $tenant->currency ?? 'JOD' }}
                        </span>
                    </div>
                </div>

            </div>
        </div>

    </form>

</div>

<!-- Modal: Test Barcodes & QR Codes for Invoicing -->
<div id="testInvoiceQrModal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-2xl max-h-[85vh] overflow-y-auto" dir="rtl">
        <div class="flex justify-between items-center border-b pb-3">
            <div>
                <h4 class="font-bold text-sm text-[#1F2E4A]">أكواد QR والباركود لاختبار القارئ المباشر</h4>
                <p class="text-xs text-gray-500">وجّه قارئ الباركود أو الـ QR نحو الشاشة لتجربة الإضافة التلقائية لجدول الفاتورة</p>
            </div>
            <button type="button" onclick="document.getElementById('testInvoiceQrModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700 font-bold text-xs">[إغلاق]</button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @php
                $sampleProducts = \Illuminate\Support\Facades\DB::table('products')->get();
            @endphp
            @foreach($sampleProducts as $p)
            <div class="p-4 border rounded-xl bg-gray-50 flex flex-col items-center text-center space-y-2 hover:border-[#1F2E4A] transition">
                <span class="text-xs font-bold text-gray-800">{{ $p->name }}</span>
                <span class="text-[10px] text-gray-500 font-mono">{{ $p->code }} • {{ $p->barcode }}</span>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data={{ urlencode($p->barcode ?: $p->code) }}" alt="QR Code" class="w-28 h-28 bg-white p-2 rounded-lg border shadow-sm">
                <span class="text-xs font-black text-[#1F2E4A]">{{ number_format($p->sale_price, 2) }} {{ $tenant->currency ?? 'JOD' }}</span>
                <button type="button" onclick="simulateInvoiceScan('{{ $p->barcode ?: $p->code }}')" class="px-3 py-1 bg-[#1F2E4A] hover:bg-[#343A40] text-white text-[11px] font-bold rounded-lg transition">
                    محاكاة مسح وإضافة المادة
                </button>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal: Device Camera Scanner for Invoicing -->
<div id="invoiceCameraModal" class="hidden fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl" dir="rtl">
        <div class="flex justify-between items-center border-b pb-3">
            <h4 class="font-bold text-sm text-[#1F2E4A]">قارئ الكاميرا المباشر للفاتورة</h4>
            <button type="button" onclick="closeInvoiceCameraModal()" class="text-gray-500 hover:text-gray-700 font-bold text-xs">[إغلاق]</button>
        </div>
        <p class="text-xs text-gray-600">وجّه كاميرا الهاتف أو الجهاز نحو الباركود أو رمز الـ QR على المنتج:</p>
        <div class="bg-black rounded-xl overflow-hidden min-h-[250px] flex items-center justify-center border-2 border-[#1F2E4A] p-1">
            <div id="invoiceCameraVideo" class="w-full"></div>
        </div>
        <button type="button" onclick="closeInvoiceCameraModal()" class="w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg text-xs">
            إيقاف الكاميرا
        </button>
    </div>
</div>

<script>
    let rowIndex = 1;

    document.addEventListener('DOMContentLoaded', function () {
        WatheeqScanner.init(addProductToInvoice, 'invoiceBarcodeInput');
        calculateTotals();
    });

    function openInvoiceCameraModal() {
        document.getElementById('invoiceCameraModal').classList.remove('hidden');
        WatheeqScanner.startCamera('invoiceCameraVideo', function (product) {
            closeInvoiceCameraModal();
            addProductToInvoice(product);
        });
    }

    function closeInvoiceCameraModal() {
        WatheeqScanner.stopCamera('invoiceCameraVideo');
        document.getElementById('invoiceCameraModal').classList.add('hidden');
    }

    function addProductToInvoice(product) {
        const rows = document.querySelectorAll('#invoiceRows tr');
        let existingRowId = null;

        // 1. Check if first row (#row_0) is empty and unpopulated
        const firstRow = document.getElementById('row_0');
        if (firstRow) {
            const firstCode = firstRow.querySelector('input[name="items[0][code]"]')?.value.trim();
            const firstName = firstRow.querySelector('input[name="items[0][name]"]')?.value.trim();
            if (!firstCode && !firstName) {
                firstRow.querySelector('input[name="items[0][code]"]').value = product.code || product.barcode;
                firstRow.querySelector('input[name="items[0][name]"]').value = product.name;
                firstRow.querySelector('input[name="items[0][price]"]').value = parseFloat(product.sale_price).toFixed(2);
                firstRow.querySelector('input[name="items[0][qty]"]').value = 1;
                calculateTotals();
                WatheeqScanner.playBeep('success');
                WatheeqScanner.showToast(`تمت إضافة المادة: ${product.name} للفاتورة`, true);
                firstRow.classList.add('bg-blue-100');
                setTimeout(() => firstRow.classList.remove('bg-blue-100'), 600);
                return;
            }
        }

        // 2. Check if product already exists in any row
        rows.forEach(tr => {
            const id = tr.id.split('_')[1];
            const codeInput = tr.querySelector(`input[name="items[${id}][code]"]`);
            const nameInput = tr.querySelector(`input[name="items[${id}][name]"]`);
            if (codeInput && (codeInput.value.trim().toLowerCase() === product.code.toLowerCase() || (product.barcode && codeInput.value.trim() === product.barcode))) {
                existingRowId = id;
            } else if (nameInput && nameInput.value.trim() === product.name) {
                existingRowId = id;
            }
        });

        if (existingRowId !== null) {
            // Product already exists in the invoice: increment quantity
            const qtyInput = document.getElementById(`qty_${existingRowId}`);
            if (qtyInput) {
                qtyInput.value = parseInt(qtyInput.value || 1) + 1;
                calculateTotals();
                WatheeqScanner.playBeep('increment');
                WatheeqScanner.showToast(`تمت زيادة كمية: ${product.name} (+1)`, true);

                const tr = document.getElementById(`row_${existingRowId}`);
                tr.classList.add('bg-blue-100');
                setTimeout(() => tr.classList.remove('bg-blue-100'), 600);
            }
        } else {
            // New product: insert row
            const tbody = document.getElementById('invoiceRows');
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50/80 transition bg-emerald-50/40';
            tr.id = `row_${rowIndex}`;
            tr.innerHTML = `
                <td class="py-2 px-2 text-center font-mono text-gray-400">${rowIndex + 1}</td>
                <td class="py-2 px-2">
                    <input type="text" name="items[${rowIndex}][code]" value="${product.code || product.barcode}" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 font-mono text-[11px] font-bold">
                </td>
                <td class="py-2 px-2">
                    <input type="text" name="items[${rowIndex}][name]" value="${product.name}" required class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
                </td>
                <td class="py-2 px-2">
                    <select class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-[11px]">
                        <option>${product.warehouse || 'المستودع الرئيسي - عمان'}</option>
                        <option>مستودع فرع إربد</option>
                    </select>
                </td>
                <td class="py-2 px-2">
                    <select name="items[${rowIndex}][unit]" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-[11px]">
                        <option value="${product.unit || 'قطعة'}">${product.unit || 'قطعة'}</option>
                        <option value="صندوق">صندوق</option>
                        <option value="متر">متر</option>
                    </select>
                </td>
                <td class="py-2 px-2">
                    <input type="number" step="1" name="items[${rowIndex}][qty]" id="qty_${rowIndex}" value="1" min="1" oninput="calculateTotals()" required class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-center font-mono font-bold">
                </td>
                <td class="py-2 px-2">
                    <input type="date" value="2030-01-01" class="w-full bg-white border border-gray-300 rounded px-1 py-1 font-mono text-[10px]">
                </td>
                <td class="py-2 px-2">
                    <input type="text" placeholder="B-00${rowIndex + 1}" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 font-mono text-[11px]">
                </td>
                <td class="py-2 px-2">
                    <input type="number" step="0.01" name="items[${rowIndex}][price]" id="price_${rowIndex}" value="${(product.sale_price || 0).toFixed(2)}" min="0" oninput="calculateTotals()" required class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-left font-mono font-bold">
                </td>
                <td class="py-2 px-2">
                    <input type="number" step="0.5" id="disc_${rowIndex}" value="0.0" min="0" max="100" oninput="calculateTotals()" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-left font-mono">
                </td>
                <td class="py-2 px-3 text-left font-mono font-black text-[#1F2E4A] bg-[#1F2E4A]/5" id="total_${rowIndex}">
                    ${(product.sale_price || 0).toFixed(2)}
                </td>
                <td class="py-2 px-2 text-center">
                    <button type="button" onclick="removeRow(${rowIndex})" class="text-rose-600 hover:text-rose-800 font-bold px-1.5 py-0.5 rounded">×</button>
                </td>
            `;
            tbody.appendChild(tr);
            setTimeout(() => tr.classList.remove('bg-emerald-50/40'), 600);
            rowIndex++;
            calculateTotals();
            WatheeqScanner.playBeep('success');
            WatheeqScanner.showToast(`تمت إضافة المادة: ${product.name} للفاتورة`, true);
        }

        const input = document.getElementById('invoiceBarcodeInput');
        if (input) {
            input.value = '';
            input.focus();
        }
    }

    function simulateInvoiceScan(code) {
        document.getElementById('testInvoiceQrModal').classList.add('hidden');
        WatheeqScanner.lookup(code).then(res => {
            if (res && res.found) {
                addProductToInvoice(res.product);
            } else {
                WatheeqScanner.playBeep('error');
                WatheeqScanner.showToast('الصنف غير موجود: ' + code, false);
            }
        });
    }

    function addNewRow() {
        const tbody = document.getElementById('invoiceRows');
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50/80 transition';
        tr.id = `row_${rowIndex}`;
        tr.innerHTML = `
            <td class="py-2 px-2 text-center font-mono text-gray-400">${rowIndex + 1}</td>
            <td class="py-2 px-2">
                <input type="text" name="items[${rowIndex}][code]" value="ITM-${100 + rowIndex + 1}" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 font-mono text-[11px]">
            </td>
            <td class="py-2 px-2">
                <input type="text" name="items[${rowIndex}][name]" placeholder="اسم الصنف أو الخدمة..." required class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
            </td>
            <td class="py-2 px-2">
                <select class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-[11px]">
                    <option>المستودع الرئيسي - عمان</option>
                    <option>مستودع فرع إربد</option>
                </select>
            </td>
            <td class="py-2 px-2">
                <select name="items[${rowIndex}][unit]" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-[11px]">
                    <option value="قطعة">قطعة</option>
                    <option value="صندوق">صندوق</option>
                    <option value="متر">متر</option>
                </select>
            </td>
            <td class="py-2 px-2">
                <input type="number" step="1" name="items[${rowIndex}][qty]" id="qty_${rowIndex}" value="1" min="1" oninput="calculateTotals()" required class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-center font-mono font-bold">
            </td>
            <td class="py-2 px-2">
                <input type="date" value="2030-01-01" class="w-full bg-white border border-gray-300 rounded px-1 py-1 font-mono text-[10px]">
            </td>
            <td class="py-2 px-2">
                <input type="text" placeholder="B-00${rowIndex + 1}" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 font-mono text-[11px]">
            </td>
            <td class="py-2 px-2">
                <input type="number" step="0.01" name="items[${rowIndex}][price]" id="price_${rowIndex}" value="50.00" min="0" oninput="calculateTotals()" required class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-left font-mono font-bold">
            </td>
            <td class="py-2 px-2">
                <input type="number" step="0.5" id="disc_${rowIndex}" value="0.0" min="0" max="100" oninput="calculateTotals()" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-left font-mono">
            </td>
            <td class="py-2 px-3 text-left font-mono font-black text-[#1F2E4A] bg-[#1F2E4A]/5" id="total_${rowIndex}">
                50.00
            </td>
            <td class="py-2 px-2 text-center">
                <button type="button" onclick="removeRow(${rowIndex})" class="text-rose-600 hover:text-rose-800 font-bold px-1.5 py-0.5 rounded">×</button>
            </td>
        `;
        tbody.appendChild(tr);
        rowIndex++;
        calculateTotals();
    }

    function removeRow(idx) {
        const row = document.getElementById(`row_${idx}`);
        if (row) {
            row.remove();
            calculateTotals();
        }
    }

    function calculateTotals() {
        let subtotal = 0;
        const rows = document.querySelectorAll('#invoiceRows tr');
        document.getElementById('itemsCount').innerText = rows.length;

        rows.forEach(tr => {
            const id = tr.id.split('_')[1];
            const qtyInput = document.getElementById(`qty_${id}`);
            const priceInput = document.getElementById(`price_${id}`);
            const discInput = document.getElementById(`disc_${id}`);
            const totalCell = document.getElementById(`total_${id}`);

            if (qtyInput && priceInput) {
                const qty = parseFloat(qtyInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const disc = parseFloat(discInput?.value || 0) || 0;

                const lineTotal = (qty * price) * (1 - disc / 100);
                if (totalCell) totalCell.innerText = lineTotal.toFixed(2);
                subtotal += lineTotal;
            }
        });

        const taxRate = 0.16; // 16% Jordanian VAT
        const taxTotal = subtotal * taxRate;
        const grandTotal = subtotal + taxTotal;

        const currency = "{{ $tenant->currency ?? 'JOD' }}";
        document.getElementById('inputSubtotal').value = subtotal.toFixed(2);
        document.getElementById('dispSubtotal').innerText = `${subtotal.toFixed(2)} ${currency}`;

        document.getElementById('inputTax').value = taxTotal.toFixed(2);
        document.getElementById('dispTax').innerText = `${taxTotal.toFixed(2)} ${currency}`;

        document.getElementById('inputGrandTotal').value = grandTotal.toFixed(2);
        document.getElementById('dispGrandTotal').innerText = `${grandTotal.toFixed(2)} ${currency}`;
    }
</script>
@endsection



