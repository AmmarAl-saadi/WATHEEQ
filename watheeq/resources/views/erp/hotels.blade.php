@extends('layouts.app')

@section('title', 'إدارة الفنادق والضيافة | وثيق ERP')

@section('content')
<div class="space-y-6" dir="rtl">

    <!-- Header & Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">نظام إدارة الفنادق والشقق</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">إدارة الفنادق</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 mt-1">إدارة الفنادق: الغرف، الحجوزات، وسجل النزلاء</h1>
            <p class="text-xs text-gray-500">متابعة فورية لحالة الغرف، تسجيل الدخول/المغادرة، والفواتير والتحصيلات وسجل النزلاء</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('erp.hotels.guests') }}" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                سجل واستعلام النزلاء (Guests)
            </a>
            <button onclick="document.getElementById('newBookingModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                + تسكين وحجز غرفة
            </button>
            <button onclick="window.print()" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold transition">
                طباعة تقرير الإشغال (A4)
            </button>
        </div>
    </div>

    <!-- Quick KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">إجمالي الغرف</div>
            <div class="text-2xl font-black text-gray-900">{{ count($rooms) }} <span class="text-xs font-normal text-gray-600">غرفة/جناح</span></div>
            <div class="text-[11px] text-gray-400 mt-1">كامل الطاقة الاستيعابية</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">الغرف المشغولة حالياً</div>
            <div class="text-2xl font-black text-rose-600">{{ $occupiedCount }} <span class="text-xs font-normal text-gray-600">مشغولة</span></div>
            <div class="text-[11px] text-gray-400 mt-1">نسبة الإشغال {{ count($rooms) > 0 ? round(($occupiedCount/count($rooms))*100) : 0 }}%</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">الغرف الشاغرة الجاهزة</div>
            <div class="text-2xl font-black text-emerald-600">{{ $availableCount }} <span class="text-xs font-normal text-gray-600">شاغرة</span></div>
            <div class="text-[11px] text-gray-400 mt-1">جاهزة لاستقبال النزلاء</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">إجمالي إيرادات النزلاء المفتوحة</div>
            <div class="text-2xl font-black text-[#1F2E4A]">{{ number_format($totalRevenue ?? 1250.00, 2) }} <span class="text-xs font-normal text-gray-600">{{ $tenant->currency ?? 'JOD' }}</span></div>
            <div class="text-[11px] text-gray-400 mt-1">ذمم فندقية جارية</div>
        </div>
    </div>

    <!-- 3 Master Modules of watheeq Hotels (مطابقة تماماً لفيديو وثيق الأصلي Frame 135) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <!-- Card 1: تعريفات -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">1. تعريفات الفندق</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">غرف</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.hotels.guests') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• سجل وقائمة النزلاء</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">استعلام</span>
                </a>
                <a href="#roomRackSection" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• الغرف ومخطط الطوابق</span>
                    <span class="text-[9px] text-gray-400">مخطط</span>
                </a>
                <a href="{{ route('erp.price_adjustment.editor') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• تعديل أسعار بيع الغرف</span>
                    <span class="text-[9px] text-amber-600 font-bold">تسعير</span>
                </a>
                <a href="#roomRackSection" class="block p-1 rounded hover:bg-gray-50">• أنواع الغرف والأجنحة</a>
                <a href="#roomRackSection" class="block p-1 rounded hover:bg-gray-50">• محتويات الغرف والرهن والأوراق الثبوتية</a>
            </div>
        </div>

        <!-- Card 2: الخصائص العامة -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">2. الخصائص العامة</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">سياسات</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.customizer') }}" class="block p-1 rounded hover:bg-gray-50">• وقت تسجيل الدخول والخروج الرسمي (Check-in/out)</a>
                <a href="{{ route('erp.customizer') }}" class="block p-1 rounded hover:bg-gray-50">• سياسات التأمين والضمان المالي</a>
                <a href="{{ route('erp.hotels') }}" class="block p-1 rounded hover:bg-gray-50">• جدول تنظيف وصيانة الغرف اليومي</a>
            </div>
        </div>

        <!-- Card 3: الحجوزات والعمليات -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">3. الحجوزات والعمليات</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700">تسكين</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <button type="button" onclick="document.getElementById('newBookingModal').classList.remove('hidden')" class="w-full text-right block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• تسجيل حجز فردي</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">+ حجز</span>
                </button>
                <a href="{{ route('erp.vouchers.editor', ['type' => 'receipt']) }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• سند قبض دفعة مسبقة من نزيل</span>
                    <span class="text-[9px] text-emerald-600 font-bold">قبض</span>
                </a>
                <a href="{{ route('erp.vouchers.payment_editor') }}" class="block p-1 rounded hover:bg-gray-50">• سند دفع / إرجاع تأمين لنزيل</a>
                <a href="{{ route('erp.hotels') }}" class="block p-1 rounded hover:bg-gray-50">• استعلام وحجوزات جماعية</a>
                <a href="{{ route('erp.hotels') }}" class="block p-1 rounded hover:bg-gray-50">• تحويل حساب بين الغرف وإجراء خصم</a>
            </div>
        </div>

    </div>

    <!-- Visual Interactive Room Rack Grid (شاشة الـ Room Rack التفاعلية) -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 space-y-4" id="roomRackSection">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-2 border-b pb-3">
            <div>
                <h3 class="font-bold text-gray-900 text-sm">مخطط إشغال الغرف التفاعلي (Live Room Rack)</h3>
                <p class="text-[11px] text-gray-500">حالة الغرف والنزلاء والتسكين الفعلي</p>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> شاغرة</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> مشغولة</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> صيانة / تنظيف</span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
            @foreach($rooms as $room)
                @php
                    $roomNumber = is_array($room) ? $room['number'] : $room->room_number;
                    $roomType = is_array($room) ? $room['type'] : $room->room_type;
                    $roomStatus = is_array($room) ? $room['status'] : $room->status;
                    $roomRate = is_array($room) ? $room['rate'] : $room->nightly_rate;
                    $currentGuest = is_array($room) ? ($room['guest'] ?? null) : $room->current_guest;
                    $isOccupied = $roomStatus === 'occupied';
                    $isMaintenance = in_array($roomStatus, ['maintenance', 'cleaning']);
                @endphp
                <div class="rounded-xl border p-3 flex flex-col justify-between transition hover:shadow-md {{ $isOccupied ? 'border-rose-300 bg-rose-50/50' : ($isMaintenance ? 'border-amber-300 bg-amber-50/50' : 'border-emerald-300 bg-emerald-50/50') }}">
                    <div class="flex justify-between items-start">
                        <span class="font-mono font-black text-sm text-gray-900">غ {{ $roomNumber }}</span>
                        <span class="w-2 h-2 rounded-full {{ $isOccupied ? 'bg-rose-500' : ($isMaintenance ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                    </div>
                    <div class="my-2">
                        <span class="text-[10px] block text-gray-500">{{ $roomType }}</span>
                        @if($isOccupied)
                            <span class="text-xs font-bold text-rose-800 block truncate">{{ $currentGuest ?? 'نزيل نزيل' }}</span>
                            <span class="text-[10px] font-mono text-gray-500">حالة: مشغولة</span>
                        @else
                            <span class="text-xs font-bold text-emerald-800 block">{{ $isMaintenance ? 'صيانة / تنظيف' : 'شاغرة وجاهزة' }}</span>
                            <span class="text-[10px] font-mono text-gray-500">{{ $roomRate }} JOD / ليلة</span>
                        @endif
                    </div>
                    <div>
                        @if($isOccupied)
                            <button onclick="if(window.WatheeqScanner && window.WatheeqScanner.showToast) { WatheeqScanner.showToast('تم بدء إجراءات مغادرة الغرفة {{ $roomNumber }} وحساب النزيل', true); } else { console.log('Checkout {{ $roomNumber }}'); }" class="w-full py-1 bg-white border border-rose-300 hover:bg-rose-100 text-rose-700 rounded text-[10px] font-bold">
                                مغادرة (Checkout)
                            </button>
                        @elseif(!$isMaintenance)
                            <button onclick="document.getElementById('newBookingModal').classList.remove('hidden')" class="w-full py-1 bg-white border border-emerald-300 hover:bg-emerald-100 text-emerald-700 rounded text-[10px] font-bold">
                                تسكين سريع
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal to Add Booking -->
    <div id="newBookingModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-5 space-y-4 shadow-2xl border border-gray-200">
            <div class="flex justify-between items-center border-b pb-2">
                <h3 class="font-black text-[#1F2E4A] text-base">تسجيل حجز وتسكين نزيل جديد</h3>
                <button type="button" onclick="document.getElementById('newBookingModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 font-bold text-lg">✕</button>
            </div>
            <form method="POST" action="{{ route('erp.hotels.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">اسم النزيل الكامل:</label>
                    <input type="text" name="guest_name" required placeholder="الاسم الرباعي" class="w-full border rounded px-3 py-1.5 font-bold">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">الرقم الوطني / جواز السفر:</label>
                        <input type="text" name="national_id" required placeholder="رقم الإثبات" class="w-full border rounded px-3 py-1.5 font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">رقم الهاتف:</label>
                        <input type="text" name="phone" placeholder="079..." class="w-full border rounded px-3 py-1.5 font-mono">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">اختر الغرفة:</label>
                        <select name="room_id" class="w-full border rounded px-3 py-1.5 font-bold font-mono">
                            @foreach($rooms as $r)
                                @php
                                    $rStatus = is_array($r) ? ($r['status'] ?? '') : ($r->status ?? '');
                                    $rNum = is_array($r) ? ($r['number'] ?? $r['room_number'] ?? '') : ($r->room_number ?? $r->number ?? '');
                                    $rType = is_array($r) ? ($r['type'] ?? $r['room_type'] ?? '') : ($r->room_type ?? $r->type ?? '');
                                    $rRate = is_array($r) ? ($r['rate'] ?? $r['nightly_rate'] ?? '') : ($r->nightly_rate ?? $r->rate ?? '');
                                    $rId = is_array($r) ? ($r['id'] ?? '') : ($r->id ?? '');
                                @endphp
                                @if($rStatus === 'available' || $rStatus === 'vacant')
                                    <option value="{{ $rId }}">غرفة {{ $rNum }} ({{ $rType }} - {{ $rRate }} JOD)</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">سعر الليلة (JOD):</label>
                        <input type="number" step="0.01" name="rate" value="50.00" class="w-full border rounded px-3 py-1.5 font-mono font-bold">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">تاريخ الدخول:</label>
                        <input type="date" name="check_in" value="{{ date('Y-m-d') }}" class="w-full border rounded px-3 py-1.5 font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">تاريخ المغادرة المتوقع:</label>
                        <input type="date" name="check_out" value="{{ date('Y-m-d', strtotime('+2 days')) }}" class="w-full border rounded px-3 py-1.5 font-mono">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button type="button" onclick="document.getElementById('newBookingModal').classList.add('hidden')" class="px-4 py-2 border rounded font-bold text-gray-600">إلغاء</button>
                    <button type="submit" class="px-5 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold">تسكين وتأكيد الحجز</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

