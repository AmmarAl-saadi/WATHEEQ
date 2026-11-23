@extends('layouts.app')

@section('title', 'تعريفات النظام الشاملة | وثيق ERP')

@section('content')
<div class="space-y-6" dir="rtl">

    <!-- Header & Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">القواعد والبيانات الأساسية</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">منظومة وثيق</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 mt-1">التعريفات العامة والإعدادات التشغيلية</h1>
            <p class="text-xs text-gray-500">البطاقات التعريفية، المخزون، العملاء، البنوك، شجرة الحسابات ومراكز التكلفة</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('erp.price_adjustment.editor') }}" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                + سند تعديل أسعار البيع
            </a>
            <a href="{{ route('erp.customizer') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition border">
                تخصيص المصطلحات والشركة
            </a>
        </div>
    </div>

    <!-- 11 Master Cards Grid (مطابقة تماماً لفيديو وثيق الأصلي Frame 15) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Card 1: تعريفات -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">1. تعريفات</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">أساسي</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.customizer') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• معلومات الشركة</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">إعداد</span>
                </a>
                <a href="{{ route('erp.phonebook') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• دليل الهاتف</span>
                    <span class="text-[9px] text-gray-400">سريع</span>
                </a>
                <a href="{{ route('erp.notepad') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• المفكرة</span>
                    <span class="text-[9px] text-gray-400">مهام</span>
                </a>
                <a href="{{ route('erp.customizer') }}" class="block p-1 rounded hover:bg-gray-50">• العملات وأسعار الصرف</a>
                <a href="{{ route('erp.accounts') }}" class="block p-1 rounded hover:bg-gray-50">• الحالات والمراحل</a>
                <a href="{{ route('erp.accounts') }}" class="block p-1 rounded hover:bg-gray-50">• الأرصدة الافتتاحية</a>
                <a href="{{ route('erp.accounts') }}" class="block p-1 rounded hover:bg-gray-50">• رأس المال والشركاء</a>
                <a href="{{ route('erp.customizer') }}" class="block p-1 rounded hover:bg-gray-50">• المصطلحات ودفتر السندات</a>
            </div>
        </div>

        <!-- Card 2: المخزون -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">2. المخزون</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-[#1F2E4A]/10 text-[#1F2E4A]">مستودعات</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.products') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• بطاقة الأصناف</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">الأصناف</span>
                </a>
                <a href="{{ route('erp.price_lists') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• فئات سعر البيع</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">تسعير</span>
                </a>
                <a href="{{ route('erp.stock_count') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• مطابقة الجرد</span>
                    <span class="text-[9px] text-emerald-600 font-bold">جرد فعلي</span>
                </a>
                <a href="{{ route('erp.price_adjustment.editor') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• سند تعديل أسعار البيع</span>
                    <span class="text-[9px] text-amber-600 font-bold">محطة عمل</span>
                </a>
                <a href="{{ route('erp.products') }}" class="block p-1 rounded hover:bg-gray-50">• إعدادات المخزون</a>
                <a href="{{ route('erp.products') }}" class="block p-1 rounded hover:bg-gray-50">• أرصدة المخزون الابتدائية</a>
                <a href="{{ route('erp.products') }}" class="block p-1 rounded hover:bg-gray-50">• تعديل أسعار التكلفة للقيم صفر</a>
            </div>
        </div>

        <!-- Card 3: العملاء -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">3. العملاء والموردين</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">ذمم</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.contacts') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• بطاقة العملاء والموردين</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">دليل</span>
                </a>
                <a href="{{ route('erp.contacts.editor') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• إضافة عميل / مورد جديد</span>
                    <span class="text-[9px] text-emerald-600 font-bold">بطاقة</span>
                </a>
                <a href="{{ route('erp.contacts') }}" class="block p-1 rounded hover:bg-gray-50">• تصنيف ومستويات العملاء</a>
                <a href="{{ route('erp.contacts') }}" class="block p-1 rounded hover:bg-gray-50">• أنواع البطاقات والخصومات</a>
                <a href="{{ route('erp.contacts') }}" class="block p-1 rounded hover:bg-gray-50">• أرصدة العملاء الابتدائية</a>
                <a href="{{ route('erp.contacts') }}" class="block p-1 rounded hover:bg-gray-50">• تعديل وتجميد أرصدة العملاء</a>
            </div>
        </div>

        <!-- Card 4: البنوك -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">4. البنوك وطرق التسديد</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">مالي</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.cheques') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• الحسابات البنكية والشخصية</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">بنوك</span>
                </a>
                <a href="{{ route('erp.cheques') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• فروع البنوك المعتمدة</span>
                    <span class="text-[9px] text-gray-400">فروع</span>
                </a>
                <a href="{{ route('erp.vouchers') }}" class="block p-1 rounded hover:bg-gray-50">• طرق التسديد والتحصيل</a>
                <a href="{{ route('erp.cheques') }}" class="block p-1 rounded hover:bg-gray-50">• أرصدة الحسابات الشخصية</a>
            </div>
        </div>

        <!-- Card 5: المصاريف / الخدمات -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">5. المصاريف / الخدمات</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">تشغيل</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.expenses') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• بطاقة المصاريف الرئيسية</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">مصاريف</span>
                </a>
                <a href="{{ route('erp.vouchers.payment_editor') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• سند صرف مصروفات</span>
                    <span class="text-[9px] text-emerald-600 font-bold">صرف</span>
                </a>
                <a href="{{ route('erp.expenses') }}" class="block p-1 rounded hover:bg-gray-50">• احتساب مصاريف مدفوعة مقدماً</a>
                <a href="{{ route('erp.products') }}" class="block p-1 rounded hover:bg-gray-50">• تعريف الخدمات والأجور</a>
            </div>
        </div>

        <!-- Card 6: شجرة الحسابات -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">6. شجرة الحسابات</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">أستاذ عام</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.accounts') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• دليل الحسابات الشجري</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">عرض</span>
                </a>
                <a href="{{ route('erp.journals.editor') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• سند قيد يومية مزدوج</span>
                    <span class="text-[9px] text-emerald-600 font-bold">محطة</span>
                </a>
                <a href="{{ route('erp.accounts') }}" class="block p-1 rounded hover:bg-gray-50">• حسابات أصول ميزانية أخرى</a>
                <a href="{{ route('erp.accounts') }}" class="block p-1 rounded hover:bg-gray-50">• حسابات خصوم ميزانية أخرى</a>
                <a href="{{ route('erp.accounts') }}" class="block p-1 rounded hover:bg-gray-50">• حسابات قائمة الدخل</a>
            </div>
        </div>

        <!-- Card 7: مراكز التكلفة -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">7. مراكز التكلفة</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">فروع</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.accounts') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• بطاقة مراكز التكلفة</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">فروع</span>
                </a>
                <a href="{{ route('erp.accounts') }}" class="block p-1 rounded hover:bg-gray-50">• توزيع نسب التكاليف بالمشاريع</a>
            </div>
        </div>

        <!-- Card 8: الصناديق النقدية -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">8. الصناديق النقدية</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">سيولة</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.vouchers') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• تعريف الصناديق والخزائن</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">صناديق</span>
                </a>
                <a href="{{ route('erp.pos.shifts') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• تسليم واستلام عهد الكاش</span>
                    <span class="text-[9px] text-amber-600 font-bold">ورديات</span>
                </a>
            </div>
        </div>

        <!-- Card 9: الأصول الثابتة -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">9. الأصول الثابتة</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">إهلاك</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.fixed_assets') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• بطاقة الأصول الثابتة</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">أصول</span>
                </a>
                <a href="{{ route('erp.fixed_assets') }}" class="block p-1 rounded hover:bg-gray-50">• احتساب الإهلاك التراكمي والقيمة الدفترية</a>
            </div>
        </div>

        <!-- Card 10: العناوين والمناطق -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">10. العناوين والمناطق</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">لوجستي</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.contacts') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• المحافظات والمدن</span>
                    <span class="text-[9px] text-gray-400">مناطق</span>
                </a>
                <a href="{{ route('erp.dispatches') }}" class="block p-1 rounded hover:bg-gray-50">• خطوط سير مناديب التوزيع</a>
            </div>
        </div>

        <!-- Card 11: معالجة الاستيراد -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">11. معالجة الاستيراد</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">جمارك</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.invoices.editor', ['type' => 'purchase']) }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• طلبية استيراد وشحن</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">شحن</span>
                </a>
                <a href="{{ route('erp.invoicing.hub') }}" class="block p-1 rounded hover:bg-gray-50">• توزيع الرسوم الجمركية على التكلفة</a>
            </div>
        </div>

    </div>

</div>
@endsection

