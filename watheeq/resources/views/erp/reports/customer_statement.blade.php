@extends('layouts.app')

@section('title', 'كشف حساب عميل مفصل | حساباتي | وثيق ERP')

@section('content')
<div class="space-y-4 font-sans text-xs" dir="rtl">

    <!-- Top Ribbon (Hesabate Reports Frame 10_10_2i) -->
    <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-2 no-print">
        <div class="flex items-center gap-2">
            <span class="text-sm font-black text-gray-800">كشف حساب عميل (Account Statement)</span>
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

    <!-- Filter & Options Matrix (طابق تماماً شاشة حساباتي Frame 5) -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-gray-600 font-bold mb-1">العميل / الحساب:</label>
                <select class="w-full border rounded px-2 py-1 font-bold bg-yellow-50">
                    <option value="">-- اختر عميل أو حساب --</option>
                    @foreach($contacts as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone ?? 'لا يوجد هاتف' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-gray-600 font-bold mb-1">من تاريخ:</label>
                <input type="date" value="{{ date('Y-01-01') }}" class="w-full border rounded px-2.5 py-1">
            </div>
            <div>
                <label class="block text-gray-600 font-bold mb-1">إلى تاريخ:</label>
                <input type="date" value="{{ date('Y-m-d') }}" class="w-full border rounded px-2.5 py-1">
            </div>
            <div>
                <label class="block text-gray-600 font-bold mb-1">العملة:</label>
                <select class="w-full border rounded px-2 py-1 font-bold">
                    <option value="JOD">دينار أردني (JOD)</option>
                    <option value="USD">دولار أمريكي (USD)</option>
                </select>
            </div>
        </div>

        <!-- Checkbox Options Grid (Hesabate Options Matrix) -->
        <div class="pt-3 border-t border-gray-100">
            <span class="block font-bold text-gray-700 mb-2">خيارات العرض والاحتساب المتقدمة:</span>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-[11px]">
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" checked class="w-3.5 h-3.5 text-[#1F2E4A] rounded">
                    <span>احتساب الرصيد المدور</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" checked class="w-3.5 h-3.5 text-[#1F2E4A] rounded">
                    <span>إظهار تفاصيل الفواتير</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" checked class="w-3.5 h-3.5 text-[#1F2E4A] rounded">
                    <span>إظهار تفاصيل السندات</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" checked class="w-3.5 h-3.5 text-[#1F2E4A] rounded">
                    <span>إظهار الشيكات المرتجعة</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" checked class="w-3.5 h-3.5 text-[#1F2E4A] rounded">
                    <span>إظهار الشيكات برسم التحصيل</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" checked class="w-3.5 h-3.5 text-[#1F2E4A] rounded">
                    <span>إظهار الملاحظات والبيان</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" class="w-3.5 h-3.5 text-[#1F2E4A] rounded">
                    <span>إظهار الحركات بالعملات الأخرى</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="checkbox" class="w-3.5 h-3.5 text-[#1F2E4A] rounded">
                    <span>إظهار تسوية طرق التسديد</span>
                </label>
            </div>
            <div class="flex justify-end mt-3">
                <button type="button" class="px-6 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white font-bold rounded shadow-sm transition">
                    تطبيق الكشف
                </button>
            </div>
        </div>
    </div>

    <!-- Printable Statement Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 space-y-4">
        <div class="border-b pb-3 flex justify-between items-center">
            <div>
                <h3 class="font-black text-sm text-[#1F2E4A]">كشف حساب العميل والذمم المدينة</h3>
                <span class="text-gray-500">الفترة من {{ date('Y-01-01') }} إلى {{ date('Y-m-d') }}</span>
            </div>
            <div class="text-left font-mono">
                <span class="text-xs text-gray-500 block">الرصيد القائم:</span>
                <span class="text-base font-black text-rose-700">1,250.00 {{ $tenant->currency ?? 'JOD' }}</span>
            </div>
        </div>

        <table class="w-full border-collapse border border-gray-200 text-right text-xs">
            <thead>
                <tr class="bg-[#1F2E4A] text-white">
                    <th class="p-2 border border-gray-300">التاريخ</th>
                    <th class="p-2 border border-gray-300">نوع الحركة والسند</th>
                    <th class="p-2 border border-gray-300">الرقم المرجعي</th>
                    <th class="p-2 border border-gray-300">البيان والتفاصيل</th>
                    <th class="p-2 border border-gray-300">مدين (+)</th>
                    <th class="p-2 border border-gray-300">دائن (-)</th>
                    <th class="p-2 border border-gray-300">الرصيد التراكمي</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 font-mono">
                <tr class="bg-gray-50/80 font-bold">
                    <td class="p-2 border" colspan="4">الرصيد المدور الافتتاحي</td>
                    <td class="p-2 border">0.00</td>
                    <td class="p-2 border">0.00</td>
                    <td class="p-2 border text-[#1F2E4A]">0.00</td>
                </tr>
                <tr>
                    <td class="p-2 border font-sans">{{ date('Y-m-05') }}</td>
                    <td class="p-2 border font-sans font-bold">فاتورة مبيعات آجل</td>
                    <td class="p-2 border">INV-2026-001</td>
                    <td class="p-2 border font-sans">بضاعة ومواد تموينية شاملة ضريبة 16%</td>
                    <td class="p-2 border text-rose-700 font-bold">1,500.00</td>
                    <td class="p-2 border text-gray-400">0.00</td>
                    <td class="p-2 border font-bold">1,500.00</td>
                </tr>
                <tr>
                    <td class="p-2 border font-sans">{{ date('Y-m-12') }}</td>
                    <td class="p-2 border font-sans font-bold text-emerald-800">سند قبض نقدي</td>
                    <td class="p-2 border">RCT-2026-088</td>
                    <td class="p-2 border font-sans">دفعة نقدية مسددة في الصندوق</td>
                    <td class="p-2 border text-gray-400">0.00</td>
                    <td class="p-2 border text-emerald-700 font-bold">250.00</td>
                    <td class="p-2 border font-bold text-rose-700">1,250.00</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="bg-gray-100 font-bold text-gray-900">
                    <td colspan="4" class="p-2 border text-left font-sans">مجموع الحركات والرصيد النهائي:</td>
                    <td class="p-2 border font-mono text-rose-700">1,500.00</td>
                    <td class="p-2 border font-mono text-emerald-700">250.00</td>
                    <td class="p-2 border font-mono font-black text-[#1F2E4A]">1,250.00 {{ $tenant->currency ?? 'JOD' }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
