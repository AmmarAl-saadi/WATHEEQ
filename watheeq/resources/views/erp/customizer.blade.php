@extends('layouts.app')

@section('title', 'تخصيص النظام للمشترك | وثيق ERP')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
        <h2 class="text-xl font-bold text-[#1F2E4A]">تخصيص بيانات وهوية المشترك</h2>
        <p class="text-xs text-gray-400">تعديل الاسم والعملة والضريبة ونمط الطباعة لكل شركة بشكل منفصل</p>
    </div>

    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A]">الامتثال الضريبي الأردني</span>
            <h3 class="font-bold text-[#1F2E4A] text-sm mt-1">نظام الفوترة الوطني الأردني (JoFotara / ISTD)</h3>
            <p class="text-xs text-gray-500">إدارة مفاتيح الربط البرمجي (Client ID / Secret Key)، اختبار الاتصال وتفعيل التزامن التلقائي للفواتير</p>
        </div>
        <a href="{{ route('erp.jofotara.hub') }}" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white text-xs font-bold rounded-xl transition shadow-sm">
            إدارة الربط مع JoFotara
        </a>
    </div>

    <form action="{{ route('erp.customizer.update') }}" method="POST" class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div>
                <label class="block font-semibold mb-1">اسم المنشأة بالعربية</label>
                <input type="text" name="name" value="{{ $tenant->name }}" required class="w-full p-2.5 border rounded-lg bg-gray-50">
            </div>
            <div>
                <label class="block font-semibold mb-1">اسم المنشأة بالإنجليزية</label>
                <input type="text" name="name_en" value="{{ $tenant->name_en }}" class="w-full p-2.5 border rounded-lg bg-gray-50">
            </div>
            <div>
                <label class="block font-semibold mb-1">الرقم الضريبي الرسمي</label>
                <input type="text" name="tax_number" value="{{ $tenant->tax_number }}" class="w-full p-2.5 border rounded-lg bg-gray-50 font-mono">
            </div>
            <div>
                <label class="block font-semibold mb-1">السجل التجاري</label>
                <input type="text" name="commercial_record" value="{{ $tenant->commercial_record }}" class="w-full p-2.5 border rounded-lg bg-gray-50 font-mono">
            </div>
            <div>
                <label class="block font-semibold mb-1">العملة الأساسية</label>
                <select name="currency" class="w-full p-2.5 border rounded-lg bg-gray-50 font-bold">
                    <option value="JOD" {{ $tenant->currency === 'JOD' ? 'selected' : '' }}>دينار أردني (JOD)</option>
                    <option value="USD" {{ $tenant->currency === 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                    <option value="SAR" {{ $tenant->currency === 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                    <option value="AED" {{ $tenant->currency === 'AED' ? 'selected' : '' }}>درهم إماراتي (AED)</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold mb-1">نسبة ضريبة المبيعات (%)</label>
                <input type="number" step="0.01" name="tax_rate" value="{{ $tenant->tax_rate }}" class="w-full p-2.5 border rounded-lg bg-gray-50 font-mono">
            </div>
            <div>
                <label class="block font-semibold mb-1">نموذج الطباعة الافتراضي</label>
                <select name="print_format" class="w-full p-2.5 border rounded-lg bg-gray-50">
                    <option value="A4" {{ $tenant->print_format === 'A4' ? 'selected' : '' }}>ورق قياسي A4</option>
                    <option value="Thermal80mm" {{ $tenant->print_format === 'Thermal80mm' ? 'selected' : '' }}>طابعة فواتير حرارية (80mm)</option>
                    <option value="A5" {{ $tenant->print_format === 'A5' ? 'selected' : '' }}>ورق نصف صفحة A5</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold mb-1">هاتف التواصل</label>
                <input type="text" name="phone" value="{{ $tenant->phone }}" class="w-full p-2.5 border rounded-lg bg-gray-50">
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold mb-1">العنوان الكامل</label>
                <input type="text" name="address" value="{{ $tenant->address }}" class="w-full p-2.5 border rounded-lg bg-gray-50">
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold mb-1">شروط وملاحظات الفاتورة الثابتة أسفل الصفحة</label>
                <textarea name="invoice_notes" rows="2" class="w-full p-2.5 border rounded-lg bg-gray-50">{{ $tenant->invoice_notes }}</textarea>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="px-6 py-2.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white text-xs font-bold rounded-xl shadow">
                حفظ التعديلات في قاعدة البيانات
            </button>
        </div>
    </form>
</div>
@endsection


