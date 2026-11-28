@extends('layouts.app')

@section('content')
<div class="container-fluid py-3" style="direction: rtl; text-align: right; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h4 class="fw-bold mb-1 text-primary"><i class="bi bi-buildings-fill me-2"></i>سجل الأصول الثابتة ومحرك الإهلاك الآلي (Fixed Assets & Depreciation)</h4>
            <span class="text-muted small">إدارة الأصول الرأسمالية، حساب نسب الإهلاك السنوي، وتوليد القيود المحاسبية التلقائية للأستاذ العام (Section 11)</span>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newAssetModal"><i class="bi bi-plus-lg me-1"></i>إضافة أصل جديد</button>
            <form action="{{ route('erp.assets.depreciate.run') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm px-3 fw-bold shadow-sm" onclick="return confirm('هل تريد بالتأكيد تشغيل محرك الإهلاك الآلي وتوليد القيود المحاسبية لهذه الفترة؟')">
                    <i class="bi bi-calculator me-1"></i>تشغيل قيد الإهلاك الآلي الفوري
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Top KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4 p-3 bg-white">
                <div class="text-muted small fw-bold mb-1">إجمالي تكلفة شراء الأصول</div>
                <div class="fs-4 fw-bold text-dark font-mono">{{ number_format($totalPurchaseCost, 3) }} <small class="fs-7 text-muted">د.أ</small></div>
                <div class="text-muted small mt-1">{{ count($assets) }} أصل مسجل بالنظام</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-danger border-4 p-3 bg-white">
                <div class="text-muted small fw-bold mb-1">مجمع الإهلاك المتراكم</div>
                <div class="fs-4 fw-bold text-danger font-mono">{{ number_format($totalAccumulatedDepreciation, 3) }} <small class="fs-7 text-muted">د.أ</small></div>
                <div class="text-danger small mt-1"><i class="bi bi-arrow-down-right me-1"></i>إهلاك مرحل دفترياً</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-success border-4 p-3 bg-white">
                <div class="text-muted small fw-bold mb-1">صافي القيمة الدفترية الحالية (Book Value)</div>
                <div class="fs-4 fw-bold text-success font-mono">{{ number_format($totalBookValue, 3) }} <small class="fs-7 text-muted">د.أ</small></div>
                <div class="text-success small mt-1 fw-bold">القيمة الحقيقية في الميزانية</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-info border-4 p-3 bg-white">
                <div class="text-muted small fw-bold mb-1">دورات الإهلاك المنفذة</div>
                <div class="fs-4 fw-bold text-info font-mono">{{ count($depreciationRuns) }} <small class="fs-7 text-muted">عملية</small></div>
                <div class="text-muted small mt-1">توليد قيود أوتوماتيكي</div>
            </div>
        </div>
    </div>

    <!-- Assets List Table -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 fw-bold d-flex justify-content-between align-items-center border-bottom">
            <span><i class="bi bi-list-check me-2 text-primary"></i>سجل تفاصيل الأصول الثابتة وحساب الإهلاك السنوي</span>
            <span class="badge bg-light text-dark border">دليل الأصول المعتمد</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0 align-middle text-center">
                <thead class="table-light small">
                    <tr>
                        <th>كود الأصل</th>
                        <th>اسم الأصل ومواصفاته</th>
                        <th>تاريخ الشراء</th>
                        <th>الموقع / الفرع</th>
                        <th>سعر الشراء (د.أ)</th>
                        <th>نسبة الإهلاك السنوي (%)</th>
                        <th>مجمع الإهلاك السابق</th>
                        <th>القيمة الدفترية الحالية</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $a)
                        <tr>
                            <td class="fw-bold font-mono text-primary">{{ $a->asset_code }}</td>
                            <td class="fw-bold text-start">{{ $a->name }}</td>
                            <td>{{ $a->purchase_date }}</td>
                            <td>{{ $a->location }}</td>
                            <td class="fw-bold font-mono">{{ number_format($a->purchase_cost, 3) }}</td>
                            <td class="font-mono text-danger fw-bold">{{ $a->depreciation_rate }}%</td>
                            <td class="font-mono text-danger">{{ number_format($a->accumulated_depreciation, 3) }}</td>
                            <td class="font-mono text-success fw-bold">{{ number_format($a->book_value, 3) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">لا يوجد أصول مسجلة حالياً. استخدم زر "إضافة أصل جديد".</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Depreciation Runs History -->
    @if(count($depreciationRuns) > 0)
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light py-2 fw-bold text-dark border-bottom">
            <i class="bi bi-clock-history me-2 text-secondary"></i>سجل دورات الإهلاك الآلي والقيود المرحلة
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0 text-center small">
                <thead class="table-light">
                    <tr>
                        <th>رقم العملية</th>
                        <th>الفترة المالية</th>
                        <th>تاريخ التشغيل</th>
                        <th>مجموع مبلغ الإهلاك (د.أ)</th>
                        <th>عدد الأصول</th>
                        <th>رقم قيد اليومية</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($depreciationRuns as $run)
                        <tr>
                            <td class="fw-bold font-mono">{{ $run->run_no }}</td>
                            <td>{{ $run->period_name }}</td>
                            <td>{{ $run->run_date }}</td>
                            <td class="fw-bold font-mono text-danger">{{ number_format($run->total_depreciation, 3) }}</td>
                            <td>{{ $run->assets_count }}</td>
                            <td class="font-mono text-primary fw-bold">{{ $run->journal_entry_no ?? 'AUTO-DEPR-JV' }}</td>
                            <td><span class="badge bg-success">مرحل للأستاذ العام</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Modal: New Asset -->
<div class="modal fade" id="newAssetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content shadow border-0" style="direction: rtl; text-align: right;">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>تسجيل أصل رأسمالي جديد في النظام</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('erp.fixed_assets.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">كود الأصل:</label>
                        <input type="text" name="asset_code" class="form-control form-control-sm font-mono fw-bold" value="AST-{{ rand(1000, 9999) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">اسم الأصل / الوصف:</label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="سيارة توزيع / سيرفر رئيسي..." required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">تاريخ الشراء:</label>
                            <input type="date" name="purchase_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">الموقع:</label>
                            <input type="text" name="location" class="form-control form-control-sm" value="المقر الرئيسي - عمان">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">تكلفة الشراء (د.أ):</label>
                            <input type="number" step="0.001" name="purchase_cost" class="form-control form-control-sm font-mono fw-bold text-primary" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">نسبة الإهلاك السنوي (%):</label>
                            <input type="number" step="0.1" name="depreciation_rate" class="form-control form-control-sm font-mono text-danger fw-bold" value="10.0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">حفظ الأصل</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
