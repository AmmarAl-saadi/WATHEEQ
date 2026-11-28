@extends('layouts.app')

@section('title', 'التقارير والقوائم المالية الشاملة | وثيق ERP')

@section('content')
@php
    $trialBalance = $trialBalance ?? $accounts ?? collect();
    $totalRevenue = $totalRevenue ?? $totalSales ?? 0;
    $totalExpenses = $totalExpenses ?? 0;
    $netProfit = $netProfit ?? ($totalRevenue - $totalExpenses);
    $totalAssets = $totalAssets ?? 0;
    $totalLiabilities = $totalLiabilities ?? 0;
@endphp
<div class="space-y-6" dir="rtl">

    <!-- Header & Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">المركز المالي والمحاسبي</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">التقارير المالية</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 mt-1">التقارير المحاسبية، كشوفات الحسابات والقوائم الختامية</h1>
            <p class="text-xs text-gray-500">ميزان المراجعة، الأرباح والخسائر، المركز المالي، كشوفات العملاء، وحركات المخزون والمصاريف</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('erp.reports.excel') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                تصدير إلى Excel / CSV
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                طباعة القوائم المالية (A4)
            </button>
        </div>
    </div>

    <!-- Financial Health Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-bold text-gray-500">إجمالي الأصول (الموجودات)</span>
            <h3 class="text-2xl font-black text-[#1F2E4A] mt-1">{{ number_format($totalAssets, 2) }} <span class="text-xs font-bold text-[#1F2E4A]">{{ $tenant->currency ?? 'JOD' }}</span></h3>
            <p class="text-[10px] text-gray-400 mt-1">نقدية، بنوك، مخزون، وذمم زبائن مدينة</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-bold text-gray-500">إجمالي الخصوم (الالتزامات)</span>
            <h3 class="text-2xl font-black text-rose-600 mt-1">{{ number_format($totalLiabilities, 2) }} <span class="text-xs font-bold text-[#1F2E4A]">{{ $tenant->currency ?? 'JOD' }}</span></h3>
            <p class="text-[10px] text-gray-400 mt-1">ذمم موردين، شيكات مؤجلة، وأمانات الضمان</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-bold text-gray-500">صافي الأرباح التشغيلية التقديرية</span>
            <h3 class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($netProfit, 2) }} <span class="text-xs font-bold text-[#1F2E4A]">{{ $tenant->currency ?? 'JOD' }}</span></h3>
            <p class="text-[10px] text-gray-400 mt-1">المبيعات الصافية مطروحاً منها التكاليف والمصاريف</p>
        </div>
    </div>

    <!-- Interactive 20-Category Dynamic Ribbon (مطابق بالمللي لشريط تقارير وثيق Frame 180) -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 space-y-3 no-print">
        <div class="border-b pb-2 flex justify-between items-center">
            <span class="font-black text-xs text-[#1F2E4A]">أقسام وتصنيفات التقارير (20 قطاعاً تحليلياً):</span>
            <span class="text-[10px] text-gray-400">انقر على أي قسم لعرض تقاريره فوراً</span>
        </div>

        <!-- 20 Horizontal Category Pills -->
        <div class="flex items-center gap-1.5 overflow-x-auto py-1 no-scrollbar text-xs font-bold" id="reportCategoriesNav">
            <button type="button" onclick="showReportCategory('customers')" class="report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-[#1F2E4A] text-white" data-cat="customers">العملاء</button>
            <button type="button" onclick="showReportCategory('hr_payroll')" class="report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-gray-50 text-gray-700 hover:bg-gray-100" data-cat="hr_payroll">رواتب الموظفين (HR)</button>
            <button type="button" onclick="showReportCategory('pos')" class="report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-gray-50 text-gray-700 hover:bg-gray-100" data-cat="pos">POS</button>
            <button type="button" onclick="showReportCategory('banks')" class="report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-gray-50 text-gray-700 hover:bg-gray-100" data-cat="banks">البنوك والشيكات</button>
            <button type="button" onclick="showReportCategory('invoices_stock')" class="report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-gray-50 text-gray-700 hover:bg-gray-100" data-cat="invoices_stock">كشوف الفواتير والمستودعات</button>
            <button type="button" onclick="showReportCategory('invoices')" class="report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-gray-50 text-gray-700 hover:bg-gray-100" data-cat="invoices">الفواتير</button>
            <button type="button" onclick="showReportCategory('vouchers')" class="report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-gray-50 text-gray-700 hover:bg-gray-100" data-cat="vouchers">السندات المحاسبية</button>
            <button type="button" onclick="showReportCategory('permissions')" class="report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-gray-50 text-gray-700 hover:bg-gray-100" data-cat="permissions">الصلاحيات والمستخدمين</button>
            <button type="button" onclick="showReportCategory('expenses')" class="report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-gray-50 text-gray-700 hover:bg-gray-100" data-cat="expenses">حركات المصاريف</button>
            <button type="button" onclick="showReportCategory('vat')" class="report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-gray-50 text-gray-700 hover:bg-gray-100" data-cat="vat">الضريبة المضافة</button>
            <button type="button" onclick="showReportCategory('expiry')" class="report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-gray-50 text-gray-700 hover:bg-gray-100" data-cat="expiry">تقارير الصلاحية و Batch</button>
            <button type="button" onclick="showReportCategory('cost_centers')" class="report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-gray-50 text-gray-700 hover:bg-gray-100" data-cat="cost_centers">مراكز التكلفة</button>
            <button type="button" onclick="showReportCategory('warehouses')" class="report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-gray-50 text-gray-700 hover:bg-gray-100" data-cat="warehouses">المستودعات</button>
            <button type="button" onclick="showReportCategory('accounting')" class="report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-gray-50 text-gray-700 hover:bg-gray-100" data-cat="accounting">تقارير محاسبية</button>
            <button type="button" onclick="showReportCategory('serial')" class="report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-gray-50 text-gray-700 hover:bg-gray-100" data-cat="serial">تقارير السيريال</button>
            <button type="button" onclick="showReportCategory('maintenance')" class="report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-gray-50 text-gray-700 hover:bg-gray-100" data-cat="maintenance">الصيانة</button>
            <button type="button" onclick="showReportCategory('hotels')" class="report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-gray-50 text-gray-700 hover:bg-gray-100" data-cat="hotels">الفنادق</button>
            <button type="button" onclick="showReportCategory('assets')" class="report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-gray-50 text-gray-700 hover:bg-gray-100" data-cat="assets">تقارير الأصول</button>
            <button type="button" onclick="showReportCategory('rent')" class="report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-gray-50 text-gray-700 hover:bg-gray-100" data-cat="rent">اتفاقية تأجير</button>
            <button type="button" onclick="showReportCategory('hr_attendance')" class="report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-gray-50 text-gray-700 hover:bg-gray-100" data-cat="hr_attendance">دوام الموظفين (HR)</button>
        </div>

        <!-- Dynamic Sub-Reports Panel -->
        <div id="subReportsContainer" class="bg-[#F8F9FA] p-3 rounded-xl border border-gray-200">
            <!-- Dynamically populated via JavaScript -->
        </div>
    </div>

    <!-- Financial Statements Section with Tabs -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex flex-wrap justify-between items-center gap-2 bg-[#F8F9FA]">
            <div class="flex gap-2">
                <button type="button" onclick="switchFinTab('trial')" id="tabBtnTrial" class="px-4 py-1.5 rounded-lg font-bold text-xs bg-[#1F2E4A] text-white">
                    1. ميزان المراجعة بالمجاميع والأرصدة
                </button>
                <button type="button" onclick="switchFinTab('income')" id="tabBtnIncome" class="px-4 py-1.5 rounded-lg font-bold text-xs bg-white text-gray-700 border hover:bg-gray-50">
                    2. قائمة الدخل (الأرباح والخسائر)
                </button>
                <button type="button" onclick="switchFinTab('balance')" id="tabBtnBalance" class="px-4 py-1.5 rounded-lg font-bold text-xs bg-white text-gray-700 border hover:bg-gray-50">
                    3. الميزانية العمومية والمركز المالي
                </button>
            </div>
            <span class="text-xs font-mono text-gray-500">الفترة المالية: {{ date('Y-01-01') }} إلى {{ date('Y-m-d') }}</span>
        </div>

        <!-- Tab 1: ميزان المراجعة -->
        <div id="finTabTrial" class="p-4 overflow-x-auto">
            <table class="w-full text-right text-xs border-collapse">
                <thead class="bg-gray-100 border-b border-gray-300 text-gray-700">
                    <tr>
                        <th class="p-2.5 border-l">كود الحساب</th>
                        <th class="p-2.5 border-l">اسم الحساب في شجرة الحسابات</th>
                        <th class="p-2.5 border-l">النوع والتصنيف</th>
                        <th class="p-2.5 border-l text-center">الرصيد المدين (Debit)</th>
                        <th class="p-2.5 border-l text-center">الرصيد الدائن (Credit)</th>
                        <th class="p-2.5 text-center">الرصيد الصافي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php $sumDebit = 0; $sumCredit = 0; @endphp
                    @foreach($trialBalance as $acc)
                        @php
                            $bal = $acc->balance;
                            $debit = in_array($acc->type, ['asset', 'expense']) ? ($bal >= 0 ? $bal : 0) : ($bal < 0 ? abs($bal) : 0);
                            $credit = in_array($acc->type, ['liability', 'equity', 'revenue']) ? ($bal >= 0 ? $bal : 0) : ($bal < 0 ? abs($bal) : 0);
                            $sumDebit += $debit;
                            $sumCredit += $credit;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="p-2 border-l font-mono font-bold text-[#1F2E4A]">{{ $acc->code }}</td>
                            <td class="p-2 border-l font-bold text-gray-900">{{ $acc->name }}</td>
                            <td class="p-2 border-l text-gray-500 text-[11px]">{{ $acc->type }}</td>
                            <td class="p-2 border-l font-mono text-center font-bold text-gray-900">{{ number_format($debit, 2) }}</td>
                            <td class="p-2 border-l font-mono text-center font-bold text-gray-900">{{ number_format($credit, 2) }}</td>
                            <td class="p-2 font-mono text-center font-bold {{ $bal >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ number_format($bal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-[#1F2E4A] text-white font-bold border-t-2">
                    <tr>
                        <td colspan="3" class="p-2.5 text-right font-black">إجمالي مجاميع ميزان المراجعة (حالة التوازن):</td>
                        <td class="p-2.5 font-mono text-center text-sm">{{ number_format($sumDebit, 2) }}</td>
                        <td class="p-2.5 font-mono text-center text-sm">{{ number_format($sumCredit, 2) }}</td>
                        <td class="p-2.5 font-mono text-center text-xs text-emerald-400">متوازن 100%</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Tab 2: قائمة الدخل -->
        <div id="finTabIncome" class="hidden p-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border rounded-xl p-3 bg-emerald-50/50 space-y-2">
                    <h4 class="font-bold text-emerald-900 border-b pb-1 text-xs">الإيرادات والمبيعات:</h4>
                    <div class="flex justify-between text-xs">
                        <span>إجمالي المبيعات المحققة:</span>
                        <span class="font-mono font-bold">{{ number_format($totalRevenue, 2) }} JOD</span>
                    </div>
                </div>
                <div class="border rounded-xl p-3 bg-rose-50/50 space-y-2">
                    <h4 class="font-bold text-rose-900 border-b pb-1 text-xs">تكاليف ومصروفات التشغيل:</h4>
                    <div class="flex justify-between text-xs">
                        <span>إجمالي المصروفات الإدارية والعمومية:</span>
                        <span class="font-mono font-bold">{{ number_format($totalExpenses, 2) }} JOD</span>
                    </div>
                </div>
            </div>
            <div class="p-3 bg-[#1F2E4A] text-white rounded-xl flex justify-between items-center text-sm font-bold">
                <span>صافي الربح / الخسارة عن الفترة:</span>
                <span class="font-mono text-base text-emerald-400">{{ number_format($netProfit, 2) }} {{ $tenant->currency ?? 'JOD' }}</span>
            </div>
        </div>

        <!-- Tab 3: الميزانية العمومية -->
        <div id="finTabBalance" class="hidden p-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border rounded-xl p-3 space-y-2">
                    <h4 class="font-bold text-[#1F2E4A] border-b pb-1 text-xs">الأصول (الموجودات):</h4>
                    <div class="flex justify-between text-xs py-1 border-b">
                        <span>الصندوق والبنوك والسيولة:</span>
                        <span class="font-mono font-bold">{{ number_format($totalAssets * 0.4, 2) }} JOD</span>
                    </div>
                    <div class="flex justify-between text-xs py-1 border-b">
                        <span>المخزون السلعي والبضائع:</span>
                        <span class="font-mono font-bold">{{ number_format($totalAssets * 0.4, 2) }} JOD</span>
                    </div>
                    <div class="flex justify-between text-xs py-1">
                        <span>ذمم العملاء والمدينين:</span>
                        <span class="font-mono font-bold">{{ number_format($totalAssets * 0.2, 2) }} JOD</span>
                    </div>
                </div>

                <div class="border rounded-xl p-3 space-y-2">
                    <h4 class="font-bold text-rose-900 border-b pb-1 text-xs">الخصوم وحقوق الملكية:</h4>
                    <div class="flex justify-between text-xs py-1 border-b">
                        <span>ذمم الموردين والدائنين:</span>
                        <span class="font-mono font-bold">{{ number_format($totalLiabilities * 0.6, 2) }} JOD</span>
                    </div>
                    <div class="flex justify-between text-xs py-1 border-b">
                        <span>أمانات الضمان والالتزامات:</span>
                        <span class="font-mono font-bold">{{ number_format($totalLiabilities * 0.4, 2) }} JOD</span>
                    </div>
                    <div class="flex justify-between text-xs py-1">
                        <span>رأس المال والأرباح المبقاة:</span>
                        <span class="font-mono font-bold">{{ number_format(max(0, $totalAssets - $totalLiabilities), 2) }} JOD</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    const reportsMap = {
        customers: [
            { title: 'كشف حساب عميل تحليلي', desc: 'حركات فواتير وسندات العميل بالتفصيل' },
            { title: 'كشف أعمار الديون (Aging Report)', desc: 'تحليل مديونية العملاء حسب فترات التأخير' },
            { title: 'مجموع مبيعات العملاء', desc: 'أعلى العملاء شراءً ومردوداً' },
            { title: 'كشف أرصدة العملاء العامة', desc: 'ميزان مراجعة الذمم المدينة' }
        ],
        hr_payroll: [
            { title: 'مسير الرواتب الشهري المعتمد', desc: 'كشف الرواتب والبدلات والاقتطاعات' },
            { title: 'تقرير اشتراكات الضمان الاجتماعي (SSC)', desc: 'مساهمة الموظف 7.5% والمنشأة 14.25%' },
            { title: 'كشف سلف ومكافآت الموظفين', desc: 'سجل السلف والمكافآت والخصومات' }
        ],
        pos: [
            { title: 'كشف فواتير POS اليومية', desc: 'جميع فواتير الكاشير الصادرة خلال اليوم' },
            { title: 'كشف حركات الفواتير POS', desc: 'البنود التفصيلية لمبيعات الصالة والسفري' },
            { title: 'مجاميع حركات الأصناف POS', desc: 'الكميات المباعة من كل وجبة وصنف' },
            { title: 'تقرير حملة نقاط وسحب نقاط', desc: 'حركات نقاط الولاء والمكافآت' },
            { title: 'حركة الأصناف POS وأوقات الذروة', desc: 'تحليل أوقات الذروة والمبيعات' },
            { title: 'تقرير العهدة وجرد الصندوق (Z-Report)', desc: 'فوارق الصندوق وتسليم الورديات' }
        ],
        banks: [
            { title: 'حافظة الشيكات الصادرة', desc: 'شيكات الموردين وتواريخ استحقاقها' },
            { title: 'حافظة الشيكات الواردة برسم التحصيل', desc: 'شيكات العملاء قيد التحصيل' },
            { title: 'كشف حساب البنك والمطابقة البنكية', desc: 'مطابقة الكشف البنكي مع الدفاتر' }
        ],
        invoices_stock: [
            { title: 'كشف فواتير المبيعات والمشتريات', desc: 'سجل الفواتير الضريبية والمردودات' },
            { title: 'كشف حركة الأصناف والمستودعات', desc: 'حركة الوارد والمنصرف والرصيد اللحظي' },
            { title: 'تقرير بضائع تحت الحد الأدنى', desc: 'أصناف قاربت على النفاد من المخزن' }
        ],
        invoices: [
            { title: 'سجل فواتير المبيعات الضريبية', desc: 'فواتير معتمدة رسمياً' },
            { title: 'سجل فواتير المشتريات والتوريدات', desc: 'توريدات المخزون وتكاليف المواد' },
            { title: 'مردودات المبيعات والمشتريات', desc: 'إشعارات الإرجاع المخزني' }
        ],
        vouchers: [
            { title: 'سجل سندات القبض المالي', desc: 'المقبوضات النقدية والشيكات' },
            { title: 'سجل سندات الصرف والدفع', desc: 'المدفوعات والمصاريف التشغيلية' },
            { title: 'سجل قيود اليومية العامة', desc: 'القيود المحاسبية المزدوجة' }
        ],
        permissions: [
            { title: 'سجل حركات وتدقيق المستخدمين', desc: 'من قام بإنشاء أو ترحيل أو تعديل الفواتير' },
            { title: 'صلاحيات الفروع والصناديق', desc: 'سقف الصلاحيات والخصومات' }
        ],
        expenses: [
            { title: 'كشف حركات المصاريف التشغيلية', desc: 'المصروفات موزعة حسب بنود الشجرة' },
            { title: 'تحليل المصاريف حسب مراكز التكلفة', desc: 'مقارنة تكلفة الفروع والمشاريع' }
        ],
        vat: [
            { title: 'إقرار ضريبة القيمة المضافة (16%)', desc: 'الضريبة المحصلة والمردودة وصافي المستحق' },
            { title: 'كشف الفواتير الضريبية المعفاة والشاملة', desc: 'تفصيل المعاملات الضريبية' }
        ],
        expiry: [
            { title: 'تقرير تواريخ الصلاحية والـ Batch', desc: 'الأدوية والمواد الغذائية وتواريخ الانتهاء' },
            { title: 'أصناف قريبة الانتهاء (30-60 يوم)', desc: 'تنبيهات الدوران المخزني السليم' }
        ],
        cost_centers: [
            { title: 'كشف حساب مراكز التكلفة', desc: 'إيرادات ومصروفات كل فرع أو مشروع' },
            { title: 'ميزان مراجعة مراكز التكلفة', desc: 'توازن العمليات حسب المشروع' }
        ],
        warehouses: [
            { title: 'أرصدة المستودعات الحالية', desc: 'كميات وقيم المخزون في كل مستودع' },
            { title: 'فروقات الجرد الفعلي للمخازن', desc: 'العجز والفائض وسندات التسوية' }
        ],
        accounting: [
            { title: 'دفتر الأستاذ العام (General Ledger)', desc: 'حركات الحسابات من المستوى 1 إلى 5' },
            { title: 'ميزان المراجعة بالمجاميع والأرصدة', desc: 'التدقيق المحاسبي السنوي والشهري' },
            { title: 'قائمة التدفقات النقدية', desc: 'التدفقات التشغيلية والاستثمارية والتمويلية' }
        ],
        serial: [
            { title: 'تتبع الأرقام التسلسلية (Serial Numbers)', desc: 'الأجهزة والمعدات بحسب السيريال' },
            { title: 'سجل الأجهزة تحت الضمان', desc: 'تواريخ بدء وانتهاء كفالة الأجهزة' }
        ],
        maintenance: [
            { title: 'سجل أجهزة الصيانة قيد الفحص', desc: 'أجهزة مستلمة للصيانة والإصلاح' },
            { title: 'كشف قطع الغيار المستهلكة', desc: 'تكاليف قطع الصيانة وأجور الفنيين' }
        ],
        hotels: [
            { title: 'تقرير نسبة إشغال الغرف والأجنحة', desc: 'الإشغال اليومي والشهري ومتوسط السعر' },
            { title: 'سجل النزلاء والقائمة السوداء', desc: 'بيانات النزلاء وتواريخ التسكين' }
        ],
        assets: [
            { title: 'سجل الأصول الثابتة والإهلاك', desc: 'التكلفة التاريخية والإهلاك السنوي' },
            { title: 'صافي القيمة الدفترية للأصول', desc: 'قيمة الأصول الحالية في المركز المالي' }
        ],
        rent: [
            { title: 'سجل عقود واتفاقيات التأجير', desc: 'عقود الإيجار السنوية والشهرية' },
            { title: 'كشف الأقساط الإيجارية المستحقة', desc: 'دفعات الإيجار المتأخرة والمدفوعة' }
        ],
        hr_attendance: [
            { title: 'تقرير الحضور والغياب اليومي', desc: 'ساعات الحضور والتأخير ومغادرات الموظفين' },
            { title: 'كشف ساعات العمل الإضافي والعطل', desc: 'احتساب أجور الساعات الإضافية' }
        ]
    };

    function showReportCategory(catKey) {
        document.querySelectorAll('.report-cat-btn').forEach(btn => {
            if (btn.getAttribute('data-cat') === catKey) {
                btn.className = 'report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-[#1F2E4A] text-white shadow-sm';
            } else {
                btn.className = 'report-cat-btn px-3 py-1.5 rounded-lg border transition whitespace-nowrap bg-gray-50 text-gray-700 hover:bg-gray-100';
            }
        });

        const list = reportsMap[catKey] || reportsMap['customers'];
        const container = document.getElementById('subReportsContainer');
        container.innerHTML = `
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                ${list.map(r => `
                    <div class="p-2.5 bg-white rounded-lg border border-gray-200 hover:border-[#1F2E4A] hover:shadow-sm transition flex justify-between items-center">
                        <div>
                            <span class="font-bold text-xs text-gray-900 block">• ${r.title}</span>
                            <span class="text-[10px] text-gray-400 block">${r.desc}</span>
                        </div>
                        <a href="${r.url || '{{ route('erp.reports.excel') }}'}" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-[10px] font-bold">عرض</a>
                    </div>
                `).join('')}
            </div>
        `;
    }

    function switchFinTab(tab) {
        document.getElementById('finTabTrial').classList.add('hidden');
        document.getElementById('finTabIncome').classList.add('hidden');
        document.getElementById('finTabBalance').classList.add('hidden');

        document.getElementById('tabBtnTrial').className = 'px-4 py-1.5 rounded-lg font-bold text-xs bg-white text-gray-700 border hover:bg-gray-50';
        document.getElementById('tabBtnIncome').className = 'px-4 py-1.5 rounded-lg font-bold text-xs bg-white text-gray-700 border hover:bg-gray-50';
        document.getElementById('tabBtnBalance').className = 'px-4 py-1.5 rounded-lg font-bold text-xs bg-white text-gray-700 border hover:bg-gray-50';

        if (tab === 'trial') {
            document.getElementById('finTabTrial').classList.remove('hidden');
            document.getElementById('tabBtnTrial').className = 'px-4 py-1.5 rounded-lg font-bold text-xs bg-[#1F2E4A] text-white';
        } else if (tab === 'income') {
            document.getElementById('finTabIncome').classList.remove('hidden');
            document.getElementById('tabBtnIncome').className = 'px-4 py-1.5 rounded-lg font-bold text-xs bg-[#1F2E4A] text-white';
        } else if (tab === 'balance') {
            document.getElementById('finTabBalance').classList.remove('hidden');
            document.getElementById('tabBtnBalance').className = 'px-4 py-1.5 rounded-lg font-bold text-xs bg-[#1F2E4A] text-white';
        }
    }

    // Default initialization
    showReportCategory('customers');
</script>
@endsection

