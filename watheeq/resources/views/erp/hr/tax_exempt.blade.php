@extends('layouts.app')

@section('title', 'سند الإعفاءات الضريبية | وثيق ERP')

@section('content')
<div class="space-y-3 font-sans text-xs" dir="rtl">

    <form method="POST" action="{{ route('erp.hr.tax_exempt.store') }}" id="taxExemptForm">
        @csrf

        <!-- Top Tool Command Ribbon -->
        <div class="bg-white p-2 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-2 no-print">
            <div class="flex items-center gap-1.5 flex-wrap">
                <button type="submit" class="px-4 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
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
                <button type="button" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    ترحيل
                </button>
                <button type="button" onclick="window.print()" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    طباعة
                </button>
                <a href="{{ route('erp.hr') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    إلغاء
                </a>
                <a href="{{ route('erp.hr.tax_exempt') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    جديد
                </a>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 bg-emerald-500 text-white font-bold rounded text-[11px]">
                    جديد
                </span>
                <span class="font-bold text-gray-800 text-sm">
                    سند الإعفاءات
                </span>
            </div>
        </div>

        <!-- Master Document Header Meta Box -->
        <div class="bg-[#F8F9FA] p-3.5 rounded-xl border border-gray-200 shadow-sm space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">رقم السند:</span>
                    <input type="text" value="{{ $nextNo }}" readonly class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono font-bold text-gray-800 text-center">
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-16 font-bold text-gray-600 text-left">تاريخه:</span>
                    <input type="date" value="{{ date('Y-m-d') }}" required class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono text-center">
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">دفتر السندات:</span>
                    <select class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold">
                        <option>الدفتر الرئيسي للإعفاءات</option>
                        <option>دفتر ضريبة الدخل</option>
                    </select>
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-16 font-bold text-gray-600 text-left">النوع:</span>
                    <select class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold">
                        <option>إعفاء شخصي / عائلي</option>
                        <option>إعفاء نفقات علاج واستشفاء</option>
                        <option>إعفاء فوائد قروض سكنية</option>
                        <option>إعفاء التعليم الجامعي</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-6 flex items-center gap-2">
                    <span class="w-20 font-bold text-gray-600 text-left">الموظف:</span>
                    <div class="flex-1 flex gap-1">
                        <select class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->national_id }})</option>
                            @endforeach
                            @if($employees->isEmpty())
                                <option>أحمد المحاسب (9921002341)</option>
                                <option>سارة المبيعات (9942008765)</option>
                            @endif
                        </select>
                        <button type="button" class="px-2 py-1 bg-gray-100 border rounded font-bold">بحث</button>
                    </div>
                </div>

                <div class="md:col-span-6 flex items-center gap-2">
                    <span class="w-24 font-bold text-gray-600 text-left">قيمة الإعفاء السنوي:</span>
                    <input type="number" step="0.001" value="1000.000" class="flex-1 bg-white border border-gray-300 rounded px-2.5 py-1 font-mono font-bold text-[#1F2E4A]">
                    <span class="font-bold text-gray-600">JOD</span>
                </div>
            </div>

            <!-- Notes -->
            <div class="flex items-center gap-2 pt-1">
                <span class="w-20 font-bold text-gray-600 text-left">ملاحظات:</span>
                <input type="text" placeholder="سند إعفاء ضريبي معتمد بموجب المستندات المرفقة..." class="flex-1 bg-white border border-gray-300 rounded px-2 py-1">
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <button type="submit" class="px-6 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
                حفظ وترحيل سند الإعفاء
            </button>
        </div>

    </form>

</div>
@endsection


