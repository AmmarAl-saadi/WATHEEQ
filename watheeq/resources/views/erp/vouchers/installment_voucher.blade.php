@extends('layouts.app')

@section('content')
<div class="container-fluid py-3" style="direction: rtl; text-align: right; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h4 class="fw-bold mb-1 text-primary"><i class="bi bi-calendar3 me-2"></i>سند الأقساط (Installment Voucher)</h4>
            <span class="text-muted small">جدولة أقساط الفواتير والذمم المالية ومتابعة استحقاقاتها الشهرية والسنوية (6_5_1i.php)</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('erp.vouchers.installments.create') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-plus-lg me-1"></i>جديد</a>
            <button type="submit" form="installmentForm" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm"><i class="bi bi-save me-1"></i>حفظ السند</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form id="installmentForm" action="{{ route('erp.vouchers.installments.store') }}" method="POST">
        @csrf
        
        <!-- Header Panel -->
        <div class="card shadow-sm border-0 mb-3 bg-light">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">الرقم:</label>
                        <input type="text" name="voucher_no" class="form-control form-control-sm bg-white fw-bold" value="{{ $voucherNo ?? 'INST-' . time() }}" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">التاريخ:</label>
                        <input type="date" name="voucher_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">العميل:</label>
                        <select name="customer_id" class="form-select form-select-sm" required>
                            <option value="">اختر العميل...</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone ?? 'بدون هاتف' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">الفواتير:</label>
                        <select name="invoice_id" class="form-select form-select-sm">
                            <option value="">(اختياري) ربط بفاتورة</option>
                            @foreach($invoices as $inv)
                                <option value="{{ $inv->id }}">{{ $inv->invoice_no ?? ('INV-' . $inv->id) }} - {{ number_format($inv->grand_total ?? ($inv->total ?? 0), 3) }} د.أ</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label small fw-bold">النوع:</label>
                        <select name="invoice_type" class="form-select form-select-sm">
                            <option value="debit">مدين</option>
                            <option value="credit">دائن</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">المجموع:</label>
                        <input type="number" step="0.001" name="total_amount" id="totalAmountInput" class="form-control form-control-sm fw-bold text-primary" value="0.000" readonly>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">سند يدوي:</label>
                        <input type="text" name="manual_voucher_no" class="form-control form-control-sm" placeholder="سند يدوي">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">التكرار:</label>
                        <select name="recurrence_type" id="recurrenceType" class="form-select form-select-sm" onchange="autoGenerateRows()">
                            <option value="monthly">التكرار شهري</option>
                            <option value="weekly">أسبوعي</option>
                            <option value="yearly">سنوي</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">عدد الأقساط:</label>
                        <div class="input-group input-group-sm">
                            <input type="number" id="installmentsCount" class="form-control form-control-sm" value="5" min="1" max="60">
                            <button type="button" class="btn btn-primary" onclick="autoGenerateRows()"><i class="bi bi-magic"></i> توليد</button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">مبلغ القسط الموحد:</label>
                        <input type="number" step="0.001" id="uniformAmount" class="form-control form-control-sm" placeholder="0.000" onkeyup="distributeUniformAmount()">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">الحالة:</label>
                        <span class="badge bg-success d-block py-2">جديد</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Installments Table (Matches Hesabate 6_5_1i.php) -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center">
                <span class="fw-bold fs-6"><i class="bi bi-list-check me-2"></i>جدول الأقساط وتواريخ الاستحقاق</span>
                <button type="button" class="btn btn-sm btn-light text-primary fw-bold" onclick="addNewRow()"><i class="bi bi-plus-circle me-1"></i>إضافة قسط</button>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 text-center align-middle" id="installmentsTable">
                    <thead class="table-light small">
                        <tr>
                            <th style="width: 50px;">الرقم</th>
                            <th style="width: 80px;">فعال</th>
                            <th>المبلغ (د.أ)</th>
                            <th>تاريخ الاستحقاق</th>
                            <th>سند يدوي</th>
                            <th style="width: 60px;">حذف</th>
                        </tr>
                    </thead>
                    <tbody id="installmentsTbody">
                        <!-- Populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Notes -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <label class="form-label small fw-bold">ملاحظات السند:</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="ملاحظات وشروط سداد الأقساط..."></textarea>
            </div>
        </div>
    </form>
</div>

<script>
let rowCount = 0;

function addNewRow(amount = 0, date = '', manualNo = '') {
    rowCount++;
    const tbody = document.getElementById('installmentsTbody');
    const tr = document.createElement('tr');
    tr.id = 'row_' + rowCount;
    
    if (!date) {
        let d = new Date();
        d.setMonth(d.getMonth() + (rowCount - 1));
        date = d.toISOString().split('T')[0];
    }
    
    tr.innerHTML = `
        <td class="fw-bold">${rowCount}</td>
        <td>
            <input type="checkbox" name="items[${rowCount}][is_active]" class="form-check-input" value="1" checked>
        </td>
        <td>
            <input type="number" step="0.001" name="items[${rowCount}][amount]" class="form-control form-control-sm text-center fw-bold amount-field" value="${amount}" oninput="calcTotal()" required>
        </td>
        <td>
            <input type="date" name="items[${rowCount}][due_date]" class="form-control form-control-sm text-center" value="${date}" required>
        </td>
        <td>
            <input type="text" name="items[${rowCount}][manual_no]" class="form-control form-control-sm text-center" value="${manualNo}" placeholder="رقم يدوي">
        </td>
        <td>
            <button type="button" class="btn btn-outline-danger btn-sm p-1" onclick="removeRow(${rowCount})"><i class="bi bi-trash"></i></button>
        </td>
    `;
    tbody.appendChild(tr);
    calcTotal();
}

function removeRow(id) {
    const el = document.getElementById('row_' + id);
    if (el) el.remove();
    calcTotal();
}

function calcTotal() {
    let sum = 0;
    document.querySelectorAll('.amount-field').forEach(input => {
        sum += parseFloat(input.value) || 0;
    });
    document.getElementById('totalAmountInput').value = sum.toFixed(3);
}

function autoGenerateRows() {
    const count = parseInt(document.getElementById('installmentsCount').value) || 5;
    const uniformAmt = parseFloat(document.getElementById('uniformAmount').value) || 0;
    const tbody = document.getElementById('installmentsTbody');
    tbody.innerHTML = '';
    rowCount = 0;
    
    const recType = document.getElementById('recurrenceType').value;
    
    for (let i = 1; i <= count; i++) {
        let d = new Date();
        if (recType === 'monthly') {
            d.setMonth(d.getMonth() + (i - 1));
        } else if (recType === 'weekly') {
            d.setDate(d.getDate() + ((i - 1) * 7));
        } else if (recType === 'yearly') {
            d.setFullYear(d.getFullYear() + (i - 1));
        }
        addNewRow(uniformAmt, d.toISOString().split('T')[0]);
    }
}

function distributeUniformAmount() {
    const uniformAmt = parseFloat(document.getElementById('uniformAmount').value) || 0;
    document.querySelectorAll('.amount-field').forEach(input => {
        input.value = uniformAmt.toFixed(3);
    });
    calcTotal();
}

document.addEventListener('DOMContentLoaded', () => {
    autoGenerateRows();
});
</script>
@endsection
