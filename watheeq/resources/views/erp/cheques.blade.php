@extends('layouts.app')

@section('title', 'حافظة الشيكات البنكية وإدارتها | وثيق ERP')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">الخزينة والبنوك</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">حافظة الشيكات البنكية</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 mt-1">إدارة الشيكات الصادرة والواردة وتواريخ الاستحقاق</h1>
            <p class="text-xs text-gray-500">متابعة الشيكات برسم التحصيل، الشيكات المودعة، المسحوبة، والمجيرة في البنوك الأردنية</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('newChequeModal').classList.remove('hidden')" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                + تسجيل شيك جديد
            </button>
            <button onclick="window.print()" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold transition">
                طباعة حافظة الشيكات (A4)
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">شيكات واردة (حقوق للمنشأة)</div>
            <div class="text-2xl font-black text-emerald-700">{{ number_format($receivedSum, 2) }} <span class="text-xs font-normal text-gray-600">{{ $tenant->currency ?? 'JOD' }}</span></div>
            <div class="text-[11px] text-gray-400 mt-1">مقبوضة من العملاء برسم التحصيل</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">شيكات صادرة (التزامات للموردين)</div>
            <div class="text-2xl font-black text-rose-600">{{ number_format($issuedSum, 2) }} <span class="text-xs font-normal text-gray-600">{{ $tenant->currency ?? 'JOD' }}</span></div>
            <div class="text-[11px] text-gray-400 mt-1">مسحوبة على حسابات الشركة البنكية</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">إجمالي الشيكات قيد التحصيل</div>
            <div class="text-2xl font-black text-[#1F2E4A]">{{ number_format($underCollectionSum, 2) }} <span class="text-xs font-normal text-gray-600">{{ $tenant->currency ?? 'JOD' }}</span></div>
            <div class="text-[11px] text-gray-400 mt-1">تستحق الصرف خلال الفترة القادمة</div>
        </div>
    </div>

    <!-- Cheques Table / Printable A4 Area -->
    <div id="printableA4Area" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50/70 flex justify-between items-center">
            <h2 class="text-sm font-black text-gray-800">سجل الشيكات البنكية المحفوظة</h2>
            <span class="text-xs text-gray-500 font-mono">العدد: {{ count($cheques) }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-gray-100/80 text-gray-600 border-b border-gray-200 font-bold">
                        <th class="py-3 px-4">رقم الشيك</th>
                        <th class="py-3 px-4">النوع</th>
                        <th class="py-3 px-4">البنك والفرع</th>
                        <th class="py-3 px-4">الساحب</th>
                        <th class="py-3 px-4">المستفيد</th>
                        <th class="py-3 px-4">تاريخ الاستحقاق</th>
                        <th class="py-3 px-4 text-left font-mono">قيمة الشيك</th>
                        <th class="py-3 px-4 text-center">حالة الشيك</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($cheques as $chq)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4 font-mono font-bold text-[#1F2E4A]">{{ $chq->cheque_no }}</td>
                            <td class="py-3 px-4">
                                @if($chq->type === 'received')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">شيك وارد</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">شيك صادر</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-800 font-medium">
                                {{ $chq->bank_name }} <span class="text-[10px] text-gray-400">({{ $chq->branch ?? 'الرئيسي' }})</span>
                            </td>
                            <td class="py-3 px-4 text-gray-700">{{ $chq->drawer_name }}</td>
                            <td class="py-3 px-4 text-gray-700 font-medium">{{ $chq->beneficiary }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-gray-900">{{ $chq->due_date }}</td>
                            <td class="py-3 px-4 text-left font-mono font-black text-gray-900 bg-gray-50/50">
                                {{ number_format($chq->amount, 2) }} {{ $tenant->currency ?? 'JOD' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($chq->status === 'under_collection')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">برسم التحصيل</span>
                                @elseif($chq->status === 'deposited')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">مودع بالبنك</span>
                                @elseif($chq->status === 'cleared')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">محصل / مسدد</span>
                                @elseif($chq->status === 'bounced')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">مرتجع بدون رصيد</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-800">{{ $chq->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400">لا توجد شيكات بنكية مسجلة حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: New Cheque -->
<div id="newChequeModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-200">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-base font-black text-gray-800">تسجيل شيك بنكي جديد في الحافظة</h3>
            <button onclick="document.getElementById('newChequeModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 font-bold text-sm">إغلاق [x]</button>
        </div>

        <form method="POST" action="{{ route('erp.cheques.store') }}" class="space-y-3 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">نوع الشيك</label>
                    <select name="type" class="w-full border rounded-lg p-2 font-bold">
                        <option value="received">شيك وارد (مستلم من عميل)</option>
                        <option value="issued">شيك صادر (مدفوع لمورد)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">رقم الشيك</label>
                    <input type="text" name="cheque_no" required placeholder="مثال: 7789421" class="w-full border rounded-lg p-2 font-mono font-bold">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">اسم البنك المسحوب عليه</label>
                    <input type="text" name="bank_name" required placeholder="مثال: البنك العربي" class="w-full border rounded-lg p-2">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">الفرع</label>
                    <input type="text" name="branch" placeholder="مثال: فرع الشميساني" class="w-full border rounded-lg p-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">الساحب (صاحب الحساب)</label>
                    <input type="text" name="drawer_name" required placeholder="اسم الساحب" class="w-full border rounded-lg p-2">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">المستفيد (لأمر)</label>
                    <input type="text" name="beneficiary" required value="{{ $tenant->name ?? 'وثيق لإدارة الأعمال' }}" class="w-full border rounded-lg p-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">مبلغ الشيك</label>
                    <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full border rounded-lg p-2 font-mono font-bold">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">تاريخ الاستحقاق</label>
                    <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+15 days')) }}" required class="w-full border rounded-lg p-2 font-mono">
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">حالة الشيك</label>
                <select name="status" class="w-full border rounded-lg p-2 font-bold">
                    <option value="under_collection" selected>برسم التحصيل في الخزينة</option>
                    <option value="deposited">مودع في الحساب البنكي</option>
                    <option value="cleared">محصل ومضاف للرصيد</option>
                    <option value="bounced">مرتجع بدون رصيد</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">ملاحظات ورقم الفاتورة المرتبطة</label>
                <input type="text" name="notes" placeholder="دفعة عن فاتورة مبيعات..." class="w-full border rounded-lg p-2">
            </div>

            <div class="pt-3 border-t flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('newChequeModal').classList.add('hidden')" class="px-4 py-2 border rounded-xl font-bold">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl font-bold">حفظ الشيك في الحافظة</button>
            </div>
        </form>
    </div>
</div>
@endsection


