@extends('layouts.app')

@section('title', 'الفواتير والسندات | وثيق ERP')

@section('content')
<div class="space-y-6" dir="rtl">

    <!-- Header & Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">الحركات المالية والمخزنية</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">الفواتير والسندات</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 mt-1">مركز التحكم بالفواتير والسندات وضبط المخزون</h1>
            <p class="text-xs text-gray-500">الفواتير، الإرساليات، السندات المالية، محفظة الشيكات، الإشعارات الدائنة والمدينة، وضبط المخازن</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('erp.jofotara.hub') }}" class="px-4 py-2 bg-[#343A40] hover:bg-black text-white rounded-xl text-xs font-bold transition shadow-sm">
                الفوترة الوطنية (JoFotara)
            </a>
            <a href="{{ route('erp.invoices.editor', ['type' => 'sale']) }}" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                + فاتورة مبيعات
            </a>
            <a href="{{ route('erp.vouchers.payment_editor') }}" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                + سند صرف ودفع
            </a>
            <a href="{{ route('erp.journals.editor') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition border">
                + قيد يومية
            </a>
        </div>
    </div>

    <!-- 12 Master Cards Grid (مطابقة تماماً لفيديو وثيق الأصلي Frame 75) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Card 1: الفواتير -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">1. الفواتير</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700">فوترة</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.jofotara.hub') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span class="text-[#1F2E4A] font-bold">• الربط مع الفوترة الوطنية (JoFotara)</span>
                    <span class="text-[9px] bg-[#1F2E4A]/10 text-[#1F2E4A] px-1 rounded font-bold">ISTD</span>
                </a>
                <a href="{{ route('erp.invoices.editor', ['type' => 'sale']) }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• فاتورة مبيعات</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">محطة</span>
                </a>
                <a href="{{ route('erp.invoices.editor', ['type' => 'purchase']) }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• فاتورة مشتريات</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">محطة</span>
                </a>
                <a href="{{ route('erp.invoices') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• مردودات المبيعات</span>
                    <span class="text-[9px] text-gray-400">سجل</span>
                </a>
                <a href="{{ route('erp.invoices') }}" class="block p-1 rounded hover:bg-gray-50">• مردودات المشتريات</a>
                <a href="{{ route('erp.invoices.editor', ['type' => 'sale']) }}" class="block p-1 rounded hover:bg-gray-50">• عرض سعر رسمي</a>
                <a href="{{ route('erp.pos') }}" class="block p-1 rounded hover:bg-gray-50">• المبيعات المباشرة (POS)</a>
            </div>
        </div>

        <!-- Card 2: الإرساليات والطلبيات -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">2. الإرساليات والطلبيات</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">لوجستي</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.dispatches') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• إرسالية مبيعات</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">إرسال</span>
                </a>
                <a href="{{ route('erp.dispatches') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• إرسالية مشتريات</span>
                    <span class="text-[9px] text-gray-400">توريد</span>
                </a>
                <a href="{{ route('erp.dispatches') }}" class="block p-1 rounded hover:bg-gray-50">• طلبية مبيعات (حجز)</a>
                <a href="{{ route('erp.dispatches') }}" class="block p-1 rounded hover:bg-gray-50">• طلبية مشتريات</a>
                <a href="{{ route('erp.dispatches') }}" class="block p-1 rounded hover:bg-gray-50">• إرسالية داخلية (تحويل)</a>
                <a href="{{ route('erp.dispatches') }}" class="block p-1 rounded hover:bg-gray-50">• طلبية استيراد</a>
            </div>
        </div>

        <!-- Card 3: السندات المحاسبية -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">3. السندات المحاسبية</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-[#1F2E4A]/10 text-[#1F2E4A]">مالية</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.vouchers.editor', ['type' => 'receipt']) }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• سند قبض مالي</span>
                    <span class="text-[9px] text-emerald-600 font-bold">وارد</span>
                </a>
                <a href="{{ route('erp.vouchers.payment_editor') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• سند صرف ودفع</span>
                    <span class="text-[9px] text-emerald-600 font-bold">محطة صادر</span>
                </a>
                <a href="{{ route('erp.vouchers.editor', ['type' => 'payment']) }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• سند دفع موردين</span>
                    <span class="text-[9px] text-gray-400">دفع</span>
                </a>
                <a href="{{ route('erp.journals.editor') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• سند قيد يومية مزدوج</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">توازن</span>
                </a>
                <a href="{{ route('erp.vouchers') }}" class="block p-1 rounded hover:bg-gray-50">• سند الأقساط</a>
                <a href="{{ route('erp.vouchers') }}" class="block p-1 rounded hover:bg-gray-50">• تسديد الفواتير الآلي</a>
            </div>
        </div>

        <!-- Card 4: البنوك والشيكات -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">4. البنوك والشيكات</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">محفظة</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.cheques') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• عمليات الشيكات الصادرة</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">شيكات</span>
                </a>
                <a href="{{ route('erp.cheques') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• عمليات الشيكات الواردة</span>
                    <span class="text-[9px] text-gray-400">تحصيل</span>
                </a>
                <a href="{{ route('erp.cheques') }}" class="block p-1 rounded hover:bg-gray-50">• عمليات البنوك والتحويلات</a>
                <a href="{{ route('erp.cheques') }}" class="block p-1 rounded hover:bg-gray-50">• أرشيف الشيكات</a>
                <a href="{{ route('erp.cheques') }}" class="block p-1 rounded hover:bg-gray-50">• إخفاء وإظهار الشيكات</a>
                <a href="{{ route('erp.cheques') }}" class="block p-1 rounded hover:bg-gray-50">• دفتر الشيكات المطبوعة</a>
            </div>
        </div>

        <!-- Card 5: ضبط المخزون -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">5. ضبط المخزون</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">مستودع</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.stock_count') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• مطابقة الجرد والتسوية</span>
                    <span class="text-[9px] text-emerald-600 font-bold">جرد</span>
                </a>
                <a href="{{ route('erp.products') }}" class="block p-1 rounded hover:bg-gray-50">• سند إدخال بضاعة</a>
                <a href="{{ route('erp.products') }}" class="block p-1 rounded hover:bg-gray-50">• سند إخراج بضاعة</a>
                <a href="{{ route('erp.products') }}" class="block p-1 rounded hover:bg-gray-50">• سند استعمال داخلي</a>
                <a href="{{ route('erp.products') }}" class="block p-1 rounded hover:bg-gray-50">• سند إتلاف بضاعة</a>
                <a href="{{ route('erp.products') }}" class="block p-1 rounded hover:bg-gray-50">• بضائع تحت الحد الأدنى</a>
            </div>
        </div>

        <!-- Card 6: سندات أخرى -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">6. سندات أخرى</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">عمليات</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.invoices') }}" class="block p-1 rounded hover:bg-gray-50">• الترحيل الآلي للحركات</a>
                <a href="{{ route('erp.invoices') }}" class="block p-1 rounded hover:bg-gray-50">• الطباعة الآلية</a>
                <a href="{{ route('erp.customizer') }}" class="block p-1 rounded hover:bg-gray-50">• تعديل دفتر السندات</a>
                <a href="{{ route('erp.price_adjustment.editor') }}" class="block p-1 rounded hover:bg-gray-50">• سند تعديل تواريخ الصلاحية والـ Batch</a>
                <a href="{{ route('erp.products') }}" class="block p-1 rounded hover:bg-gray-50">• سند تعديل السيريال والقياسات</a>
            </div>
        </div>

        <!-- Card 7: الإشعارات -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">7. الإشعارات</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">تسوية</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.vouchers.editor', ['type' => 'receipt']) }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• إشعار دائن / خصم لعميل</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">دائن</span>
                </a>
                <a href="{{ route('erp.vouchers.editor', ['type' => 'payment']) }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• إشعار مدين / خصم من عميل</span>
                    <span class="text-[9px] text-gray-400">مدين</span>
                </a>
            </div>
        </div>

        <!-- Card 8: التحويل -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">8. التحويل</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">صناديق</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.journals.editor') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• سند من صندوق إلى صندوق</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">مناقلة</span>
                </a>
                <a href="{{ route('erp.journals.editor') }}" class="block p-1 rounded hover:bg-gray-50">• سند تحويل عملة وصرف</a>
            </div>
        </div>

        <!-- Card 9: الصيانة -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">9. الصيانة</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">خدمات</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.dispatches') }}" class="block p-1 rounded hover:bg-gray-50">• سند استلام جهاز للصيانة</a>
                <a href="{{ route('erp.dispatches') }}" class="block p-1 rounded hover:bg-gray-50">• سند الصيانة وقطع الغيار</a>
                <a href="{{ route('erp.dispatches') }}" class="block p-1 rounded hover:bg-gray-50">• سند تسليم الجهاز</a>
            </div>
        </div>

        <!-- Card 10: اتفاقية تأجير -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">10. اتفاقية تأجير</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">عقود</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.invoices.editor', ['type' => 'sale']) }}" class="block p-1 rounded hover:bg-gray-50">• إنشاء عقد واتفاقية تأجير</a>
                <a href="{{ route('erp.invoices') }}" class="block p-1 rounded hover:bg-gray-50">• متابعة أقساط الإيجار الدورية</a>
            </div>
        </div>

        <!-- Card 11: سند إنتاج -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">11. سند إنتاج</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">تصنيع</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.products') }}" class="block p-1 rounded hover:bg-gray-50">• سند إنتاج وتجميع أصناف</a>
                <a href="{{ route('erp.products') }}" class="block p-1 rounded hover:bg-gray-50">• شجرة مكونات المادة (BOM)</a>
            </div>
        </div>

        <!-- Card 12: الفوترة الآلية -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">12. الفوترة الآلية</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">أوتوميشن</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.invoices') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• الفوترة الدورية المجدولة</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">آلي</span>
                </a>
                <a href="{{ route('erp.invoices') }}" class="block p-1 rounded hover:bg-gray-50">• استيراد الفواتير من ملف Excel</a>
            </div>
        </div>

    </div>

</div>
@endsection

