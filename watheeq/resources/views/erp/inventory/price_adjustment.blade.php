@extends('layouts.app')

@section('title', 'سند تعديل أسعار البيع | وثيق ERP')

@section('content')
<div class="space-y-3 font-sans text-xs" dir="rtl">

    <form method="POST" action="{{ route('erp.price_adjustment.store') }}" id="priceAdjustmentForm">
        @csrf

        <!-- Top Tool Command Ribbon (مطابق لشاشة وثيق define/3_9_10i.php) -->
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
                    حذف
                </button>
                <a href="{{ route('erp.price_adjustment.editor') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    جديد
                </a>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 bg-emerald-500 text-white font-bold rounded text-[11px]">
                    جديد
                </span>
                <span class="font-bold text-gray-800 text-sm">
                    سند تعديل أسعار البيع
                </span>
            </div>
        </div>

        <!-- Master Document Header Meta Box -->
        <div class="bg-[#F8F9FA] p-3.5 rounded-xl border border-gray-200 shadow-sm space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-16 font-bold text-gray-600 text-left">الرقم:</span>
                    <input type="text" name="voucher_no" value="PRC-ADJ-{{ sprintf('%04d', $nextNo) }}" readonly class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono font-bold text-gray-800 text-center">
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-16 font-bold text-gray-600 text-left">من تاريخ:</span>
                    <input type="date" name="from_date" value="{{ date('Y-m-d') }}" required class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono text-center">
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-16 font-bold text-gray-600 text-left">إلى تاريخ:</span>
                    <input type="date" name="to_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-mono text-center">
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <span class="w-14 font-bold text-gray-600 text-left">الاسم:</span>
                    <input type="text" name="notes" placeholder="وصف تعديل الأسعار..." class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold">
                </div>
            </div>
        </div>

        <!-- Filter & Adjustment Matrix (مطابق لوثيق) -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-4">
            <h4 class="font-black text-[#1F2E4A] border-b pb-2 text-sm">محددات الأصناف وتعديل الأسعار:</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Col 1 -->
                <div class="space-y-2.5">
                    <div class="flex items-center gap-2">
                        <span class="w-28 font-bold text-gray-700 text-left">التصنيف:</span>
                        <select name="category" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold">
                            <option value="">جميع التصنيفات</option>
                            <option value="إلكترونيات">إلكترونيات وأجهزة</option>
                            <option value="مواد غذائية">مواد غذائية وتموينية</option>
                            <option value="أدوات مكتبية">قرطاسية وأدوات مكتبية</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="w-28 font-bold text-gray-700 text-left">العلامة التجارية:</span>
                        <input type="text" name="brand" placeholder="مثال: Samsung, Dell..." class="flex-1 bg-white border border-gray-300 rounded px-2 py-1">
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="w-28 font-bold text-gray-700 text-left">الموديل:</span>
                        <input type="text" name="model" placeholder="Model-2026" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1">
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="w-28 font-bold text-gray-700 text-left">حسب الصنف:</span>
                        <div class="flex-1 flex gap-1">
                            <select class="flex-1 bg-white border border-gray-300 rounded px-2 py-1">
                                <option value="">اختر صنفاً محدداً (اختياري)...</option>
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->name }} ({{ $prod->sale_price }} JOD)</option>
                                @endforeach
                            </select>
                            <button type="button" class="px-2 py-1 bg-gray-100 border rounded font-bold">بحث</button>
                        </div>
                    </div>
                </div>

                <!-- Col 2 -->
                <div class="space-y-2.5">
                    <div class="flex items-center gap-2">
                        <span class="w-28 font-bold text-gray-700 text-left">الشركة المنتجة:</span>
                        <input type="text" placeholder="الشركة المصنعة" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1">
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="w-28 font-bold text-gray-700 text-left">الطراز:</span>
                        <input type="text" placeholder="الطراز الفني" class="flex-1 bg-white border border-gray-300 rounded px-2 py-1">
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="w-28 font-bold text-gray-700 text-left">ألوان المخزون:</span>
                        <input type="text" placeholder="أسود، أبيض، فضي..." class="flex-1 bg-white border border-gray-300 rounded px-2 py-1">
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="w-28 font-bold text-gray-700 text-left">فئات سعر البيع:</span>
                        <select class="flex-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold">
                            <option>سعر البيع 1 (مفرق)</option>
                            <option>سعر البيع 2 (جملة)</option>
                            <option>سعر البيع 3 (موزع)</option>
                            <option>سعر التكلفة</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Action Button -->
            <div class="pt-3 border-t flex justify-between items-center">
                <span class="text-xs text-gray-500 font-medium">سيتم تطبيق التعديلات على كافة الأصناف المطابقة للشروط المحددة أعلاه فور الحفظ.</span>
                <button type="submit" class="px-6 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
                    تطبيق وحفظ التعديل
                </button>
            </div>
        </div>

    </form>

</div>
@endsection



