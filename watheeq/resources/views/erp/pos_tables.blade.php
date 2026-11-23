@extends('layouts.app')

@section('title', 'إدارة الطاولات وصالات نقاط البيع | وثيق POS')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">تحكم صالات POS</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">الطاولات والمستخدمين</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 mt-1">مخطط طاولات صالة المبيعات والطلبات المباشرة</h1>
            <p class="text-xs text-gray-500">متابعة إشغال الطاولات، تعيين النوادل/المستخدمين، وربط الطلبات بفاتورة POS</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('newTableModal').classList.remove('hidden')" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                + تعريف طاولة جديدة
            </button>
            <a href="{{ route('erp.pos') }}" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                فتح شاشة كاشير POS
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">إجمالي الطاولات المعرفة</div>
            <div class="text-2xl font-black text-gray-900">{{ count($tables) }} <span class="text-xs font-normal text-gray-600">طاولة</span></div>
            <div class="text-[11px] text-gray-400 mt-1">موزعة على كافة الأقسام والصالات</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">الطاولات المشغولة حالياً</div>
            <div class="text-2xl font-black text-rose-600">{{ $occupiedCount }} <span class="text-xs font-normal text-gray-600">مشغولة</span></div>
            <div class="text-[11px] text-gray-400 mt-1">تحتوي على طلبات معلقة قيد الخدمة</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">الطاولات الجاهزة للاستقبال</div>
            <div class="text-2xl font-black text-emerald-600">{{ $availableCount }} <span class="text-xs font-normal text-gray-600">متاحة فوراً</span></div>
            <div class="text-[11px] text-gray-400 mt-1">جاهزة لتسكين الزبائن الجدد</div>
        </div>
    </div>

    <!-- Visual Floor Rack (Tables Grid) -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h2 class="text-sm font-black text-gray-800">مخطط صالة الطعام ونقاط الخدمة الميدانية</h2>
            <div class="flex items-center gap-4 text-xs font-bold">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span> متاح</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-rose-500 inline-block"></span> مشغول بطلب</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-500 inline-block"></span> محجوز مسبقاً</span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach($tables as $t)
                <div class="p-4 rounded-2xl border transition {{ $t->status === 'available' ? 'bg-emerald-50/40 border-emerald-300' : ($t->status === 'occupied' ? 'bg-rose-50/40 border-rose-300' : 'bg-amber-50/40 border-amber-300') }}">
                    <div class="flex justify-between items-start">
                        <span class="font-mono font-black text-lg {{ $t->status === 'available' ? 'text-emerald-800' : ($t->status === 'occupied' ? 'text-rose-800' : 'text-amber-800') }}">
                            {{ $t->table_number }}
                        </span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $t->status === 'available' ? 'bg-emerald-200 text-emerald-900' : ($t->status === 'occupied' ? 'bg-rose-200 text-rose-900' : 'bg-amber-200 text-amber-900') }}">
                            {{ $t->status === 'available' ? 'متاحة' : ($t->status === 'occupied' ? 'مشغولة' : 'محجوزة') }}
                        </span>
                    </div>

                    <div class="text-xs font-bold text-gray-800 mt-2">{{ $t->section }}</div>
                    <div class="text-[11px] text-gray-500 mt-0.5">سعة: {{ $t->capacity }} مقاعد</div>

                    @if($t->current_waiter)
                        <div class="text-[11px] text-gray-700 font-medium mt-1">الويتر: {{ $t->current_waiter }}</div>
                    @endif

                    @if($t->current_order_total > 0)
                        <div class="mt-3 pt-2 border-t border-rose-200 flex justify-between items-center text-xs font-mono font-bold text-rose-800">
                            <span>الحساب:</span>
                            <span>{{ number_format($t->current_order_total, 2) }} {{ $tenant->currency ?? 'JOD' }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

</div>

<!-- Modal: New Table -->
<div id="newTableModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-200">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-base font-black text-gray-800">تعريف طاولة جديدة في الصالة</h3>
            <button onclick="document.getElementById('newTableModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 font-bold text-sm">إغلاق [x]</button>
        </div>

        <form method="POST" action="{{ route('erp.pos.tables.store') }}" class="space-y-3 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">رقم الطاولة / الكود</label>
                    <input type="text" name="table_number" required placeholder="مثال: T-05" class="w-full border rounded-lg p-2 font-mono font-bold">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">القسم / الصالة</label>
                    <input type="text" name="section" value="الصالة الرئيسية" required class="w-full border rounded-lg p-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">سعة المقاعد (الأفراد)</label>
                    <input type="number" name="capacity" value="4" min="1" required class="w-full border rounded-lg p-2 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">الحالة الابتدائية</label>
                    <select name="status" class="w-full border rounded-lg p-2 font-bold">
                        <option value="available" selected>متاحة فوراً</option>
                        <option value="occupied">مشغولة</option>
                        <option value="reserved">محجوزة مسبقاً</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">المستخدم / النادل المسؤول (اختياري)</label>
                <input type="text" name="current_waiter" placeholder="اسم النادل" class="w-full border rounded-lg p-2">
            </div>

            <div class="pt-3 border-t flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('newTableModal').classList.add('hidden')" class="px-4 py-2 border rounded-xl font-bold">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl font-bold">حفظ الطاولة</button>
            </div>
        </form>
    </div>
</div>
@endsection


