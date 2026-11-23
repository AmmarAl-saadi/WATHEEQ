@extends('layouts.app')

@section('title', 'الواجهة الرئيسية | وثيق ERP')

@section('content')
<div class="space-y-6">
    <!-- Quick Action Launch Tiles (Clean Corporate Typography, No Emojis) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
        <a href="{{ route('erp.invoices.editor', ['type' => 'sale']) }}" class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm hover:border-[#1F2E4A] hover:shadow transition flex flex-col items-center text-center">
            <span class="text-xs font-bold text-gray-800">فاتورة مبيعات</span>
            <span class="text-[11px] text-gray-500 font-medium mt-1">إصدار ضريبي</span>
        </a>

        <a href="{{ route('erp.invoices.editor', ['type' => 'purchase']) }}" class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm hover:border-[#1F2E4A] hover:shadow transition flex flex-col items-center text-center">
            <span class="text-xs font-bold text-gray-800">فاتورة مشتريات</span>
            <span class="text-[11px] text-gray-500 font-medium mt-1">توريد مخزون</span>
        </a>

        <a href="{{ route('erp.vouchers.editor', ['type' => 'receipt']) }}" class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm hover:border-[#1F2E4A] hover:shadow transition flex flex-col items-center text-center">
            <span class="text-xs font-bold text-gray-800">سند قبض</span>
            <span class="text-[11px] text-gray-500 font-medium mt-1">مقبوضات واردة</span>
        </a>

        <a href="{{ route('erp.vouchers.editor', ['type' => 'payment']) }}" class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm hover:border-[#1F2E4A] hover:shadow transition flex flex-col items-center text-center">
            <span class="text-xs font-bold text-gray-800">سند دفع</span>
            <span class="text-[11px] text-gray-500 font-medium mt-1">دفعات موردين</span>
        </a>

        <a href="{{ route('erp.vouchers.payment_editor') }}" class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm hover:border-[#1F2E4A] hover:shadow transition flex flex-col items-center text-center">
            <span class="text-xs font-bold text-gray-800">سند صرف</span>
            <span class="text-[11px] text-gray-500 font-medium mt-1">مصاريف عامة</span>
        </a>

        <a href="{{ route('erp.phonebook') }}" class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm hover:border-[#1F2E4A] hover:shadow transition flex flex-col items-center text-center">
            <span class="text-xs font-bold text-gray-800">دليل الهاتف</span>
            <span class="text-[11px] text-gray-500 font-medium mt-1">العملاء والموردين</span>
        </a>

        <a href="{{ route('erp.notepad') }}" class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm hover:border-[#1F2E4A] hover:shadow transition flex flex-col items-center text-center">
            <span class="text-xs font-bold text-gray-800">المفكرة</span>
            <span class="text-[11px] text-gray-500 font-medium mt-1">ملاحظات ومواعيد</span>
        </a>
    </div>

    <!-- Comparative Bar Graph Visualizer -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <div>
                <h3 class="text-sm font-bold text-[#1F2E4A]">المؤشر المالي العام (المبيعات مقابل المصروفات والمشتريات)</h3>
                <p class="text-xs text-gray-400">رسم بياني تحليلي لمقارنة الحركات المالية المعتمدة في النظام</p>
            </div>
            <div class="flex items-center gap-4 text-xs font-bold">
                <span class="flex items-center gap-1.5 text-blue-600">
                    <span class="w-3 h-3 rounded-sm bg-blue-500 inline-block"></span> إجمالي المبيعات
                </span>
                <span class="flex items-center gap-1.5 text-amber-600">
                    <span class="w-3 h-3 rounded-sm bg-amber-400 inline-block"></span> مجموع المصروفات
                </span>
                <span class="flex items-center gap-1.5 text-purple-600">
                    <span class="w-3 h-3 rounded-sm bg-purple-600 inline-block"></span> إجمالي المشتريات
                </span>
            </div>
        </div>

        <div class="space-y-4 pt-2">
            <div>
                <div class="flex justify-between text-xs font-bold mb-1">
                    <span class="text-blue-700">إجمالي المبيعات المكتملة</span>
                    <span class="font-mono">{{ number_format($totalSales, 2) }} {{ $tenant->currency }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3.5 overflow-hidden">
                    <div class="bg-blue-500 h-3.5 rounded-full" style="width: 78%"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between text-xs font-bold mb-1">
                    <span class="text-amber-700">مجموع المصروفات العمومية والتشغيلية</span>
                    <span class="font-mono">12,500.00 {{ $tenant->currency }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3.5 overflow-hidden">
                    <div class="bg-amber-400 h-3.5 rounded-full" style="width: 35%"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between text-xs font-bold mb-1">
                    <span class="text-purple-700">إجمالي المشتريات وتوريدات المخزون</span>
                    <span class="font-mono">{{ number_format($totalPayables, 2) }} {{ $tenant->currency }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-3.5 overflow-hidden">
                    <div class="bg-purple-600 h-3.5 rounded-full" style="width: 52%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Financial Health Counters -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm relative overflow-hidden">
            <span class="text-xs font-semibold text-gray-500">السيولة النقدية والبنكية</span>
            <h3 class="text-2xl font-black text-[#1F2E4A] mt-1">{{ number_format($totalCashBank, 2) }} <span class="text-xs text-[#1F2E4A]">{{ $tenant->currency }}</span></h3>
            <p class="text-[10px] text-gray-400 mt-1">الصندوق الرئيسي + البنك العربي</p>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-[#1F2E4A]"></div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm relative overflow-hidden">
            <span class="text-xs font-semibold text-gray-500">الذمم المدينة (حقوق الزبائن)</span>
            <h3 class="text-2xl font-black text-gray-800 mt-1">{{ number_format($totalReceivables, 2) }} <span class="text-xs text-[#1F2E4A]">{{ $tenant->currency }}</span></h3>
            <p class="text-[10px] text-gray-400 mt-1">مستحقة لدى العملاء</p>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-blue-600"></div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm relative overflow-hidden">
            <span class="text-xs font-semibold text-gray-500">الذمم الدائنة (التزامات للموردين)</span>
            <h3 class="text-2xl font-black text-red-600 mt-1">{{ number_format($totalPayables, 2) }} <span class="text-xs text-[#1F2E4A]">{{ $tenant->currency }}</span></h3>
            <p class="text-[10px] text-gray-400 mt-1">واجبة السداد للموردين</p>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-red-600"></div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm relative overflow-hidden">
            <span class="text-xs font-semibold text-gray-500">تنبيهات المخزون</span>
            <h3 class="text-2xl font-black text-amber-600 mt-1">{{ $lowStockCount }} <span class="text-xs text-gray-400">أصناف</span></h3>
            <p class="text-[10px] text-amber-700 font-bold mt-1">وصلت حد الطلب الأدنى</p>
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-amber-500"></div>
        </div>
    </div>
</div>
@endsection

