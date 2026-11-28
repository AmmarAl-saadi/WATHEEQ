@extends('layouts.app')

@section('title', 'بطاقة صنف مادة ومستودع | وثيق ERP')

@section('content')
<div class="space-y-3 font-sans text-xs" dir="rtl">

    <form method="POST" action="{{ route('erp.products.store') }}" id="watheeqProductForm">
        @csrf

        <!-- Top Tool Command Ribbon (مطابق لحساباتي Frame 3_9_4i) -->
        <div class="bg-white p-2 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-2 no-print">
            <div class="flex items-center gap-1.5 flex-wrap">
                <button type="button" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border font-mono">|&lt;&lt;</button>
                <button type="button" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border font-mono">&lt;&lt;</button>
                <button type="button" class="px-3 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-800 rounded font-bold border border-sky-200">استعلام</button>
                <button type="button" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border font-mono">&gt;&gt;</button>
                <button type="button" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border font-mono">&gt;&gt;|</button>

                <div class="h-6 w-px bg-gray-300 mx-1"></div>

                <button type="submit" class="px-4 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
                    حفظ
                </button>
                <button type="button" onclick="alert('حفظ كصنف جديد مشابه (نسخ البيانات)')" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 rounded font-bold border border-amber-300 transition">
                    جديد مشابه
                </button>
                <a href="{{ route('erp.products.editor') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    جديد
                </a>
                <button type="button" onclick="window.print()" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    طباعة
                </button>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    مرفقات
                </button>
                <button type="button" onclick="alert('طباعة ملصق الباركود والوزن للميزان')" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded font-bold border border-indigo-200 transition">
                    طباعة الباركود - الوزن
                </button>
                <a href="{{ route('erp.products') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    سجل الأصناف
                </a>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 bg-emerald-500 text-white font-bold rounded text-[11px] animate-pulse">
                    تعريف مادة
                </span>
                <span class="font-bold text-gray-800 text-sm">
                    بطاقة الأصناف والمخزون (Item Master Card)
                </span>
            </div>
        </div>

        <!-- Master Document Header Meta Box -->
        <div class="bg-[#F8F9FA] p-3.5 rounded-xl border border-gray-200 shadow-sm space-y-2.5">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">رقم الصنف:</span>
                    <input type="text" name="code" value="{{ $nextCode }}" readonly class="flex-1 bg-white border border-gray-300 rounded px-2.5 py-1 font-mono font-bold text-gray-800">
                </div>

                <div class="md:col-span-6 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">اسم الصنف:</span>
                    <input type="text" name="name" required placeholder="مثال: شاشة سامسونج LED 27 بوصة" class="flex-1 bg-white border border-gray-300 rounded px-2.5 py-1 font-bold text-gray-800">
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-16 font-bold text-gray-600 text-left">عمولة البيع:</span>
                    <input type="number" step="0.1" value="0.0" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono text-center">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-6 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">Name:</span>
                    <input type="text" name="name_en" placeholder="Samsung LED Monitor 27-inch" class="flex-1 bg-white border border-gray-300 rounded px-2.5 py-1 font-sans text-gray-800">
                </div>

                <div class="md:col-span-6 flex items-center gap-2">
                    <span class="w-24 font-bold text-gray-600 text-left">الاسم للطباعة:</span>
                    <input type="text" name="print_name" placeholder="الاسم الذي يظهر على فاتورة الزبون..." class="flex-1 bg-white border border-gray-300 rounded px-2.5 py-1 text-gray-800">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-4 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">الباركود:</span>
                    <input type="text" name="barcode" placeholder="6251009849201" class="flex-1 bg-white border border-gray-300 rounded px-2.5 py-1 font-mono font-bold text-[#1F2E4A]">
                </div>

                <div class="md:col-span-4 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">الضريبة (%):</span>
                    <input type="number" step="0.5" value="16.0" class="w-20 bg-white border border-gray-300 rounded px-2 py-1 font-mono font-bold text-center">
                    <label class="flex items-center gap-1 text-gray-600 cursor-pointer mr-2">
                        <input type="checkbox"> <span>معفاه من الضريبة</span>
                    </label>
                </div>

                <div class="md:col-span-4 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">آخر تعديل:</span>
                    <input type="text" value="{{ date('Y-m-d H:i') }}" readonly class="flex-1 bg-gray-100 border border-gray-300 rounded px-2 py-1 font-mono text-[11px] text-gray-500 text-center">
                </div>
            </div>
        </div>

        <!-- Hesabate Standard 10-Tabs Ribbon (مطابق بالمللي لشريط تبويبات Frame 3_9_4i) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex border-b text-xs font-bold bg-gray-50/80 overflow-x-auto no-print">
                <button type="button" onclick="switchProductTab('general')" id="ptabGeneral" class="py-2.5 px-4 border-b-2 border-[#1F2E4A] text-[#1F2E4A] whitespace-nowrap bg-white">
                    معلومات الأصناف
                </button>
                <button type="button" onclick="switchProductTab('units')" id="ptabUnits" class="py-2.5 px-4 border-b-2 border-transparent text-gray-600 hover:text-gray-900 whitespace-nowrap">
                    الوحدات
                </button>
                <button type="button" onclick="switchProductTab('barcodes')" id="ptabBarcodes" class="py-2.5 px-4 border-b-2 border-transparent text-gray-600 hover:text-gray-900 whitespace-nowrap">
                    باركود متعدد
                </button>
                <button type="button" onclick="switchProductTab('availability')" id="ptabAvailability" class="py-2.5 px-4 border-b-2 border-transparent text-gray-600 hover:text-gray-900 whitespace-nowrap">
                    توفر المادة بالمخازن
                </button>
                <button type="button" onclick="switchProductTab('substitutes')" id="ptabSubstitutes" class="py-2.5 px-4 border-b-2 border-transparent text-gray-600 hover:text-gray-900 whitespace-nowrap">
                    بدائل الصنف
                </button>
                <button type="button" onclick="switchProductTab('bom')" id="ptabBom" class="py-2.5 px-4 border-b-2 border-transparent text-gray-600 hover:text-gray-900 whitespace-nowrap">
                    معادلة الإنتاج (BOM)
                </button>
                <button type="button" onclick="switchProductTab('categories')" id="ptabCategories" class="py-2.5 px-4 border-b-2 border-transparent text-gray-600 hover:text-gray-900 whitespace-nowrap">
                    التصنيفات
                </button>
                <button type="button" onclick="switchProductTab('expiry')" id="ptabExpiry" class="py-2.5 px-4 border-b-2 border-transparent text-gray-600 hover:text-gray-900 whitespace-nowrap">
                    تاريخ الصلاحية
                </button>
                <button type="button" onclick="switchProductTab('pos')" id="ptabPos" class="py-2.5 px-4 border-b-2 border-transparent text-gray-600 hover:text-gray-900 whitespace-nowrap">
                    نقاط البيع (POS)
                </button>
            </div>

            <!-- Tab 1: معلومات الأصناف والتسعير المتعدد -->
            <div id="pcontentGeneral" class="p-4 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <!-- Left Column: فئات سعر البيع -->
                    <div class="md:col-span-6 bg-gray-50/70 p-3 rounded-xl border border-gray-200 space-y-2">
                        <span class="font-bold text-xs text-[#1F2E4A] block border-b pb-1">فئات سعر البيع (مفرق، جملة، خاص):</span>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <span class="w-24 font-bold text-gray-600">سعر المفرق:</span>
                                <input type="number" step="0.01" name="sale_price" value="0.00" required class="flex-1 bg-white border rounded px-2 py-1 font-mono font-bold text-emerald-800 text-left">
                                <select class="bg-white border rounded px-1.5 py-1 text-[11px] font-mono"><option>JOD</option><option>USD</option></select>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="w-24 font-bold text-gray-600">سعر الجملة:</span>
                                <input type="number" step="0.01" name="wholesale_price" value="0.00" class="flex-1 bg-white border rounded px-2 py-1 font-mono font-bold text-left">
                                <select class="bg-white border rounded px-1.5 py-1 text-[11px] font-mono"><option>JOD</option><option>USD</option></select>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="w-24 font-bold text-gray-600">الحد الأدنى للبيع:</span>
                                <input type="number" step="0.01" name="min_price" value="0.00" class="flex-1 bg-white border rounded px-2 py-1 font-mono text-left">
                                <span class="w-20 font-bold text-gray-600 text-center">الحد الأعلى:</span>
                                <input type="number" step="0.01" name="max_price" value="0.00" class="flex-1 bg-white border rounded px-2 py-1 font-mono text-left">
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: تكلفة الشراء والسيريال والمخزون الابتدائي -->
                    <div class="md:col-span-6 bg-gray-50/70 p-3 rounded-xl border border-gray-200 space-y-2">
                        <span class="font-bold text-xs text-[#1F2E4A] block border-b pb-1">التكلفة والضبط المخزني:</span>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <span class="w-32 font-bold text-gray-600">سعر التكلفة الابتدائي:</span>
                                <input type="number" step="0.01" name="cost_price" value="0.00" required class="flex-1 bg-white border rounded px-2 py-1 font-mono font-bold text-left">
                                <select class="bg-white border rounded px-1.5 py-1 text-[11px] font-mono"><option>JOD</option><option>USD</option></select>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="w-32 font-bold text-gray-600">هل له نظام سيريال:</span>
                                <select name="has_serial" class="flex-1 bg-white border rounded px-2 py-1 font-bold">
                                    <option value="0">لا (بدون سيريال منفرد)</option>
                                    <option value="1">نعم (تتبع السيريال لكل قطعة)</option>
                                </select>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="w-32 font-bold text-gray-600">رصيد أول المدة (كمية):</span>
                                <input type="number" step="1" name="stock_qty" value="0" class="flex-1 bg-white border rounded px-2 py-1 font-mono font-black text-center">
                                <span class="w-24 font-bold text-gray-600 text-center">المستودع:</span>
                                <select name="warehouse" class="flex-1 bg-white border rounded px-2 py-1">
                                    <option>المستودع الرئيسي</option>
                                    <option>مستودع المعرض</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Row: حدود إعادة الطلب والتذكير -->
                <div class="p-3 bg-blue-50/50 rounded-xl border border-blue-200/60 grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-blue-950">أقل كمية للتذكير (حد الطلب):</span>
                        <input type="number" step="1" name="min_alert_qty" value="5" class="w-20 bg-white border rounded px-2 py-1 font-mono font-bold text-center">
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-blue-950">الحد الأقصى للمخزون:</span>
                        <input type="number" step="1" value="500" class="w-20 bg-white border rounded px-2 py-1 font-mono text-center">
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-blue-950">معامل الربح الخاص (%):</span>
                        <input type="number" step="0.5" value="20.0" class="w-20 bg-white border rounded px-2 py-1 font-mono text-center">
                    </div>
                </div>
            </div>

            <!-- Tab 2: الوحدات المتعددة (Units) -->
            <div id="pcontentUnits" class="hidden p-4 space-y-3">
                <span class="font-bold text-xs text-[#1F2E4A]">الوحدات المتعددة ومعاملات التحويل (قطعة / علبة / صندوق):</span>
                <table class="w-full text-right text-xs">
                    <thead class="bg-gray-100 font-bold">
                        <tr>
                            <th class="py-2 px-3">اسم الوحدة</th>
                            <th class="py-2 px-3">معامل التحويل للوحدة الصغرى</th>
                            <th class="py-2 px-3">الباركود الخاص بالوحدة</th>
                            <th class="py-2 px-3">سعر البيع لهذه الوحدة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y font-mono">
                        <tr>
                            <td class="py-2 px-3 font-sans font-bold">قطعة (الوحدة الأساسية)</td>
                            <td class="py-2 px-3">1.00</td>
                            <td class="py-2 px-3">6251009849201</td>
                            <td class="py-2 px-3">10.00 JOD</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 font-sans font-bold">كرتونة (12 قطعة)</td>
                            <td class="py-2 px-3">12.00</td>
                            <td class="py-2 px-3">6251009849218</td>
                            <td class="py-2 px-3">110.00 JOD</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Tab 3: توفر المادة بالمخازن -->
            <div id="pcontentAvailability" class="hidden p-4 space-y-3">
                <span class="font-bold text-xs text-[#1F2E4A]">الأرصدة الحالية للصنف عبر شبكة الفروع والمستودعات:</span>
                <table class="w-full text-right text-xs">
                    <thead class="bg-gray-100 font-bold">
                        <tr>
                            <th class="py-2 px-3">اسم المستودع / الفرع</th>
                            <th class="py-2 px-3 text-center">الرصيد الفعلي</th>
                            <th class="py-2 px-3 text-center">محجوز لطلبيات</th>
                            <th class="py-2 px-3 text-center">المتاح للبيع الفوري</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y font-mono">
                        <tr>
                            <td class="py-2 px-3 font-sans font-bold">المستودع الرئيسي - عمان</td>
                            <td class="py-2 px-3 text-center text-emerald-700 font-bold">150</td>
                            <td class="py-2 px-3 text-center text-amber-600">10</td>
                            <td class="py-2 px-3 text-center text-blue-700 font-black">140</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 font-sans font-bold">مستودع صالة العرض / المعرض</td>
                            <td class="py-2 px-3 text-center text-emerald-700 font-bold">45</td>
                            <td class="py-2 px-3 text-center text-amber-600">0</td>
                            <td class="py-2 px-3 text-center text-blue-700 font-black">45</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </form>

</div>

<script>
    function switchProductTab(tab) {
        const tabs = ['general', 'units', 'barcodes', 'availability', 'substitutes', 'bom', 'categories', 'expiry', 'pos'];
        tabs.forEach(t => {
            const btn = document.getElementById('ptab' + t.charAt(0).toUpperCase() + t.slice(1));
            const content = document.getElementById('pcontent' + t.charAt(0).toUpperCase() + t.slice(1));
            if (btn) {
                if (t === tab) {
                    btn.className = "py-2.5 px-4 border-b-2 border-[#1F2E4A] text-[#1F2E4A] whitespace-nowrap bg-white font-black";
                    if (content) content.classList.remove('hidden');
                } else {
                    btn.className = "py-2.5 px-4 border-b-2 border-transparent text-gray-600 hover:text-gray-900 whitespace-nowrap";
                    if (content) content.classList.add('hidden');
                }
            }
        });
    }
</script>
@endsection
