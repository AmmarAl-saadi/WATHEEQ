@extends('layouts.app')

@section('title', 'شاشة الكاشير السريعة الاحترافية | POS')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Left Section: Products Search, Barcode Reader, Categories & Grid (8 Cols) -->
    <div class="lg:col-span-8 space-y-4">
        <!-- High-Speed Hardware Barcode & QR Scanner Bar -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm space-y-2.5">
            <div class="flex justify-between items-center text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="font-bold text-[#1F2E4A]">قارئ رمز QR والباركود نشط وجاهز (HID Auto-Wedge)</span>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="openCameraScannerModal()" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg text-[11px] border transition">
                        مسح بكاميرا الجهاز
                    </button>
                    <button type="button" onclick="document.getElementById('testQrModal').classList.remove('hidden')" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-[#1F2E4A] font-bold rounded-lg text-[11px] border border-blue-200 transition">
                        أكواد QR للتجربة
                    </button>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <input 
                        type="text" 
                        id="barcodeInput" 
                        autofocus 
                        placeholder="امسح رمز QR أو الباركود بجهاز القارئ (Scanner) أو اكتب واضغط Enter..."
                        class="w-full pr-4 pl-4 py-2.5 text-xs bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1F2E4A] font-mono font-bold"
                    >
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <select id="categoryFilter" onchange="filterProducts()" class="text-xs p-2.5 border rounded-lg bg-gray-50 font-semibold">
                        <option value="all">جميع الأقسام</option>
                        <option value="أجهزة ومعدات">أجهزة ومعدات</option>
                        <option value="شبكات وتمديدات">شبكات وتمديدات</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Products Visual Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3" id="productsGrid">
            @foreach($products as $p)
            <button 
                type="button" 
                onclick="addToCart({{ json_encode($p) }})"
                data-category="{{ $p->category }}"
                data-name="{{ $p->name }}"
                data-barcode="{{ $p->barcode }}"
                class="product-card bg-white p-3.5 rounded-xl border border-gray-200 hover:border-[#1F2E4A] text-right shadow-sm hover:shadow transition flex flex-col justify-between h-32 group"
            >
                <div>
                    <span class="text-[10px] text-gray-400 font-mono block">{{ $p->code }}</span>
                    <h4 class="text-xs font-bold text-gray-800 line-clamp-2 mt-0.5 group-hover:text-[#1F2E4A]">
                        {{ $p->name }}
                    </h4>
                </div>
                <div class="flex justify-between items-baseline pt-2 border-t border-gray-100">
                    <span class="text-[10px] text-gray-400 font-bold">مخزون: {{ $p->quantity }}</span>
                    <span class="text-xs font-black text-[#1F2E4A]">
                        {{ number_format($p->sale_price, 2) }} {{ $tenant->currency }}
                    </span>
                </div>
            </button>
            @endforeach
        </div>
    </div>

    <!-- Right Section: Interactive Cashier Terminal & Basket (4 Cols) -->
    <div class="lg:col-span-4 bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col h-[650px] justify-between">
        <!-- Terminal Top Bar -->
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-[#CED4DA] rounded-t-xl">
            <div class="flex items-center gap-2">
                <span class="text-lg"></span>
                <div>
                    <h3 class="text-xs font-bold text-[#1F2E4A]">فاتورة كاشير نقطة البيع</h3>
                    <p class="text-[10px] text-gray-500">الوردية النشطة • كاشير: م. العلي</p>
                </div>
            </div>
            <button type="button" onclick="clearCart()" class="text-xs text-red-600 hover:underline font-bold">
                تفريغ السلة
            </button>
        </div>

        <!-- Cart Items Dynamic Container -->
        <div id="cartItemsList" class="p-3 overflow-y-auto flex-1 space-y-2">
            <!-- Dynamically populated via JS -->
            <div id="emptyCartNotice" class="h-full flex flex-col items-center justify-center text-gray-400 text-center py-16">
                <span class="text-4xl mb-2"></span>
                <p class="text-xs font-bold">سلة الكاشير فارغة</p>
                <p class="text-[11px] text-gray-400 mt-1">امسح باركود أو اضغط على صنف لإضافته فوراً</p>
            </div>
        </div>

        <!-- Terminal Financial Totals & Fast Checkout -->
        <div class="p-4 bg-[#CED4DA] border-t border-gray-200 space-y-3 rounded-b-xl">
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between text-gray-600">
                    <span>المجموع الصافي:</span>
                    <span id="subtotalDisplay" class="font-mono font-bold">0.00 {{ $tenant->currency }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>ضريبة المبيعات ({{ $tenant->tax_rate }}%):</span>
                    <span id="taxDisplay" class="font-mono font-bold">0.00 {{ $tenant->currency }}</span>
                </div>
                <div class="flex justify-between text-base font-black text-[#1F2E4A] pt-2 border-t border-gray-300">
                    <span>المبلغ المستحق:</span>
                    <span id="grandTotalDisplay" class="font-mono">0.00 {{ $tenant->currency }}</span>
                </div>
            </div>

            <!-- Form to Submit to Laravel Backend -->
            <form id="posCheckoutForm" action="{{ route('erp.invoices.store') }}" method="POST">
                @csrf
                <input type="hidden" name="contact_id" value="1">
                <input type="hidden" name="type" value="sale">
                <input type="hidden" id="formPaymentMethod" name="payment_method" value="cash">
                <input type="hidden" id="formSubtotal" name="subtotal" value="0">
                <input type="hidden" id="formTaxTotal" name="tax_total" value="0">
                <input type="hidden" id="formGrandTotal" name="grand_total" value="0">
                <input type="hidden" name="notes" value="فاتورة نقاط بيع كاشير سريعة">
                <div id="hiddenItemInputs"></div>

                <div class="grid grid-cols-2 gap-2 pt-2">
                    <button 
                        type="button" 
                        onclick="submitCheckout('cash')" 
                        id="btnCashPay" 
                        disabled 
                        class="py-3 px-3 bg-[#1F2E4A] hover:bg-[#243b2a] disabled:opacity-40 text-white text-xs font-bold rounded-xl shadow flex items-center justify-center gap-1.5 transition"
                    >
                        <span></span> دفع نقدي (كاش)
                    </button>
                    <button 
                        type="button" 
                        onclick="submitCheckout('card')" 
                        id="btnCardPay" 
                        disabled 
                        class="py-3 px-3 bg-[#1F2E4A] hover:bg-[#343A40] disabled:opacity-40 text-white text-xs font-bold rounded-xl shadow flex items-center justify-center gap-1.5 transition"
                    >
                        <span></span> دفع ببطاقة (فيزا)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Thermal Receipt Print Preview (80mm) -->
<div id="receiptModal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 no-print">
    <div id="printablePosReceipt" class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4">
        <div class="text-center pb-2 border-b border-dashed border-gray-400">
            <h4 class="font-bold text-sm text-[#1F2E4A]">{{ $tenant->name }}</h4>
            <p class="text-[10px] text-gray-500">{{ $tenant->address }}</p>
            <p class="text-[10px] text-gray-600 font-mono">الرقم الضريبي: {{ $tenant->tax_number }}</p>
            <span class="inline-block mt-1 px-2 py-0.5 bg-gray-100 text-[10px] font-bold rounded">إيصال كاشير ضريبي رسمي</span>
        </div>

        <div id="receiptItemsContainer" class="text-xs space-y-1 font-mono">
            <!-- Dynamic receipt items -->
        </div>

        <div class="pt-2 border-t border-dashed border-gray-400 space-y-1 text-xs">
            <div class="flex justify-between font-bold">
                <span>المبلغ الإجمالي:</span>
                <span id="receiptGrandTotal" class="font-black text-[#1F2E4A]"></span>
            </div>
            <div class="flex justify-between text-[10px] text-gray-500">
                <span>طريقة الدفع:</span>
                <span id="receiptMethod"></span>
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t no-print">
            <button type="button" onclick="closeReceiptModal()" class="px-3 py-1.5 bg-gray-100 text-xs font-bold rounded-lg">إغلاق</button>
            <button type="button" onclick="watheeqPrint('printablePosReceipt', 'thermal')" class="px-4 py-1.5 bg-[#1F2E4A] text-white text-xs font-bold rounded-lg shadow">طباعة الإيصال</button>
        </div>
    </div>
</div>

<!-- Modal: Camera Scanner -->
<div id="cameraScannerModal" class="hidden fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-5 space-y-4 shadow-2xl" dir="rtl">
        <div class="flex justify-between items-center border-b pb-2">
            <h4 class="font-bold text-sm text-[#1F2E4A]">مسح رمز QR أو الباركود بكاميرا الجهاز</h4>
            <button type="button" onclick="closeCameraScannerModal()" class="text-gray-500 hover:text-gray-700 font-bold text-xs">[إغلاق]</button>
        </div>
        <div class="bg-black rounded-xl overflow-hidden min-h-[250px] flex items-center justify-center p-1">
            <div id="cameraVideo" class="w-full"></div>
        </div>
        <p class="text-[11px] text-gray-500 text-center">وجّه الكاميرا نحو باركود أو رمز QR الصنف، وسيتم إدراجه تلقائياً في السلة فور قراءته.</p>
        <div class="flex justify-end pt-2 border-t">
            <button type="button" onclick="closeCameraScannerModal()" class="px-4 py-1.5 bg-gray-100 font-bold text-xs rounded-xl">إلغاء</button>
        </div>
    </div>
</div>

<!-- Modal: Test Barcodes & QR Codes -->
<div id="testQrModal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-2xl max-h-[85vh] overflow-y-auto" dir="rtl">
        <div class="flex justify-between items-center border-b pb-3">
            <div>
                <h4 class="font-bold text-sm text-[#1F2E4A]">أكواد QR والباركود لاختبار القارئ المباشر</h4>
                <p class="text-xs text-gray-500">وجّه قارئ الباركود أو الـ QR نحو الشاشة لتجربة الإضافة التلقائية الفورية</p>
            </div>
            <button type="button" onclick="document.getElementById('testQrModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700 font-bold text-xs">[إغلاق]</button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($products as $p)
            <div class="p-4 border rounded-xl bg-gray-50 flex flex-col items-center text-center space-y-2 hover:border-[#1F2E4A] transition">
                <span class="text-xs font-bold text-gray-800">{{ $p->name }}</span>
                <span class="text-[10px] text-gray-500 font-mono">{{ $p->code }} • {{ $p->barcode }}</span>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data={{ urlencode($p->barcode ?: $p->code) }}" alt="QR Code" class="w-28 h-28 bg-white p-2 rounded-lg border shadow-sm">
                <span class="text-xs font-black text-[#1F2E4A]">{{ number_format($p->sale_price, 2) }} {{ $tenant->currency }}</span>
                <button type="button" onclick="simulateScan('{{ $p->barcode ?: $p->code }}')" class="px-3 py-1 bg-[#1F2E4A] hover:bg-[#343A40] text-white text-[11px] font-bold rounded-lg transition">
                    محاكاة المسح الآن
                </button>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    let cart = [];
    const taxRate = {{ $tenant->tax_rate ?? 16 }};
    const currency = '{{ $tenant->currency ?? "JOD" }}';

    // Initialize Global Hardware & Wedge Scanner
    document.addEventListener('DOMContentLoaded', function () {
        WatheeqScanner.init(function (product) {
            addToCart(product);
        }, 'barcodeInput');
    });

    function addToCart(product) {
        const existing = cart.find(item => item.id === product.id || item.name === product.name);
        if (existing) {
            existing.qty += 1;
            WatheeqScanner.playBeep('increment');
            WatheeqScanner.showToast(`تمت زيادة كمية: ${product.name} (${existing.qty})`, true);
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                price: parseFloat(product.sale_price),
                unit: product.unit || 'قطعة',
                qty: 1
            });
            WatheeqScanner.playBeep('success');
            WatheeqScanner.showToast(`تمت إضافة: ${product.name}`, true);
        }
        renderCart();

        // Keep scanner input focused and clear
        const input = document.getElementById('barcodeInput');
        if (input) {
            input.value = '';
            input.focus();
        }
    }

    function simulateScan(code) {
        document.getElementById('testQrModal').classList.add('hidden');
        WatheeqScanner.lookup(code).then(res => {
            if (res && res.found) {
                addToCart(res.product);
            } else {
                WatheeqScanner.playBeep('error');
                WatheeqScanner.showToast('الصنف غير موجود: ' + code, false);
            }
        });
    }

    function openCameraScannerModal() {
        document.getElementById('cameraScannerModal').classList.remove('hidden');
        WatheeqScanner.startCamera('cameraVideo', function (product) {
            closeCameraScannerModal();
            addToCart(product);
        });
    }

    function closeCameraScannerModal() {
        WatheeqScanner.stopCamera('cameraVideo');
        document.getElementById('cameraScannerModal').classList.add('hidden');
    }

    function updateQty(id, delta) {
        const item = cart.find(i => i.id === id);
        if (item) {
            item.qty += delta;
            if (item.qty <= 0) {
                cart = cart.filter(i => i.id !== id);
            }
        }
        renderCart();
    }

    function clearCart() {
        cart = [];
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cartItemsList');
        const emptyNotice = document.getElementById('emptyCartNotice');
        const btnCash = document.getElementById('btnCashPay');
        const btnCard = document.getElementById('btnCardPay');

        if (cart.length === 0) {
            container.innerHTML = `
                <div id="emptyCartNotice" class="h-full flex flex-col items-center justify-center text-gray-400 text-center py-16">
                    <p class="text-xs font-bold text-gray-600">سلة الكاشير فارغة</p>
                    <p class="text-[11px] text-gray-400 mt-1">امسح رمز QR أو الباركود أو اضغط على صنف لإضافته فوراً</p>
                </div>
            `;
            btnCash.disabled = true;
            btnCard.disabled = true;
            updateTotals(0, 0, 0);
            return;
        }

        btnCash.disabled = false;
        btnCard.disabled = false;

        let html = '';
        let subtotal = 0;

        cart.forEach(item => {
            const lineTotal = item.price * item.qty;
            subtotal += lineTotal;
            html += `
                <div class="p-2.5 rounded-lg border border-gray-100 bg-gray-50 flex justify-between items-center text-xs">
                    <div class="flex-1 pr-2">
                        <h5 class="font-bold text-gray-800 line-clamp-1">${item.name}</h5>
                        <span class="text-[10px] text-gray-500 font-mono">${item.price.toFixed(2)} × ${item.qty} ${item.unit}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center border border-gray-300 rounded bg-white">
                            <button type="button" onclick="updateQty(${item.id}, -1)" class="px-2 py-0.5 hover:bg-gray-100 font-bold">-</button>
                            <span class="px-2 font-bold font-mono text-xs">${item.qty}</span>
                            <button type="button" onclick="updateQty(${item.id}, 1)" class="px-2 py-0.5 hover:bg-gray-100 font-bold">+</button>
                        </div>
                        <span class="font-bold text-[#1F2E4A] w-14 text-left font-mono">${lineTotal.toFixed(2)}</span>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        const taxTotal = (subtotal * taxRate) / 100;
        const grandTotal = subtotal + taxTotal;
        updateTotals(subtotal, taxTotal, grandTotal);
    }

    function updateTotals(sub, tax, grand) {
        document.getElementById('subtotalDisplay').innerText = sub.toFixed(2) + ' ' + currency;
        document.getElementById('taxDisplay').innerText = tax.toFixed(2) + ' ' + currency;
        document.getElementById('grandTotalDisplay').innerText = grand.toFixed(2) + ' ' + currency;

        document.getElementById('formSubtotal').value = sub.toFixed(2);
        document.getElementById('formTaxTotal').value = tax.toFixed(2);
        document.getElementById('formGrandTotal').value = grand.toFixed(2);
    }

    function submitCheckout(method) {
        if (cart.length === 0) return;
        document.getElementById('formPaymentMethod').value = method;

        // Build hidden line items inputs for POST request
        const hiddenDiv = document.getElementById('hiddenItemInputs');
        hiddenDiv.innerHTML = '';

        cart.forEach((item, idx) => {
            hiddenDiv.innerHTML += `
                <input type="hidden" name="items[${idx}][product_id]" value="${item.id}">
                <input type="hidden" name="items[${idx}][name]" value="${item.name}">
                <input type="hidden" name="items[${idx}][unit]" value="${item.unit}">
                <input type="hidden" name="items[${idx}][price]" value="${item.price}">
                <input type="hidden" name="items[${idx}][qty]" value="${item.qty}">
            `;
        });

        document.getElementById('posCheckoutForm').submit();
    }

    function filterProducts() {
        const cat = document.getElementById('categoryFilter').value;
        const cards = document.querySelectorAll('.product-card');
        cards.forEach(c => {
            if (cat === 'all' || c.dataset.category === cat) {
                c.style.display = 'flex';
            } else {
                c.style.display = 'none';
            }
        });
    }
</script>
@endsection

