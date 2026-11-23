@extends('layouts.app')

@section('title', 'إدارة الأصناف والمخزون والمستودعات | وثيق ERP')

@section('content')
<div class="space-y-6">
    <!-- Top Header & Action Controls -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm no-print">
        <div>
            <h2 class="text-xl font-bold text-[#1F2E4A]">إدارة بطاقات الأصناف وحركات المخزون</h2>
            <p class="text-xs text-gray-500">مراقبة الأرصدة المستودعية، الباركود، تكلفة الشراء وهوامش الربح</p>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button onclick="document.getElementById('newProductModal').classList.remove('hidden')" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#243b2a] text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5 whitespace-nowrap">
                <span>+</span> تعريف صنف جديد
            </button>
            <button onclick="window.print()" class="px-3 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1">
                <span></span> كشف الجرد
            </button>
        </div>
    </div>

    <!-- Inventory Summary Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $totalStockValue = $products->sum(function($p) { return $p->cost_price * $p->quantity; });
            $totalSalesValue = $products->sum(function($p) { return $p->sale_price * $p->quantity; });
            $lowStockItems = $products->filter(function($p) { return $p->quantity <= $p->min_quantity; });
        @endphp
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-500">إجمالي عدد الأصناف المعرفة</span>
            <h3 class="text-2xl font-black text-[#1F2E4A] mt-1">{{ count($products) }} <span class="text-xs font-bold text-gray-400">صنف</span></h3>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-500">قيمة المخزون بسعر التكلفة</span>
            <h3 class="text-2xl font-black text-gray-800 mt-1">{{ number_format($totalStockValue, 2) }} <span class="text-xs font-bold text-[#1F2E4A]">{{ $tenant->currency }}</span></h3>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-500">القيمة التقديرية للبيع</span>
            <h3 class="text-2xl font-black text-[#1F2E4A] mt-1">{{ number_format($totalSalesValue, 2) }} <span class="text-xs font-bold text-[#1F2E4A]">{{ $tenant->currency }}</span></h3>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-500">أصناف قاربت على النفاد</span>
            <h3 class="text-2xl font-black text-amber-600 mt-1">{{ count($lowStockItems) }} <span class="text-xs font-bold text-gray-400">بحاجة لطلب</span></h3>
        </div>
    </div>

    <!-- Products Table View with Filtering -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3 no-print">
            <input 
                type="text" 
                id="searchProductTable" 
                placeholder="بحث سريع بالاسم، الكود، أو الباركود..." 
                oninput="filterProductTable()" 
                class="w-full sm:w-80 text-xs p-2.5 border rounded-lg bg-gray-50 focus:outline-none focus:ring-1 focus:ring-[#1F2E4A]"
            >
            <div class="text-xs font-bold text-gray-500">
                موقع التخزين النشط: <span class="text-[#1F2E4A] font-extrabold">المستودع الرئيسي (فرع عمان)</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs" id="productTable">
                <thead class="bg-[#1F2E4A] text-white font-bold">
                    <tr>
                        <th class="py-3 px-4">كود الصنف</th>
                        <th class="py-3 px-4">الباركود الدولي</th>
                        <th class="py-3 px-4">اسم وبيان الصنف</th>
                        <th class="py-3 px-4">القسم / التصنيف</th>
                        <th class="py-3 px-4">سعر الشراء (التكلفة)</th>
                        <th class="py-3 px-4">سعر البيع</th>
                        <th class="py-3 px-4">هامش الربح</th>
                        <th class="py-3 px-4 text-center">الرصيد المتبقي</th>
                        <th class="py-3 px-4 text-center">الحالة</th>
                        <th class="py-3 px-4 text-center">رمز QR والملصق</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-medium">
                    @foreach($products as $p)
                    @php
                        $margin = $p->sale_price > 0 ? (($p->sale_price - $p->cost_price) / $p->sale_price) * 100 : 0;
                        $isLow = $p->quantity <= $p->min_quantity;
                    @endphp
                    <tr class="hover:bg-gray-50 transition product-row">
                        <td class="py-3.5 px-4 font-mono font-bold text-[#1F2E4A]">{{ $p->code }}</td>
                        <td class="py-3.5 px-4 font-mono text-gray-500">{{ $p->barcode ?? '—' }}</td>
                        <td class="py-3.5 px-4 font-bold text-gray-800">{{ $p->name }}</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 text-[10px] font-bold">{{ $p->category }}</span>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-gray-600">{{ number_format($p->cost_price, 2) }} {{ $tenant->currency }}</td>
                        <td class="py-3.5 px-4 font-mono font-bold text-gray-900">{{ number_format($p->sale_price, 2) }} {{ $tenant->currency }}</td>
                        <td class="py-3.5 px-4 font-mono text-emerald-700 font-bold">+{{ number_format($margin, 1) }}%</td>
                        <td class="py-3.5 px-4 text-center font-mono font-black text-sm {{ $isLow ? 'text-amber-600' : 'text-[#1F2E4A]' }}">
                            {{ $p->quantity }} <span class="text-[10px] text-gray-500 font-normal">{{ $p->unit }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($isLow)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">قارب على النفاد</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">متوفر</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <button 
                                type="button" 
                                onclick="showProductQrModal('{{ $p->code }}', '{{ addslashes($p->name) }}', '{{ $p->barcode ?? $p->code }}', '{{ number_format($p->sale_price, 2) }} {{ $tenant->currency }}')" 
                                class="px-2.5 py-1 bg-[#1F2E4A]/10 hover:bg-[#1F2E4A] text-[#1F2E4A] hover:text-white rounded text-[11px] font-bold border border-[#1F2E4A]/20 transition"
                            >
                                عرض وطباعة الملصق
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: New Product Form -->
<div id="newProductModal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4 my-8">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-base font-bold text-[#1F2E4A]">إضافة بطاقة صنف جديدة للمستودع</h3>
            <button onclick="document.getElementById('newProductModal').classList.add('hidden')" class="text-gray-400 text-xl font-bold">&times;</button>
        </div>

        <form action="{{ route('erp.products.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold mb-1">اسم الصنف والبيان التجاري</label>
                <input type="text" name="name" required placeholder="مثال: طابعة ليزر HP LaserJet Pro" class="w-full text-xs p-2.5 border rounded-lg bg-gray-50">
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                    <label class="block font-semibold mb-1">الباركود الدولي (Barcode)</label>
                    <input type="text" name="barcode" placeholder="اتركه فارغاً للتوليد التلقائي" class="w-full p-2.5 border rounded-lg bg-gray-50 font-mono">
                </div>
                <div>
                    <label class="block font-semibold mb-1">التصنيف / العائلة</label>
                    <input type="text" name="category" value="عام" class="w-full p-2.5 border rounded-lg bg-gray-50">
                </div>
                <div>
                    <label class="block font-semibold mb-1">وحدة القياس</label>
                    <select name="unit" class="w-full p-2.5 border rounded-lg bg-gray-50">
                        <option value="قطعة">قطعة (Piece)</option>
                        <option value="صندوق / كرتون">صندوق / كرتون (Box)</option>
                        <option value="كيلوجرام">كيلوجرام (KG)</option>
                        <option value="متر">متر (Meter)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1">المستودع / الفرع</label>
                    <input type="text" name="warehouse" value="المستودع الرئيسي" class="w-full p-2.5 border rounded-lg bg-gray-50">
                </div>
                <div>
                    <label class="block font-semibold mb-1">سعر التكلفة (شراء)</label>
                    <input type="number" step="0.01" name="cost_price" required value="0.00" class="w-full p-2.5 border rounded-lg bg-gray-50 font-mono">
                </div>
                <div>
                    <label class="block font-semibold mb-1">سعر البيع</label>
                    <input type="number" step="0.01" name="sale_price" required value="0.00" class="w-full p-2.5 border rounded-lg bg-gray-50 font-mono font-bold text-[#1F2E4A]">
                </div>
                <div>
                    <label class="block font-semibold mb-1">الرصيد الافتتاحي في المخزن</label>
                    <input type="number" name="quantity" value="10" class="w-full p-2.5 border rounded-lg bg-gray-50 font-mono">
                </div>
                <div>
                    <label class="block font-semibold mb-1">حد الطلب الأدنى للتنبيه</label>
                    <input type="number" name="min_quantity" value="5" class="w-full p-2.5 border rounded-lg bg-gray-50 font-mono">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="document.getElementById('newProductModal').classList.add('hidden')" class="px-4 py-2 text-xs font-bold text-gray-500 bg-gray-100 rounded-lg">إلغاء</button>
                <button type="submit" class="px-6 py-2 text-xs font-bold text-white bg-[#1F2E4A] rounded-lg shadow">حفظ الصنف في قاعدة البيانات</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Product QR & Barcode Label Viewer -->
<div id="productQrModal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 no-print">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 space-y-4 shadow-2xl text-center" dir="rtl">
        <div class="flex justify-between items-center border-b pb-3">
            <h4 class="font-bold text-sm text-[#1F2E4A]">ملصق الباركود ورمز الـ QR للصنف</h4>
            <button type="button" onclick="document.getElementById('productQrModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700 font-bold text-xs">[إغلاق]</button>
        </div>

        <!-- The Printable Sticker Area -->
        <div id="printableLabelCard" class="p-4 border-2 border-dashed border-gray-300 rounded-xl bg-white space-y-2">
            <div class="text-[11px] font-bold text-gray-500">{{ $tenant->name ?? 'وثيق ERP' }}</div>
            <div id="modalItemName" class="font-bold text-sm text-gray-900 leading-snug"></div>
            <div class="flex justify-center py-2">
                <img id="modalQrImg" src="" alt="QR" class="w-36 h-36 border border-gray-200 p-1.5 rounded-lg bg-white shadow-sm">
            </div>
            <div id="modalItemCode" class="font-mono font-bold text-xs text-gray-700"></div>
            <div id="modalItemBarcode" class="font-mono text-[11px] text-gray-500 tracking-wider"></div>
            <div id="modalItemPrice" class="font-mono font-black text-base text-[#1F2E4A] pt-1 border-t border-gray-200"></div>
        </div>

        <div class="flex gap-2 pt-2">
            <button type="button" onclick="printThermalLabel()" class="flex-1 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-lg text-xs font-bold transition shadow-sm">
                طباعة الملصق (Label Print)
            </button>
            <button type="button" onclick="document.getElementById('productQrModal').classList.add('hidden')" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold transition">
                إلغاء
            </button>
        </div>
    </div>
</div>

<script>
    function filterProductTable() {
        const query = document.getElementById('searchProductTable').value.toLowerCase();
        const rows = document.querySelectorAll('.product-row');
        rows.forEach(r => {
            const text = r.innerText.toLowerCase();
            r.style.display = text.includes(query) ? '' : 'none';
        });
    }

    function showProductQrModal(code, name, barcode, price) {
        document.getElementById('modalItemName').innerText = name;
        document.getElementById('modalItemCode').innerText = 'كود: ' + code;
        document.getElementById('modalItemBarcode').innerText = barcode ? ('باركود: ' + barcode) : '';
        document.getElementById('modalItemPrice').innerText = price;
        
        const scanData = barcode || code;
        document.getElementById('modalQrImg').src = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' + encodeURIComponent(scanData);
        document.getElementById('productQrModal').classList.remove('hidden');
    }

    function printThermalLabel() {
        watheeqPrint('printableLabelCard', 'label');
    }
</script>
@endsection


