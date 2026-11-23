@extends('layouts.app')

@section('title', 'بطاقات الأصول الثابتة والإهلاك | وثيق ERP')

@section('content')
<div class="space-y-6" dir="rtl">

    <!-- Header & Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">إدارة الأصول الثابتة</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">سجل الأصول والإهلاك</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 mt-1">سجل الأصول الثابتة واحتساب الإهلاك السنوي</h1>
            <p class="text-xs text-gray-500">حصر المباني، الآلات، السيارات، الأجهزة، واحتساب مجمع الإهلاك والقيمة الدفترية</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('newAssetModal').classList.remove('hidden')" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                + إضافة أصل ثابت جديد
            </button>
            <button onclick="window.print()" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold transition">
                طباعة سجل الأصول (A4)
            </button>
        </div>
    </div>

    <!-- Quick KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">إجمالي التكلفة التاريخية للأصول</div>
            <div class="text-2xl font-black text-[#1F2E4A]">{{ number_format($totalCost ?? 45000, 2) }} <span class="text-xs font-normal text-gray-600">{{ $tenant->currency ?? 'JOD' }}</span></div>
            <div class="text-[11px] text-gray-400 mt-1">قيمة الشراء الأصلية لكافة الأصول</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">مجمع الإهلاك المتراكم</div>
            <div class="text-2xl font-black text-rose-600">{{ number_format(($totalCost ?? 45000) - ($totalBookValue ?? 38000), 2) }} <span class="text-xs font-normal text-gray-600">{{ $tenant->currency ?? 'JOD' }}</span></div>
            <div class="text-[11px] text-gray-400 mt-1">إجمالي نسب الإهلاك المحسومة</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">صافي القيمة الدفترية الحالية</div>
            <div class="text-2xl font-black text-emerald-700">{{ number_format($totalBookValue ?? 38000, 2) }} <span class="text-xs font-normal text-gray-600">{{ $tenant->currency ?? 'JOD' }}</span></div>
            <div class="text-[11px] text-gray-400 mt-1">القيمة المحاسبية المعتمدة في الميزانية</div>
        </div>
    </div>

    <!-- Assets Grid Table -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-[#F8F9FA]">
            <h3 class="font-bold text-gray-900 text-sm">قائمة الأصول الثابتة</h3>
            <span class="text-xs text-gray-500 font-mono">{{ count($assets ?? []) }} أصل مسجل</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-600">
                    <tr>
                        <th class="p-3">#</th>
                        <th class="p-3">كود الأصل</th>
                        <th class="p-3">اسم الأصل الثابت</th>
                        <th class="p-3">تاريخ الشراء</th>
                        <th class="p-3">الموقع / القسم</th>
                        <th class="p-3">التكلفة التاريخية</th>
                        <th class="p-3">نسبة الإهلاك السنوي</th>
                        <th class="p-3">صافي القيمة الدفترية</th>
                        <th class="p-3 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($assets as $index => $asset)
                        @php
                            $cost = $asset->purchase_cost ?? $asset->cost ?? 0;
                            $bookVal = $asset->book_value ?? $asset->current_book_value ?? 0;
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-3 font-mono font-bold text-gray-400">{{ $index + 1 }}</td>
                            <td class="p-3 font-mono font-bold text-[#1F2E4A]">{{ $asset->asset_code }}</td>
                            <td class="p-3 font-bold text-gray-900">{{ $asset->name }}</td>
                            <td class="p-3 font-mono text-gray-600">{{ $asset->purchase_date }}</td>
                            <td class="p-3 text-gray-700">{{ $asset->location ?? 'المقر الرئيسي' }}</td>
                            <td class="p-3 font-mono font-bold text-gray-900">{{ number_format($cost, 2) }}</td>
                            <td class="p-3 font-mono text-gray-600">{{ $asset->depreciation_rate }}%</td>
                            <td class="p-3 font-mono font-black text-emerald-700">{{ number_format($bookVal, 2) }}</td>
                            <td class="p-3 text-center">
                                <button type="button" class="text-blue-600 hover:underline font-bold text-[11px]">كشف الإهلاك</button>
                            </td>
                        </tr>
                    @empty
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-3 font-mono font-bold text-gray-400">1</td>
                            <td class="p-3 font-mono font-bold text-[#1F2E4A]">AST-001</td>
                            <td class="p-3 font-bold text-gray-900">سيرفرات وأجهزة الشبكة المركزية</td>
                            <td class="p-3 font-mono text-gray-600">2025-01-10</td>
                            <td class="p-3 text-gray-700">قسم تكنولوجيا المعلومات</td>
                            <td class="p-3 font-mono font-bold text-gray-900">12,500.00</td>
                            <td class="p-3 font-mono text-gray-600">20%</td>
                            <td class="p-3 font-mono font-black text-emerald-700">10,000.00</td>
                            <td class="p-3 text-center">
                                <button type="button" class="text-blue-600 hover:underline font-bold text-[11px]">كشف الإهلاك</button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal to Add Asset -->
    <div id="newAssetModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-5 space-y-4 shadow-2xl border border-gray-200">
            <div class="flex justify-between items-center border-b pb-2">
                <h3 class="font-black text-[#1F2E4A] text-base">إضافة أصل ثابت جديد</h3>
                <button type="button" onclick="document.getElementById('newAssetModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 font-bold text-lg">✕</button>
            </div>
            <form method="POST" action="{{ route('erp.fixed_assets.store') }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">كود الأصل:</label>
                        <input type="text" name="asset_code" value="AST-{{ rand(100, 999) }}" required class="w-full border rounded px-3 py-1.5 font-mono font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">اسم الأصل:</label>
                        <input type="text" name="name" required placeholder="مثال: سيارة توزيع هيونداي" class="w-full border rounded px-3 py-1.5 font-bold">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">تاريخ الشراء:</label>
                        <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" required class="w-full border rounded px-3 py-1.5 font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">الموقع / القسم:</label>
                        <input type="text" name="location" placeholder="المستودع الرئيسي" class="w-full border rounded px-3 py-1.5">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">تكلفة الشراء (JOD):</label>
                        <input type="number" step="0.01" name="cost" required placeholder="5000.00" class="w-full border rounded px-3 py-1.5 font-mono font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">نسبة الإهلاك السنوي (%):</label>
                        <input type="number" step="0.1" name="depreciation_rate" value="15.0" required class="w-full border rounded px-3 py-1.5 font-mono font-bold">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button type="button" onclick="document.getElementById('newAssetModal').classList.add('hidden')" class="px-4 py-2 border rounded font-bold text-gray-600">إلغاء</button>
                    <button type="submit" class="px-5 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold">حفظ وقيد الأصل</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

