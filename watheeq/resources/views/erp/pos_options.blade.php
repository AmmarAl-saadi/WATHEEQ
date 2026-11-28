@extends('layouts.app')

@section('title', 'خيارات وإعدادات حساباتي POS | وثيق ERP')

@section('content')
<div class="space-y-4 font-sans text-xs" dir="rtl">

    <form method="POST" action="{{ route('erp.pos.options.store') }}">
        @csrf

        <!-- Top Tool Command Ribbon (Hesabate POS Options Ribbon) -->
        <div class="bg-white p-2 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-2 no-print">
            <div class="flex items-center gap-2 flex-wrap">
                <button type="submit" class="px-5 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
                    حفظ
                </button>
                <a href="{{ route('erp.pos') }}" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    فتح شاشة الكاشير POS
                </a>
                <a href="{{ route('erp.pos.hub') }}" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    مركز إدارة POS
                </a>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm font-black text-gray-800">خيارات الـ POS (مطعم / بيع تجزئة)</span>
                <span class="bg-[#1F2E4A] text-white text-[11px] px-2 py-0.5 rounded font-black">حساباتي POS</span>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <!-- 5 Tabs Navigation (طباعة، معلومات مالية، إظهار/إخفاء، خيارات الـ POS، POS Terminals) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center gap-1 border-b border-gray-200 bg-gray-50 p-1.5 text-xs font-bold">
                <button type="button" onclick="switchPosTab('print')" id="pos-btn-print" class="pos-tab-btn px-4 py-1.5 rounded text-white bg-[#1F2E4A]">طباعة</button>
                <button type="button" onclick="switchPosTab('financial')" id="pos-btn-financial" class="pos-tab-btn px-4 py-1.5 rounded text-gray-600 hover:bg-gray-200">معلومات مالية</button>
                <button type="button" onclick="switchPosTab('display')" id="pos-btn-display" class="pos-tab-btn px-4 py-1.5 rounded text-gray-600 hover:bg-gray-200">إظهار / إخفاء</button>
                <button type="button" onclick="switchPosTab('pos_options')" id="pos-btn-pos_options" class="pos-tab-btn px-4 py-1.5 rounded text-gray-600 hover:bg-gray-200">خيارات الـ POS</button>
                <button type="button" onclick="switchPosTab('terminals')" id="pos-btn-terminals" class="pos-tab-btn px-4 py-1.5 rounded text-gray-600 hover:bg-gray-200">POS TERMINALS</button>
            </div>

            <!-- Tab 1: طباعة (Hesabate POS Frame 18) -->
            <div id="pos-pane-print" class="pos-tab-pane p-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-3 bg-gray-50/50 p-4 rounded-xl border border-gray-200">
                        <div>
                            <label class="block text-gray-700 font-bold mb-1">عنوان الملاحظات الأولى بالفاتورة:</label>
                            <input type="text" name="print_header_1" value="أهلاً وسهلاً بكم في متجرنا" class="w-full border rounded px-3 py-1.5 bg-white font-bold">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-1">عنوان الملاحظات الثانية بالفاتورة:</label>
                            <input type="text" name="print_header_2" value="البضاعة المباعة ترد وتستبدل خلال 3 أيام" class="w-full border rounded px-3 py-1.5 bg-white">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-gray-700 font-bold mb-1">عرض الشعار (px):</label>
                                <input type="number" name="logo_width" value="80" class="w-full border rounded px-3 py-1.5 bg-white text-center font-bold">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-1">طول الشعار (px):</label>
                                <input type="number" name="logo_height" value="120" class="w-full border rounded px-3 py-1.5 bg-white text-center font-bold">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3 bg-gray-50/50 p-4 rounded-xl border border-gray-200">
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="font-bold text-gray-800">طباعة الفاتورة بعد التسديد تلقائياً:</span>
                            <select name="auto_print_after_pay" class="border rounded px-3 py-1 font-bold">
                                <option value="1">نعم</option>
                                <option value="0">لا</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="font-bold text-gray-800">طباعة فواتير ضريبية (شاملة JoFotara QR):</span>
                            <select name="print_tax_invoice" class="border rounded px-3 py-1 font-bold text-emerald-700">
                                <option value="1">نعم</option>
                                <option value="0">لا</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="font-bold text-gray-800">أوامر الطباعة للمطبخ (Kitchen Orders):</span>
                            <select name="kitchen_print_command" class="border rounded px-3 py-1 font-bold">
                                <option value="نعم">نعم</option>
                                <option value="لا">لا</option>
                                <option value="الكل">الكل</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="font-bold text-gray-800">إظهار الرقم الأصلي إلى المطبخ:</span>
                            <select name="show_original_no_kitchen" class="border rounded px-3 py-1 font-bold">
                                <option value="1">نعم</option>
                                <option value="0">لا</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: معلومات مالية (Hesabate POS Frame 20) -->
            <div id="pos-pane-financial" class="pos-tab-pane hidden p-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-3 bg-gray-50/50 p-4 rounded-xl border border-gray-200">
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="font-bold text-gray-800">فتح صندوق الكاش عند التسديد (Drawer Kick):</span>
                            <select name="open_cash_drawer" class="border rounded px-3 py-1 font-bold">
                                <option value="1">نعم</option>
                                <option value="0">لا</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="font-bold text-gray-800">الخصم في الفاتورة:</span>
                            <select name="pos_discount_type" class="border rounded px-3 py-1 font-bold">
                                <option value="value_or_pct">القيمة / نسبة</option>
                                <option value="value_only">قيمة فقط</option>
                                <option value="pct_only">نسبة فقط</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="font-bold text-gray-800">نسبة خدمة الصالة % (Service Charge):</span>
                            <input type="number" step="0.1" name="service_charge_pct" value="0.0" class="w-24 text-center border rounded px-2 py-1 font-bold">
                        </div>
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="font-bold text-gray-800">احتساب الباقي بكل العملات:</span>
                            <select name="calc_change_all_currencies" class="border rounded px-3 py-1 font-bold">
                                <option value="1">نعم</option>
                                <option value="0">لا</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-3 bg-gray-50/50 p-4 rounded-xl border border-gray-200">
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="font-bold text-gray-800">تسديد جزئي (مقسم بين عملاء الطاولة):</span>
                            <select name="allow_partial_payment" class="border rounded px-3 py-1 font-bold">
                                <option value="1">نعم</option>
                                <option value="0">لا</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="font-bold text-gray-800">البيع بالسالب = مردودات مبيعات:</span>
                            <select name="negative_sale_as_return" class="border rounded px-3 py-1 font-bold">
                                <option value="1">نعم</option>
                                <option value="0">لا</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="font-bold text-gray-800">حملة نقاط ولاء العملاء (Loyalty Points):</span>
                            <select name="loyalty_campaign_active" class="border rounded px-3 py-1 font-bold text-blue-700">
                                <option value="1">مفعلة على مجموع الفاتورة</option>
                                <option value="0">غير مفعلة</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: خيارات الـ POS (Hesabate POS Frame 22) -->
            <div id="pos-pane-pos_options" class="pos-tab-pane hidden p-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-3 bg-gray-50/50 p-4 rounded-xl border border-gray-200">
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="font-bold text-gray-800">Take Away هو الخيار الأساسي:</span>
                            <select name="takeaway_default" class="border rounded px-3 py-1 font-bold">
                                <option value="1">نعم</option>
                                <option value="0">لا (صالة / طاولات)</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="font-bold text-gray-800">تلوين الطاولة قبل موعد الحجز بعدد دقائق:</span>
                            <input type="number" name="table_color_minutes_before" value="20" class="w-20 text-center border rounded px-2 py-1 font-bold text-[#1F2E4A]">
                        </div>
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="font-bold text-gray-800">عدد الطاولات في السطر (عرض الشبكة):</span>
                            <input type="number" name="tables_per_row" value="10" class="w-20 text-center border rounded px-2 py-1 font-bold">
                        </div>
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="font-bold text-gray-800">عدد الأصناف في السطر:</span>
                            <input type="number" name="items_per_row" value="4" class="w-20 text-center border rounded px-2 py-1 font-bold">
                        </div>
                    </div>

                    <div class="space-y-3 bg-gray-50/50 p-4 rounded-xl border border-gray-200">
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="font-bold text-gray-800">السعر عند الإدخال في الفواتير يشمل الضريبة:</span>
                            <select name="pos_tax_inclusive_default" class="border rounded px-3 py-1 font-bold text-emerald-800 bg-emerald-50">
                                <option value="1">نعم (السعر شامل ضريبة 16%)</option>
                                <option value="0">لا (يضاف 16% على الإجمالي)</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="font-bold text-gray-800">نقل الطاولات بالسحب والإفلات (Drag & Drop):</span>
                            <select name="drag_drop_tables" class="border rounded px-3 py-1 font-bold">
                                <option value="1">نعم</option>
                                <option value="0">لا</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <span class="font-bold text-gray-800">تحويل الحركات آلياً إلى المحاسبة المركزية:</span>
                            <select name="auto_sync_accounting" class="border rounded px-3 py-1 font-bold">
                                <option value="1">نعم (فوري مع كل فاتورة)</option>
                                <option value="0">عند إقفال الوردية فقط</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 4: إظهار / إخفاء -->
            <div id="pos-pane-display" class="pos-tab-pane hidden p-5 space-y-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <label class="flex items-center gap-2 p-2.5 bg-gray-50 rounded border cursor-pointer font-bold">
                        <input type="checkbox" name="show_customer_search" checked class="w-4 h-4 text-[#1F2E4A] rounded">
                        <span>إظهار بحث العملاء</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 bg-gray-50 rounded border cursor-pointer font-bold">
                        <input type="checkbox" name="show_discount_button" checked class="w-4 h-4 text-[#1F2E4A] rounded">
                        <span>إظهار زر الخصم</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 bg-gray-50 rounded border cursor-pointer font-bold">
                        <input type="checkbox" name="show_product_image" checked class="w-4 h-4 text-[#1F2E4A] rounded">
                        <span>إظهار صورة الصنف</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 bg-gray-50 rounded border cursor-pointer font-bold">
                        <input type="checkbox" name="show_pending_bills" checked class="w-4 h-4 text-[#1F2E4A] rounded">
                        <span>إظهار الفواتير المعلقة</span>
                    </label>
                </div>
            </div>

            <!-- Tab 5: POS Terminals -->
            <div id="pos-pane-terminals" class="pos-tab-pane hidden p-5 space-y-3">
                <table class="w-full border-collapse border border-gray-200 text-right">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 font-bold">
                            <th class="p-2 border">رقم الجهاز</th>
                            <th class="p-2 border">اسم نقطة البيع (Terminal Name)</th>
                            <th class="p-2 border">المستودع الافتراضي</th>
                            <th class="p-2 border">صندوق الكاشير الافتراضي</th>
                            <th class="p-2 border">طابعة الفواتير (Thermal Printer)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-2 border font-bold text-center">POS-01</td>
                            <td class="p-2 border"><input type="text" value="كاشير الصالة الرئيسي" class="w-full border rounded px-2 py-1"></td>
                            <td class="p-2 border"><input type="text" value="مستودع المعرض" class="w-full border rounded px-2 py-1"></td>
                            <td class="p-2 border"><input type="text" value="صندوق كاشير 1" class="w-full border rounded px-2 py-1"></td>
                            <td class="p-2 border"><input type="text" value="EPSON TM-T20III (USB)" class="w-full border rounded px-2 py-1"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

<script>
function switchPosTab(tabId) {
    document.querySelectorAll('.pos-tab-pane').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.pos-tab-btn').forEach(el => {
        el.classList.remove('bg-[#1F2E4A]', 'text-white');
        el.classList.add('text-gray-600');
    });

    const activePane = document.getElementById('pos-pane-' + tabId);
    const activeBtn = document.getElementById('pos-btn-' + tabId);
    if(activePane) activePane.classList.remove('hidden');
    if(activeBtn) {
        activeBtn.classList.add('bg-[#1F2E4A]', 'text-white');
        activeBtn.classList.remove('text-gray-600');
    }
}
</script>
@endsection
