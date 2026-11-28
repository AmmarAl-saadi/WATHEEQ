@extends('layouts.app')

@section('title', 'كشف حركات الأصناف والمخزون | حساباتي | وثيق ERP')

@section('content')
<div class="space-y-4 font-sans text-xs" dir="rtl">

    <!-- Top Ribbon (Hesabate Reports Frame 10_11_1i) -->
    <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-2 no-print">
        <div class="flex items-center gap-2">
            <span class="text-sm font-black text-gray-800">كشف حركات الأصناف والمستودعات (Stock Item Ledger)</span>
            <span class="bg-[#1F2E4A] text-white text-[11px] px-2 py-0.5 rounded font-black">حساباتي التقارير</span>
        </div>
        <div class="flex items-center gap-1.5 flex-wrap">
            <button type="button" onclick="window.print()" class="px-4 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
                طباعة الكشف (A4)
            </button>
            <a href="{{ route('erp.reports') }}" class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                شجرة التقارير
            </a>
        </div>
    </div>

    <!-- Filter Matrix (طابق شاشة حساباتي Frame 15) -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-gray-600 font-bold mb-1">الصنف أو الباركود:</label>
                <select class="w-full border rounded px-2 py-1 font-bold bg-yellow-50">
                    <option value="">-- جميع الأصناف والمخزون --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->barcode ?? 'N/A' }} - {{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-gray-600 font-bold mb-1">المستودع / المخزن:</label>
                <select class="w-full border rounded px-2 py-1">
                    <option value="all">كافة المستودعات والفروع</option>
                    <option value="main">المستودع الرئيسي</option>
                    <option value="showroom">مستودع المعرض</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-600 font-bold mb-1">نوع الحركة:</label>
                <select class="w-full border rounded px-2 py-1 font-bold">
                    <option value="all">كافة الحركات (مبيعات، مشتريات، تحويل، تصنيع)</option>
                    <option value="sale">فواتير مبيعات</option>
                    <option value="purchase">فواتير مشتريات</option>
                    <option value="transfer">تحويلات مستودعية</option>
                    <option value="manufacturing">سندات تصنيع</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-600 font-bold mb-1">الفترة الزمنية:</label>
                <div class="flex gap-1">
                    <input type="date" value="{{ date('Y-01-01') }}" class="w-1/2 border rounded px-1.5 py-1">
                    <input type="date" value="{{ date('Y-m-d') }}" class="w-1/2 border rounded px-1.5 py-1">
                </div>
            </div>
        </div>

        <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-4 text-[11px]">
                <label class="flex items-center gap-1 cursor-pointer">
                    <input type="checkbox" checked class="w-3.5 h-3.5 text-[#1F2E4A] rounded">
                    <span>إظهار الباتش ورقم التشغيلة (Batch)</span>
                </label>
                <label class="flex items-center gap-1 cursor-pointer">
                    <input type="checkbox" checked class="w-3.5 h-3.5 text-[#1F2E4A] rounded">
                    <span>إظهار تاريخ الصلاحية (Expiry)</span>
                </label>
                <label class="flex items-center gap-1 cursor-pointer">
                    <input type="checkbox" checked class="w-3.5 h-3.5 text-[#1F2E4A] rounded">
                    <span>إظهار التكلفة والربح المتوقع</span>
                </label>
            </div>
            <button type="button" class="px-6 py-1.5 bg-[#1F2E4A] text-white font-bold rounded shadow-sm hover:bg-[#343A40] transition">
                تطبيق البحث
            </button>
        </div>
    </div>

    <!-- Stock Ledger Grid -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 space-y-3">
        <table class="w-full border-collapse border border-gray-200 text-right text-xs">
            <thead>
                <tr class="bg-[#1F2E4A] text-white">
                    <th class="p-2 border">التاريخ</th>
                    <th class="p-2 border">رقم الحركة</th>
                    <th class="p-2 border">نوع الحركة</th>
                    <th class="p-2 border">المستودع</th>
                    <th class="p-2 border">اسم الصنف</th>
                    <th class="p-2 border">الوحدة</th>
                    <th class="p-2 border">الوارد (+)</th>
                    <th class="p-2 border">المنصرف (-)</th>
                    <th class="p-2 border">الرصيد المتبقي</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 font-mono">
                <tr>
                    <td class="p-2 border font-sans">{{ date('Y-m-01') }}</td>
                    <td class="p-2 border">PUR-2026-004</td>
                    <td class="p-2 border font-sans font-bold text-blue-700">فاتورة مشتريات</td>
                    <td class="p-2 border font-sans">المستودع الرئيسي</td>
                    <td class="p-2 border font-sans font-bold">زيت زيتون بكر ممتاز 16 لتر</td>
                    <td class="p-2 border font-sans">تنكة</td>
                    <td class="p-2 border text-emerald-700 font-bold">100</td>
                    <td class="p-2 border text-gray-400">0</td>
                    <td class="p-2 border font-bold">100</td>
                </tr>
                <tr>
                    <td class="p-2 border font-sans">{{ date('Y-m-05') }}</td>
                    <td class="p-2 border">TRF-2026-012</td>
                    <td class="p-2 border font-sans font-bold text-amber-700">تحويل مستودعي</td>
                    <td class="p-2 border font-sans">مستودع المعرض</td>
                    <td class="p-2 border font-sans font-bold">زيت زيتون بكر ممتاز 16 لتر</td>
                    <td class="p-2 border font-sans">تنكة</td>
                    <td class="p-2 border text-gray-400">0</td>
                    <td class="p-2 border text-rose-700 font-bold">20</td>
                    <td class="p-2 border font-bold">80</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
