@extends('layouts.app')

@section('title', 'إدارة العملات وأسعار الصرف | وثيق ERP')

@section('content')
<div class="space-y-6" dir="rtl">

    <!-- Header & Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">التعريفات المالية</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">وثيق ERP</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 mt-1">العملات وأسعار الصرف الرسمية</h1>
            <p class="text-xs text-gray-500">إدارة العملات المعتمدة، أسعار الصرف مقابل العملة الأساسية ({{ $tenant->currency ?? 'JOD' }})، وفروقات تقييم العملات</p>
        </div>
        <div class="flex gap-2">
            <button type="button" onclick="document.getElementById('addCurrencyModal').classList.remove('hidden')" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                + إضافة عملة جديدة
            </button>
            <a href="{{ route('erp.definitions') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition border">
                العودة للتعريفات
            </a>
        </div>
    </div>

    <!-- Currencies Table -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h2 class="text-xs font-bold text-gray-700">قائمة العملات وأسعار التحويل المعتمدة في النظام ({{ count($currencies) }})</h2>
            <span class="text-[11px] text-gray-500">العملة الأساسية للنظام: <strong class="text-[#1F2E4A] font-bold">{{ $tenant->currency ?? 'JOD' }}</strong></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-gray-50 text-gray-600 font-bold border-b border-gray-200">
                    <tr>
                        <th class="p-3.5">#</th>
                        <th class="p-3.5">رمز العملة (ISO)</th>
                        <th class="p-3.5">اسم العملة بالعربية</th>
                        <th class="p-3.5">الاسم بالإنجليزية</th>
                        <th class="p-3.5">الرمز المختصر</th>
                        <th class="p-3.5 font-mono">سعر الصرف (مقابل {{ $tenant->currency ?? 'JOD' }})</th>
                        <th class="p-3.5">النوع</th>
                        <th class="p-3.5">الحالة</th>
                        <th class="p-3.5 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                    @forelse($currencies as $index => $curr)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="p-3.5 font-mono text-gray-400">{{ $index + 1 }}</td>
                            <td class="p-3.5 font-bold font-mono text-[#1F2E4A]">
                                <span class="bg-[#1F2E4A]/10 text-[#1F2E4A] px-2 py-0.5 rounded border border-[#1F2E4A]/20">{{ $curr->code }}</span>
                            </td>
                            <td class="p-3.5 font-bold text-gray-900">{{ $curr->name_ar }}</td>
                            <td class="p-3.5 font-mono text-gray-600">{{ $curr->name_en ?? '-' }}</td>
                            <td class="p-3.5 font-bold text-[#1F2E4A]">{{ $curr->symbol }}</td>
                            <td class="p-3.5 font-mono font-bold text-gray-900">
                                @if($curr->is_primary)
                                    <span class="text-emerald-700 font-bold">1.000000 (أساسي)</span>
                                @else
                                    <span class="text-blue-800">{{ number_format($curr->exchange_rate, 4) }}</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                @if($curr->is_primary)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">العملة الأساسية</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">عملة فرعية</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                @if($curr->is_active)
                                    <span class="text-emerald-600 font-bold text-[11px]">● نشط في المعاملات</span>
                                @else
                                    <span class="text-gray-400 font-bold text-[11px]">○ موقوف مؤقتاً</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                <button type="button" onclick="editCurrency({{ json_encode($curr) }})" class="px-2.5 py-1 text-[11px] font-bold text-[#1F2E4A] hover:bg-[#1F2E4A]/10 rounded border transition">
                                    تعديل الصرف
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-gray-400 font-medium">لم يتم تسجيل عملات إضافية بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <h3 class="font-bold text-[#1F2E4A] mb-1">العملة الرسمية والتقارير</h3>
            <p class="text-gray-500 leading-relaxed">تُترجم جميع قيود اليومية، ميزان المراجعة، والإقرار الضريبي لضريبة الدخل والمبيعات إلى العملة الأساسية للنظام آلياً.</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <h3 class="font-bold text-[#1F2E4A] mb-1">تحديث أسعار التحويل</h3>
            <p class="text-gray-500 leading-relaxed">يمكنك تحديث سعر صرف الدولار أو اليورو أو الريال في أي وقت، ولا يؤثر التعديل بأثر رجعي على القيود المحاسبية المرحلة سابقاً.</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <h3 class="font-bold text-[#1F2E4A] mb-1">الفوترة متعددة العملات</h3>
            <p class="text-gray-500 leading-relaxed">تتيح منظومة وثيق إصدار فواتير بيع وتصدير بالدولار مع احتساب المعادل بالدينار الأردني للامتثال لشروط الفوترة الإلكترونية JoFotara.</p>
        </div>
    </div>

</div>

<!-- Modal: Add / Edit Currency -->
<div id="addCurrencyModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200 text-right space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 id="currencyModalTitle" class="font-bold text-sm text-gray-900">إضافة عملة جديدة لسجل النظام</h3>
            <button type="button" onclick="document.getElementById('addCurrencyModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-700 font-bold">✕</button>
        </div>

        <form action="{{ route('erp.currencies.store') }}" method="POST" class="space-y-3.5 text-xs">
            @csrf
            <input type="hidden" name="id" id="modal_curr_id" value="">

            <div>
                <label class="block font-bold text-gray-700 mb-1">رمز العملة الدولي (ISO Code)</label>
                <input type="text" name="code" id="modal_curr_code" placeholder="مثلاً: USD, EUR, GBP" required maxlength="10" class="w-full p-2.5 border rounded-xl bg-gray-50 uppercase font-mono font-bold">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">الاسم بالعربية</label>
                    <input type="text" name="name_ar" id="modal_curr_name_ar" placeholder="مثلاً: دولار أمريكي" required class="w-full p-2.5 border rounded-xl bg-gray-50">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">الاسم بالإنجليزية</label>
                    <input type="text" name="name_en" id="modal_curr_name_en" placeholder="US Dollar" class="w-full p-2.5 border rounded-xl bg-gray-50 font-mono">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">الرمز المختصر</label>
                    <input type="text" name="symbol" id="modal_curr_symbol" placeholder="$, €, د.أ" required class="w-full p-2.5 border rounded-xl bg-gray-50">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">سعر الصرف (مقابل {{ $tenant->currency ?? 'JOD' }})</label>
                    <input type="number" step="0.0001" min="0.0001" name="exchange_rate" id="modal_curr_rate" value="1.0000" required class="w-full p-2.5 border rounded-xl bg-gray-50 font-mono font-bold">
                </div>
            </div>

            <div class="pt-2 flex justify-end gap-2 border-t mt-4">
                <button type="button" onclick="document.getElementById('addCurrencyModal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold transition">
                    إلغاء
                </button>
                <button type="submit" class="px-5 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl font-bold transition shadow-sm">
                    حفظ العملة
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function editCurrency(curr) {
        document.getElementById('modal_curr_id').value = curr.id;
        document.getElementById('modal_curr_code').value = curr.code;
        document.getElementById('modal_curr_name_ar').value = curr.name_ar;
        document.getElementById('modal_curr_name_en').value = curr.name_en || '';
        document.getElementById('modal_curr_symbol').value = curr.symbol;
        document.getElementById('modal_curr_rate').value = curr.exchange_rate;
        document.getElementById('currencyModalTitle').innerText = 'تعديل سعر صرف ' + curr.name_ar + ' (' + curr.code + ')';
        document.getElementById('addCurrencyModal').classList.remove('hidden');
    }
</script>
@endsection
