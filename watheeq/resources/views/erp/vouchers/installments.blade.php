@extends('layouts.app')

@section('content')
<div class="container-fluid py-3" style="direction: rtl; text-align: right; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h4 class="fw-bold mb-1 text-primary"><i class="bi bi-calendar3 me-2"></i>سندات الأقساط وجداول التحصيل</h4>
            <span class="text-muted small">عرض ومتابعة كافة سندات الأقساط المجدولة واستحقاقاتها</span>
        </div>
        <a href="{{ route('erp.vouchers.installments.create') }}" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm"><i class="bi bi-plus-lg me-1"></i>سند أقساط جديد</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0 text-center align-middle">
                <thead class="table-light small">
                    <tr>
                        <th>رقم السند</th>
                        <th>التاريخ</th>
                        <th>العميل</th>
                        <th>النوع</th>
                        <th>المبلغ الإجمالي (د.أ)</th>
                        <th>التكرار</th>
                        <th>الحالة</th>
                        <th>ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vouchers as $v)
                        <tr>
                            <td class="fw-bold">{{ $v->voucher_no }}</td>
                            <td>{{ $v->voucher_date }}</td>
                            <td>{{ $v->customer_name ?? '—' }}</td>
                            <td><span class="badge {{ $v->invoice_type == 'debit' ? 'bg-danger' : 'bg-success' }}">{{ $v->invoice_type == 'debit' ? 'مدين' : 'دائن' }}</span></td>
                            <td class="fw-bold text-primary">{{ number_format($v->total_amount, 3) }}</td>
                            <td>{{ $v->recurrence_type == 'monthly' ? 'شهري' : ($v->recurrence_type == 'weekly' ? 'أسبوعي' : 'سنوي') }}</td>
                            <td><span class="badge bg-info text-dark">{{ $v->status }}</span></td>
                            <td class="small text-muted">{{ $v->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">لا يوجد سندات أقساط مسجلة حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($vouchers, 'links'))
            <div class="card-footer bg-white py-2">
                {{ $vouchers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
