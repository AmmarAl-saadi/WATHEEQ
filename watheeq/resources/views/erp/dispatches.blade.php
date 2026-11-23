@extends('layouts.app')

@section('title', 'الإرساليات ومناقلات المخزون | وثيق ERP')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">المخازن واللوجستيات</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">الإرساليات ومناقلات الفروع</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 mt-1">إرساليات البضاعة، التحويل بين المستودعات والطلبيات</h1>
            <p class="text-xs text-gray-500">توثيق خروج ودخول البضائع، بيانات السائقين والمركبات، والمناقلة بين الفروع</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('newDispatchModal').classList.remove('hidden')" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                + إصدار إرسالية جديدة
            </button>
            <button onclick="window.print()" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold transition">
                طباعة بوليصة الإرسالية (A4)
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">إرساليات مبيعات صادرة للعملاء</div>
            <div class="text-2xl font-black text-[#1F2E4A]">{{ $totalDeliveries }} <span class="text-xs font-normal text-gray-600">إرسالية</span></div>
            <div class="text-[11px] text-gray-400 mt-1">خرجت من المستودعات وسلمت للزبائن</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">إرساليات استلام وتوريد بضائع</div>
            <div class="text-2xl font-black text-emerald-700">{{ $totalReceipts }} <span class="text-xs font-normal text-gray-600">شحنة واردة</span></div>
            <div class="text-[11px] text-gray-400 mt-1">تم إدخالها وتفريغها في المستودع</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">مناقلات وشحنات قيد النقل والتوصيل</div>
            <div class="text-2xl font-black text-amber-600">{{ $inTransit }} <span class="text-xs font-normal text-gray-600">على الطريق</span></div>
            <div class="text-[11px] text-gray-400 mt-1">بين مستودعات الفروع والمشاريع</div>
        </div>
    </div>

    <!-- Dispatches Table / Printable A4 Area -->
    <div id="printableA4Area" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50/70 flex justify-between items-center">
            <h2 class="text-sm font-black text-gray-800">سجل الإرساليات ومناقلات المخزون</h2>
            <span class="text-xs text-gray-500 font-mono">العدد: {{ count($dispatches) }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-gray-100/80 text-gray-600 border-b border-gray-200 font-bold">
                        <th class="py-3 px-4">رقم الإرسالية</th>
                        <th class="py-3 px-4">النوع</th>
                        <th class="py-3 px-4">الطرف الثاني / الوجهة</th>
                        <th class="py-3 px-4">التاريخ</th>
                        <th class="py-3 px-4">السائق والمركبة</th>
                        <th class="py-3 px-4">المستودع المصدر -> الهدف</th>
                        <th class="py-3 px-4 text-center">الكمية</th>
                        <th class="py-3 px-4 text-center">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($dispatches as $dsp)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4 font-mono font-bold text-[#1F2E4A]">{{ $dsp->dispatch_no }}</td>
                            <td class="py-3 px-4">
                                @if($dsp->type === 'delivery_out')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">إرسالية مبيعات</span>
                                @elseif($dsp->type === 'receipt_in')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">إرسالية استلام</span>
                                @elseif($dsp->type === 'branch_transfer')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">مناقلة فروع</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-800">طلبية</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-bold text-gray-900">{{ $dsp->party_name }}</td>
                            <td class="py-3 px-4 font-mono text-gray-600">{{ $dsp->date }}</td>
                            <td class="py-3 px-4 text-gray-700">
                                {{ $dsp->driver_name ?? '-' }} <span class="text-[10px] text-gray-400 font-mono">({{ $dsp->vehicle_no ?? 'بدون مركبة' }})</span>
                            </td>
                            <td class="py-3 px-4 text-gray-600 text-[11px]">
                                {{ $dsp->source_warehouse }} &larr; {{ $dsp->dest_warehouse ?? 'العميل' }}
                            </td>
                            <td class="py-3 px-4 text-center font-mono font-bold text-gray-900">{{ number_format($dsp->total_qty, 0) }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($dsp->status === 'delivered')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">تم التسليم</span>
                                @elseif($dsp->status === 'in_transit')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">قيد النقل</span>
                                @elseif($dsp->status === 'received')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-800">مستلمة بالمستودع</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-800">{{ $dsp->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400">لا توجد إرساليات مسجلة حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: New Dispatch -->
<div id="newDispatchModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-200">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-base font-black text-gray-800">إصدار بوليصة إرسالية جديدة</h3>
            <button onclick="document.getElementById('newDispatchModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 font-bold text-sm">إغلاق [x]</button>
        </div>

        <form method="POST" action="{{ route('erp.dispatches.store') }}" class="space-y-3 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">نوع الإرسالية</label>
                    <select name="type" class="w-full border rounded-lg p-2 font-bold">
                        <option value="delivery_out">إرسالية مبيعات صادرة</option>
                        <option value="receipt_in">إرسالية استلام واردة</option>
                        <option value="branch_transfer">مناقلة داخلية بين الفروع</option>
                        <option value="sales_order">طلبية مبيعات</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">تاريخ الإرسالية</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full border rounded-lg p-2 font-mono">
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">اسم الطرف الثاني / العميل أو المورد</label>
                <input type="text" name="party_name" required placeholder="مثال: مؤسسة القدس للمقاولات" class="w-full border rounded-lg p-2">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">المستودع المصدر</label>
                    <input type="text" name="source_warehouse" value="المستودع الرئيسي - عمان" required class="w-full border rounded-lg p-2">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">المستودع الهدف / الوجهة</label>
                    <input type="text" name="dest_warehouse" placeholder="موقع العميل أو مستودع الفرع" class="w-full border rounded-lg p-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">اسم السائق</label>
                    <input type="text" name="driver_name" placeholder="مثال: أحمد الشوبكي" class="w-full border rounded-lg p-2">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">رقم لوحة المركبة</label>
                    <input type="text" name="vehicle_no" placeholder="مثال: شحن 12-9844" class="w-full border rounded-lg p-2 font-mono">
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">إجمالي عدد الطرود / الكمية</label>
                <input type="number" step="0.01" name="total_qty" value="1.00" required class="w-full border rounded-lg p-2 font-mono font-bold">
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">ملاحظات الإرسالية وموقع التفريغ</label>
                <textarea name="notes" rows="2" placeholder="أي تعليمات خاصة بالسائق والتسليم..." class="w-full border rounded-lg p-2"></textarea>
            </div>

            <div class="pt-3 border-t flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('newDispatchModal').classList.add('hidden')" class="px-4 py-2 border rounded-xl font-bold">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl font-bold">إصدار الإرسالية</button>
            </div>
        </form>
    </div>
</div>
@endsection


