@extends('layouts.app')

@section('title', 'سجل النزلاء والفنادق | وثيق ERP')

@section('content')
<div class="space-y-4 font-sans text-xs" dir="rtl">

    <!-- Top Action & Search Bar (مطابق بالمللي لشاشة النزلاء hotel/h0_8ii.php) -->
    <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-3 no-print">
        <div class="flex items-center gap-2 w-full md:w-auto">
            <button type="button" onclick="document.getElementById('addGuestModal').classList.remove('hidden')" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold transition shadow-sm">
                + جديد (إضافة نزيل)
            </button>
            <a href="{{ route('erp.hotels') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                لوحة الغرف (Room Rack)
            </a>
        </div>

        <!-- Search Bar with Live Filter -->
        <div class="flex items-center gap-2 w-full md:w-1/2">
            <span class="font-bold text-gray-700 whitespace-nowrap">استعلام:</span>
            <input type="text" id="guestSearchInput" onkeyup="filterGuests()" placeholder="ابحث بالاسم، الرقم الوطني، أو الهاتف..." class="flex-1 bg-yellow-50 border border-gray-300 rounded px-3 py-1.5 font-bold text-gray-800 focus:bg-white transition">
            <button type="button" onclick="filterGuests()" class="px-3 py-1.5 bg-[#1F2E4A] text-white rounded font-bold">بحث</button>
        </div>

        <div class="flex items-center gap-2">
            <span class="font-black text-gray-800 text-sm">النزلاء ({{ $guests->count() }})</span>
        </div>
    </div>

    <!-- Master Guests DataGrid (مطابق لشاشة وثيق الأصلية) -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse" id="guestsTable">
                <thead class="bg-[#1F2E4A] text-white text-[11px]">
                    <tr>
                        <th class="p-2.5 border-l border-white/20 text-center w-10">#</th>
                        <th class="p-2.5 border-l border-white/20">الاسم</th>
                        <th class="p-2.5 border-l border-white/20">الرقم / كود النزيل</th>
                        <th class="p-2.5 border-l border-white/20">تاريخ الميلاد</th>
                        <th class="p-2.5 border-l border-white/20">العنوان</th>
                        <th class="p-2.5 border-l border-white/20">ملاحظات</th>
                        <th class="p-2.5 border-l border-white/20 text-center">قائمة سوداء</th>
                        <th class="p-2.5 border-l border-white/20 text-center w-14">تعديل</th>
                        <th class="p-2.5 border-l border-white/20 text-center w-14">حذف</th>
                        <th class="p-2.5 text-center w-16">ترحيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($guests as $index => $guest)
                        <tr class="hover:bg-gray-50 transition guest-row">
                            <td class="p-2 border-l text-center font-bold text-gray-500">{{ $index + 1 }}</td>
                            <td class="p-2 border-l font-bold text-gray-900 guest-name">{{ $guest->name }}</td>
                            <td class="p-2 border-l font-mono text-gray-700 guest-no">{{ $guest->guest_no }}</td>
                            <td class="p-2 border-l font-mono text-gray-600">{{ $guest->birth_date ?? '-' }}</td>
                            <td class="p-2 border-l text-gray-700">{{ $guest->address ?? '-' }}</td>
                            <td class="p-2 border-l text-gray-500">{{ $guest->notes ?? '-' }}</td>
                            <td class="p-2 border-l text-center">
                                @if($guest->is_blacklisted)
                                    <span class="px-2 py-0.5 bg-red-100 text-red-700 font-bold rounded text-[10px] border border-red-200">محظور (Blacklisted)</span>
                                @else
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 font-bold rounded text-[10px]">نشط (سليم)</span>
                                @endif
                            </td>
                            <td class="p-2 border-l text-center">
                                <button type="button" class="text-blue-600 hover:text-blue-800 font-bold">تعديل</button>
                            </td>
                            <td class="p-2 border-l text-center">
                                <button type="button" class="text-red-500 hover:text-red-700 font-bold">حذف</button>
                            </td>
                            <td class="p-2 text-center">
                                <button type="button" class="px-2 py-0.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[10px] font-bold">ترحيل</button>
                            </td>
                        </tr>
                    @empty
                        <tr class="guest-row">
                            <td class="p-2 border-l text-center font-bold text-gray-500">1</td>
                            <td class="p-2 border-l font-bold text-gray-900 guest-name">سيف الدين الهاشمي</td>
                            <td class="p-2 border-l font-mono text-gray-700 guest-no">GST-00101</td>
                            <td class="p-2 border-l font-mono text-gray-600">1988-06-14</td>
                            <td class="p-2 border-l text-gray-700">عمان - الشميساني</td>
                            <td class="p-2 border-l text-gray-500">عميل VIP دائم</td>
                            <td class="p-2 border-l text-center">
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 font-bold rounded text-[10px]">نشط (سليم)</span>
                            </td>
                            <td class="p-2 border-l text-center">
                                <button type="button" class="text-blue-600 hover:text-blue-800 font-bold">تعديل</button>
                            </td>
                            <td class="p-2 border-l text-center">
                                <button type="button" class="text-red-500 hover:text-red-700 font-bold">حذف</button>
                            </td>
                            <td class="p-2 text-center">
                                <button type="button" class="px-2 py-0.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[10px] font-bold">ترحيل</button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Adding Guest -->
    <div id="addGuestModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-5 space-y-4 shadow-2xl border border-gray-200">
            <div class="flex justify-between items-center border-b pb-2">
                <h3 class="font-black text-[#1F2E4A] text-base">إضافة نزيل جديد إلى السجل</h3>
                <button type="button" onclick="document.getElementById('addGuestModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 font-bold text-lg">✕</button>
            </div>
            <form method="POST" action="{{ route('erp.hotels.guests.store') }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">كود / رقم النزيل:</label>
                        <input type="text" name="guest_no" value="GST-{{ sprintf('%05d', $nextNo) }}" required class="w-full border rounded px-2.5 py-1.5 font-mono font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">اسم النزيل الرباعي:</label>
                        <input type="text" name="name" required placeholder="الاسم الكامل" class="w-full border rounded px-2.5 py-1.5 font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">الرقم الوطني / جواز السفر:</label>
                        <input type="text" name="national_id" placeholder="الرقم الوطني" class="w-full border rounded px-2.5 py-1.5 font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">تاريخ الميلاد:</label>
                        <input type="date" name="birth_date" class="w-full border rounded px-2.5 py-1.5 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">رقم الهاتف:</label>
                        <input type="text" name="phone" placeholder="079..." class="w-full border rounded px-2.5 py-1.5 font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">العنوان / المدينة:</label>
                        <input type="text" name="address" placeholder="عمان، إربد..." class="w-full border rounded px-2.5 py-1.5">
                    </div>
                </div>

                <div class="flex items-center gap-2 p-2 bg-red-50 rounded-lg border border-red-200">
                    <input type="checkbox" name="is_blacklisted" id="blacklistCheck" class="w-4 h-4 text-red-600 rounded">
                    <label for="blacklistCheck" class="font-bold text-red-700 text-xs">إدراج في القائمة السوداء (Blacklist - تحذير منع التسكين)</label>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">ملاحظات خاصة:</label>
                    <textarea name="notes" rows="2" placeholder="ملاحظات الحجز أو العادات الخاصة..." class="w-full border rounded p-2 text-xs"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button type="button" onclick="document.getElementById('addGuestModal').classList.add('hidden')" class="px-4 py-2 border rounded font-bold text-gray-600">إلغاء</button>
                    <button type="submit" class="px-5 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold">حفظ النزيل</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function filterGuests() {
        const query = document.getElementById('guestSearchInput').value.toLowerCase();
        const rows = document.querySelectorAll('.guest-row');
        rows.forEach(r => {
            const name = r.querySelector('.guest-name')?.innerText.toLowerCase() || '';
            const no = r.querySelector('.guest-no')?.innerText.toLowerCase() || '';
            if (name.includes(query) || no.includes(query)) {
                r.style.display = '';
            } else {
                r.style.display = 'none';
            }
        });
    }
</script>
@endsection



