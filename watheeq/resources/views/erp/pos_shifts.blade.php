@extends('layouts.app')

@section('title', 'تسليم العهدة وورديات الكاشير | وثيق POS')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">الخزينة والكاشير</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">تسليم العهدة والورديات</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 mt-1">إقفال وردية الكاشير ومطابقة الصندوق الفعلي</h1>
            <p class="text-xs text-gray-500">جرد النقدية المستلمة، مقارنة المبيعات المتوقعة، واحتساب فروقات العهدة (عجز / زيادة)</p>
        </div>
        <div class="flex gap-2">
            @if($currentShift)
                <button onclick="document.getElementById('closeShiftModal').classList.remove('hidden')" class="px-4 py-2 bg-rose-700 hover:bg-rose-800 text-white rounded-xl text-xs font-bold transition shadow-sm">
                    إقفال وتسليم الوردية الحالية
                </button>
            @endif
            <button onclick="window.print()" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold transition">
                طباعة تقرير الإقفال (A4)
            </button>
        </div>
    </div>

    <!-- Active Shift Status Card -->
    @if($currentShift)
        <div class="bg-gradient-to-r from-[#1F2E4A] to-[#243b2a] text-white p-6 rounded-2xl shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-white/20 pb-3">
                <div>
                    <span class="text-xs text-[#CED4DA] font-bold block">الوردية المفتوحة حالياً</span>
                    <h2 class="text-lg font-black">{{ $currentShift->shift_no }} - الكاشير: {{ $currentShift->cashier_name }}</h2>
                </div>
                <div class="bg-white/20 px-3 py-1 rounded-lg text-xs font-bold">
                    وقت الفتح: {{ $currentShift->opened_at }}
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="text-gray-300 block">رصيد بداية الوردية (العهدة):</span>
                    <span class="text-lg font-black font-mono">{{ number_format($currentShift->opening_cash, 2) }} {{ $tenant->currency ?? 'JOD' }}</span>
                </div>
                <div>
                    <span class="text-gray-300 block">مبيعات نقدية (كاش):</span>
                    <span class="text-lg font-black font-mono text-emerald-300">{{ number_format($currentShift->cash_sales, 2) }} {{ $tenant->currency ?? 'JOD' }}</span>
                </div>
                <div>
                    <span class="text-gray-300 block">مبيعات بطاقات (فيزا):</span>
                    <span class="text-lg font-black font-mono text-blue-300">{{ number_format($currentShift->card_sales, 2) }} {{ $tenant->currency ?? 'JOD' }}</span>
                </div>
                <div>
                    <span class="text-gray-300 block">المبلغ المتوقع في الدرج:</span>
                    <span class="text-lg font-black font-mono text-[#CED4DA]">{{ number_format($currentShift->expected_cash, 2) }} {{ $tenant->currency ?? 'JOD' }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Shifts History Table -->
    <div id="printableA4Area" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50/70 flex justify-between items-center">
            <h2 class="text-sm font-black text-gray-800">سجل ورديات الكاشير وتسليم العهدة</h2>
            <span class="text-xs text-gray-500 font-mono">العدد: {{ count($shifts) }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-gray-100/80 text-gray-600 border-b border-gray-200 font-bold">
                        <th class="py-3 px-4">رقم الوردية</th>
                        <th class="py-3 px-4">اسم الكاشير</th>
                        <th class="py-3 px-4">وقت البدء</th>
                        <th class="py-3 px-4">وقت الإقفال</th>
                        <th class="py-3 px-4 text-left font-mono">عهدة البداية</th>
                        <th class="py-3 px-4 text-left font-mono">مبيعات الكاش</th>
                        <th class="py-3 px-4 text-left font-mono">المتوقع</th>
                        <th class="py-3 px-4 text-left font-mono">الفعلي بالدرج</th>
                        <th class="py-3 px-4 text-left font-mono">الفارق</th>
                        <th class="py-3 px-4 text-center">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($shifts as $s)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4 font-mono font-bold text-[#1F2E4A]">{{ $s->shift_no }}</td>
                            <td class="py-3 px-4 font-bold text-gray-900">{{ $s->cashier_name }}</td>
                            <td class="py-3 px-4 font-mono text-gray-600">{{ $s->opened_at }}</td>
                            <td class="py-3 px-4 font-mono text-gray-600">{{ $s->closed_at ?? 'قيد العمل' }}</td>
                            <td class="py-3 px-4 text-left font-mono">{{ number_format($s->opening_cash, 2) }}</td>
                            <td class="py-3 px-4 text-left font-mono text-emerald-600 font-bold">{{ number_format($s->cash_sales, 2) }}</td>
                            <td class="py-3 px-4 text-left font-mono font-bold text-gray-800">{{ number_format($s->expected_cash, 2) }}</td>
                            <td class="py-3 px-4 text-left font-mono font-bold">{{ $s->actual_cash ? number_format($s->actual_cash, 2) : '-' }}</td>
                            <td class="py-3 px-4 text-left font-mono font-bold {{ $s->difference < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $s->closed_at ? number_format($s->difference, 2) : '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($s->status === 'open')
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800">مفتوحة</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-gray-100 text-gray-800">مقفلة ومطابقة</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-gray-400">لا توجد ورديات مسجلة.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Close Shift -->
@if($currentShift)
<div id="closeShiftModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-base font-black text-gray-800">إقفال الوردية وجرد النقدية الفعلي</h3>
            <button onclick="document.getElementById('closeShiftModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 font-bold text-sm">إغلاق [x]</button>
        </div>

        <form method="POST" action="{{ route('erp.pos.shifts.close') }}" class="space-y-3 text-xs">
            @csrf
            <div class="bg-gray-50 p-3 rounded-xl border space-y-1">
                <div class="flex justify-between">
                    <span class="text-gray-500">المبلغ المتوقع في الصندوق:</span>
                    <span class="font-bold font-mono">{{ number_format($currentShift->expected_cash, 2) }} {{ $tenant->currency ?? 'JOD' }}</span>
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">المبلغ الفعلي المعدود في الصندوق (الكاش)</label>
                <input type="number" step="0.01" name="actual_cash" required placeholder="0.00" class="w-full border rounded-lg p-2 font-mono font-black text-base text-[#1F2E4A]">
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">ملاحظات الإقفال وفروقات الجرد</label>
                <textarea name="notes" rows="2" placeholder="أي توضيحات بخصوص العجز أو الزيادة..." class="w-full border rounded-lg p-2"></textarea>
            </div>

            <div class="pt-3 border-t flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('closeShiftModal').classList.add('hidden')" class="px-4 py-2 border rounded-xl font-bold">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-rose-700 hover:bg-rose-800 text-white rounded-xl font-bold">تأكيد الإقفال وترحيل العهدة</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection


