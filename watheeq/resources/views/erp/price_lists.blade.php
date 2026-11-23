@extends('layouts.app')

@section('title', 'فئات أسعار البيع وتعديل الأسعار | وثيق ERP')

@section('content')
<div class="space-y-4 font-sans text-xs" dir="rtl">

    <!-- Action Bar -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-3 no-print">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">المخزون والتسعير</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">فئات أسعار البيع والتكلفة</span>
            </div>
            <h1 class="text-base font-black text-gray-900 mt-1">تعديل أسعار بيع وتكلفة الأصناف الجماعي</h1>
            <p class="text-[11px] text-gray-500">تطبيق نسب مئوية لرفع أو خفض أسعار البيع والتكلفة على مستودعات المنشأة</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('bulkPriceModal').classList.remove('hidden')" class="px-4 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
                + تعديل أسعار جماعي بنسبة %
            </button>
            <a href="{{ route('erp.products') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                سجل المستودعات
            </a>
        </div>
    </div>

    <!-- Products Pricing Table -->
    <div id="printableA4Area" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-3 border-b bg-gray-50 flex justify-between items-center">
            <h2 class="font-bold text-gray-800">قائمة تسعير وهوامش ربح الأصناف</h2>
            <span class="font-mono text-gray-500">العدد: {{ count($products) }} صنف</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-[#1F2E4A] text-white font-bold text-[11px]">
                    <tr>
                        <th class="py-2.5 px-3">كود الصنف</th>
                        <th class="py-2.5 px-3">اسم المادة</th>
                        <th class="py-2.5 px-3">المستودع</th>
                        <th class="py-2.5 px-3 text-left font-mono">سعر التكلفة الحالي</th>
                        <th class="py-2.5 px-3 text-left font-mono">سعر البيع الافتراضي</th>
                        <th class="py-2.5 px-3 text-left font-mono">هامش الربح المتوقع</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 font-medium">
                    @foreach($products as $p)
                        @php
                            $margin = $p->sale_price - $p->cost_price;
                            $marginPct = ($p->cost_price > 0) ? round(($margin / $p->cost_price) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-2.5 px-3 font-mono font-bold text-[#1F2E4A]">{{ $p->code }}</td>
                            <td class="py-2.5 px-3 font-bold text-gray-900">{{ $p->name }}</td>
                            <td class="py-2.5 px-3 text-gray-600">{{ $p->warehouse }}</td>
                            <td class="py-2.5 px-3 text-left font-mono text-gray-700">{{ number_format($p->cost_price, 2) }} {{ $tenant->currency ?? 'JOD' }}</td>
                            <td class="py-2.5 px-3 text-left font-mono font-black text-[#1F2E4A]">{{ number_format($p->sale_price, 2) }} {{ $tenant->currency ?? 'JOD' }}</td>
                            <td class="py-2.5 px-3 text-left font-mono font-bold {{ $margin >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ number_format($margin, 2) }} ({{ $marginPct }}%)
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Bulk Price Adjustment -->
<div id="bulkPriceModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-base font-black text-gray-800">تعديل جماعي لأسعار البيع أو التكلفة</h3>
            <button onclick="document.getElementById('bulkPriceModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 font-bold text-sm">إغلاق [x]</button>
        </div>

        <form method="POST" action="{{ route('erp.price_lists.update') }}" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-gray-700 mb-1">الهدف المراد تعديله</label>
                <select name="target" class="w-full border rounded-lg p-2 font-bold">
                    <option value="sale_price">أسعار البيع للمستهلك (سعر البيع)</option>
                    <option value="cost_price">أسعار التكلفة والمشتريات</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">نوع الحركة</label>
                    <select name="type" class="w-full border rounded-lg p-2 font-bold">
                        <option value="increase">زيادة (+)</option>
                        <option value="decrease">تخفيض (-)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">النسبة المئوية %</label>
                    <input type="number" step="0.5" name="percentage" value="5.0" required class="w-full border rounded-lg p-2 font-mono font-bold">
                </div>
            </div>

            <div class="pt-3 border-t flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('bulkPriceModal').classList.add('hidden')" class="px-4 py-2 border rounded-xl font-bold">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl font-bold">تطبيق التعديل الجماعي</button>
            </div>
        </form>
    </div>
</div>
@endsection


