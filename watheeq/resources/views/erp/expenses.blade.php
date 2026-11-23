@extends('layouts.app')

@section('title', 'سندات المصاريف والصرف المالي | وثيق ERP')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">المدفوعات والمصاريف</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">سندات الصرف المالي</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 mt-1">سندات صرف النفقات والمصاريف التشغيلية</h1>
            <p class="text-xs text-gray-500">توثيق المصاريف العمومية، الإيجارات، الفواتير، وربطها بمراكز التكلفة المحاسبية</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('newExpenseModal').classList.remove('hidden')" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                + تحرير سند صرف جديد
            </button>
            <button onclick="window.print()" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold transition">
                طباعة كشف المصاريف (A4)
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">إجمالي المصاريف المصروفة</div>
            <div class="text-2xl font-black text-rose-600">{{ number_format($totalExpenses, 2) }} <span class="text-xs font-normal text-gray-600">{{ $tenant->currency ?? 'JOD' }}</span></div>
            <div class="text-[11px] text-gray-400 mt-1">مصاريف تشغيلية مقيدة بالدفاتر</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">عدد سندات الصرف</div>
            <div class="text-2xl font-black text-gray-900">{{ count($expenses) }} <span class="text-xs font-normal text-gray-600">سند</span></div>
            <div class="text-[11px] text-gray-400 mt-1">موثقة ومرحلة للحسابات</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">مراكز التكلفة النشطة</div>
            <div class="text-2xl font-black text-[#1F2E4A]">{{ count($costCenters) }} <span class="text-xs font-normal text-gray-600">مركز تكلفة</span></div>
            <div class="text-[11px] text-gray-400 mt-1">لتوزيع وتحليل النفقات والمشاريع</div>
        </div>
    </div>

    <!-- Expenses Table / Printable A4 Area -->
    <div id="printableA4Area" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50/70 flex justify-between items-center">
            <h2 class="text-sm font-black text-gray-800">سجل سندات الصرف والمصاريف العمومية</h2>
            <span class="text-xs text-gray-500 font-mono">العدد: {{ count($expenses) }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-gray-100/80 text-gray-600 border-b border-gray-200 font-bold">
                        <th class="py-3 px-4">رقم السند</th>
                        <th class="py-3 px-4">التاريخ</th>
                        <th class="py-3 px-4">بند المصروف / التصنيف</th>
                        <th class="py-3 px-4">المدفوع له (المستفيد)</th>
                        <th class="py-3 px-4">طريقة الدفع والصندوق</th>
                        <th class="py-3 px-4">البيان والشرح</th>
                        <th class="py-3 px-4 text-left font-mono">المبلغ المصروف</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($expenses as $exp)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4 font-mono font-bold text-[#1F2E4A]">{{ $exp->expense_no }}</td>
                            <td class="py-3 px-4 font-mono text-gray-600">{{ $exp->date }}</td>
                            <td class="py-3 px-4 font-bold text-gray-900">{{ $exp->category }}</td>
                            <td class="py-3 px-4 text-gray-700 font-medium">{{ $exp->paid_to }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $exp->payment_method }}</td>
                            <td class="py-3 px-4 text-gray-500 text-[11px] leading-relaxed">{{ $exp->description }}</td>
                            <td class="py-3 px-4 text-left font-mono font-black text-rose-700 bg-rose-50/30">
                                {{ number_format($exp->amount, 2) }} {{ $tenant->currency ?? 'JOD' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400">لا توجد سندات صرف مقيدة حتى الآن.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: New Expense -->
<div id="newExpenseModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-200">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-base font-black text-gray-800">تحرير سند صرف نفقات جديد</h3>
            <button onclick="document.getElementById('newExpenseModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 font-bold text-sm">إغلاق [x]</button>
        </div>

        <form method="POST" action="{{ route('erp.expenses.store') }}" class="space-y-3 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">بند المصروف / التصنيف</label>
                    <select name="category" class="w-full border rounded-lg p-2 font-bold">
                        <option value="إيجار المقرات والفروع">إيجار المقرات والفروع</option>
                        <option value="كهرباء ومياه وإنارة">كهرباء ومياه وإنارة</option>
                        <option value="ضيافة وبوفيه ونظافة">ضيافة وبوفيه ونظافة</option>
                        <option value="محروقات ونقل وتوصيل">محروقات ونقل وتوصيل</option>
                        <option value="صيانة ومعدات">صيانة ومعدات</option>
                        <option value="اتصالات وإنترنت">اتصالات وإنترنت</option>
                        <option value="قرطاسية ومطبوعات">قرطاسية ومطبوعات</option>
                        <option value="مصاريف حكومية ورسوم">مصاريف حكومية ورسوم</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">المبلغ المصروف</label>
                    <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full border rounded-lg p-2 font-mono font-bold">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">تاريخ السند</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full border rounded-lg p-2 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">المدفوع له (المستفيد)</label>
                    <input type="text" name="paid_to" required placeholder="اسم الشخص أو المؤسسة" class="w-full border rounded-lg p-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">طريقة الصرف</label>
                    <select name="payment_method" class="w-full border rounded-lg p-2 font-bold">
                        <option value="نقد من الصندوق الرئيسي">نقد من الصندوق الرئيسي</option>
                        <option value="شيك بنكي مسحوب على البنك العربي">شيك بنكي مسحوب على البنك العربي</option>
                        <option value="تحويل عبر إي فواتيركم eFAWATEERcom">تحويل عبر إي فواتيركم eFAWATEERcom</option>
                        <option value="بطاقة بنكية مدى/فيزا">بطاقة بنكية مدى/فيزا</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">مركز التكلفة (المشروع)</label>
                    <select name="cost_center_id" class="w-full border rounded-lg p-2 font-bold">
                        <option value="">بدون مركز تكلفة محدد</option>
                        @foreach($costCenters as $cc)
                            <option value="{{ $cc->id }}">{{ $cc->name }} ({{ $cc->code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">البيان والشرح التفصيلي</label>
                <textarea name="description" rows="2" required placeholder="سبب الصرف وأرقام الفواتير المرفقة..." class="w-full border rounded-lg p-2"></textarea>
            </div>

            <div class="pt-3 border-t flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('newExpenseModal').classList.add('hidden')" class="px-4 py-2 border rounded-xl font-bold">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl font-bold">حفظ وترحيل سند الصرف</button>
            </div>
        </form>
    </div>
</div>
@endsection


