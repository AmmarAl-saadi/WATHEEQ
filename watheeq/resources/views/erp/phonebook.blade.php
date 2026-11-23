@extends('layouts.app')

@section('title', 'دليل الهاتف وسجل العناوين | وثيق ERP')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">إدارة العلاقات والاتصال</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">دليل الهاتف السريع</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 mt-1">دليل الهاتف وسجل جهات الاتصال</h1>
            <p class="text-xs text-gray-500">البحث السريع عن أرقام هواتف العملاء، الموردين، البنوك، والدوائر الحكومية</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('newContactModal').classList.remove('hidden')" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                + إضافة جهة اتصال جديدة
            </button>
            <button onclick="window.print()" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold transition">
                طباعة الدليل (A4)
            </button>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col sm:flex-row gap-3 items-center justify-between no-print">
        <form method="GET" action="{{ route('erp.phonebook') }}" class="flex-1 flex gap-2 w-full">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="ابحث بالاسم، رقم الهاتف، أو اسم الشركة..." 
                class="flex-1 border border-gray-300 rounded-xl px-4 py-2 text-xs focus:outline-none focus:border-[#1F2E4A]"
            >
            <button type="submit" class="px-5 py-2 bg-[#1F2E4A] text-white rounded-xl text-xs font-bold">
                بحث
            </button>
            @if(request('search'))
                <a href="{{ route('erp.phonebook') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold flex items-center">
                    إلغاء التصفية
                </a>
            @endif
        </form>
        <div class="text-xs font-bold text-gray-500 font-mono">
            إجمالي جهات الاتصال: {{ count($contacts) }}
        </div>
    </div>

    <!-- Contacts Grid & Table -->
    <div id="printableA4Area" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50/70 flex justify-between items-center">
            <h2 class="text-sm font-black text-gray-800">سجل أرقام الهواتف والعناوين</h2>
            <span class="text-xs text-gray-500 font-mono">العدد: {{ count($contacts) }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-gray-100/80 text-gray-600 border-b border-gray-200 font-bold">
                        <th class="py-3 px-4">الاسم / الجهة</th>
                        <th class="py-3 px-4">التصنيف</th>
                        <th class="py-3 px-4">الشركة / المؤسسة</th>
                        <th class="py-3 px-4 font-mono">الهاتف الأرضي</th>
                        <th class="py-3 px-4 font-mono">الموبايل</th>
                        <th class="py-3 px-4">المدينة والمنطقة</th>
                        <th class="py-3 px-4">ملاحظات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($contacts as $c)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4 font-bold text-gray-900">{{ $c->name }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A]">
                                    {{ $c->category }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-700 font-medium">{{ $c->company ?? '-' }}</td>
                            <td class="py-3 px-4 font-mono font-bold text-[#1F2E4A]">{{ $c->phone }}</td>
                            <td class="py-3 px-4 font-mono text-gray-600">{{ $c->mobile ?? '-' }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $c->city ?? '-' }}</td>
                            <td class="py-3 px-4 text-gray-500 text-[11px]">{{ $c->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400">لا توجد جهات اتصال مطابقة لبحثك.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: New Contact -->
<div id="newContactModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-200">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-base font-black text-gray-800">إضافة جهة اتصال جديدة لدليل الهاتف</h3>
            <button onclick="document.getElementById('newContactModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 font-bold text-sm">إغلاق [x]</button>
        </div>

        <form method="POST" action="{{ route('erp.phonebook.store') }}" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-gray-700 mb-1">الاسم الكامل / جهة الاتصال</label>
                <input type="text" name="name" required placeholder="مثال: المهندس سمير عبد الرحيم" class="w-full border rounded-lg p-2">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">التصنيف</label>
                    <select name="category" class="w-full border rounded-lg p-2 font-bold">
                        <option value="عميل">عميل</option>
                        <option value="مورد معتمد">مورد معتمد</option>
                        <option value="بنك ومصرف">بنك ومصرف</option>
                        <option value="جهة حكومية">جهة حكومية</option>
                        <option value="شريك أعمال">شريك أعمال</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">اسم المؤسسة / الشركة</label>
                    <input type="text" name="company" placeholder="مثال: شركة النماء الدولية" class="w-full border rounded-lg p-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">رقم الهاتف الأرضي / المباشر</label>
                    <input type="text" name="phone" required placeholder="+962 6 ..." class="w-full border rounded-lg p-2 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">رقم الخلوي / الموبايل</label>
                    <input type="text" name="mobile" placeholder="+962 7 ..." class="w-full border rounded-lg p-2 font-mono">
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">المدينة والمنطقة</label>
                <input type="text" name="city" placeholder="مثال: عمان - شارع الجاردنز" class="w-full border rounded-lg p-2">
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">ملاحظات إضافية</label>
                <textarea name="notes" rows="2" placeholder="أي تفاصيل أو ساعات عمل..." class="w-full border rounded-lg p-2"></textarea>
            </div>

            <div class="pt-3 border-t flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('newContactModal').classList.add('hidden')" class="px-4 py-2 border rounded-xl font-bold">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl font-bold">حفظ جهة الاتصال</button>
            </div>
        </form>
    </div>
</div>
@endsection


