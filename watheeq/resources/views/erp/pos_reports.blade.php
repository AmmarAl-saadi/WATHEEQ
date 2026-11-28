@extends('layouts.app')

@section('title', 'كشف فواتير وحركات نقاط البيع POS | وثيق ERP')

@section('content')
<div class="space-y-4 font-sans text-xs" dir="rtl">

    <!-- Top Ribbon & Filter Header (Hesabate POS Frame 42) -->
    <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-2 no-print">
        <div class="flex items-center gap-2">
            <span class="text-sm font-black text-gray-800">كشف فواتير نقاط البيع (POS Invoices Report)</span>
            <span class="bg-[#1F2E4A] text-white text-[11px] px-2 py-0.5 rounded font-black">حساباتي POS</span>
        </div>
        <div class="flex items-center gap-1.5 flex-wrap">
            <button type="button" onclick="window.print()" class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                طباعة الكشف
            </button>
            <a href="{{ route('erp.pos') }}" class="px-3.5 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold transition shadow-sm">
                نقطة البيع POS
            </a>
        </div>
    </div>

    <!-- Filter Criteria Card (مطابق تماماً لشاشات استعلام وفلاتر تقارير حساباتي Frame 42 & 45) -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-gray-600 font-bold mb-1">من تاريخ:</label>
                <input type="date" value="{{ date('Y-m-01') }}" class="w-full border rounded px-2.5 py-1">
            </div>
            <div>
                <label class="block text-gray-600 font-bold mb-1">إلى تاريخ:</label>
                <input type="date" value="{{ date('Y-m-d') }}" class="w-full border rounded px-2.5 py-1">
            </div>
            <div>
                <label class="block text-gray-600 font-bold mb-1">طريقة الدفع:</label>
                <select class="w-full border rounded px-2 py-1 font-bold">
                    <option value="all">الكل (نقدي، ذمم، بطاقات، Jawwal Pay)</option>
                    <option value="cash">نقدي</option>
                    <option value="credit">ذمم</option>
                    <option value="card">بطاقات ائتمان</option>
                    <option value="jawwal_pay">Jawwal Pay / محافظ إلكترونية</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-600 font-bold mb-1">حالة الفاتورة:</label>
                <select class="w-full border rounded px-2 py-1">
                    <option value="all">الكل</option>
                    <option value="completed">مسددة بالكامل</option>
                    <option value="pending">معلقة / بانتظار التحويل</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-1.5 cursor-pointer font-bold text-gray-700">
                    <input type="checkbox" checked class="w-4 h-4 text-[#1F2E4A] rounded">
                    <span>إظهار تفاصيل الأصناف في السندات</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer font-bold text-gray-700">
                    <input type="checkbox" checked class="w-4 h-4 text-[#1F2E4A] rounded">
                    <span>إظهار اسم المستخدم / الكاشير</span>
                </label>
            </div>
            <button type="button" class="px-6 py-1.5 bg-[#1F2E4A] text-white font-bold rounded shadow-sm hover:bg-[#343A40] transition">
                تطبيق الفلتر
            </button>
        </div>
    </div>

    <!-- Data Grid Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-x-auto">
        <table class="w-full border-collapse border border-gray-200 text-right text-xs">
            <thead>
                <tr class="bg-gray-100 text-gray-700 font-bold">
                    <th class="p-2.5 border">رقم الفاتورة</th>
                    <th class="p-2.5 border">التاريخ والوقت</th>
                    <th class="p-2.5 border">نقطة البيع (Terminal)</th>
                    <th class="p-2.5 border">الكاشير</th>
                    <th class="p-2.5 border">العميل / الطاولة</th>
                    <th class="p-2.5 border">طريقة الدفع</th>
                    <th class="p-2.5 border">الصافي</th>
                    <th class="p-2.5 border">الضريبة 16%</th>
                    <th class="p-2.5 border">الإجمالي</th>
                    <th class="p-2.5 border text-center">الحالة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($invoices as $inv)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-2.5 border font-bold text-[#1F2E4A]">{{ $inv->invoice_no }}</td>
                        <td class="p-2.5 border text-gray-600">{{ $inv->date ?? date('Y-m-d') }}</td>
                        <td class="p-2.5 border">POS-01</td>
                        <td class="p-2.5 border font-bold">كاشير الصالة</td>
                        <td class="p-2.5 border">{{ $inv->contact_name ?? 'عميل نقدي' }}</td>
                        <td class="p-2.5 border font-bold">{{ $inv->payment_method ?? 'نقدي' }}</td>
                        <td class="p-2.5 border font-mono">{{ number_format($inv->subtotal ?? 0, 2) }}</td>
                        <td class="p-2.5 border font-mono text-emerald-700">{{ number_format($inv->tax_total ?? 0, 2) }}</td>
                        <td class="p-2.5 border font-mono font-black text-gray-900">{{ number_format($inv->grand_total ?? 0, 2) }} {{ $tenant->currency ?? 'JOD' }}</td>
                        <td class="p-2.5 border text-center">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">مسددة</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="p-8 text-center text-gray-400">لا توجد حركات فواتير معلقة حتى الآن</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
