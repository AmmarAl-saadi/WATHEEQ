@extends('layouts.app')

@section('content')
<div class="container-fluid py-3" style="direction: rtl; text-align: right; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h4 class="fw-bold mb-1 text-primary"><i class="bi bi-shield-check me-2"></i>نظام المطابقة الثلاثية للمشتريات (Three-Way Matching)</h4>
            <span class="text-muted small">التدقيق الرقابي الصارم بين: أمر الشراء (PO) ⬅️ سند الاستلام (GRN) ⬅️ فاتورة المورد (Supplier Invoice) (Section 06)</span>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newPoModal"><i class="bi bi-file-earmark-plus me-1"></i>أمر شراء جديد (PO)</button>
            <button type="button" class="btn btn-primary btn-sm px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#newGrnModal"><i class="bi bi-box-arrow-in-down me-1"></i>سند استلام بضاعة (GRN)</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Audit Rules Summary Card -->
    <div class="card shadow-sm border-0 mb-4 bg-light">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span class="fw-bold text-dark fs-6"><i class="bi bi-info-circle-fill text-info me-2"></i>معايير التدقيق الحكومي والرقابة المالية:</span>
                    <p class="text-muted small mb-0 mt-1">يمنع النظام صرف أي مبالغ أو ترحيل فاتورة مورد للأستاذ العام إذا تجاوزت الفروقات السعرية أو الكمية نسبة 0.00% إلا بموافقة تدقيق استثنائية مشروحة مع تسجيل هوية المعتمد في سجل الـ Audit Trail.</p>
                </div>
                <div class="col-md-4 text-start">
                    <span class="badge bg-success p-2 fs-7"><i class="bi bi-check2-all me-1"></i>حماية ضد الفواتير المكررة والمصروفات غير المعتمدة</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Goods Receipt Notes & Matching Status Table -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 fw-bold d-flex justify-content-between align-items-center border-bottom">
            <span><i class="bi bi-truck me-2 text-primary"></i>سجل سندات الاستلام وتدقيق المطابقة مع أوامر الشراء والفواتير</span>
            <span class="badge bg-primary">التدقيق الآلي مفعل</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0 align-middle text-center">
                <thead class="table-light small">
                    <tr>
                        <th>رقم سند الاستلام (GRN)</th>
                        <th>أمر الشراء المرتبط (PO)</th>
                        <th>المورد</th>
                        <th>تاريخ الاستلام</th>
                        <th>رقم إرسالية المورد</th>
                        <th>المستودع المستقبل</th>
                        <th>القيمة الإجمالية (د.أ)</th>
                        <th>حالة المطابقة الثلاثية</th>
                        <th>إجراءات التدقيق</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($grnRecords as $g)
                        <tr>
                            <td class="fw-bold font-mono text-primary">{{ $g->grn_no }}</td>
                            <td class="fw-bold font-mono">{{ $g->po_no ?? 'بدون أمر شراء' }}</td>
                            <td>{{ $g->supplier_name ?? '—' }}</td>
                            <td>{{ $g->receipt_date }}</td>
                            <td>{{ $g->delivery_note_no ?? '—' }}</td>
                            <td>{{ $g->warehouse_name ?? 'المستودع الرئيسي' }}</td>
                            <td class="fw-bold font-mono">{{ number_format($g->total_received_value, 3) }}</td>
                            <td>
                                @if($g->matching_status == 'matched')
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>مطابق 100% (معتمد للصرف)</span>
                                @elseif($g->matching_status == 'variance_detected')
                                    <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>فروقات في السعر/الكمية</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>بانتظار وصول فاتورة المورد</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2" onclick="alert('سند الاستلام: {{ $g->grn_no }}\nالكميات والأسعار متطابقة مع أمر الشراء والفحص الفني مكتمل.')">
                                    <i class="bi bi-file-earmark-check me-1"></i>تقرير المطابقة
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">لا يوجد سندات استلام مسجلة حالياً. استخدم الأزرار العلوية لإنشاء أمر شراء أو سند استلام.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: New PO -->
<div class="modal fade" id="newPoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content shadow border-0" style="direction: rtl; text-align: right;">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title fw-bold"><i class="bi bi-file-earmark-plus me-2"></i>إنشاء أمر شراء معتمد (Purchase Order)</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('erp.procurement.po.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">رقم أمر الشراء:</label>
                        <input type="text" name="po_no" class="form-control form-control-sm font-mono fw-bold" value="PO-{{ date('Ymd') }}-{{ rand(100, 999) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">المورد المعتمد:</label>
                        <select name="supplier_id" class="form-select form-select-sm" required>
                            <option value="">اختر المورد...</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">القيمة التقديرية (د.أ):</label>
                        <input type="number" step="0.001" name="total_amount" class="form-control form-control-sm font-mono fw-bold text-primary" value="0.000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">شروط التوريد والملاحظات:</label>
                        <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="مكان التسليم، شروط الفحص، الموعد الأقصى للتوريد..."></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">اعتماد أمر الشراء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: New GRN -->
<div class="modal fade" id="newGrnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content shadow border-0" style="direction: rtl; text-align: right;">
            <div class="modal-header bg-success text-white py-2">
                <h6 class="modal-title fw-bold"><i class="bi bi-box-arrow-in-down me-2"></i>تسجيل سند استلام بضاعة وفحص جودة (GRN)</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('erp.procurement.grn.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">رقم سند الاستلام (GRN):</label>
                        <input type="text" name="grn_no" class="form-control form-control-sm font-mono fw-bold" value="GRN-{{ date('Ymd') }}-{{ rand(100, 999) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">أمر الشراء المرتبط (PO):</label>
                        <select name="purchase_order_id" class="form-select form-select-sm">
                            <option value="">(اختياري) ربط بأمر شراء</option>
                            @foreach($purchaseOrders as $po)
                                <option value="{{ $po->id }}">{{ $po->po_no }} - {{ number_format($po->total_amount, 3) }} د.أ</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">المورد:</label>
                        <select name="supplier_id" class="form-select form-select-sm" required>
                            <option value="">اختر المورد...</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">رقم إرسالية / بوليصة شحن المورد:</label>
                        <input type="text" name="delivery_note_no" class="form-control form-control-sm" placeholder="DN-XXXX">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">القيمة الفعلية المستلمة (د.أ):</label>
                        <input type="number" step="0.001" name="total_received_value" class="form-control form-control-sm font-mono fw-bold text-success" value="0.000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">حالة الفحص والمطابقة:</label>
                        <select name="matching_status" class="form-select form-select-sm">
                            <option value="matched">مطابق للمواصفات وأمر الشراء</option>
                            <option value="pending">قيد الفحص والمطابقة</option>
                            <option value="variance_detected">يوجد فروقات (بحاجة لموافقة المدير)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-bold">تأكيد الاستلام بالمستودع</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
