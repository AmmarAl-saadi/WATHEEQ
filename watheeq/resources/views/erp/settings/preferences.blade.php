@extends('layouts.app')

@section('content')
<div class="container-fluid py-3" style="direction: rtl; text-align: right; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h4 class="fw-bold mb-1 text-primary"><i class="bi bi-gear-wide-connected me-2"></i>خصائص ومحددات البرنامج (System Preferences)</h4>
            <span class="text-muted small">ضبط قواعد إدارة المخزون، الفواتير، أسعار التكلفة، الضرائب، التكرار، والأنظمة الإضافية (14_3i.php)</span>
        </div>
        <div>
            <button type="submit" form="preferencesForm" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm"><i class="bi bi-save me-1"></i>حفظ كافة الإعدادات</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form id="preferencesForm" action="{{ route('erp.settings.preferences.save') }}" method="POST">
        @csrf
        <div class="row g-3">
            <!-- Sidebar Navigation Tabs (Hesabate 14_3i layout) -->
            <div class="col-md-3">
                <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                    <div class="list-group list-group-flush rounded" id="prefTabs" role="tablist">
                        <a class="list-group-item list-group-item-action active fw-bold py-3" data-bs-toggle="list" href="#tab-inventory" role="tab"><i class="bi bi-box-seam me-2"></i>المخزون</a>
                        <a class="list-group-item list-group-item-action fw-bold py-3" data-bs-toggle="list" href="#tab-invoices" role="tab"><i class="bi bi-receipt me-2"></i>الفواتير</a>
                        <a class="list-group-item list-group-item-action fw-bold py-3" data-bs-toggle="list" href="#tab-tax" role="tab"><i class="bi bi-percent me-2"></i>الضريبة</a>
                        <a class="list-group-item list-group-item-action fw-bold py-3" data-bs-toggle="list" href="#tab-saveprint" role="tab"><i class="bi bi-printer me-2"></i>خيارات حفظ و طباعة</a>
                        <a class="list-group-item list-group-item-action fw-bold py-3" data-bs-toggle="list" href="#tab-display" role="tab"><i class="bi bi-eye-slash me-2"></i>إخفاء - إظهار</a>
                        <a class="list-group-item list-group-item-action fw-bold py-3" data-bs-toggle="list" href="#tab-search" role="tab"><i class="bi bi-search me-2"></i>خصائص البحث عن الأصناف</a>
                        <a class="list-group-item list-group-item-action fw-bold py-3" data-bs-toggle="list" href="#tab-extra" role="tab"><i class="bi bi-puzzle me-2"></i>خصائص الأنظمة الإضافية</a>
                    </div>
                </div>
            </div>

            <!-- Content Panes -->
            <div class="col-md-9">
                <div class="tab-content">
                    <!-- Tab 1: المخزون -->
                    <div class="tab-pane fade show active" id="tab-inventory" role="tabpanel">
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header bg-white py-3 fw-bold border-bottom"><i class="bi bi-box-seam text-primary me-2"></i>إعدادات تقييم المخزون وحساب التكلفة</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">طريقة تقييم المخزون:</label>
                                        <select name="pref[stock_valuation_method]" class="form-select">
                                            <option value="weighted_average" {{ ($prefs['stock_valuation_method'] ?? 'weighted_average') == 'weighted_average' ? 'selected' : '' }}>المتوسط المرجح (Weighted Average)</option>
                                            <option value="fifo" {{ ($prefs['stock_valuation_method'] ?? '') == 'fifo' ? 'selected' : '' }}>الوارد أولاً صادر أولاً (FIFO)</option>
                                            <option value="lifo" {{ ($prefs['stock_valuation_method'] ?? '') == 'lifo' ? 'selected' : '' }}>الوارد أخيراً صادر أولاً (LIFO)</option>
                                        </select>
                                        <div class="form-text small text-danger">في حال اعتماد طريقة تقييم المخزون المتوسط المرجح يمنع الرصيد السالب بالمخزون ويجب عمل إقفال دورة ضريبية عند الحاجة.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">احتساب المردودات في سعر التكلفة:</label>
                                        <select name="pref[include_returns_in_cost]" class="form-select">
                                            <option value="no" {{ ($prefs['include_returns_in_cost'] ?? 'no') == 'no' ? 'selected' : '' }}>لا</option>
                                            <option value="yes" {{ ($prefs['include_returns_in_cost'] ?? '') == 'yes' ? 'selected' : '' }}>نعم</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">احتساب سند إدخال البضاعة:</label>
                                        <select name="pref[stock_in_cost_effect]" class="form-select">
                                            <option value="qty_only" {{ ($prefs['stock_in_cost_effect'] ?? 'qty_only') == 'qty_only' ? 'selected' : '' }}>تأثر التكلفة بالكمية فقط</option>
                                            <option value="qty_and_price" {{ ($prefs['stock_in_cost_effect'] ?? '') == 'qty_and_price' ? 'selected' : '' }}>تأثر التكلفة بالكمية والسعر</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">وحدة الوزن الافتراضية:</label>
                                        <select name="pref[default_weight_unit]" class="form-select">
                                            <option value="none">لم يحدد</option>
                                            <option value="kg" {{ ($prefs['default_weight_unit'] ?? '') == 'kg' ? 'selected' : '' }}>كيلوغرام (KG)</option>
                                            <option value="g" {{ ($prefs['default_weight_unit'] ?? '') == 'g' ? 'selected' : '' }}>غرام (G)</option>
                                            <option value="ton" {{ ($prefs['default_weight_unit'] ?? '') == 'ton' ? 'selected' : '' }}>طن (Ton)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">معامل قسمة المقاييس:</label>
                                        <div class="input-group">
                                            <input type="number" name="pref[scale_divisor]" class="form-control" value="{{ $prefs['scale_divisor'] ?? '10000' }}">
                                            <span class="input-group-text">~</span>
                                            <input type="number" name="pref[scale_decimals]" class="form-control" style="max-width: 80px;" value="{{ $prefs['scale_decimals'] ?? '2' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Code قراءة الميزان الباركودي:</label>
                                        <input type="text" name="pref[scale_barcode_prefixes]" class="form-control" value="{{ $prefs['scale_barcode_prefixes'] ?? '21, 22, 27' }}" placeholder="21, 22, 27">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">إنتاج الأصناف الآلي من مخزن معادلة الإنتاج:</label>
                                        <select name="pref[auto_production_from_bom]" class="form-select">
                                            <option value="yes" {{ ($prefs['auto_production_from_bom'] ?? 'yes') == 'yes' ? 'selected' : '' }}>نعم</option>
                                            <option value="no" {{ ($prefs['auto_production_from_bom'] ?? '') == 'no' ? 'selected' : '' }}>لا</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">السماح بتعديل معادلة الإنتاج:</label>
                                        <select name="pref[allow_edit_bom]" class="form-select">
                                            <option value="yes" {{ ($prefs['allow_edit_bom'] ?? 'yes') == 'yes' ? 'selected' : '' }}>نعم</option>
                                            <option value="no" {{ ($prefs['allow_edit_bom'] ?? '') == 'no' ? 'selected' : '' }}>لا</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: الفواتير -->
                    <div class="tab-pane fade" id="tab-invoices" role="tabpanel">
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header bg-white py-3 fw-bold border-bottom"><i class="bi bi-receipt text-primary me-2"></i>محددات وقواعد الفواتير والبيع</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">عند البيع بأقل أو بسعر التكلفة:</label>
                                        <select name="pref[sell_below_cost_action]" class="form-select">
                                            <option value="warning" {{ ($prefs['sell_below_cost_action'] ?? 'warning') == 'warning' ? 'selected' : '' }}>تنبيه فقط</option>
                                            <option value="prevent" {{ ($prefs['sell_below_cost_action'] ?? '') == 'prevent' ? 'selected' : '' }}>منع البيع نهائياً</option>
                                            <option value="allow" {{ ($prefs['sell_below_cost_action'] ?? '') == 'allow' ? 'selected' : '' }}>السماح دون تنبيه</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">السؤال عند تغيير سعر الشراء:</label>
                                        <select name="pref[ask_on_purchase_price_change]" class="form-select">
                                            <option value="all" {{ ($prefs['ask_on_purchase_price_change'] ?? 'all') == 'all' ? 'selected' : '' }}>الكل</option>
                                            <option value="yes" {{ ($prefs['ask_on_purchase_price_change'] ?? '') == 'yes' ? 'selected' : '' }}>نعم</option>
                                            <option value="no" {{ ($prefs['ask_on_purchase_price_change'] ?? '') == 'no' ? 'selected' : '' }}>لا</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">السماح بالقبض أو الدفع بأكثر من مبلغ الفاتورة:</label>
                                        <select name="pref[allow_overpayment]" class="form-select">
                                            <option value="yes" {{ ($prefs['allow_overpayment'] ?? 'yes') == 'yes' ? 'selected' : '' }}>نعم</option>
                                            <option value="no" {{ ($prefs['allow_overpayment'] ?? '') == 'no' ? 'selected' : '' }}>لا</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">الخصم في بطاقة العميل هو نسبة:</label>
                                        <select name="pref[customer_card_discount_type]" class="form-select">
                                            <option value="invoice_total" {{ ($prefs['customer_card_discount_type'] ?? 'invoice_total') == 'invoice_total' ? 'selected' : '' }}>على مجموع الفاتورة</option>
                                            <option value="per_item" {{ ($prefs['customer_card_discount_type'] ?? '') == 'per_item' ? 'selected' : '' }}>على كل صنف</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">عدد الأصناف الافتراضي في شاشة الفواتير:</label>
                                        <input type="number" name="pref[default_invoice_rows_count]" class="form-control" value="{{ $prefs['default_invoice_rows_count'] ?? '40' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check pt-4">
                                            <input class="form-check-input" type="checkbox" name="pref[allow_negative_stock]" id="p_neg" value="1" {{ !empty($prefs['allow_negative_stock']) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="p_neg">السماح بالرصيد السالب في المخزون</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="pref[allow_expired_drugs_sale]" id="p_exp" value="1" {{ !empty($prefs['allow_expired_drugs_sale']) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-danger" for="p_exp">السماح ببيع الدواء / الصنف منتهي الصلاحية</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="pref[allow_edit_item_accounting_sum]" id="p_edit_sum" value="1" {{ !empty($prefs['allow_edit_item_accounting_sum']) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="p_edit_sum">السماح بتعديل المجموع الحسابي للصنف</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: الضريبة -->
                    <div class="tab-pane fade" id="tab-tax" role="tabpanel">
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header bg-white py-3 fw-bold border-bottom"><i class="bi bi-percent text-primary me-2"></i>إعدادات الضريبة العامة على المبيعات (Jordanian VAT 16% & ISTD)</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">قيمة الضريبة المضافة الافتراضية (%):</label>
                                        <input type="number" step="0.01" name="pref[default_vat_rate]" class="form-control fw-bold text-primary" value="{{ $prefs['default_vat_rate'] ?? '16' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">عملة الضريبة:</label>
                                        <select name="pref[vat_currency]" class="form-select">
                                            <option value="JOD" {{ ($prefs['vat_currency'] ?? 'JOD') == 'JOD' ? 'selected' : '' }}>دينار أردني (JOD)</option>
                                            <option value="USD" {{ ($prefs['vat_currency'] ?? '') == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">السعر عند الإدخال في الفواتير يشمل الضريبة (مبيعات):</label>
                                        <select name="pref[sales_price_includes_tax]" class="form-select">
                                            <option value="yes" {{ ($prefs['sales_price_includes_tax'] ?? 'yes') == 'yes' ? 'selected' : '' }}>نعم (شامل الضريبة 16%)</option>
                                            <option value="no" {{ ($prefs['sales_price_includes_tax'] ?? '') == 'no' ? 'selected' : '' }}>لا (غير شامل الضريبة)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">السعر عند الإدخال في الفواتير يشمل الضريبة (مشتريات):</label>
                                        <select name="pref[purchase_price_includes_tax]" class="form-select">
                                            <option value="yes" {{ ($prefs['purchase_price_includes_tax'] ?? 'yes') == 'yes' ? 'selected' : '' }}>نعم</option>
                                            <option value="no" {{ ($prefs['purchase_price_includes_tax'] ?? '') == 'no' ? 'selected' : '' }}>لا</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">سعر التكلفة يشمل الضريبة:</label>
                                        <select name="pref[cost_price_includes_tax]" class="form-select">
                                            <option value="no" {{ ($prefs['cost_price_includes_tax'] ?? 'no') == 'no' ? 'selected' : '' }}>لا (المعيار المحاسبي المعتمد)</option>
                                            <option value="yes" {{ ($prefs['cost_price_includes_tax'] ?? '') == 'yes' ? 'selected' : '' }}>نعم</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check pt-4">
                                            <input class="form-check-input" type="checkbox" name="pref[salaries_subject_to_tax]" id="p_taxsal" value="1" {{ !empty($prefs['salaries_subject_to_tax']) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="p_taxsal">الرواتب خاضعة للضريبة واقتطاعات ضريبة الدخل</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4: خيارات حفظ و طباعة -->
                    <div class="tab-pane fade" id="tab-saveprint" role="tabpanel">
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header bg-white py-3 fw-bold border-bottom"><i class="bi bi-printer text-primary me-2"></i>خيارات إجراءات الحفظ والطباعة التلقائية</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">عند اختيار الحفظ فإن العملية التي ستتم هي:</label>
                                        <select name="pref[on_save_action]" class="form-select">
                                            <option value="save_only" {{ ($prefs['on_save_action'] ?? 'save_only') == 'save_only' ? 'selected' : '' }}>حفظ فقط</option>
                                            <option value="save_and_print" {{ ($prefs['on_save_action'] ?? '') == 'save_and_print' ? 'selected' : '' }}>حفظ و طباعة</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">بعد الحفظ فتح سند جديد:</label>
                                        <select name="pref[after_save_action]" class="form-select">
                                            <option value="new_voucher" {{ ($prefs['after_save_action'] ?? 'new_voucher') == 'new_voucher' ? 'selected' : '' }}>نعم (فتح سند جديد فارغ)</option>
                                            <option value="stay" {{ ($prefs['after_save_action'] ?? '') == 'stay' ? 'selected' : '' }}>لا (البقاء على السند الحالي)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">تنسيق الأرقام في الطباعة:</label>
                                        <select name="pref[print_number_format]" class="form-select">
                                            <option value="yes" {{ ($prefs['print_number_format'] ?? 'yes') == 'yes' ? 'selected' : '' }}>نعم (فواصل الآلاف 1,000.000)</option>
                                            <option value="no" {{ ($prefs['print_number_format'] ?? '') == 'no' ? 'selected' : '' }}>لا</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check pt-4">
                                            <input class="form-check-input" type="checkbox" name="pref[confirm_on_save_edit]" id="p_confirm" value="1" {{ !empty($prefs['confirm_on_save_edit']) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="p_confirm">هل تريد حفظ/تعديل البيانات التي أدخلتها (رسالة تأكيد قبل الحفظ)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 5: إخفاء - إظهار -->
                    <div class="tab-pane fade" id="tab-display" role="tabpanel">
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header bg-white py-3 fw-bold border-bottom"><i class="bi bi-eye-slash text-primary me-2"></i>خيارات إخفاء وإظهار أعمدة التقارير</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="pref[show_report_footer]" id="p_repfoot" value="1" {{ !empty($prefs['show_report_footer']) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="p_repfoot">تذييل التقارير</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="pref[show_day_with_date_reports]" id="p_repday" value="1" {{ !empty($prefs['show_day_with_date_reports']) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="p_repday">إظهار اليوم إضافة للتاريخ في التقارير</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="pref[show_time_with_date_reports]" id="p_reptime" value="1" {{ !empty($prefs['show_time_with_date_reports']) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="p_reptime">إظهار الوقت إضافة للتاريخ في التقارير</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="pref[show_cheques_classification]" id="p_repchq" value="1" {{ !empty($prefs['show_cheques_classification']) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="p_repchq">إظهار تصنيف الشيكات</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 6: خصائص البحث عن الأصناف -->
                    <div class="tab-pane fade" id="tab-search" role="tabpanel">
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header bg-white py-3 fw-bold border-bottom"><i class="bi bi-search text-primary me-2"></i>خيارات وحقول البحث عن الأصناف والفواتير</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">تحديث البحث مباشرة من بطاقة الأصناف:</label>
                                        <select name="pref[live_search_from_item_card]" class="form-select">
                                            <option value="yes" {{ ($prefs['live_search_from_item_card'] ?? 'yes') == 'yes' ? 'selected' : '' }}>نعم (البحث الفوري)</option>
                                            <option value="no" {{ ($prefs['live_search_from_item_card'] ?? '') == 'no' ? 'selected' : '' }}>لا</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">إظهار القيم صفر في كشوفات الأصناف:</label>
                                        <select name="pref[show_zero_values]" class="form-select">
                                            <option value="yes" {{ ($prefs['show_zero_values'] ?? 'yes') == 'yes' ? 'selected' : '' }}>نعم</option>
                                            <option value="no" {{ ($prefs['show_zero_values'] ?? '') == 'no' ? 'selected' : '' }}>لا</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">طريقة عرض أسماء الأصناف:</label>
                                        <select name="pref[item_name_display_lang]" class="form-select">
                                            <option value="ar" {{ ($prefs['item_name_display_lang'] ?? 'ar') == 'ar' ? 'selected' : '' }}>عربي</option>
                                            <option value="en" {{ ($prefs['item_name_display_lang'] ?? '') == 'en' ? 'selected' : '' }}>إنجليزي</option>
                                            <option value="both" {{ ($prefs['item_name_display_lang'] ?? '') == 'both' ? 'selected' : '' }}>الاسمين معاً</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">عدد الأحرف في عمود الملاحظات:</label>
                                        <input type="number" name="pref[notes_column_char_count]" class="form-control" value="{{ $prefs['notes_column_char_count'] ?? '0' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 7: الأنظمة الإضافية -->
                    <div class="tab-pane fade" id="tab-extra" role="tabpanel">
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header bg-white py-3 fw-bold border-bottom"><i class="bi bi-puzzle text-primary me-2"></i>أنظمة الصيانة، السيريال، تاريخ الصلاحية، وتأجير المركبات</div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="pref[vehicle_rental_agreement]" id="p_vehrent" value="1" {{ !empty($prefs['vehicle_rental_agreement']) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="p_vehrent">تفعيل اتفاقية التأجير للمركبات</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="pref[mandatory_serial_input]" id="p_manser" value="1" {{ !empty($prefs['mandatory_serial_input']) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="p_manser">إدخال السيريال إجباري في الفواتير</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="pref[mandatory_expiry_date]" id="p_manexp" value="1" {{ !empty($prefs['mandatory_expiry_date']) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="p_manexp">تاريخ الصلاحية إلزامي للأصناف الدوائية والغذائية</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">طباعة ميزات خاصة:</label>
                                        <select name="pref[special_features_print_engine]" class="form-select">
                                            <option value="google_viewer">Google Viewer</option>
                                            <option value="native_pdf">المولد المباشر Native PDF</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
