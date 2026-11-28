@extends('layouts.app')

@section('content')
<div class="container-fluid py-3" style="direction: rtl; text-align: right; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <!-- Top Header -->
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h4 class="fw-bold mb-1 text-primary"><i class="bi bi-briefcase-fill me-2"></i>إدارة المشاريع والعطاءات والخدمة الميدانية (Projects & Tenders)</h4>
            <span class="text-muted small">متابعة ربحية المشاريع، جداول الكميات (BOQ)، التكلفة الفعلية مقابل التقديرية، والمحتجزات النقدية (Section 10 & Visual Page 53)</span>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#newProjectModal">
                <i class="bi bi-plus-lg me-1"></i>مشروع / عطاء جديد
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Visual Appendix Page 53 Metric Cards (Exact Design from PDF Spec) -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4 p-3 bg-white">
                <div class="text-muted small fw-bold mb-1">إجمالي قيمة العقود (Contracts)</div>
                <div class="fs-4 fw-bold text-dark font-mono">{{ number_format($totalContractValue, 3) }} <small class="fs-7 text-muted">د.أ</small></div>
                <div class="text-success small mt-1"><i class="bi bi-check-circle me-1"></i>{{ $activeProjectsCount }} مشاريع قيد التنفيذ</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-warning border-4 p-3 bg-white">
                <div class="text-muted small fw-bold mb-1">التكلفة التقديرية (Expected Cost)</div>
                <div class="fs-4 fw-bold text-dark font-mono">{{ number_format($totalExpectedCost, 3) }} <small class="fs-7 text-muted">د.أ</small></div>
                <div class="text-muted small mt-1">بناءً على جداول الكميات BOQ</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-danger border-4 p-3 bg-white">
                <div class="text-muted small fw-bold mb-1">التكلفة الفعلية المنفذة (Actual Cost)</div>
                <div class="fs-4 fw-bold text-danger font-mono">{{ number_format($totalActualCost, 3) }} <small class="fs-7 text-muted">د.أ</small></div>
                <div class="text-muted small mt-1">مشتريات ومصروفات وساعات عمل</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-success border-4 p-3 bg-white">
                <div class="text-muted small fw-bold mb-1">إجمالي الربح التقديري (Gross Margin)</div>
                <div class="fs-4 fw-bold text-success font-mono">{{ number_format($totalGrossProfit, 3) }} <small class="fs-7 text-muted">د.أ</small></div>
                <div class="text-success small mt-1 fw-bold"><i class="bi bi-graph-up me-1"></i>هامش ربح {{ $averageGrossMargin }}%</div>
            </div>
        </div>
    </div>

    <!-- Active Projects Table & Progress Indicators -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 fw-bold d-flex justify-content-between align-items-center border-bottom">
            <span><i class="bi bi-kanban me-2 text-primary"></i>سجل المشاريع التنفيذية ونسب الإنجاز والتحصيل المالي</span>
            <span class="badge bg-light text-dark border">نظام مراقبة الأداء المتزامن</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0 align-middle text-center">
                <thead class="table-light small">
                    <tr>
                        <th>كود المشروع</th>
                        <th>اسم المشروع / العطاء</th>
                        <th>الجهة المالكة / العميل</th>
                        <th>قيمة العقد (د.أ)</th>
                        <th>التكلفة الفعلية</th>
                        <th>نسبة الإنجاز (Completion)</th>
                        <th>الميزانية المستهلكة (Budget)</th>
                        <th>المبالغ المحصلة (Cash)</th>
                        <th>المحتجزات (Retention)</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $p)
                        @php
                            $budgetConsumedPct = $p->expected_cost > 0 ? round(($p->actual_cost / $p->expected_cost) * 100) : 0;
                            $cashCollectedPct = $p->contract_value > 0 ? round(($p->cash_collected / $p->contract_value) * 100) : 0;
                        @endphp
                        <tr>
                            <td class="fw-bold font-mono text-primary">{{ $p->code }}</td>
                            <td class="fw-bold text-start">{{ $p->name }}</td>
                            <td>{{ $p->customer_name ?? '—' }}</td>
                            <td class="fw-bold font-mono">{{ number_format($p->contract_value, 3) }}</td>
                            <td class="font-mono text-danger">{{ number_format($p->actual_cost, 3) }}</td>
                            
                            <!-- Completion Bar -->
                            <td style="min-width: 130px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $p->completion_percentage }}%;" aria-valuenow="{{ $p->completion_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="small font-mono fw-bold">{{ $p->completion_percentage }}%</span>
                                </div>
                            </td>

                            <!-- Budget Consumed Bar -->
                            <td style="min-width: 130px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar {{ $budgetConsumedPct > 90 ? 'bg-danger' : 'bg-warning' }}" role="progressbar" style="width: {{ min($budgetConsumedPct, 100) }}%;"></div>
                                    </div>
                                    <span class="small font-mono fw-bold">{{ $budgetConsumedPct }}%</span>
                                </div>
                            </td>

                            <!-- Cash Collected Bar -->
                            <td style="min-width: 130px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ min($cashCollectedPct, 100) }}%;"></div>
                                    </div>
                                    <span class="small font-mono fw-bold">{{ $cashCollectedPct }}%</span>
                                </div>
                            </td>

                            <td class="font-mono small">{{ number_format($p->retention_amount, 3) }} ({{ $p->retention_rate }}%)</td>
                            <td>
                                <span class="badge {{ $p->status == 'completed' ? 'bg-success' : ($p->status == 'in_progress' ? 'bg-primary' : 'bg-secondary') }}">
                                    {{ $p->status == 'completed' ? 'مكتمل' : ($p->status == 'in_progress' ? 'قيد التنفيذ' : 'عطاء / مسودة') }}
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline-primary btn-sm py-1 px-2" onclick="openBoqModal({{ $p->id }}, '{{ $p->name }}')">
                                    <i class="bi bi-list-task me-1"></i>جدول الكميات BOQ
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">لا يوجد مشاريع مسجلة حالياً. انقر على "مشروع / عطاء جديد" للإضافة.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: New Project -->
<div class="modal fade" id="newProjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow border-0" style="direction: rtl; text-align: right;">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title fw-bold"><i class="bi bi-briefcase me-2"></i>تسجيل مشروع أو عطاء حكومي / تجاري جديد</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('erp.projects.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">كود المشروع:</label>
                            <input type="text" name="code" class="form-control form-control-sm font-mono fw-bold" value="PRJ-{{ date('Y') }}-{{ rand(100, 999) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">اسم المشروع / المناقصة:</label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="مشروع تركيب وتوريد أجهزة..." required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">الجهة المالكة / العميل:</label>
                            <select name="customer_id" class="form-select form-select-sm">
                                <option value="">اختر العميل...</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">قيمة العقد الإجمالية (د.أ):</label>
                            <input type="number" step="0.001" name="contract_value" class="form-control form-control-sm fw-bold text-primary font-mono" value="0.000" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">التكلفة التقديرية المخططة (د.أ):</label>
                            <input type="number" step="0.001" name="expected_cost" class="form-control form-control-sm font-mono" value="0.000" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">نسبة المحتجزات النقدية (% Retention):</label>
                            <input type="number" step="0.1" name="retention_rate" class="form-control form-control-sm font-mono" value="10.0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">تاريخ البدء:</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">تاريخ التسليم المتوقع:</label>
                            <input type="date" name="end_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">الحالة:</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="in_progress">قيد التنفيذ</option>
                                <option value="tender">عطاء / قيد الدراسة</option>
                                <option value="approved">معتمد</option>
                                <option value="on_hold">معلق</option>
                                <option value="completed">مكتمل</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">ملاحظات ومواصفات المشروع:</label>
                            <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="شروط التسليم والدفع والكفالات البنكية..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">حفظ المشروع</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openBoqModal(projectId, projectName) {
    alert("مشروع: " + projectName + "\nجدول الكميات مدمج تلقائياً مع فواتير المشتريات ومخزون المواد المخصصة للمشروع!");
}
</script>
@endsection
