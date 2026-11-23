@extends('layouts.app')

@section('title', 'إدارة الضرائب والإقرار الضريبي الدوري | وثيق ERP')

@section('content')
<div class="space-y-6" dir="rtl">

    <!-- Top Command Ribbon & Header -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">الامتثال الضريبي والحكومي</span>
                <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">دائرة ضريبة الدخل والمبيعات (ISTD)</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 mt-1.5">إدارة الضرائب، الإقرار الدوري وضريبة المبيعات 16%</h1>
            <p class="text-xs text-gray-500 font-medium">متابعة ضريبة المخرجات والمدخلات، احتساب صافي المستحق للدائرة، وتوليد كشف الإقرار الضريبي الرسمي</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <!-- Tax Period Filter -->
            <form method="GET" action="{{ route('erp.taxes') }}" class="flex items-center gap-1.5">
                <select name="period" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 text-xs font-bold text-gray-700 focus:outline-none focus:ring-1 focus:ring-[#1F2E4A]">
                    <option value="all" {{ $period === 'all' ? 'selected' : '' }}>كامل السجلات الضريبية</option>
                    <option value="{{ date('Y-m') }}" {{ $period === date('Y-m') ? 'selected' : '' }}>الشهر الحالي ({{ date('Y-m') }})</option>
                    <option value="{{ date('Y') }}" {{ $period === date('Y') ? 'selected' : '' }}>السنة المالية الحالية ({{ date('Y') }})</option>
                </select>
            </form>

            <button type="button" onclick="printOfficialTaxReturn()" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                طباعة نموذج الإقرار الضريبي الرسمي (A4)
            </button>
        </div>
    </div>

    <!-- Live Tax Financial Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 no-print">
        <!-- Card 1: Taxable Sales Base -->
        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm space-y-1">
            <span class="text-xs font-bold text-gray-500 block">إجمالي المبيعات الخاضعة للضريبة</span>
            <div class="text-2xl font-black text-[#1F2E4A] font-mono">
                {{ number_format($totalSalesTaxable, 2) }} <span class="text-xs font-bold text-gray-500">{{ $tenant->currency ?? 'JOD' }}</span>
            </div>
            <div class="text-[11px] text-gray-400 font-medium">قاعدة احتساب ضريبة المبيعات 16%</div>
        </div>

        <!-- Card 2: Output VAT Collected -->
        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm space-y-1">
            <span class="text-xs font-bold text-gray-500 block">ضريبة المخرجات المحصلة (16%)</span>
            <div class="text-2xl font-black text-blue-700 font-mono">
                {{ number_format($outputTax, 2) }} <span class="text-xs font-bold text-gray-500">{{ $tenant->currency ?? 'JOD' }}</span>
            </div>
            <div class="text-[11px] text-gray-400 font-medium">الضريبة المقيدة على فواتير البيع للعملاء</div>
        </div>

        <!-- Card 3: Input VAT Deductible -->
        <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm space-y-1">
            <span class="text-xs font-bold text-gray-500 block">ضريبة المدخلات القابلة للخصم</span>
            <div class="text-2xl font-black text-emerald-700 font-mono">
                {{ number_format($inputTax, 2) }} <span class="text-xs font-bold text-gray-500">{{ $tenant->currency ?? 'JOD' }}</span>
            </div>
            <div class="text-[11px] text-gray-400 font-medium">الضريبة المدفوعة للموردين على المشتريات</div>
        </div>

        <!-- Card 4: Net VAT Payable / Refundable -->
        @php
            $isPayable = $netTaxPayable >= 0;
        @endphp
        <div class="bg-white p-4 rounded-2xl border {{ $isPayable ? 'border-[#1F2E4A]/30 bg-[#1F2E4A]/5' : 'border-emerald-200 bg-emerald-50/50' }} shadow-sm space-y-1">
            <span class="text-xs font-bold text-gray-700 block">
                {{ $isPayable ? 'صافي الضريبة المستحقة للدائرة' : 'رصيد دائن مدور للفترة القادمة' }}
            </span>
            <div class="text-2xl font-black {{ $isPayable ? 'text-[#1F2E4A]' : 'text-emerald-700' }} font-mono">
                {{ number_format(abs($netTaxPayable), 2) }} <span class="text-xs font-bold">{{ $tenant->currency ?? 'JOD' }}</span>
            </div>
            <div class="text-[11px] font-bold {{ $isPayable ? 'text-rose-700' : 'text-emerald-700' }}">
                {{ $isPayable ? 'واجبة التوريد لضريبة الدخل والمبيعات' : 'فائض ضريبي مدور للاسترداد أو الخصم' }}
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-gray-200 gap-2 no-print overflow-x-auto text-xs font-bold">
        <button type="button" onclick="switchTaxTab('declarationTab')" id="taxTabBtn_declarationTab" class="tax-tab-btn py-2.5 px-4 rounded-t-xl bg-[#1F2E4A] text-white transition">
            نموذج الإقرار الضريبي الرسمي (ISTD Return)
        </button>
        <button type="button" onclick="switchTaxTab('ledgerTab')" id="taxTabBtn_ledgerTab" class="tax-tab-btn py-2.5 px-4 rounded-t-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
            سجل الفواتير الضريبية (Tax Ledger)
        </button>
        <button type="button" onclick="switchTaxTab('withholdingTab')" id="taxTabBtn_withholdingTab" class="tax-tab-btn py-2.5 px-4 rounded-t-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
            استقطاعات الرواتب والخدمات (WHT)
        </button>
        <button type="button" onclick="switchTaxTab('settingsTab')" id="taxTabBtn_settingsTab" class="tax-tab-btn py-2.5 px-4 rounded-t-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
            إعدادات الملف الضريبي للمنشأة
        </button>
    </div>

    <!-- ── TAB 1: Official ISTD Tax Return Form (نموذج الإقرار الضريبي الرسمي) ── -->
    <div id="declarationTab" class="tax-pane space-y-4">
        <div id="printableA4Area" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8 space-y-6">
            
            <!-- Declaration Official Letterhead -->
            <div class="border-b-2 border-gray-900 pb-5 flex justify-between items-start">
                <div class="space-y-1">
                    <div class="text-xs font-bold text-gray-500">المملكة الأردنية الهاشمية</div>
                    <div class="text-sm font-black text-gray-900">دائرة ضريبة الدخل والمبيعات (ISTD)</div>
                    <div class="text-base font-black text-[#1F2E4A] mt-1">كشف الإقرار الضريبي الدوري للضريبة العامة على المبيعات</div>
                    <div class="text-xs text-gray-500 font-bold">بموجب أحكام قانون الضريبة العامة على المبيعات وتعديلاته</div>
                </div>

                <div class="text-left space-y-1 bg-gray-50 p-3 rounded-xl border border-gray-200 text-xs">
                    <div>الرقم الضريبي للمكلف (TIN): <strong class="font-mono text-sm text-[#1F2E4A]">{{ $tenant->tax_number ?? '1004829103' }}</strong></div>
                    <div>اسم المكلف / المنشأة: <strong class="text-gray-900">{{ $tenant->name ?? 'وثيق لإدارة الأعمال' }}</strong></div>
                    <div>السجل التجاري: <strong class="font-mono text-gray-800">{{ $tenant->commercial_register ?? 'CR-29381' }}</strong></div>
                    <div>الفترة الضريبية: <strong class="text-[#1F2E4A]">{{ $period === 'all' ? 'كامل الحركات حتى تاريخه' : $period }}</strong></div>
                    <div>تاريخ تحرير الإقرار: <strong class="font-mono text-gray-600">{{ date('Y-m-d') }}</strong></div>
                </div>
            </div>

            <!-- Tax Declaration Lines Table (مطابق لنموذج ضريبة المبيعات الأردني) -->
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs border-collapse">
                    <thead class="bg-[#1F2E4A] text-white font-bold text-[11px]">
                        <tr>
                            <th class="py-2.5 px-3 border border-gray-400 w-16 text-center">رقم البند</th>
                            <th class="py-2.5 px-3 border border-gray-400">بيان البند الضريبي (مبيعات / مشتريات)</th>
                            <th class="py-2.5 px-3 border border-gray-400 w-40 text-left font-mono">قيمة المبيعات / المشتريات</th>
                            <th class="py-2.5 px-3 border border-gray-400 w-24 text-center">النسبة %</th>
                            <th class="py-2.5 px-3 border border-gray-400 w-40 text-left font-mono">قيمة الضريبة المستحقة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300 font-medium text-gray-800">
                        
                        <!-- Header Group 1: المبيعات وضريبة المخرجات -->
                        <tr class="bg-gray-100 font-black text-gray-900 text-[11px]">
                            <td colspan="5" class="py-2 px-3 border border-gray-300 bg-gray-100">
                                أولاً: المبيعات المحلية والسلع والخدمات المصدرة (ضريبة المخرجات)
                            </td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-center font-mono font-bold border border-gray-200">1</td>
                            <td class="py-2.5 px-3 border border-gray-200 font-bold">المبيعات المحلية للسلع والخدمات الخاضعة للنسبة العامة</td>
                            <td class="py-2.5 px-3 text-left font-mono font-bold border border-gray-200">{{ number_format($totalSalesTaxable, 2) }}</td>
                            <td class="py-2.5 px-3 text-center font-mono border border-gray-200">16%</td>
                            <td class="py-2.5 px-3 text-left font-mono font-black text-blue-700 border border-gray-200">{{ number_format($outputTax, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-center font-mono font-bold border border-gray-200">2</td>
                            <td class="py-2.5 px-3 border border-gray-200">المبيعات الخاضعة لنسب خاصة أو مخفضة (4% / 8% / 10%)</td>
                            <td class="py-2.5 px-3 text-left font-mono border border-gray-200">0.00</td>
                            <td class="py-2.5 px-3 text-center font-mono border border-gray-200">—</td>
                            <td class="py-2.5 px-3 text-left font-mono border border-gray-200">0.00</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-center font-mono font-bold border border-gray-200">3</td>
                            <td class="py-2.5 px-3 border border-gray-200">الصادرات من السلع والخدمات والمبيعات بنسبة الصفر (0%)</td>
                            <td class="py-2.5 px-3 text-left font-mono border border-gray-200">0.00</td>
                            <td class="py-2.5 px-3 text-center font-mono border border-gray-200">0%</td>
                            <td class="py-2.5 px-3 text-left font-mono border border-gray-200">0.00</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-center font-mono font-bold border border-gray-200">4</td>
                            <td class="py-2.5 px-3 border border-gray-200">المبيعات المعفاة من الضريبة العامة على المبيعات</td>
                            <td class="py-2.5 px-3 text-left font-mono border border-gray-200">{{ number_format($totalSalesExempt, 2) }}</td>
                            <td class="py-2.5 px-3 text-center font-mono border border-gray-200">معفاة</td>
                            <td class="py-2.5 px-3 text-left font-mono border border-gray-200">0.00</td>
                        </tr>
                        <tr class="bg-blue-50/70 font-black text-gray-900">
                            <td class="py-2.5 px-3 text-center font-mono border border-gray-300">A</td>
                            <td class="py-2.5 px-3 border border-gray-300">مجموع المبيعات وضريبة المخرجات المستحقة (البنود 1 + 2 + 3 + 4)</td>
                            <td class="py-2.5 px-3 text-left font-mono font-black border border-gray-300">{{ number_format($totalSalesTaxable + $totalSalesExempt, 2) }}</td>
                            <td class="py-2.5 px-3 text-center font-mono border border-gray-300">—</td>
                            <td class="py-2.5 px-3 text-left font-mono font-black text-blue-800 border border-gray-300">{{ number_format($outputTax, 2) }} {{ $tenant->currency ?? 'JOD' }}</td>
                        </tr>

                        <!-- Header Group 2: المشتريات وضريبة المدخلات -->
                        <tr class="bg-gray-100 font-black text-gray-900 text-[11px]">
                            <td colspan="5" class="py-2 px-3 border border-gray-300 bg-gray-100">
                                ثانياً: المشتريات والمستوردات المؤهلة للخصم (ضريبة المدخلات)
                            </td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-center font-mono font-bold border border-gray-200">5</td>
                            <td class="py-2.5 px-3 border border-gray-200 font-bold">المشتريات المحلية والمستوردات الخاضعة للنسبة العامة (16%)</td>
                            <td class="py-2.5 px-3 text-left font-mono font-bold border border-gray-200">{{ number_format($totalPurchasesTaxable, 2) }}</td>
                            <td class="py-2.5 px-3 text-center font-mono border border-gray-200">16%</td>
                            <td class="py-2.5 px-3 text-left font-mono font-black text-emerald-700 border border-gray-200">{{ number_format($inputTax, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-center font-mono font-bold border border-gray-200">6</td>
                            <td class="py-2.5 px-3 border border-gray-200">مشتريات الأصول الثابتة والرأسمالية الخاضعة للخصم</td>
                            <td class="py-2.5 px-3 text-left font-mono border border-gray-200">0.00</td>
                            <td class="py-2.5 px-3 text-center font-mono border border-gray-200">16%</td>
                            <td class="py-2.5 px-3 text-left font-mono border border-gray-200">0.00</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-center font-mono font-bold border border-gray-200">7</td>
                            <td class="py-2.5 px-3 border border-gray-200">المشتريات غير الخاضعة للضريبة أو المعفاة</td>
                            <td class="py-2.5 px-3 text-left font-mono border border-gray-200">{{ number_format($totalPurchasesExempt, 2) }}</td>
                            <td class="py-2.5 px-3 text-center font-mono border border-gray-200">معفاة</td>
                            <td class="py-2.5 px-3 text-left font-mono border border-gray-200">0.00</td>
                        </tr>
                        <tr class="bg-emerald-50/70 font-black text-gray-900">
                            <td class="py-2.5 px-3 text-center font-mono border border-gray-300">B</td>
                            <td class="py-2.5 px-3 border border-gray-300">مجموع المشتريات وضريبة المدخلات القابلة للخصم (البنود 5 + 6 + 7)</td>
                            <td class="py-2.5 px-3 text-left font-mono font-black border border-gray-300">{{ number_format($totalPurchasesTaxable + $totalPurchasesExempt, 2) }}</td>
                            <td class="py-2.5 px-3 text-center font-mono border border-gray-300">—</td>
                            <td class="py-2.5 px-3 text-left font-mono font-black text-emerald-800 border border-gray-300">{{ number_format($inputTax, 2) }} {{ $tenant->currency ?? 'JOD' }}</td>
                        </tr>

                        <!-- Final Calculation Summary -->
                        <tr class="bg-[#1F2E4A]/10 font-black text-gray-900 text-sm">
                            <td class="py-3 px-3 text-center font-mono border border-gray-400">C</td>
                            <td class="py-3 px-3 border border-gray-400">
                                صافي الضريبة الواجبة التوريد للدائرة عن الفترة (المخرجات A - المدخلات B)
                            </td>
                            <td class="py-3 px-3 border border-gray-400 text-left font-mono text-xs text-gray-500">—</td>
                            <td class="py-3 px-3 border border-gray-400 text-center font-mono text-xs text-gray-500">—</td>
                            <td class="py-3 px-3 border border-gray-400 text-left font-mono font-black text-base {{ $isPayable ? 'text-[#1F2E4A]' : 'text-emerald-700' }}">
                                {{ number_format($netTaxPayable, 2) }} {{ $tenant->currency ?? 'JOD' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Legal Declaration & Signature Box -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t-2 border-gray-300 text-xs">
                <div class="space-y-2">
                    <span class="font-bold text-gray-700 block">إقرار وتعهد المفوض بالتوقيع:</span>
                    <p class="text-[11px] text-gray-600 leading-relaxed">
                        أقر أنا الموقع أدناه بأن جميع البيانات الواردة في هذا الإقرار صحيحة ومطابقة للدفاتر والسجلات المحاسبية والفواتير الضريبية المعتمدة لدى دائرة ضريبة الدخل والمبيعات والمنظومة الوطنية للفوترة (JoFotara).
                    </p>
                </div>
                <div class="space-y-4 text-left border-r sm:pr-6 border-gray-200">
                    <div>اسم المفوض بالتوقيع: <strong class="text-gray-900">{{ $tenant->signatory_name ?? 'المدير العام' }}</strong></div>
                    <div>التوقيع والخاتم الرسمي: ___________________________</div>
                    <div>التاريخ: <strong class="font-mono text-gray-700">{{ date('Y-m-d') }}</strong></div>
                </div>
            </div>

        </div>
    </div>

    <!-- ── TAB 2: Tax Invoices Register (سجل الفواتير الضريبية) ── -->
    <div id="ledgerTab" class="tax-pane hidden space-y-4 no-print">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-sm text-[#1F2E4A]">سجل فواتير المبيعات الضريبية الصادرة</h3>
                    <p class="text-[11px] text-gray-500">الفواتير المحملة بضريبة المبيعات 16% الصادرة للعملاء خلال الفترة</p>
                </div>
                <span class="font-mono font-bold text-xs bg-[#1F2E4A]/10 text-[#1F2E4A] px-2.5 py-1 rounded-lg">
                    العدد: {{ count($salesInvoices) }} فاتورة
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-[#1F2E4A] text-white font-bold text-[11px]">
                        <tr>
                            <th class="py-2.5 px-3">رقم الفاتورة</th>
                            <th class="py-2.5 px-3">تاريخ الإصدار</th>
                            <th class="py-2.5 px-3">المستفيد / العميل</th>
                            <th class="py-2.5 px-3 text-left font-mono">المبلغ الصافي (قبل الضريبة)</th>
                            <th class="py-2.5 px-3 text-left font-mono">الضريبة 16%</th>
                            <th class="py-2.5 px-3 text-left font-mono">الإجمالي النهائي</th>
                            <th class="py-2.5 px-3 text-center">الفوترة الوطنية (JoFotara)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 font-medium">
                        @forelse($salesInvoices as $inv)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-2.5 px-3 font-mono font-bold text-[#1F2E4A]">#{{ $inv->id }}</td>
                                <td class="py-2.5 px-3 font-mono text-gray-600">{{ substr($inv->created_at, 0, 10) }}</td>
                                <td class="py-2.5 px-3 font-bold text-gray-900">عميل تجاري نقدي</td>
                                <td class="py-2.5 px-3 text-left font-mono font-bold text-gray-800">{{ number_format($inv->subtotal, 2) }}</td>
                                <td class="py-2.5 px-3 text-left font-mono font-bold text-blue-700">+{{ number_format($inv->tax_total, 2) }}</td>
                                <td class="py-2.5 px-3 text-left font-mono font-black text-gray-900">{{ number_format($inv->grand_total, 2) }} {{ $tenant->currency ?? 'JOD' }}</td>
                                <td class="py-2.5 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        معتمدة ضريبياً
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-gray-400">لا توجد فواتير مسجلة في هذه الفترة الضريبية</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ── TAB 3: Withholding Tax (استقطاعات ضريبة الدخل) ── -->
    <div id="withholdingTab" class="tax-pane hidden space-y-4 no-print">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
            <div>
                <h3 class="font-bold text-sm text-[#1F2E4A]">استقطاعات ضريبة الدخل والخدمات المهنية (Withholding Tax - WHT)</h3>
                <p class="text-xs text-gray-500">الضريبة المقتطعة من المنبع لحساب دائرة ضريبة الدخل والمبيعات</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-xl border border-gray-200 bg-gray-50 space-y-2">
                    <span class="text-xs font-bold text-gray-600 block">1. ضريبة الدخل المستقطعة من رواتب الموظفين:</span>
                    <div class="text-xl font-black text-[#1F2E4A] font-mono">{{ number_format($salaryTaxWithholding, 2) }} {{ $tenant->currency ?? 'JOD' }}</div>
                    <p class="text-[11px] text-gray-500">بموجب شرائح الإعفاءات الضريبية الشخصية والعائلية للموظفين في نظام HR.</p>
                </div>

                <div class="p-4 rounded-xl border border-gray-200 bg-gray-50 space-y-2">
                    <span class="text-xs font-bold text-gray-600 block">2. استقطاع أتعاب الخدمات والمهن الحرة (5%):</span>
                    <div class="text-xl font-black text-gray-800 font-mono">0.00 {{ $tenant->currency ?? 'JOD' }}</div>
                    <p class="text-[11px] text-gray-500">استقطاع 5% من أتعاب المحامين، المحاسبين، الاستشاريين، والمهندسين غير المشمولين بالفاتورة الوطنية.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ── TAB 4: Tax Profile Settings (إعدادات الملف الضريبي) ── -->
    <div id="settingsTab" class="tax-pane hidden space-y-4 no-print">
        <form method="POST" action="{{ route('erp.taxes.settings') }}" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-5">
            @csrf
            <div class="border-b pb-3">
                <h3 class="font-bold text-sm text-[#1F2E4A]">بيانات وإعدادات الملف الضريبي للمنشأة</h3>
                <p class="text-xs text-gray-500">المعلومات المعتمدة في ترويسة الفواتير والإقرارات الضريبية الرسمية</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="space-y-1">
                    <label class="block font-bold text-gray-700">الرقم الضريبي المعتمد للمكلف (TIN):</label>
                    <input type="text" name="tax_number" value="{{ old('tax_number', $tenant->tax_number ?? '1004829103') }}" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 font-mono font-bold text-[#1F2E4A]">
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-gray-700">نسبة الضريبة العامة الافتراضية (%):</label>
                    <input type="number" step="0.1" name="tax_rate" value="{{ old('tax_rate', $tenant->tax_rate ?? 16) }}" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 font-mono font-bold text-gray-900">
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-gray-700">رقم السجل التجاري (Commercial Register):</label>
                    <input type="text" name="commercial_register" value="{{ old('commercial_register', $tenant->commercial_register ?? 'CR-29381') }}" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 font-mono font-bold text-gray-800">
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-gray-700">دورية تقديم الإقرار الضريبي:</label>
                    <select name="tax_period" class="w-full bg-gray-50 border border-gray-300 rounded-xl px-3 py-2 font-bold text-gray-800">
                        <option value="شهري (Monthly)" selected>شهرياً (Monthly - نهاية كل شهر ميلادي)</option>
                        <option value="كل شهرين (Bi-Monthly)">كل شهرين (Bi-Monthly - معتمد لبعض القطاعات)</option>
                        <option value="ربع سنوي (Quarterly)">ربع سنوي (Quarterly)</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end pt-3 border-t">
                <button type="submit" class="px-6 py-2.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                    حفظ وتحديث الملف الضريبي
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    function switchTaxTab(tabId) {
        document.querySelectorAll('.tax-pane').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tax-tab-btn').forEach(el => {
            el.classList.remove('bg-[#1F2E4A]', 'text-white');
            el.classList.add('bg-gray-100', 'text-gray-700');
        });

        document.getElementById(tabId).classList.remove('hidden');
        const activeBtn = document.getElementById('taxTabBtn_' + tabId);
        if (activeBtn) {
            activeBtn.classList.remove('bg-gray-100', 'text-gray-700');
            activeBtn.classList.add('bg-[#1F2E4A]', 'text-white');
        }
    }

    function printOfficialTaxReturn() {
        switchTaxTab('declarationTab');
        watheeqPrint('printableA4Area', 'a4');
    }
</script>
@endsection

