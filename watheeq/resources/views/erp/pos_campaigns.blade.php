@extends('layouts.app')

@section('title', 'حملات نقاط البيع والخصومات | وثيق POS')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">نقاط البيع</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">إدارة الحملات والعروض</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 mt-1">الحملات الترويجية، برامج النقاط والخصومات</h1>
            <p class="text-xs text-gray-500">إدارة عروض الشراء، مضاعفة نقاط ولاء العملاء، وخصومات البطاقات الائتمانية</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('newCampaignModal').classList.remove('hidden')" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                + إطلاق حملة ترويجية جديدة
            </button>
            <a href="{{ route('erp.pos.hub') }}" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold transition">
                لوحة تحكم POS
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">الحملات النشطة حالياً</div>
            <div class="text-2xl font-black text-emerald-700">{{ $activeCount }} <span class="text-xs font-normal text-gray-600">حملة مفعلة</span></div>
            <div class="text-[11px] text-gray-400 mt-1">تطبق آلياً في شاشات الكاشير</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">إجمالي الحملات المسجلة</div>
            <div class="text-2xl font-black text-[#1F2E4A]">{{ count($campaigns) }} <span class="text-xs font-normal text-gray-600">حملة</span></div>
            <div class="text-[11px] text-gray-400 mt-1">أرشيف العروض الموسمية والتسويقية</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">حالة التطبيق على الفواتير</div>
            <div class="text-lg font-bold text-[#1F2E4A] mt-1">تزامن فوري مع الكاشير</div>
            <div class="text-[11px] text-gray-400 mt-1">احتساب الخصم تلقائياً عند مسح الباركود</div>
        </div>
    </div>

    <!-- Campaigns Table / Printable A4 Area -->
    <div id="printableA4Area" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50/70 flex justify-between items-center">
            <h2 class="text-sm font-black text-gray-800">قائمة الحملات الترويجية والخصومات المعتمدة</h2>
            <span class="text-xs text-gray-500 font-mono">العدد: {{ count($campaigns) }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-gray-100/80 text-gray-600 border-b border-gray-200 font-bold">
                        <th class="py-3 px-4">اسم الحملة / العرض</th>
                        <th class="py-3 px-4">نوع الحملة</th>
                        <th class="py-3 px-4">نسبة الخصم %</th>
                        <th class="py-3 px-4">مضاعف النقاط</th>
                        <th class="py-3 px-4 font-mono">تاريخ البدء</th>
                        <th class="py-3 px-4 font-mono">تاريخ الانتهاء</th>
                        <th class="py-3 px-4">شروط وملاحظات</th>
                        <th class="py-3 px-4 text-center">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($campaigns as $camp)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4 font-bold text-gray-900">{{ $camp->title }}</td>
                            <td class="py-3 px-4">
                                @if($camp->type === 'general')
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A]">الحملة الشاملة</span>
                                @elseif($camp->type === 'points')
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-800">حملة نقاط ولاء</span>
                                @elseif($camp->type === 'cards_prices')
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-blue-100 text-blue-800">بطاقات وأسعار</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-purple-100 text-purple-800">خصم زمني</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-rose-600">{{ $camp->discount_rate > 0 ? $camp->discount_rate . '%' : '-' }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-gray-800">{{ $camp->points_multiplier > 1 ? $camp->points_multiplier . 'X' : '1X' }}</td>
                            <td class="py-3 px-4 font-mono text-gray-600">{{ $camp->start_date }}</td>
                            <td class="py-3 px-4 font-mono text-gray-600">{{ $camp->end_date }}</td>
                            <td class="py-3 px-4 text-gray-500 text-[11px]">{{ $camp->notes ?? '-' }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($camp->status === 'active')
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800">سارية ونشطة</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-gray-100 text-gray-800">منتهية</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400">لا توجد حملات ترويجية مسجلة حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: New Campaign -->
<div id="newCampaignModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-200">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-base font-black text-gray-800">إطلاق حملة ترويجية جديدة لنقاط البيع</h3>
            <button onclick="document.getElementById('newCampaignModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 font-bold text-sm">إغلاق [x]</button>
        </div>

        <form method="POST" action="{{ route('erp.pos.campaigns.store') }}" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-gray-700 mb-1">عنوان الحملة أو العرض الترويجي</label>
                <input type="text" name="title" required placeholder="مثال: خصم الأعياد الوطني 15%" class="w-full border rounded-lg p-2">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">نوع الحملة</label>
                    <select name="type" class="w-full border rounded-lg p-2 font-bold">
                        <option value="general">الحملة الشاملة (كافة الأصناف)</option>
                        <option value="points">حملة نقاط الولاء والمكافآت</option>
                        <option value="cards_prices">حملة بطاقات وأسعار البنوك</option>
                        <option value="period_discount">خصم مؤقت خلال فترة محددة</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">نسبة الخصم المئوية %</label>
                    <input type="number" step="0.5" name="discount_rate" value="10.00" required class="w-full border rounded-lg p-2 font-mono font-bold">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">مضاعف النقاط</label>
                    <input type="number" name="points_multiplier" value="1" min="1" required class="w-full border rounded-lg p-2 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">تاريخ البدء</label>
                    <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required class="w-full border rounded-lg p-2 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">تاريخ الانتهاء</label>
                    <input type="date" name="end_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required class="w-full border rounded-lg p-2 font-mono">
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">شروط التطبيق وملاحظات الكاشير</label>
                <textarea name="notes" rows="2" placeholder="الحد الأدنى للفاتورة، البطاقات المؤهلة..." class="w-full border rounded-lg p-2"></textarea>
            </div>

            <div class="pt-3 border-t flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('newCampaignModal').classList.add('hidden')" class="px-4 py-2 border rounded-xl font-bold">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl font-bold">تفعيل الحملة فوراً</button>
            </div>
        </form>
    </div>
</div>
@endsection

