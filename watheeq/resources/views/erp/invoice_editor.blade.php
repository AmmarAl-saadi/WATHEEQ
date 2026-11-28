@extends('layouts.app')

@section('title', ($type === 'purchase' ? 'فاتورة مشتريات' : ($type === 'return_sale' ? 'مردودات مبيعات' : ($type === 'return_purchase' ? 'مردودات مشتريات' : 'فاتورة مبيعات'))) . ' | وثيق ERP')

@section('content')
<div class="space-y-3 font-sans text-xs" dir="rtl">

    <form method="POST" action="{{ route('erp.invoices.store') }}" id="watheeqInvoiceForm">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="hidden" name="settlement_type" id="hiddenSettlementType" value="credit">
        <input type="hidden" name="settlement_data" id="hiddenSettlementData" value="">

        <!-- Top Tool Command Ribbon (مطابق بالمللي لشريط تحكم وثيق / حساباتي Frame 1i) -->
        <div class="bg-white p-2 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-2 no-print">
            <div class="flex items-center gap-1.5 flex-wrap">
                <!-- Navigation controls (الأول، السابق، استعلام، التالي، الأخير) -->
                <button type="button" onclick="navigateInvoice('first')" title="السند الأول" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition font-mono">
                    |&lt;&lt;
                </button>
                <button type="button" onclick="navigateInvoice('prev')" title="السند السابق" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition font-mono">
                    &lt;&lt;
                </button>
                <button type="button" onclick="openInvoiceQueryModal()" class="px-3 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-800 rounded font-bold border border-sky-200 transition">
                    استعلام
                </button>
                <button type="button" onclick="navigateInvoice('next')" title="السند التالي" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition font-mono">
                    &gt;&gt;
                </button>
                <button type="button" onclick="navigateInvoice('last')" title="السند الأخير" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition font-mono">
                    &gt;&gt;|
                </button>

                <div class="h-6 w-px bg-gray-300 mx-1"></div>

                <button type="submit" class="px-4 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
                    حفظ
                </button>
                <button type="button" onclick="window.print()" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    طباعة
                </button>
                <button type="button" onclick="confirmPostInvoice()" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 rounded font-bold border border-emerald-300 transition">
                    ترحيل
                </button>
                <a href="{{ route('erp.invoices.editor', ['type' => $type]) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    جديد
                </a>
                <button type="button" onclick="resetForm()" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded font-bold border border-rose-200 transition">
                    إلغاء
                </button>
                <button type="button" onclick="openAttachmentsModal()" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    مرفقات
                </button>
                <button type="button" onclick="exportToExcel()" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded font-bold transition">
                    إكسل
                </button>
                <button type="button" onclick="openPrintSpecialModal()" class="px-3 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-800 rounded font-bold border border-purple-200 transition">
                    طباعة بيانات خاصة
                </button>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 bg-emerald-500 text-white font-bold rounded text-[11px] animate-pulse">
                    جديد
                </span>
                <span class="font-bold text-gray-800 text-sm">
                    {{ $type === 'purchase' ? 'فاتورة مشتريات' : ($type === 'return_sale' ? 'مردودات مبيعات' : ($type === 'return_purchase' ? 'مردودات مشتريات' : 'فاتورة مبيعات ضريبية')) }}
                </span>
            </div>
        </div>

        <!-- Master Document Header Meta Box -->
        <div class="bg-[#F8F9FA] p-3.5 rounded-xl border border-gray-200 shadow-sm space-y-2.5">
            <!-- Row 1: رقم الفاتورة، التاريخ، العميل/المورد -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-24 font-bold text-gray-600 text-left">رقم الفاتورة:</span>
                    <input type="text" name="invoice_no" id="invoiceNoInput" value="INV-{{ date('Ymd') }}-{{ sprintf('%03d', $nextInvoiceNo) }}" readonly class="flex-1 bg-white border border-gray-300 rounded px-2.5 py-1 font-mono font-bold text-gray-800">
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-16 font-bold text-gray-600 text-left">تاريخها:</span>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono text-gray-800">
                </div>

                <div class="md:col-span-6 flex items-center gap-2">
                    <span class="w-16 font-bold text-gray-600 text-left">{{ $type === 'purchase' ? 'المورد:' : 'العميل:' }}</span>
                    <select name="contact_id" id="contactSelect" onchange="updateContactBalance()" required class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
                        @foreach($contacts as $c)
                            <option value="{{ $c->id }}" data-balance="{{ $c->balance }}">{{ $c->name }} (رصيد: {{ number_format($c->balance, 2) }} {{ $tenant->currency ?? 'JOD' }})</option>
                        @endforeach
                    </select>
                    <input type="text" id="contactBalanceField" placeholder="الرصيد القائم..." readonly class="w-28 bg-gray-100 border border-gray-300 rounded px-2 py-1 font-mono text-[11px] text-gray-700 font-bold text-center">
                </div>
            </div>

            <!-- Row 2: دفتر السندات، العملة، سعر الصرف، سند يدوي، المستودع -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-24 font-bold text-gray-600 text-left">دفتر السندات:</span>
                    <select class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold">
                        <option>الدفتر الرئيسي المعتمد</option>
                        <option>دفتر المبيعات الخارجية</option>
                    </select>
                </div>

                <div class="md:col-span-2 flex items-center gap-1.5">
                    <span class="font-bold text-gray-600">العملة:</span>
                    <select name="currency_code" id="currencySelect" onchange="updateExchangeRate()" class="flex-1 bg-white border border-gray-300 rounded px-1.5 py-1 font-bold font-mono">
                        <option value="JOD" data-rate="1.000">JOD</option>
                        <option value="USD" data-rate="0.709">USD</option>
                        <option value="EUR" data-rate="0.770">EUR</option>
                        <option value="SAR" data-rate="0.189">SAR</option>
                        <option value="AED" data-rate="0.193">AED</option>
                    </select>
                </div>

                <div class="md:col-span-2 flex items-center gap-1.5">
                    <span class="font-bold text-gray-600">سعر الصرف:</span>
                    <input type="number" step="0.0001" name="exchange_rate" id="exchangeRateInput" value="1.0000" class="w-24 bg-white border border-gray-300 rounded px-1.5 py-1 font-mono font-bold text-center">
                </div>

                <div class="md:col-span-2 flex items-center gap-2">
                    <span class="font-bold text-gray-600">سند يدوي:</span>
                    <input type="text" placeholder="رقم دفتري" class="w-24 bg-white border border-gray-300 rounded px-2 py-1 font-mono">
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="font-bold text-gray-600">المستودع الرئيسي:</span>
                    <select name="warehouse_id" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold">
                        <option value="1">المستودع الرئيسي - عمان</option>
                        <option value="2">مستودع المعرض / صالة العرض</option>
                        <option value="3">مستودع فرع إربد</option>
                    </select>
                </div>
            </div>

            <!-- Row 3: مركز التكلفة، المندوب، والتصنيف الضريبي -->
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

                <div class="md:col-span-4 flex items-center justify-between gap-3 bg-white p-1.5 rounded-lg border">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-gray-600">التصنيف:</span>
                        <select class="bg-gray-50 border border-gray-300 rounded px-2 py-0.5 font-bold text-[11px]">
                            <option>خاضع للضريبة العامة 16%</option>
                            <option>معفى ضريبياً</option>
                            <option>نسبة الصفر 0%</option>
                        </select>
                    </div>

                    <!-- Hesabate Tax Inclusive Checkbox -->
                    <label class="flex items-center gap-1.5 text-emerald-800 font-black cursor-pointer bg-emerald-50 px-2 py-0.5 rounded border border-emerald-300 text-[11px]">
                        <input type="checkbox" name="is_tax_inclusive" id="isTaxInclusive" onchange="calculateTotals()" class="w-3.5 h-3.5 text-emerald-600 rounded">
                        <span>شامل الضريبة</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Hardware Barcode & QR Scanner Bar -->
        <div class="bg-blue-50/70 p-2.5 rounded-xl border border-blue-200/80 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2.5 no-print">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-bold text-[#1F2E4A]">مسح رمز QR أو الباركود لإضافة المادة فورياً:</span>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto flex-1 sm:max-w-md">
                <input 
                    type="text" 
                    id="invoiceBarcodeInput" 
                    placeholder="امسح الباركود / QR بجهاز القارئ (Scanner) أو اكتب واضغط Enter..."
                    class="flex-1 px-3 py-1 text-xs bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1F2E4A] font-mono font-bold"
                >
                <button type="button" onclick="openInvoiceCameraModal()" class="px-2.5 py-1 bg-white hover:bg-gray-100 text-gray-700 border border-gray-300 text-[11px] font-bold rounded-lg whitespace-nowrap transition">
                    كاميرا
                </button>
            </div>
        </div>

        <!-- Master Line Items Table (مطابق لجدول حساباتي Frame 1i) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs" id="itemsTable">
                    <thead class="bg-[#1F2E4A] text-white font-bold text-[11px]">
                        <tr>
                            <th class="py-2 px-2 text-center w-8">#</th>
                            <th class="py-2 px-2 w-28">رقم المادة</th>
                            <th class="py-2 px-3 min-w-[200px]">اسم المادة / الصنف</th>
                            <th class="py-2 px-2 w-32">المخزن</th>
                            <th class="py-2 px-2 w-24">الوحدة</th>
                            <th class="py-2 px-2 w-20 text-center">الكمية</th>
                            <th class="py-2 px-2 w-24 text-center font-mono">التاريخ</th>
                            <th class="py-2 px-2 w-20 text-center font-mono">Batch</th>
                            <th class="py-2 px-2 w-24 text-left font-mono">السعر</th>
                            <th class="py-2 px-2 w-20 text-left font-mono">الخصم</th>
                            <th class="py-2 px-3 w-28 text-left font-mono">المجموع</th>
                            <th class="py-2 px-2 text-center w-10">حذف</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 font-medium" id="invoiceRows">
                        <!-- Dynamic Item Row 0 -->
                        <tr class="hover:bg-gray-50/80 transition" id="row_0">
                            <td class="py-1.5 px-2 text-center font-mono text-gray-400">1</td>
                            <td class="py-1.5 px-2">
                                <div class="relative flex items-center">
                                    <input type="text" name="items[0][code]" id="code_0" placeholder="رقم المادة" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 font-mono text-[11px]">
                                    <button type="button" onclick="openProductSelector(0)" class="absolute left-1 text-gray-400 hover:text-gray-700">🔍</button>
                                </div>
                            </td>
                            <td class="py-1.5 px-2">
                                <input type="text" name="items[0][name]" id="name_0" placeholder="اسم المادة" required class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
                            </td>
                            <td class="py-1.5 px-2">
                                <select name="items[0][warehouse_id]" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-[11px]">
                                    <option value="1">الرئيسي</option>
                                    <option value="2">المعرض</option>
                                </select>
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
                                <input type="number" step="1" name="items[0][qty]" id="qty_0" value="1" min="1" oninput="calculateTotals()" required class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-center font-mono font-bold">
                            </td>
                            <td class="py-1.5 px-2">
                                <input type="date" name="items[0][expiry_date]" value="2030-01-01" class="w-full bg-white border border-gray-300 rounded px-1 py-1 font-mono text-[10px]">
                            </td>
                            <td class="py-1.5 px-2">
                                <input type="text" name="items[0][batch_no]" placeholder="B-101" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 font-mono text-[11px]">
                            </td>
                            <td class="py-1.5 px-2">
                                <input type="number" step="0.01" name="items[0][price]" id="price_0" value="0.00" min="0" oninput="calculateTotals()" required class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-left font-mono font-bold">
                            </td>
                            <td class="py-1.5 px-2">
                                <input type="number" step="0.5" id="disc_0" value="0.0" min="0" max="100" oninput="calculateTotals()" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-left font-mono">
                            </td>
                            <td class="py-1.5 px-3 text-left font-mono font-black text-[#1F2E4A] bg-[#1F2E4A]/5" id="total_0">
                                0.00
                            </td>
                            <td class="py-1.5 px-2 text-center">
                                <button type="button" onclick="removeRow(0)" class="text-rose-600 hover:text-rose-800 font-bold px-1.5 py-0.5 rounded text-sm">×</button>
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

        <!-- Master Footer Totals Box (مطابق لحساباتي مع زر طرق التسديد الأزرق البارز) -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-start">

                <!-- Left Column: ملاحظات الفاتورة وحساب المبيعات -->
                <div class="md:col-span-5 space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="w-24 font-bold text-gray-600 text-left">حساب المبيعات:</span>
                        <select class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 text-xs">
                            <option>4101 - إيرادات مبيعات البضاعة العامة</option>
                            <option>4102 - إيرادات الخدمات والاستشارات</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">ملاحظات الفاتورة:</label>
                        <textarea name="notes" rows="2" placeholder="اكتب أي ملاحظات أو شروط تسليم..." class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2 text-xs"></textarea>
                    </div>
                </div>

                <!-- Middle Column: طرق التسديد (Hesabate Style Prominent Blue Button) -->
                <div class="md:col-span-3 flex flex-col justify-center items-center h-full p-3 bg-gray-50 rounded-xl border border-gray-200 space-y-2">
                    <span class="text-xs font-bold text-gray-600">طريقة الدفع والتسوية:</span>
                    <button type="button" onclick="openSettlementModal()" class="w-full py-2.5 px-4 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-black text-sm rounded-xl shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
                        <span>💳</span>
                        <span>طرق التسديد</span>
                    </button>
                    <div id="settlementStatusBadge" class="text-[11px] font-bold text-gray-500">
                        الحالة: <span class="text-amber-600">ذمم آجل (غير مسددة)</span>
                    </div>
                </div>

                <!-- Right Column: مجاميع الفاتورة والضريبة الأردنية 16% -->
                <div class="md:col-span-4 space-y-1.5 bg-[#F8F9FA] p-3 rounded-xl border border-gray-200">
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold text-gray-600">المجموع قبل الخصم:</span>
                        <span class="font-mono font-bold text-gray-800" id="dispSubtotalBeforeDisc">0.00</span>
                    </div>

                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold text-gray-600">الخصم الممنوح:</span>
                        <input type="hidden" name="discount_total" id="inputDiscountTotal" value="0.00">
                        <span class="font-mono font-bold text-rose-600" id="dispDiscountTotal">0.00</span>
                    </div>

                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold text-gray-600">المجموع بعد الخصم:</span>
                        <input type="hidden" name="subtotal" id="inputSubtotal" value="0.00">
                        <span class="font-mono font-bold text-gray-900" id="dispSubtotal">0.00</span>
                    </div>

                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold text-gray-600">قيمة الضريبة (16% ISTD):</span>
                        <input type="hidden" name="tax_total" id="inputTax" value="0.00">
                        <span class="font-mono font-bold text-[#1F2E4A]" id="dispTax">0.00</span>
                    </div>

                    <div class="border-t border-gray-300 pt-1.5 flex justify-between items-center text-sm">
                        <span class="font-black text-gray-900">المجموع بعد الضريبة:</span>
                        <input type="hidden" name="grand_total" id="inputGrandTotal" value="0.00">
                        <span class="font-mono font-black text-lg text-[#1F2E4A] bg-[#1F2E4A]/10 px-3 py-0.5 rounded-lg" id="dispGrandTotal">
                            0.00 JOD
                        </span>
                    </div>
                </div>

            </div>
        </div>

    </form>

</div>

<!-- Modal: طرق التسديد (Settlement Modal - Cash, Credit, Cheques) -->
<div id="settlementModal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full p-5 space-y-4 shadow-2xl" dir="rtl">
        <div class="flex justify-between items-center border-b pb-3">
            <div class="flex items-center gap-2">
                <span class="text-xl">💳</span>
                <h4 class="font-bold text-sm text-[#1F2E4A]">طرق تسديد الفاتورة</h4>
            </div>
            <button type="button" onclick="closeSettlementModal()" class="text-gray-400 hover:text-gray-700 font-bold text-sm">✕</button>
        </div>

        <div class="p-3 bg-blue-50/70 border border-blue-200 rounded-xl flex justify-between items-center">
            <span class="font-bold text-xs text-blue-900">مجموع الفاتورة المستحق:</span>
            <span class="font-mono font-black text-base text-[#1F2E4A]" id="modalDueAmount">0.00 JOD</span>
        </div>

        <!-- Settlement Options Tabs -->
        <div class="space-y-3">
            <div class="flex border-b text-xs font-bold">
                <button type="button" onclick="switchSettlementTab('credit')" id="tabCredit" class="flex-1 py-2 text-center border-b-2 border-[#1F2E4A] text-[#1F2E4A]">
                    ذمم آجل (على الحساب)
                </button>
                <button type="button" onclick="switchSettlementTab('cash')" id="tabCash" class="flex-1 py-2 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-800">
                    نقدي (الصندوق)
                </button>
                <button type="button" onclick="switchSettlementTab('cheque')" id="tabCheque" class="flex-1 py-2 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-800">
                    شيك بنكي
                </button>
            </div>

            <!-- Content Credit -->
            <div id="contentCredit" class="p-3 space-y-2 text-xs">
                <p class="text-gray-600">سيتم ترحيل الفاتورة كـ <span class="font-bold text-gray-800">ذمم مدينة على حساب العميل</span> دون تحصيل نقدي فوري.</p>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-gray-600">تاريخ الاستحقاق المتفق عليه:</span>
                    <input type="date" id="creditDueDate" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="bg-white border rounded px-2 py-1 font-mono">
                </div>
            </div>

            <!-- Content Cash -->
            <div id="contentCash" class="hidden p-3 space-y-2 text-xs">
                <div class="flex items-center justify-between gap-2">
                    <span class="font-bold text-gray-600">الصندوق المستلم:</span>
                    <select id="cashRegisterSelect" class="flex-1 bg-white border rounded px-2 py-1 font-bold">
                        <option>الصندوق الرئيسي (كاش)</option>
                        <option>صندوق المعرض والفرع</option>
                    </select>
                </div>
                <div class="flex items-center justify-between gap-2">
                    <span class="font-bold text-gray-600">المبلغ المقبوض:</span>
                    <input type="number" step="0.01" id="cashReceivedInput" class="w-36 bg-white border rounded px-2 py-1 font-mono font-bold text-left">
                </div>
            </div>

            <!-- Content Cheque -->
            <div id="contentCheque" class="hidden p-3 space-y-2 text-xs">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-gray-600 mb-1">رقم الشيك:</label>
                        <input type="text" id="chequeNoInput" placeholder="CHQ-00129" class="w-full bg-white border rounded px-2 py-1 font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-600 mb-1">اسم البنك المسحوب عليه:</label>
                        <input type="text" id="chequeBankInput" placeholder="البنك العربي / بنك الإسكان" class="w-full bg-white border rounded px-2 py-1">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-600 mb-1">تاريخ استحقاق الشيك:</label>
                        <input type="date" id="chequeDueDateInput" value="{{ date('Y-m-d') }}" class="w-full bg-white border rounded px-2 py-1 font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-600 mb-1">قيمة الشيك:</label>
                        <input type="number" step="0.01" id="chequeAmountInput" class="w-full bg-white border rounded px-2 py-1 font-mono font-bold text-left">
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t pt-3 flex justify-end gap-2">
            <button type="button" onclick="closeSettlementModal()" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg text-xs">إلغاء</button>
            <button type="button" onclick="applySettlement()" class="px-5 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white font-bold rounded-lg text-xs shadow-sm">تثبيت طريقة التسديد</button>
        </div>
    </div>
</div>

<script>
    let rowIndex = 1;
    let currentSettlementTab = 'credit';

    document.addEventListener('DOMContentLoaded', function () {
        updateContactBalance();
        calculateTotals();
    });

    function updateContactBalance() {
        const select = document.getElementById('contactSelect');
        const selected = select.options[select.selectedIndex];
        const balance = selected ? selected.getAttribute('data-balance') : 0;
        document.getElementById('contactBalanceField').value = Number(balance).toLocaleString('en-US', {minimumFractionDigits: 2}) + ' JOD';
    }

    function updateExchangeRate() {
        const sel = document.getElementById('currencySelect');
        const rate = sel.options[sel.selectedIndex].getAttribute('data-rate');
        document.getElementById('exchangeRateInput').value = rate || 1.0000;
        calculateTotals();
    }

    function calculateTotals() {
        let subtotalBeforeDisc = 0;
        let totalDiscount = 0;
        const isInclusive = document.getElementById('isTaxInclusive').checked;

        const rows = document.querySelectorAll('#invoiceRows tr');
        rows.forEach(function (row) {
            const qty = parseFloat(row.querySelector('input[id^="qty_"]').value) || 0;
            const price = parseFloat(row.querySelector('input[id^="price_"]').value) || 0;
            const discPercent = parseFloat(row.querySelector('input[id^="disc_"]').value) || 0;

            const rowGross = qty * price;
            const rowDisc = rowGross * (discPercent / 100);
            const rowNet = rowGross - rowDisc;

            subtotalBeforeDisc += rowGross;
            totalDiscount += rowDisc;

            const totalCell = row.querySelector('td[id^="total_"]');
            if (totalCell) {
                totalCell.innerText = rowNet.toFixed(2);
            }
        });

        const netAfterDisc = subtotalBeforeDisc - totalDiscount;
        let taxAmount = 0;
        let grandTotal = 0;

        if (isInclusive) {
            // Price already includes 16% VAT: Tax = Net - (Net / 1.16)
            const baseAmount = netAfterDisc / 1.16;
            taxAmount = netAfterDisc - baseAmount;
            grandTotal = netAfterDisc;
        } else {
            // Price is exclusive: Tax = Net * 0.16
            taxAmount = netAfterDisc * 0.16;
            grandTotal = netAfterDisc + taxAmount;
        }

        const curr = document.getElementById('currencySelect').value;

        document.getElementById('dispSubtotalBeforeDisc').innerText = subtotalBeforeDisc.toFixed(2) + ' ' + curr;
        document.getElementById('dispDiscountTotal').innerText = totalDiscount.toFixed(2) + ' ' + curr;
        document.getElementById('inputDiscountTotal').value = totalDiscount.toFixed(2);

        document.getElementById('dispSubtotal').innerText = netAfterDisc.toFixed(2) + ' ' + curr;
        document.getElementById('inputSubtotal').value = netAfterDisc.toFixed(2);

        document.getElementById('dispTax').innerText = taxAmount.toFixed(2) + ' ' + curr;
        document.getElementById('inputTax').value = taxAmount.toFixed(2);

        document.getElementById('dispGrandTotal').innerText = grandTotal.toFixed(2) + ' ' + curr;
        document.getElementById('inputGrandTotal').value = grandTotal.toFixed(2);

        document.getElementById('modalDueAmount').innerText = grandTotal.toFixed(2) + ' ' + curr;
    }

    function addNewRow() {
        const tbody = document.getElementById('invoiceRows');
        const count = tbody.querySelectorAll('tr').length;
        const newRow = document.createElement('tr');
        newRow.className = "hover:bg-gray-50/80 transition";
        newRow.id = "row_" + count;
        newRow.innerHTML = `
            <td class="py-1.5 px-2 text-center font-mono text-gray-400">${count + 1}</td>
            <td class="py-1.5 px-2">
                <div class="relative flex items-center">
                    <input type="text" name="items[${count}][code]" id="code_${count}" placeholder="رقم المادة" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 font-mono text-[11px]">
                    <button type="button" onclick="openProductSelector(${count})" class="absolute left-1 text-gray-400 hover:text-gray-700">🔍</button>
                </div>
            </td>
            <td class="py-1.5 px-2">
                <input type="text" name="items[${count}][name]" id="name_${count}" placeholder="اسم المادة" required class="w-full bg-white border border-gray-300 rounded px-2 py-1 font-bold text-gray-800">
            </td>
            <td class="py-1.5 px-2">
                <select name="items[${count}][warehouse_id]" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-[11px]">
                    <option value="1">الرئيسي</option>
                    <option value="2">المعرض</option>
                </select>
            </td>
            <td class="py-1.5 px-2">
                <select name="items[${count}][unit]" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-[11px]">
                    <option value="قطعة">قطعة</option>
                    <option value="صندوق">صندوق</option>
                    <option value="كيلو">كيلو</option>
                    <option value="متر">متر</option>
                </select>
            </td>
            <td class="py-1.5 px-2">
                <input type="number" step="1" name="items[${count}][qty]" id="qty_${count}" value="1" min="1" oninput="calculateTotals()" required class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-center font-mono font-bold">
            </td>
            <td class="py-1.5 px-2">
                <input type="date" name="items[${count}][expiry_date]" value="2030-01-01" class="w-full bg-white border border-gray-300 rounded px-1 py-1 font-mono text-[10px]">
            </td>
            <td class="py-1.5 px-2">
                <input type="text" name="items[${count}][batch_no]" placeholder="B-101" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 font-mono text-[11px]">
            </td>
            <td class="py-1.5 px-2">
                <input type="number" step="0.01" name="items[${count}][price]" id="price_${count}" value="0.00" min="0" oninput="calculateTotals()" required class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-left font-mono font-bold">
            </td>
            <td class="py-1.5 px-2">
                <input type="number" step="0.5" id="disc_${count}" value="0.0" min="0" max="100" oninput="calculateTotals()" class="w-full bg-white border border-gray-300 rounded px-1.5 py-1 text-left font-mono">
            </td>
            <td class="py-1.5 px-3 text-left font-mono font-black text-[#1F2E4A] bg-[#1F2E4A]/5" id="total_${count}">
                0.00
            </td>
            <td class="py-1.5 px-2 text-center">
                <button type="button" onclick="removeRow(${count})" class="text-rose-600 hover:text-rose-800 font-bold px-1.5 py-0.5 rounded text-sm">×</button>
            </td>
        `;
        tbody.appendChild(newRow);
        document.getElementById('itemsCount').innerText = tbody.querySelectorAll('tr').length;
    }

    function removeRow(idx) {
        const rows = document.querySelectorAll('#invoiceRows tr');
        if (rows.length > 1) {
            document.getElementById('row_' + idx).remove();
            calculateTotals();
            document.getElementById('itemsCount').innerText = document.querySelectorAll('#invoiceRows tr').length;
        } else {
            alert('يجب الإبقاء على سطر مادة واحد على الأقل في الفاتورة.');
        }
    }

    // Fast Settlement Modal
    function openSettlementModal() {
        calculateTotals();
        document.getElementById('settlementModal').classList.remove('hidden');
        document.getElementById('cashReceivedInput').value = document.getElementById('inputGrandTotal').value;
        document.getElementById('chequeAmountInput').value = document.getElementById('inputGrandTotal').value;
    }

    function closeSettlementModal() {
        document.getElementById('settlementModal').classList.add('hidden');
    }

    function switchSettlementTab(tab) {
        currentSettlementTab = tab;
        ['credit', 'cash', 'cheque'].forEach(t => {
            const btn = document.getElementById('tab' + t.charAt(0).toUpperCase() + t.slice(1));
            const content = document.getElementById('content' + t.charAt(0).toUpperCase() + t.slice(1));
            if (t === tab) {
                btn.className = "flex-1 py-2 text-center border-b-2 border-[#1F2E4A] text-[#1F2E4A] font-bold";
                content.classList.remove('hidden');
            } else {
                btn.className = "flex-1 py-2 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-800";
                content.classList.add('hidden');
            }
        });
    }

    function applySettlement() {
        document.getElementById('hiddenSettlementType').value = currentSettlementTab;
        const badge = document.getElementById('settlementStatusBadge');
        if (currentSettlementTab === 'credit') {
            badge.innerHTML = `الحالة: <span class="text-amber-600 font-bold">ذمم آجل (على الحساب)</span>`;
        } else if (currentSettlementTab === 'cash') {
            badge.innerHTML = `الحالة: <span class="text-emerald-700 font-bold">مسددة نقداً (الصندوق)</span>`;
        } else if (currentSettlementTab === 'cheque') {
            const chq = document.getElementById('chequeNoInput').value || 'شيك بنكي';
            badge.innerHTML = `الحالة: <span class="text-blue-700 font-bold">مسددة بشيك (${chq})</span>`;
        }
        closeSettlementModal();
    }

    // Top Navigation Simulation
    function navigateInvoice(direction) {
        const invInput = document.getElementById('invoiceNoInput');
        alert('استعراض السجلات المحاسبية (' + direction + '): تم تحميل السند بنجاح من قاعدة البيانات.');
    }

    function openInvoiceQueryModal() {
        const queryNo = prompt('أدخل رقم الفاتورة أو السند المراد الاستعلام عنه:');
        if (queryNo) {
            alert('جاري استرجاع بيانات الفاتورة رقم: ' + queryNo);
        }
    }

    function confirmPostInvoice() {
        if (confirm('هل أنت متأكد من ترحيل هذه الفاتورة محاسبياً؟ الترحيل يثبت القيود في دفتر اليومية العامة.')) {
            document.getElementById('watheeqInvoiceForm').submit();
        }
    }

    function resetForm() {
        if (confirm('هل ترغب بإلغاء التعديلات والبدء بسند جديد؟')) {
            window.location.reload();
        }
    }

    function exportToExcel() {
        alert('جاري تصدير الفاتورة وجدول المواد إلى ملف إكسل Excel (.xlsx)...');
    }

    function openPrintSpecialModal() {
        alert('نافذة طباعة بيانات خاصة: إعداد نموذج ترويسة الشركة والباركود.');
    }
</script>
@endsection
