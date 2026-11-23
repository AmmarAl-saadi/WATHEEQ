@extends('layouts.app')

@section('title', 'بطاقة صنف ومخزون جديدة | وثيق ERP')

@section('content')
<div class="space-y-3 font-sans text-xs" dir="rtl">

    <form method="POST" action="{{ route('erp.products.store') }}" id="watheeqProductForm">
        @csrf

        <!-- Top Tool Command Ribbon -->
        <div class="bg-white p-2 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-2 no-print">
            <div class="flex items-center gap-1.5 flex-wrap">
                <button type="submit" class="px-4 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
                    حفظ الصنف
                </button>
                <button type="button" onclick="window.print()" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    طباعة بطاقة الصنف
                </button>
                <a href="{{ route('erp.products.editor') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    جديد
                </a>
                <a href="{{ route('erp.products') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    سجل المستودعات والأصناف
                </a>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 bg-emerald-500 text-white font-bold rounded text-[11px]">
                    تعريف مخزون
                </span>
                <span class="font-bold text-gray-700 text-sm">
                    بطاقة صنف مادة ومستودع (Item Master Card)
                </span>
            </div>
        </div>

        <!-- Master Document Body -->
        <div class="bg-[#F8F9FA] p-4 rounded-xl border border-gray-200 shadow-sm space-y-4">
            
            <!-- Section 1: البيانات التعريفية للصنف -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-3">
                <h3 class="text-xs font-bold text-[#1F2E4A] border-b pb-2">1. التعريف الأساسي والباركود</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                    <div class="md:col-span-3">
                        <label class="block font-bold text-gray-700 mb-1">رقم المادة (كود الصنف):</label>
                        <input type="text" name="code" value="{{ $nextCode }}" readonly class="w-full bg-gray-50 border border-gray-300 rounded px-2.5 py-1.5 font-mono font-bold text-gray-800">
                    </div>

                    <div class="md:col-span-6">
                        <label class="block font-bold text-gray-700 mb-1">اسم الصنف / المادة باللغة العربية:</label>
                        <input type="text" name="name" required placeholder="مثال: شاشة سامسونج LED 27 بوصة" class="w-full bg-white border border-gray-300 rounded px-2.5 py-1.5 font-bold text-gray-800">
                    </div>

                    <div class="md:col-span-3">
                        <label class="block font-bold text-gray-700 mb-1">الباركود الدولي (Barcode):</label>
                        <input type="text" name="barcode" placeholder="6251009849201" class="w-full bg-white border border-gray-300 rounded px-2.5 py-1.5 font-mono font-bold text-[#1F2E4A]">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">المجموعة / التصنيف:</label>
                        <select name="category" class="w-full bg-white border border-gray-300 rounded px-2.5 py-1.5 font-bold">
                            <option value="أجهزة كهربائية وإلكترونيات">أجهزة كهربائية وإلكترونيات</option>
                            <option value="قطع غيار وشبكات">قطع غيار وشبكات</option>
                            <option value="مواد استهلاكية">مواد استهلاكية</option>
                            <option value="عام">عام</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">وحدة القياس الرئيسية:</label>
                        <select name="unit" class="w-full bg-white border border-gray-300 rounded px-2.5 py-1.5 font-bold">
                            <option value="قطعة">قطعة</option>
                            <option value="كرتونة">كرتونة</option>
                            <option value="متر">متر</option>
                            <option value="كيلوغرام">كيلوغرام</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">المستودع الافتراضي:</label>
                        <input type="text" name="warehouse" value="المستودع الرئيسي - عمان" class="w-full bg-white border border-gray-300 rounded px-2.5 py-1.5">
                    </div>
                </div>
            </div>

            <!-- Section 2: الأسعار وهوامش الربح -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-3">
                <h3 class="text-xs font-bold text-[#1F2E4A] border-b pb-2">2. التسعير وتكلفة الشراء وهامش الربح</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">سعر التكلفة (شراء):</label>
                        <input type="number" step="0.01" name="cost_price" value="0.00" required class="w-full bg-white border border-gray-300 rounded px-2.5 py-1.5 font-mono font-bold text-gray-900">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">سعر البيع الافتراضي (نقداً):</label>
                        <input type="number" step="0.01" name="sale_price" value="0.00" required class="w-full bg-white border border-gray-300 rounded px-2.5 py-1.5 font-mono font-bold text-[#1F2E4A]">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">نسبة الضريبة العامة:</label>
                        <input type="text" value="16% ضريبة مبيعات عامة" readonly class="w-full bg-gray-50 border border-gray-300 rounded px-2.5 py-1.5 font-bold text-gray-600">
                    </div>
                </div>
            </div>

            <!-- Section 3: الأرصدة وحدود الطلب المستودعي -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-3">
                <h3 class="text-xs font-bold text-[#1F2E4A] border-b pb-2">3. إدارة المخزون وتنبيهات إعادة الطلب</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">الرصيد الابتدائي الافتتاحي بالمستودع:</label>
                        <input type="number" name="quantity" value="0" min="0" class="w-full bg-white border border-gray-300 rounded px-2.5 py-1.5 font-mono font-bold text-gray-900">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">الحد الأدنى للطلب (Reorder Point):</label>
                        <input type="number" name="min_quantity" value="5" min="1" class="w-full bg-white border border-gray-300 rounded px-2.5 py-1.5 font-mono font-bold text-amber-700">
                    </div>
                </div>
            </div>

        </div>

    </form>

</div>
@endsection



