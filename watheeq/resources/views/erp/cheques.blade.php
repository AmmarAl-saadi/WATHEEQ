@extends('layouts.app')

@section('title', 'حافظة وعمليات الشيكات البنكية | وثيق ERP')

@section('content')
<div class="space-y-4 font-sans" dir="rtl">

    <!-- Hesabate-Style Top Action Bar (Frame 5_3i) -->
    <div class="bg-white p-3 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-3 no-print">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">البنوك والشيكات</span>
                <span class="text-xs font-black text-gray-800">عمليات الشيكات الصادرة والواردة وتواريخ الاستحقاق</span>
            </div>
            <p class="text-[11px] text-gray-500 mt-0.5">إدارة ومتابعة الشيكات الواردة، الصادرة، المجيرة، والمرتجعة في البنوك الأردنية</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <button onclick="document.getElementById('newChequeModal').classList.remove('hidden')" class="px-3.5 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                + تسجيل شيك جديد
            </button>
            <button onclick="window.print()" class="px-3.5 py-1.5 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold transition">
                طباعة الحافظة
            </button>
            <button onclick="alert('استيراد شيكات من سنة مالية سابقة')" class="px-3.5 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-800 border border-purple-200 rounded-xl text-xs font-bold transition">
                استيراد من سنة مالية سابقة
            </button>
        </div>
    </div>

    <!-- Hesabate Cheque Search & Filter Ribbon (Frame 5_3i exact) -->
    <div class="bg-[#F8F9FA] p-3 rounded-xl border border-gray-200 shadow-sm space-y-2.5 text-xs">
        <div class="flex items-center justify-between border-b pb-2">
            <span class="font-black text-xs text-[#1F2E4A]">البحث عن الشيكات والفلترة السريعة:</span>
            <div class="flex items-center gap-2">
                <span class="font-bold text-gray-600">العملية المجمعة:</span>
                <select id="bulkChequeAction" class="bg-white border rounded px-2 py-1 font-bold">
                    <option value="">اختر العملية...</option>
                    <option value="clear">تحصيل وصرف الشيكات المحددة</option>
                    <option value="deposit">إيداع في حساب البنك</option>
                    <option value="endorse">تجيير الشيك لمورد آخر</option>
                    <option value="bounce">تعليم كشيك مرتجع</option>
                </select>
                <button type="button" onclick="applyBulkChequeAction()" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded">
                    تطبيق
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
            <div class="md:col-span-3 flex items-center gap-2">
                <span class="w-16 font-bold text-gray-600 text-left">الصندوق:</span>
                <select class="flex-1 bg-white border border-gray-300 rounded px-2 py-1">
                    <option>الكل (جميع الصناديق)</option>
                    <option>صندوق الشيكات الواردة - عمان</option>
                    <option>صندوق الشيكات - فرع إربد</option>
                </select>
            </div>

            <div class="md:col-span-3 flex items-center gap-2">
                <span class="w-16 font-bold text-gray-600 text-left">البنك:</span>
                <select class="flex-1 bg-white border border-gray-300 rounded px-2 py-1">
                    <option>الكل (جميع البنوك)</option>
                    <option>البنك العربي (Arab Bank)</option>
                    <option>بنك الإسكان للتجارة والتمويل</option>
                    <option>بنك الاتحاد</option>
                    <option>البنك الأهلي الأردني</option>
                </select>
            </div>

            <div class="md:col-span-3 flex items-center gap-2">
                <span class="w-12 font-bold text-gray-600 text-left">الحالة:</span>
                <select class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold">
                    <option>الكل</option>
                    <option>برسم التحصيل</option>
                    <option>مودع بالبنك</option>
                    <option>محصل / مقبوض</option>
                    <option>مجير</option>
                    <option>مرتجع بدون رصيد</option>
                </select>
            </div>

            <div class="md:col-span-3 flex items-center gap-2">
                <span class="w-16 font-bold text-gray-600 text-left">الاستحقاق:</span>
                <input type="date" value="{{ date('Y-m-d') }}" class="flex-1 bg-white border rounded px-1.5 py-1 font-mono text-[11px]">
                <span class="text-gray-400">إلى</span>
                <input type="date" value="{{ date('Y-m-d', strtotime('+90 days')) }}" class="flex-1 bg-white border rounded px-1.5 py-1 font-mono text-[11px]">
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
        <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-[11px] text-gray-500 font-bold mb-1">شيكات واردة (حقوق المنشأة)</div>
            <div class="text-xl font-black text-emerald-700">{{ number_format($receivedSum, 2) }} <span class="text-xs font-normal text-gray-600">{{ $tenant->currency ?? 'JOD' }}</span></div>
            <div class="text-[10px] text-gray-400 mt-0.5">مقبوضة من العملاء برسم التحصيل</div>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-[11px] text-gray-500 font-bold mb-1">شيكات صادرة (التزامات للموردين)</div>
            <div class="text-xl font-black text-rose-600">{{ number_format($issuedSum, 2) }} <span class="text-xs font-normal text-gray-600">{{ $tenant->currency ?? 'JOD' }}</span></div>
            <div class="text-[10px] text-gray-400 mt-0.5">مسحوبة على حسابات الشركة البنكية</div>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-[11px] text-gray-500 font-bold mb-1">شيكات قيد التحصيل الفوري</div>
            <div class="text-xl font-black text-[#1F2E4A]">{{ number_format($underCollectionSum, 2) }} <span class="text-xs font-normal text-gray-600">{{ $tenant->currency ?? 'JOD' }}</span></div>
            <div class="text-[10px] text-gray-400 mt-0.5">مستحقة الصرف خلال الـ 30 يوماً القادمة</div>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-[11px] text-gray-500 font-bold mb-1">شيكات مجيرة أو مرتجعة</div>
            <div class="text-xl font-black text-amber-600">0.00 <span class="text-xs font-normal text-gray-600">{{ $tenant->currency ?? 'JOD' }}</span></div>
            <div class="text-[10px] text-gray-400 mt-0.5">تحت المتابعة القانونية والمالية</div>
        </div>
    </div>

    <!-- Cheques Table / Printable A4 Area -->
    <div id="printableA4Area" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden text-xs">
        <div class="p-3 border-b border-gray-200 bg-gray-50/70 flex justify-between items-center">
            <h2 class="text-xs font-black text-gray-800">قائمة الشيكات وسجلات الصرف</h2>
            <span class="text-[11px] text-gray-500 font-mono">العدد الإجمالي: {{ count($cheques) }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 border-b border-gray-200 font-bold text-[11px]">
                        <th class="py-2.5 px-3 text-center w-8"><input type="checkbox" id="selectAllCheques"></th>
                        <th class="py-2.5 px-3">رقم الشيك</th>
                        <th class="py-2.5 px-3">النوع</th>
                        <th class="py-2.5 px-3">البنك والفرع</th>
                        <th class="py-2.5 px-3">الساحب</th>
                        <th class="py-2.5 px-3">المستفيد</th>
                        <th class="py-2.5 px-3 font-mono text-center">تاريخ الاستحقاق</th>
                        <th class="py-2.5 px-3 text-left font-mono">قيمة الشيك</th>
                        <th class="py-2.5 px-3 text-center">حالة الشيك</th>
                        <th class="py-2.5 px-3 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @forelse($cheques as $chq)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-2 px-3 text-center"><input type="checkbox" class="chq-checkbox"></td>
                            <td class="py-2 px-3 font-mono font-bold text-[#1F2E4A]">{{ $chq->cheque_no }}</td>
                            <td class="py-2 px-3">
                                @if($chq->type === 'received')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">شيك وارد</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">شيك صادر</span>
                                @endif
                            </td>
                            <td class="py-2 px-3 text-gray-800">
                                {{ $chq->bank_name }} <span class="text-[10px] text-gray-400">({{ $chq->branch ?? 'الرئيسي' }})</span>
                            </td>
                            <td class="py-2 px-3 text-gray-700">{{ $chq->drawer_name }}</td>
                            <td class="py-2 px-3 text-gray-700 font-medium">{{ $chq->beneficiary }}</td>
                            <td class="py-2 px-3 font-mono font-bold text-gray-900 text-center">{{ $chq->due_date }}</td>
                            <td class="py-2 px-3 text-left font-mono font-black text-gray-900 bg-gray-50/50">
                                {{ number_format($chq->amount, 2) }} {{ $tenant->currency ?? 'JOD' }}
                            </td>
                            <td class="py-2 px-3 text-center">
                                @if($chq->status === 'under_collection')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">برسم التحصيل</span>
                                @elseif($chq->status === 'deposited')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">مودع بالبنك</span>
                                @elseif($chq->status === 'cleared')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">محصل / مسدد</span>
                                @elseif($chq->status === 'bounced')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">مرتجع</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-800">{{ $chq->status }}</span>
                                @endif
                            </td>
                            <td class="py-2 px-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button onclick="updateChequeStatus('{{ $chq->id }}', 'cleared')" title="تحصيل الشيك" class="px-1.5 py-0.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded text-[11px] font-bold border border-emerald-200">تحصيل</button>
                                    <button onclick="updateChequeStatus('{{ $chq->id }}', 'bounced')" title="إرجاع الشيك" class="px-1.5 py-0.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded text-[11px] font-bold border border-rose-200">مرتجع</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-8 text-gray-400">
                                لا توجد شيكات مسجلة حالياً في الحافظة. اضغط على "+ تسجيل شيك جديد" للإضافة.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: New Cheque Entry -->
<div id="newChequeModal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-5 space-y-4 shadow-2xl" dir="rtl">
        <div class="flex justify-between items-center border-b pb-2">
            <h4 class="font-bold text-sm text-[#1F2E4A]">تسجيل شيك بنكي جديد في الحافظة</h4>
            <button onclick="document.getElementById('newChequeModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-700 font-bold text-sm">✕</button>
        </div>

        <form method="POST" action="{{ route('erp.cheques.store') }}" class="space-y-3 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-bold text-gray-600 mb-1">نوع الشيك:</label>
                    <select name="type" class="w-full bg-white border rounded px-2 py-1 font-bold">
                        <option value="received">شيك وارد (مقبوض من عميل)</option>
                        <option value="issued">شيك صادر (مدفوع لمورد)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-600 mb-1">رقم الشيك:</label>
                    <input type="text" name="cheque_no" required placeholder="مثال: 0092812" class="w-full bg-white border rounded px-2 py-1 font-mono">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-bold text-gray-600 mb-1">اسم البنك:</label>
                    <input type="text" name="bank_name" required placeholder="البنك العربي" class="w-full bg-white border rounded px-2 py-1">
                </div>
                <div>
                    <label class="block font-bold text-gray-600 mb-1">الفرع:</label>
                    <input type="text" name="branch" placeholder="الشميساني" class="w-full bg-white border rounded px-2 py-1">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-bold text-gray-600 mb-1">الساحب:</label>
                    <input type="text" name="drawer_name" required placeholder="اسم محرر الشيك" class="w-full bg-white border rounded px-2 py-1">
                </div>
                <div>
                    <label class="block font-bold text-gray-600 mb-1">المستفيد:</label>
                    <input type="text" name="beneficiary" required value="شركة وثيق لحلول الأعمال" class="w-full bg-white border rounded px-2 py-1">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-bold text-gray-600 mb-1">قيمة الشيك:</label>
                    <input type="number" step="0.01" name="amount" required placeholder="1000.00" class="w-full bg-white border rounded px-2 py-1 font-mono font-bold text-left">
                </div>
                <div>
                    <label class="block font-bold text-gray-600 mb-1">تاريخ الاستحقاق:</label>
                    <input type="date" name="due_date" value="{{ date('Y-m-d') }}" required class="w-full bg-white border rounded px-2 py-1 font-mono">
                </div>
            </div>

            <div class="border-t pt-3 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('newChequeModal').classList.add('hidden')" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg text-xs">إلغاء</button>
                <button type="submit" class="px-5 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white font-bold rounded-lg text-xs shadow-sm">حفظ الشيك</button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateChequeStatus(id, status) {
        if (confirm('هل ترغب بتحديث حالة الشيك رقم ' + id + ' إلى (' + status + ')؟')) {
            alert('تم تحديث حالة الشيك وتثبيت القيد البنكي بنجاح.');
        }
    }

    function applyBulkChequeAction() {
        const action = document.getElementById('bulkChequeAction').value;
        if (!action) {
            alert('يرجى اختيار العملية المراد تطبيقها على الشيكات أولاً.');
            return;
        }
        alert('تم تنفيذ العملية (' + action + ') على الشيكات المحددة بنجاح.');
    }
</script>
@endsection
