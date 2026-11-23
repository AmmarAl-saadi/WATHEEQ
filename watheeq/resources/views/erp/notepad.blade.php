@extends('layouts.app')

@section('title', 'المفكرة والملاحظات المحاسبية | وثيق ERP')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">أدوات المحاسب الذكية</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">المفكرة والمواعيد</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 mt-1">المفكرة المحاسبية وقائمة المهام الدورية</h1>
            <p class="text-xs text-gray-500">تدوين استحقاقات الشيكات، الإقرارات الضريبية، ومواعيد التسويات البنكية</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('newNoteModal').classList.remove('hidden')" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                + تدوين ملاحظة / مهمة جديدة
            </button>
            <button onclick="window.print()" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold transition">
                طباعة قائمة المهام (A4)
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">المهام قيد المتابعة</div>
            <div class="text-2xl font-black text-amber-600">{{ $pendingCount }} <span class="text-xs font-normal text-gray-600">مهمة معلقة</span></div>
            <div class="text-[11px] text-gray-400 mt-1">تتطلب إجراء ومراجعة محاسبية</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">المهام المنجزة والمغلقة</div>
            <div class="text-2xl font-black text-emerald-700">{{ $completedCount }} <span class="text-xs font-normal text-gray-600">مهمة مكتملة</span></div>
            <div class="text-[11px] text-gray-400 mt-1">تمت تسويتها واعتمادها بالدفاتر</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">إجمالي التدوينات المسجلة</div>
            <div class="text-2xl font-black text-[#1F2E4A]">{{ count($notes) }} <span class="text-xs font-normal text-gray-600">ملاحظة</span></div>
            <div class="text-[11px] text-gray-400 mt-1">أرشيف المهام والمذكرات الداخلية</div>
        </div>
    </div>

    <!-- Notes List -->
    <div id="printableA4Area" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50/70 flex justify-between items-center">
            <h2 class="text-sm font-black text-gray-800">قائمة المهام والمذكرات المحاسبية</h2>
            <span class="text-xs text-gray-500 font-mono">العدد: {{ count($notes) }}</span>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($notes as $note)
                <div class="p-4 hover:bg-gray-50/80 transition flex flex-col md:flex-row justify-between items-start md:items-center gap-4 {{ $note->is_completed ? 'opacity-60 bg-gray-50/40' : '' }}">
                    <div class="space-y-1.5 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $note->priority === 'urgent' ? 'bg-rose-100 text-rose-800' : ($note->priority === 'high' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ $note->priority === 'urgent' ? 'عاجل وهام' : ($note->priority === 'high' ? 'أولوية مرتفعة' : 'أولوية عادية') }}
                            </span>
                            <h3 class="text-sm font-bold text-gray-900 {{ $note->is_completed ? 'line-through text-gray-500' : '' }}">
                                {{ $note->title }}
                            </h3>
                        </div>
                        <p class="text-xs text-gray-600 leading-relaxed pr-2">
                            {{ $note->content }}
                        </p>
                        @if($note->due_date)
                            <div class="text-[11px] text-gray-400 font-mono pr-2">
                                تاريخ الاستحقاق: <span class="font-bold text-gray-700">{{ $note->due_date }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 no-print self-end md:self-center">
                        <form method="POST" action="{{ route('erp.notepad.toggle', $note->id) }}">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $note->is_completed ? 'bg-gray-200 hover:bg-gray-300 text-gray-700' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }}">
                                {{ $note->is_completed ? 'إعادة للتعليق' : 'تعيين كمنجز' }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-400 text-xs">
                    لا توجد ملاحظات أو مهام مدونة في المفكرة حالياً.
                </div>
            @endforelse
        </div>
    </div>

</div>

<!-- Modal: New Note -->
<div id="newNoteModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-200">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-base font-black text-gray-800">تدوين ملاحظة محاسبية جديدة</h3>
            <button onclick="document.getElementById('newNoteModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 font-bold text-sm">إغلاق [x]</button>
        </div>

        <form method="POST" action="{{ route('erp.notepad.store') }}" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-gray-700 mb-1">عنوان الملاحظة أو المهمة</label>
                <input type="text" name="title" required placeholder="مثال: مراجعة كشف البنك العربي وإقفال العهدة" class="w-full border rounded-lg p-2">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">درجة الأهمية</label>
                    <select name="priority" class="w-full border rounded-lg p-2 font-bold">
                        <option value="urgent">عاجل وهام</option>
                        <option value="high">أولوية مرتفعة</option>
                        <option value="normal" selected>أولوية عادية</option>
                        <option value="low">منخفضة</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">تاريخ الاستحقاق أو المتابعة</label>
                    <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+3 days')) }}" class="w-full border rounded-lg p-2 font-mono">
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">تفاصيل الملاحظة والإجراء المطلوب</label>
                <textarea name="content" rows="3" required placeholder="اكتب التفاصيل والمبالغ والأرقام المراد متابعتها..." class="w-full border rounded-lg p-2"></textarea>
            </div>

            <div class="pt-3 border-t flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('newNoteModal').classList.add('hidden')" class="px-4 py-2 border rounded-xl font-bold">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl font-bold">حفظ الملاحظة في المفكرة</button>
            </div>
        </form>
    </div>
</div>
@endsection


