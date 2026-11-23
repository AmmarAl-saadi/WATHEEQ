@extends('layouts.app')

@section('title', ($type === 'customer' ? 'بطاقة عميل جديدة' : 'بطاقة مورد جديد') . ' | وثيق ERP')

@section('content')
<div class="space-y-3 font-sans text-xs" dir="rtl">

    <form method="POST" action="{{ route('erp.contacts.store') }}" id="watheeqContactForm">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">

        <!-- Top Tool Command Ribbon -->
        <div class="bg-white p-2 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center justify-between gap-2 no-print">
            <div class="flex items-center gap-1.5 flex-wrap">
                <button type="submit" class="px-4 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
                    حفظ البطاقة
                </button>
                <button type="button" onclick="window.print()" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    طباعة
                </button>
                <a href="{{ route('erp.contacts.editor', ['type' => $type]) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    جديد
                </a>
                <a href="{{ route('erp.contacts') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    سجل الذمم والحسابات
                </a>
            </div>

            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 bg-emerald-500 text-white font-bold rounded text-[11px]">
                    تعريف
                </span>
                <span class="font-bold text-gray-700 text-sm">
                    {{ $type === 'customer' ? 'بطاقة عميل (زبون مدين)' : 'بطاقة مورد (ذمة دائنة)' }}
                </span>
            </div>
        </div>

        <!-- Master Document Body (مقسمة إلى تبويبات وأقسام بيانات تفصيلية مطابقة لوثيق) -->
        <div class="bg-[#F8F9FA] p-4 rounded-xl border border-gray-200 shadow-sm space-y-4">
            
            <!-- Section 1: البيانات الأساسية -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-3">
                <h3 class="text-xs font-bold text-[#1F2E4A] border-b pb-2">1. البيانات التعريفية الأساسية</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                    <div class="md:col-span-3">
                        <label class="block font-bold text-gray-700 mb-1">رمز / كود الحساب:</label>
                        <input type="text" name="code" value="{{ $nextCode }}" readonly class="w-full bg-gray-50 border border-gray-300 rounded px-2.5 py-1.5 font-mono font-bold text-gray-800">
                    </div>

                    <div class="md:col-span-6">
                        <label class="block font-bold text-gray-700 mb-1">الاسم التجاري / اسم العميل الكامل:</label>
                        <input type="text" name="name" required placeholder="مثال: شركة النماء للاستيراد والتصدير" class="w-full bg-white border border-gray-300 rounded px-2.5 py-1.5 font-bold text-gray-800">
                    </div>

                    <div class="md:col-span-3">
                        <label class="block font-bold text-gray-700 mb-1">الرقم الضريبي / الوطني:</label>
                        <input type="text" name="tax_id" placeholder="مثال: 1004829103" class="w-full bg-white border border-gray-300 rounded px-2.5 py-1.5 font-mono">
                    </div>
                </div>
            </div>

            <!-- Section 2: الاتصال والعناوين -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-3">
                <h3 class="text-xs font-bold text-[#1F2E4A] border-b pb-2">2. معلومات الاتصال والموقع الجغرافي</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">رقم الهاتف الأرضي / المباشر:</label>
                        <input type="text" name="phone" placeholder="+962 6 ..." class="w-full bg-white border border-gray-300 rounded px-2.5 py-1.5 font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">البريد الإلكتروني الرسمي:</label>
                        <input type="email" name="email" placeholder="finance@company.com" class="w-full bg-white border border-gray-300 rounded px-2.5 py-1.5 font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">المدينة والمنطقة:</label>
                        <input type="text" name="address" placeholder="عمان - شارع مكة - مجمع 42" class="w-full bg-white border border-gray-300 rounded px-2.5 py-1.5">
                    </div>
                </div>
            </div>

            <!-- Section 3: الضبط المالي والائتمان (Credit Control) -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-3">
                <h3 class="text-xs font-bold text-[#1F2E4A] border-b pb-2">3. الضبط الائتماني والسياسات المالية</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">سقف حد الدين الائتماني (Credit Limit):</label>
                        <input type="number" step="100" name="credit_limit" value="5000.00" class="w-full bg-white border border-gray-300 rounded px-2.5 py-1.5 font-mono font-bold text-rose-700">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">الرصيد الافتتاحي القائم:</label>
                        <input type="number" step="0.01" name="balance" value="0.00" class="w-full bg-white border border-gray-300 rounded px-2.5 py-1.5 font-mono font-bold text-gray-800">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">فترة السماح بالسداد (بالأيام):</label>
                        <input type="number" value="30" class="w-full bg-white border border-gray-300 rounded px-2.5 py-1.5 font-mono">
                    </div>
                </div>
            </div>

        </div>

    </form>

</div>
@endsection



