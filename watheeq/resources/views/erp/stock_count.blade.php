@extends('layouts.app')

@section('title', 'مطابقة الجرد الفعلي للمخزون | وثيق ERP')

@section('content')
<div class="space-y-4 font-sans text-xs" dir="rtl">

    <form method="POST" action="{{ route('erp.stock_count.reconcile') }}" id="stockCountForm">
        @csrf

        <!-- Action Bar -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-3 no-print">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">إدارة المستودعات</span>
                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">الجرد الدوري والسنوي</span>
                </div>
                <h1 class="text-base font-black text-gray-900 mt-1">مطابقة وتسوية الجرد الفعلي للمستودع</h1>
                <p class="text-[11px] text-gray-500">امسح المواد بجهاز الباركود أو كاميرا الهاتف لجرد الكميات تلقائياً وتوليد الفروقات آلياً</p>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-5 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
                    اعتماد الجرد وتوليد سند التسوية
                </button>
                <button type="button" onclick="window.print()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                    طباعة نموذج الجرد (A4)
                </button>
            </div>
        </div>

        <!-- Hardware Barcode & QR Scanner Bar for Stock Counting -->
        <div class="bg-blue-50/70 p-3 rounded-xl border border-blue-200/80 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 no-print">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-bold text-[#1F2E4A]">قارئ رمز QR والباركود للجرد الفعلي المباشر:</span>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto flex-1 sm:max-w-md">
                <input 
                    type="text" 
                    id="stockBarcodeInput" 
                    placeholder="امسح باركود أو QR المادة لجردها وزيادة كميتها تلقائياً..." 
                    class="w-full bg-white border border-blue-300 rounded-lg px-3 py-1.5 text-xs font-mono font-bold focus:outline-none focus:ring-2 focus:ring-[#1F2E4A] shadow-inner"
                    autocomplete="off"
                >
                <button 
                    type="button" 
                    onclick="openStockCameraModal()" 
                    class="px-2.5 py-1.5 bg-white hover:bg-gray-100 text-gray-700 border border-gray-300 rounded-lg text-xs font-bold whitespace-nowrap transition"
                    title="مسح بواسطة كاميرا الجهاز"
                >
                    كاميرا
                </button>
                <button 
                    type="button" 
                    onclick="document.getElementById('testStockQrModal').classList.remove('hidden')" 
                    class="px-2.5 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-lg text-xs font-bold whitespace-nowrap transition"
                    title="أكواد تجريبية للشاشة"
                >
                    أكواد للتجربة
                </button>
            </div>
        </div>

        <!-- Inventory Count Table -->
        <div id="printableA4Area" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-3 border-b bg-gray-50 flex justify-between items-center">
                <h2 class="font-bold text-gray-800">بيانات الجرد الفعلي للأصناف</h2>
                <span class="font-mono text-gray-500">المستودع: الرئيسي - عمان | التاريخ: {{ date('Y-m-d') }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead class="bg-[#1F2E4A] text-white font-bold text-[11px]">
                        <tr>
                            <th class="py-2.5 px-3">كود الصنف</th>
                            <th class="py-2.5 px-3">اسم المادة / الصنف</th>
                            <th class="py-2.5 px-3">الوحدة</th>
                            <th class="py-2.5 px-3 text-center font-mono">الرصيد الدفتري (بالنظام)</th>
                            <th class="py-2.5 px-3 text-center font-mono w-36">الكمية الفعلية المجرودة</th>
                            <th class="py-2.5 px-3 text-center font-mono">فارق الجرد</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 font-medium">
                        @foreach($products as $p)
                            <tr class="hover:bg-gray-50/80 transition" id="row_{{ $p->id }}">
                                <td class="py-2.5 px-3 font-mono font-bold text-[#1F2E4A]">{{ $p->code }}</td>
                                <td class="py-2.5 px-3 font-bold text-gray-900">{{ $p->name }}</td>
                                <td class="py-2.5 px-3 text-gray-600">{{ $p->unit }}</td>
                                <td class="py-2.5 px-3 text-center font-mono font-bold text-gray-800" id="sysQty_{{ $p->id }}">{{ $p->quantity }}</td>
                                <td class="py-2.5 px-3 text-center">
                                    <input 
                                        type="number" 
                                        name="counts[{{ $p->id }}]" 
                                        id="actualQty_{{ $p->id }}" 
                                        value="{{ $p->quantity }}" 
                                        min="0" 
                                        oninput="calcDiff({{ $p->id }})" 
                                        class="w-24 bg-white border border-gray-300 rounded px-2 py-1 text-center font-mono font-black text-sm"
                                    >
                                </td>
                                <td class="py-2.5 px-3 text-center font-mono font-bold text-gray-400" id="diff_{{ $p->id }}">
                                    0 (مطابق)
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </form>

</div>

<!-- Modal: Test Barcodes & QR Codes for Stock Counting -->
<div id="testStockQrModal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-2xl max-h-[85vh] overflow-y-auto" dir="rtl">
        <div class="flex justify-between items-center border-b pb-3">
            <div>
                <h4 class="font-bold text-sm text-[#1F2E4A]">أكواد QR والباركود لاختبار جرد المستودع</h4>
                <p class="text-xs text-gray-500">امسح الكود بقارئ الباركود أو انقر على الزر لتجربة مطابقة وجرد الصنف فورياً</p>
            </div>
            <button type="button" onclick="document.getElementById('testStockQrModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700 font-bold text-xs">[إغلاق]</button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($products as $p)
                <div class="p-3 border rounded-xl bg-gray-50/50 flex flex-col items-center text-center space-y-2">
                    <div class="font-bold text-xs text-gray-800">{{ $p->name }}</div>
                    <div class="text-[11px] font-mono text-[#1F2E4A]">{{ $p->code }} | باركود: {{ $p->barcode ?? 'لا يوجد' }}</div>
                    <div class="bg-white p-2 rounded border border-gray-200">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data={{ urlencode($p->barcode ?? $p->code) }}" alt="QR" class="w-24 h-24">
                    </div>
                    <button type="button" onclick="simulateStockScan('{{ $p->barcode ?? $p->code }}')" class="w-full py-1 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded text-xs font-bold transition">
                        تجربة المسح الآن
                    </button>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal: Device Camera Scanner -->
<div id="stockCameraModal" class="hidden fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl" dir="rtl">
        <div class="flex justify-between items-center border-b pb-3">
            <h4 class="font-bold text-sm text-[#1F2E4A]">قارئ الكاميرا المباشر لجرد المخزون</h4>
            <button type="button" onclick="closeStockCameraModal()" class="text-gray-500 hover:text-gray-700 font-bold text-xs">[إغلاق]</button>
        </div>
        <p class="text-xs text-gray-600">وجّه كاميرا الهاتف أو الجهاز نحو الباركود أو رمز الـ QR على المنتج:</p>
        <div class="bg-black rounded-xl overflow-hidden min-h-[250px] flex items-center justify-center border-2 border-[#1F2E4A] p-1">
            <div id="stockCameraVideo" class="w-full"></div>
        </div>
        <button type="button" onclick="closeStockCameraModal()" class="w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg text-xs">
            إيقاف الكاميرا
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        WatheeqScanner.init(onStockScanned, 'stockBarcodeInput');
    });

    function onStockScanned(product) {
        const input = document.getElementById(`actualQty_${product.id}`);
        const tr = document.getElementById(`row_${product.id}`);

        if (input && tr) {
            const currentVal = parseInt(input.value || 0);
            input.value = currentVal + 1;
            calcDiff(product.id);

            // Highlight and scroll into view smoothly
            tr.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            tr.classList.add('bg-emerald-100');
            setTimeout(() => tr.classList.remove('bg-emerald-100'), 800);

            WatheeqScanner.playBeep('increment');
            WatheeqScanner.showToast(`تم مسح وجرد: ${product.name} (الكمية: ${input.value})`, true);
        } else {
            WatheeqScanner.playBeep('error');
            WatheeqScanner.showToast(`الصنف ${product.name} غير مدرج في جدول الجرد الحالي`, false);
        }
    }

    function simulateStockScan(code) {
        document.getElementById('testStockQrModal').classList.add('hidden');
        WatheeqScanner.lookup(code).then(res => {
            if (res && res.found) {
                onStockScanned(res.product);
            } else {
                WatheeqScanner.playBeep('error');
                WatheeqScanner.showToast('الصنف غير موجود: ' + code, false);
            }
        });
    }

    function openStockCameraModal() {
        document.getElementById('stockCameraModal').classList.remove('hidden');
        WatheeqScanner.startCamera('stockCameraVideo', function (product) {
            closeStockCameraModal();
            onStockScanned(product);
        });
    }

    function closeStockCameraModal() {
        WatheeqScanner.stopCamera('stockCameraVideo');
        document.getElementById('stockCameraModal').classList.add('hidden');
    }

    function calcDiff(id) {
        const sys = parseFloat(document.getElementById(`sysQty_${id}`).innerText) || 0;
        const actual = parseFloat(document.getElementById(`actualQty_${id}`).value) || 0;
        const diff = actual - sys;
        const diffCell = document.getElementById(`diff_${id}`);

        if (diff === 0) {
            diffCell.innerText = '0 (مطابق)';
            diffCell.className = 'py-2.5 px-3 text-center font-mono font-bold text-gray-400';
        } else if (diff > 0) {
            diffCell.innerText = `+${diff} (زيادة جرد)`;
            diffCell.className = 'py-2.5 px-3 text-center font-mono font-bold text-emerald-700 bg-emerald-50';
        } else {
            diffCell.innerText = `${diff} (عجز جرد)`;
            diffCell.className = 'py-2.5 px-3 text-center font-mono font-bold text-rose-700 bg-rose-50';
        }
    }
</script>
@endsection


