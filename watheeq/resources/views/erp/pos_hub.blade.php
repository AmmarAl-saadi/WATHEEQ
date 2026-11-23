@extends('layouts.app')

@section('title', 'نقاط البيع (POS) | وثيق ERP')

@section('content')
<div class="space-y-6" dir="rtl">

    <!-- Header & Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">منظومة التجزئة والمطاعم</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">نقاط البيع</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 mt-1">نقاط البيع (POS): الكاشير، الحملات، والورديات</h1>
            <p class="text-xs text-gray-500">إدارة فواتير البيع المباشر، طاولات المطاعم، العروض وحملات النقاط، وجرد الصناديق</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('erp.pos') }}" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                فتح شاشة كاشير POS المباشرة
            </a>
            <a href="{{ route('erp.pos.shifts') }}" class="px-4 py-2.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                تسليم واستلام عهدة الوردية
            </a>
        </div>
    </div>

    <!-- Quick KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">مبيعات اليوم النقدية والبطاقات</div>
            <div class="text-2xl font-black text-[#1F2E4A]">{{ number_format($todaySales, 2) }} <span class="text-xs font-normal text-gray-600">{{ $tenant->currency ?? 'JOD' }}</span></div>
            <div class="text-[11px] text-gray-400 mt-1">إجمالي الفواتير الصادرة اليوم</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">الحملات الترويجية الفعالة</div>
            <div class="text-2xl font-black text-emerald-600">{{ $campaignsCount }} <span class="text-xs font-normal text-gray-600">حملة نشطة</span></div>
            <div class="text-[11px] text-gray-400 mt-1">عروض وخصومات ونقاط ولاء</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">طاولات الصالة المعرفة</div>
            <div class="text-2xl font-black text-gray-900">{{ $tablesCount }} <span class="text-xs font-normal text-gray-600">طاولة</span></div>
            <div class="text-[11px] text-gray-400 mt-1">صالات المطاعم والمقاهي</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">الوردية الحالية للكاشير</div>
            <div class="text-2xl font-black text-emerald-700">مفتوحة <span class="text-xs font-normal text-gray-600">نشطة</span></div>
            <div class="text-[11px] text-gray-400 mt-1">صندوق الكاشير الرئيسي</div>
        </div>
    </div>

    <!-- 5 Master Modules of وثيق POS (مطابقة تماماً لفيديو وثيق الأصلي Frame 165) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Card 1: تعريفات -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">1. تعريفات POS</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">قوائم</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.pos.tables') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• الطاولات وصالات الجلوس</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">طاولات</span>
                </a>
                <a href="{{ route('erp.products') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• تعريف القوائم والمجموعات السريعة</span>
                    <span class="text-[9px] text-gray-400">أصناف</span>
                </a>
                <a href="{{ route('erp.pos.campaigns') }}" class="block p-1 rounded hover:bg-gray-50">• خصومات البطاقات البنكية</a>
                <a href="{{ route('erp.pos') }}" class="block p-1 rounded hover:bg-gray-50">• ربط الميزان الإلكتروني للوزن المباشر</a>
            </div>
        </div>

        <!-- Card 2: الحملات والعروض -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">2. الحملات الترويجية</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700">عروض</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.pos.campaigns') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• الحملة الشاملة والخصومات</span>
                    <span class="text-[9px] text-emerald-600 font-bold">حملات</span>
                </a>
                <a href="{{ route('erp.pos.campaigns') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• حملة نقاط الولاء والمكافآت</span>
                    <span class="text-[9px] text-gray-400">نقاط</span>
                </a>
                <a href="{{ route('erp.pos.campaigns') }}" class="block p-1 rounded hover:bg-gray-50">• حملة بطاقات وأسعار خاصة</a>
                <a href="{{ route('erp.pos.campaigns') }}" class="block p-1 rounded hover:bg-gray-50">• حملة خصم على الشراء خلال فترة محددة</a>
            </div>
        </div>

        <!-- Card 3: تحكم والورديات -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">3. تحكم والورديات</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-[#1F2E4A]/10 text-[#1F2E4A]">عهدة</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.pos.shifts') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• تسليم واستلام العهدة</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">ورديات</span>
                </a>
                <a href="{{ route('erp.journals.editor') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• تحويل حركات POS إلى المحاسبة</span>
                    <span class="text-[9px] text-emerald-600 font-bold">ترحيل</span>
                </a>
                <a href="{{ route('erp.pos.tables') }}" class="block p-1 rounded hover:bg-gray-50">• الطاولات والمستخدمين</a>
                <a href="{{ route('erp.products') }}" class="block p-1 rounded hover:bg-gray-50">• عهدة أصناف الكاشير</a>
            </div>
        </div>

        <!-- Card 4: كاشير POS -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">4. عمليات POS</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">بيع سريع</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.pos') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• شاشة فاتورة POS السريعة</span>
                    <span class="text-[9px] text-emerald-600 font-bold">كاشير</span>
                </a>
                <a href="{{ route('erp.pos') }}" class="block p-1 rounded hover:bg-gray-50">• تعليق واسترجاع الفواتير (Hold)</a>
                <a href="{{ route('erp.customizer') }}" class="block p-1 rounded hover:bg-gray-50">• فئات العملات المقبولة بالكاشير</a>
                <a href="{{ route('erp.customizer') }}" class="block p-1 rounded hover:bg-gray-50">• خيارات طابعة الإيصالات 80mm ودرج الكاش</a>
            </div>
        </div>

    </div>

    <!-- Card 5: تقارير الـ POS التفصيلية (15 تقرير مطابق لوثيق) -->
    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm space-y-3">
        <div class="flex items-center justify-between border-b pb-2">
            <h3 class="text-sm font-black text-[#1F2E4A]">5. تقارير الـ POS التحليلية</h3>
            <span class="text-xs font-bold text-gray-500">15 تقريراً تحليلياً شاملاً</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2.5 text-xs">
            <a href="{{ route('erp.reports') }}" class="p-2 rounded-lg border border-gray-100 hover:border-[#1F2E4A] hover:bg-gray-50 flex flex-col">
                <span class="font-bold text-gray-800">• كشف فواتير POS</span>
                <span class="text-[10px] text-gray-400">سجل فواتير الصندوق</span>
            </a>
            <a href="{{ route('erp.reports') }}" class="p-2 rounded-lg border border-gray-100 hover:border-[#1F2E4A] hover:bg-gray-50 flex flex-col">
                <span class="font-bold text-gray-800">• كشف حركات الفواتير</span>
                <span class="text-[10px] text-gray-400">حركات تفصيلية</span>
            </a>
            <a href="{{ route('erp.reports') }}" class="p-2 rounded-lg border border-gray-100 hover:border-[#1F2E4A] hover:bg-gray-50 flex flex-col">
                <span class="font-bold text-gray-800">• مجاميع حركات الأصناف</span>
                <span class="text-[10px] text-gray-400">كميات ومبالغ</span>
            </a>
            <a href="{{ route('erp.reports') }}" class="p-2 rounded-lg border border-gray-100 hover:border-[#1F2E4A] hover:bg-gray-50 flex flex-col">
                <span class="font-bold text-gray-800">• تقرير حملة نقاط</span>
                <span class="text-[10px] text-gray-400">نقاط مكتسبة</span>
            </a>
            <a href="{{ route('erp.reports') }}" class="p-2 rounded-lg border border-gray-100 hover:border-[#1F2E4A] hover:bg-gray-50 flex flex-col">
                <span class="font-bold text-gray-800">• تقرير سحب نقاط</span>
                <span class="text-[10px] text-gray-400">نقاط مستبدلة</span>
            </a>
            <a href="{{ route('erp.reports') }}" class="p-2 rounded-lg border border-gray-100 hover:border-[#1F2E4A] hover:bg-gray-50 flex flex-col">
                <span class="font-bold text-gray-800">• حركة الأصناف POS</span>
                <span class="text-[10px] text-gray-400">سحب المخازن</span>
            </a>
            <a href="{{ route('erp.reports') }}" class="p-2 rounded-lg border border-gray-100 hover:border-[#1F2E4A] hover:bg-gray-50 flex flex-col">
                <span class="font-bold text-gray-800">• كشف أوقات الفواتير</span>
                <span class="text-[10px] text-gray-400">ذروة المبيعات</span>
            </a>
            <a href="{{ route('erp.reports') }}" class="p-2 rounded-lg border border-gray-100 hover:border-[#1F2E4A] hover:bg-gray-50 flex flex-col">
                <span class="font-bold text-gray-800">• كشف فواتير معلقة</span>
                <span class="text-[10px] text-gray-400">معلقة على الطاولات</span>
            </a>
            <a href="{{ route('erp.reports') }}" class="p-2 rounded-lg border border-gray-100 hover:border-[#1F2E4A] hover:bg-gray-50 flex flex-col">
                <span class="font-bold text-gray-800">• تقرير العهدة والدرج</span>
                <span class="text-[10px] text-gray-400">فوارق العهدة</span>
            </a>
            <a href="{{ route('erp.reports') }}" class="p-2 rounded-lg border border-gray-100 hover:border-[#1F2E4A] hover:bg-gray-50 flex flex-col">
                <span class="font-bold text-gray-800">• كشف حساب صندوق POS</span>
                <span class="text-[10px] text-gray-400">نقد وفيزا</span>
            </a>
            <a href="{{ route('erp.reports') }}" class="p-2 rounded-lg border border-gray-100 hover:border-[#1F2E4A] hover:bg-gray-50 flex flex-col">
                <span class="font-bold text-gray-800">• نسب المبيعات الشهرية</span>
                <span class="text-[10px] text-gray-400">مقارنات الأداء</span>
            </a>
            <a href="{{ route('erp.reports') }}" class="p-2 rounded-lg border border-gray-100 hover:border-[#1F2E4A] hover:bg-gray-50 flex flex-col">
                <span class="font-bold text-gray-800">• تقرير فتح الصندوق</span>
                <span class="text-[10px] text-gray-400">سجل أوقات الفتح</span>
            </a>
            <a href="{{ route('erp.reports') }}" class="p-2 rounded-lg border border-gray-100 hover:border-[#1F2E4A] hover:bg-gray-50 flex flex-col">
                <span class="font-bold text-gray-800">• بطاقات الائتمان</span>
                <span class="text-[10px] text-gray-400">تسويات البنوك</span>
            </a>
            <a href="{{ route('erp.reports') }}" class="p-2 rounded-lg border border-gray-100 hover:border-[#1F2E4A] hover:bg-gray-50 flex flex-col">
                <span class="font-bold text-gray-800">• تقرير الأرقام والأوقات</span>
                <span class="text-[10px] text-gray-400">تحليل الموظفين</span>
            </a>
            <a href="{{ route('erp.reports') }}" class="p-2 rounded-lg border border-gray-100 hover:border-[#1F2E4A] hover:bg-gray-50 flex flex-col">
                <span class="font-bold text-gray-800">• تقرير نسب المبيعات</span>
                <span class="text-[10px] text-gray-400">أرباح الأصناف</span>
            </a>
        </div>
    </div>

</div>
@endsection


