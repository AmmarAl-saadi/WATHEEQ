@extends('layouts.app')

@section('title', 'كشف حركات الشيكات الواردة والصادرة | حساباتي | وثيق ERP')

@section('content')
<div class="space-y-4 font-sans text-xs" dir="rtl">

    <!-- Top Ribbon (Hesabate Reports Frame 5_12i) -->
    <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-2 no-print">
        <div class="flex items-center gap-2">
            <span class="text-sm font-black text-gray-800">كشف حركات الشيكات (Cheque Portfolio Report)</span>
            <span class="bg-[#1F2E4A] text-white text-[11px] px-2 py-0.5 rounded font-black">حساباتي البنوك</span>
        </div>
        <div class="flex items-center gap-1.5 flex-wrap">
            <button type="button" onclick="window.print()" class="px-4 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
                طباعة الكشف (A4)
            </button>
            <a href="{{ route('erp.cheques') }}" class="px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                محفظة الشيكات
            </a>
        </div>
    </div>

    <!-- Filter Matrix (طابق شاشة حساباتي Frame 35) -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-gray-600 font-bold mb-1">نوع الشيك:</label>
                <select class="w-full border rounded px-2 py-1 font-bold bg-yellow-50">
                    <option value="all">كافة الشيكات (واردة وصادرة)</option>
                    <option value="inbound">شيكات واردة (قبض)</option>
                    <option value="outbound">شيكات صادرة (دفع)</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-600 font-bold mb-1">حالة الشيك في البنك:</label>
                <select class="w-full border rounded px-2 py-1 font-bold">
                    <option value="all">الكل</option>
                    <option value="portfolio">في المحفظة / برسم التحصيل</option>
                    <option value="cleared">محصل ومودع في الحساب</option>
                    <option value="bounced">مرجع / مرفوض</option>
                    <option value="endorsed">مجير لطرف ثالث</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-600 font-bold mb-1">البنك المسحوب عليه:</label>
                <input type="text" placeholder="البنك العربي، بنك الإسكان..." class="w-full border rounded px-2.5 py-1">
            </div>
            <div>
                <label class="block text-gray-600 font-bold mb-1">تاريخ الاستحقاق:</label>
                <div class="flex gap-1">
                    <input type="date" value="{{ date('Y-m-01') }}" class="w-1/2 border rounded px-1.5 py-1">
                    <input type="date" value="{{ date('Y-m-d', strtotime('+3 months')) }}" class="w-1/2 border rounded px-1.5 py-1">
                </div>
            </div>
        </div>

        <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
            <label class="flex items-center gap-1.5 cursor-pointer font-bold text-gray-700">
                <input type="checkbox" checked class="w-4 h-4 text-[#1F2E4A] rounded">
                <span>احتساب الرصيد الإجمالي للشيكات القائمة</span>
            </label>
            <button type="button" class="px-6 py-1.5 bg-[#1F2E4A] text-white font-bold rounded shadow-sm hover:bg-[#343A40] transition">
                تطبيق الفلتر
            </button>
        </div>
    </div>

    <!-- Cheques Grid Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 space-y-3">
        <table class="w-full border-collapse border border-gray-200 text-right text-xs">
            <thead>
                <tr class="bg-[#1F2E4A] text-white">
                    <th class="p-2 border">رقم الشيك</th>
                    <th class="p-2 border">تاريخ الاستحقاق</th>
                    <th class="p-2 border">البنك</th>
                    <th class="p-2 border">الساحب / المستفيد</th>
                    <th class="p-2 border">المبلغ</th>
                    <th class="p-2 border">العملة</th>
                    <th class="p-2 border">الحالة</th>
                    <th class="p-2 border">ملاحظات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 font-mono">
                @forelse($cheques as $chk)
                    <tr>
                        <td class="p-2 border font-bold">{{ $chk->cheque_no }}</td>
                        <td class="p-2 border font-sans">{{ $chk->due_date }}</td>
                        <td class="p-2 border font-sans">{{ $chk->bank_name }}</td>
                        <td class="p-2 border font-sans font-bold">عميل تجاري</td>
                        <td class="p-2 border font-bold text-[#1F2E4A]">{{ number_format($chk->amount, 2) }}</td>
                        <td class="p-2 border font-sans">{{ $chk->currency }}</td>
                        <td class="p-2 border font-sans">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">برسم التحصيل</span>
                        </td>
                        <td class="p-2 border font-sans text-gray-500">دفعة فاتورة</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-6 text-center text-gray-400 font-sans">لا توجد شيكات مسجلة مطابقة للبحث</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
