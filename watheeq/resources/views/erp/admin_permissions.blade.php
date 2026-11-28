@extends('layouts.app')

@section('title', 'صلاحيات المستخدمين المتقدمة | حساباتي | وثيق ERP')

@section('content')
<div class="space-y-4 font-sans text-xs" dir="rtl">

    <form method="POST" action="{{ route('erp.admin.permissions.store') }}">
        @csrf

        <!-- Top Tool Command Ribbon (Hesabate Manage 14_2i) -->
        <div class="bg-white p-2 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-2 no-print">
            <div class="flex items-center gap-1.5 flex-wrap">
                <button type="submit" class="px-5 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
                    حفظ
                </button>
                <button type="button" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    &gt;|
                </button>
                <button type="button" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    &gt;&gt;
                </button>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    استعلام
                </button>
                <button type="button" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    &lt;&lt;
                </button>
                <button type="button" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    |&lt;
                </button>
                <a href="{{ route('erp.admin') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    رجوع للإدارة
                </a>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    جديد مشابه
                </button>
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    مرفقات
                </button>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm font-black text-gray-800">الصلاحيات والمستخدمين (جدول الصلاحيات التفصيلي)</span>
                <span class="bg-[#1F2E4A] text-white text-[11px] px-2.5 py-0.5 rounded font-black">حساباتي Manage</span>
            </div>
        </div>

        @if(session('success'))
            <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <!-- User Master Card Header (Hesabate Frame 14_2i) -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">الرقم:</label>
                        <input type="text" value="2" readonly class="w-full bg-gray-50 border rounded px-2.5 py-1 font-bold text-center">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">اسم المستخدم:</label>
                        <input type="text" name="user_name" value="كاشير الفرع 1" class="w-full border rounded px-2.5 py-1 font-bold bg-yellow-50">
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">بريد إلكتروني:</label>
                        <input type="email" name="email" value="cashier@watheeq.jo" class="w-full border rounded px-2.5 py-1">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">نوع الحساب:</label>
                        <select name="role" class="w-full border rounded px-2 py-1 font-bold">
                            <option value="cashier">كاشير نقطة بيع POS</option>
                            <option value="accountant">محاسب</option>
                            <option value="storekeeper">أمين مستودع</option>
                            <option value="admin">مدير نظام</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">المخزن الافتراضي:</label>
                        <select name="default_warehouse" class="w-full border rounded px-2 py-1">
                            <option value="المستودع الرئيسي">المستودع الرئيسي</option>
                            <option value="مستودع المعرض">مستودع المعرض</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">الصندوق الافتراضي:</label>
                        <select name="default_cash_box" class="w-full border rounded px-2 py-1">
                            <option value="صندوق الكاشير 1">صندوق الكاشير 1</option>
                            <option value="الصندوق الرئيسي">الصندوق الرئيسي</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">سعر البيع (POS):</label>
                        <select name="price_tier" class="w-full border rounded px-2 py-1">
                            <option value="retail">مفرق</option>
                            <option value="wholesale">جملة</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="w-24 text-gray-600 font-bold">الحد الأقصى للخصم %:</label>
                        <input type="number" step="0.1" name="max_discount_pct" value="5.0" class="w-full border rounded px-2.5 py-1 text-center font-bold">
                    </div>
                </div>
            </div>
        </div>

        <!-- Hesabate Permissions Matrix Grid (شبكة الصلاحيات الكاملة: حفظ، تعديل، ترحيل، طباعة، إلغاء، استعلام، المشاهدة، مرفقات) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-x-auto">
            <div class="p-3 bg-gray-50 border-b flex items-center justify-between">
                <span class="font-bold text-gray-800 text-xs">مصفوفة أذونات الشاشات والعمليات (Hesabate Security Matrix)</span>
                <label class="flex items-center gap-1.5 cursor-pointer font-bold text-[#1F2E4A]">
                    <input type="checkbox" onchange="toggleAllPerms(this)" class="w-4 h-4 rounded text-[#1F2E4A]">
                    <span>تحديد جميع الصلاحيات</span>
                </label>
            </div>

            <table class="w-full border-collapse border border-gray-200 text-center text-xs">
                <thead>
                    <tr class="bg-[#1F2E4A] text-white">
                        <th class="p-2 border border-gray-300 text-right pr-4">الشاشة / السند</th>
                        <th class="p-2 border border-gray-300 w-16">حفظ</th>
                        <th class="p-2 border border-gray-300 w-16">تعديل</th>
                        <th class="p-2 border border-gray-300 w-16">ترحيل</th>
                        <th class="p-2 border border-gray-300 w-16">طباعة</th>
                        <th class="p-2 border border-gray-300 w-16">إلغاء</th>
                        <th class="p-2 border border-gray-300 w-16">استعلام</th>
                        <th class="p-2 border border-gray-300 w-16">المشاهدة</th>
                        <th class="p-2 border border-gray-300 w-16">مرفقات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php
                        $screens = [
                            'فاتورة مبيعات',
                            'فاتورة مشتريات',
                            'مردودات مبيعات',
                            'سند قبض نقدي',
                            'سند صرف نقدي',
                            'سند قبض شيكات',
                            'سند دفع شيكات',
                            'كاشير نقاط البيع (POS)',
                            'تسليم عهدة الكاشير',
                            'التحويل بين المستودعات',
                            'سند التصنيع والإنتاج',
                            'بطاقة الموظف وسندات HR',
                            'حجوزات الفنادق والغرف',
                            'التقارير المالية والضريبية'
                        ];
                    @endphp
                    @foreach($screens as $idx => $scr)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="p-2 border border-gray-200 text-right font-bold text-gray-800 pr-4">
                                {{ $scr }}
                            </td>
                            <td class="p-2 border border-gray-200"><input type="checkbox" class="perm-cb w-4 h-4 text-[#1F2E4A] rounded" checked></td>
                            <td class="p-2 border border-gray-200"><input type="checkbox" class="perm-cb w-4 h-4 text-[#1F2E4A] rounded" checked></td>
                            <td class="p-2 border border-gray-200"><input type="checkbox" class="perm-cb w-4 h-4 text-[#1F2E4A] rounded" checked></td>
                            <td class="p-2 border border-gray-200"><input type="checkbox" class="perm-cb w-4 h-4 text-[#1F2E4A] rounded" checked></td>
                            <td class="p-2 border border-gray-200"><input type="checkbox" class="perm-cb w-4 h-4 text-[#1F2E4A] rounded"></td>
                            <td class="p-2 border border-gray-200"><input type="checkbox" class="perm-cb w-4 h-4 text-[#1F2E4A] rounded" checked></td>
                            <td class="p-2 border border-gray-200"><input type="checkbox" class="perm-cb w-4 h-4 text-[#1F2E4A] rounded" checked></td>
                            <td class="p-2 border border-gray-200"><input type="checkbox" class="perm-cb w-4 h-4 text-[#1F2E4A] rounded" checked></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>
</div>

<script>
function toggleAllPerms(master) {
    document.querySelectorAll('.perm-cb').forEach(cb => cb.checked = master.checked);
}
</script>
@endsection
