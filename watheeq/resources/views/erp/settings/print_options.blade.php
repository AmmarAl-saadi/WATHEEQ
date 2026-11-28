@extends('layouts.app')

@section('content')
<div class="container-fluid py-3" style="direction: rtl; text-align: right; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <div>
            <h4 class="fw-bold mb-1 text-primary"><i class="bi bi-printer-fill me-2"></i>خيارات الطباعة وتخصيص السندات والفواتير</h4>
            <span class="text-muted small">تخصيص كامل لأدق تفاصيل ترويسة، محتوى، تذييل، وأعمدة طباعة كافة الفواتير والسندات مع دعم QR-TLV الأردني</span>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="previewPrint()"><i class="bi bi-eye me-1"></i>معاينة شكل الطباعة</button>
            <button type="submit" form="printOptionsForm" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm"><i class="bi bi-save me-1"></i>حفظ التعديلات</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form id="printOptionsForm" action="{{ route('erp.settings.print_options.save') }}" method="POST">
        @csrf
        
        <!-- Document Selector Bar (Hesabate Style Dropdown) -->
        <div class="card shadow-sm border-0 mb-4 bg-light">
            <div class="card-body py-3">
                <div class="row align-items-center">
                    <label class="col-md-2 col-form-label fw-bold text-dark fs-6">نوع السند / الفاتورة:</label>
                    <div class="col-md-7">
                        <select name="document_type" id="documentTypeSelect" class="form-select form-select-lg border-primary fw-bold" onchange="changeDocType(this.value)">
                            <optgroup label="الفواتير الرئيسية">
                                <option value="sales_invoice" {{ $docType == 'sales_invoice' ? 'selected' : '' }}>فاتورة مبيعات</option>
                                <option value="purchase_invoice" {{ $docType == 'purchase_invoice' ? 'selected' : '' }}>فاتورة مشتريات</option>
                                <option value="sales_return" {{ $docType == 'sales_return' ? 'selected' : '' }}>مردود مبيعات</option>
                                <option value="purchase_return" {{ $docType == 'purchase_return' ? 'selected' : '' }}>مردود مشتريات</option>
                                <option value="pos_invoice" {{ $docType == 'pos_invoice' ? 'selected' : '' }}>فاتورة POS</option>
                                <option value="pos_kiosk" {{ $docType == 'pos_kiosk' ? 'selected' : '' }}>فاتورة POS Kiosk</option>
                                <option value="kitchen_kiosk" {{ $docType == 'kitchen_kiosk' ? 'selected' : '' }}>إلى المطبخ Kiosk</option>
                                <option value="pending_invoices" {{ $docType == 'pending_invoices' ? 'selected' : '' }}>فواتير معلقة</option>
                            </optgroup>
                            <optgroup label="السندات المالية">
                                <option value="receipt_voucher" {{ $docType == 'receipt_voucher' ? 'selected' : '' }}>سند قبض</option>
                                <option value="payment_voucher" {{ $docType == 'payment_voucher' ? 'selected' : '' }}>سند صرف</option>
                                <option value="cheque_voucher" {{ $docType == 'cheque_voucher' ? 'selected' : '' }}>سند شيكات</option>
                                <option value="bank_transfer_voucher" {{ $docType == 'bank_transfer_voucher' ? 'selected' : '' }}>سند تحويل من حساب البنك</option>
                                <option value="dues_settlement_voucher" {{ $docType == 'dues_settlement_voucher' ? 'selected' : '' }}>سند تسديد مستحقات</option>
                                <option value="profit_withdrawal_voucher" {{ $docType == 'profit_withdrawal_voucher' ? 'selected' : '' }}>سند سحب من أرباح</option>
                                <option value="installment_voucher" {{ $docType == 'installment_voucher' ? 'selected' : '' }}>سند الأقساط</option>
                            </optgroup>
                            <optgroup label="المستودعات والإنتاج">
                                <option value="stock_in_voucher" {{ $docType == 'stock_in_voucher' ? 'selected' : '' }}>سند إدخال بضاعة</option>
                                <option value="stock_out_voucher" {{ $docType == 'stock_out_voucher' ? 'selected' : '' }}>سند إخراج بضاعة</option>
                                <option value="stock_transfer_voucher" {{ $docType == 'stock_transfer_voucher' ? 'selected' : '' }}>سند تحويل بضاعة بين المستودعات</option>
                                <option value="production_voucher" {{ $docType == 'production_voucher' ? 'selected' : '' }}>سند إنتاج</option>
                                <option value="waste_voucher" {{ $docType == 'waste_voucher' ? 'selected' : '' }}>سند إتلاف</option>
                                <option value="packing_list" {{ $docType == 'packing_list' ? 'selected' : '' }}>قائمة التعبئة</option>
                                <option value="rental_agreement" {{ $docType == 'rental_agreement' ? 'selected' : '' }}>اتفاقية تأجير</option>
                                <option value="maintenance_voucher" {{ $docType == 'maintenance_voucher' ? 'selected' : '' }}>سند الصيانة</option>
                                <option value="receipt_delivery_voucher" {{ $docType == 'receipt_delivery_voucher' ? 'selected' : '' }}>سند الاستلام والتسليم</option>
                            </optgroup>
                            <optgroup label="الرواتب وشؤون الموظفين والتقارير">
                                <option value="salary_journal_voucher" {{ $docType == 'salary_journal_voucher' ? 'selected' : '' }}>قيد إثبات راتب</option>
                                <option value="employee_advance_voucher" {{ $docType == 'employee_advance_voucher' ? 'selected' : '' }}>سند سلفة موظف</option>
                                <option value="employee_deduction_voucher" {{ $docType == 'employee_deduction_voucher' ? 'selected' : '' }}>سند خصم</option>
                                <option value="employee_overtime_voucher" {{ $docType == 'employee_overtime_voucher' ? 'selected' : '' }}>سند عمل إضافي</option>
                                <option value="reports_header" {{ $docType == 'reports_header' ? 'selected' : '' }}>ترويسة - التقارير</option>
                                <option value="font_options" {{ $docType == 'font_options' ? 'selected' : '' }}>خيارات الخطوط</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-md-3 text-start">
                        <span class="badge bg-info text-dark p-2 fs-7"><i class="bi bi-info-circle me-1"></i>تخصيص مباشر لكل نموذج</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 1: Header / الترويسة -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center border-bottom">
                <span class="fw-bold text-dark fs-6"><i class="bi bi-card-heading text-primary me-2"></i>ترويسة المستند (Header Designer)</span>
                <div class="d-flex align-items-center gap-2">
                    <label class="small text-muted mb-0">لون الترويسة والحدود:</label>
                    <input type="color" name="header_color" class="form-control form-control-color" id="headerColor" value="{{ $template->header_color ?? '#1e293b' }}" title="اختر اللون">
                </div>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <label class="form-label small text-muted">محتوى وترويسة السند (نص ترحيبي، شعار، أسماء الفروع، أرقام السجلات التجارية، آيات قرآنية أو ترويسة مخصصة):</label>
                    <textarea name="header_html" id="headerHtml" class="form-control" rows="4" placeholder="اكتب ترويسة الفاتورة هنا أو استخدم كود HTML مخصص...">{{ $template->header_html ?? '' }}</textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="insertHeaderTag('[COMPANY_NAME]')">+ اسم الشركة</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="insertHeaderTag('[TAX_NO]')">+ الرقم الضريبي</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="insertHeaderTag('[COMMERCIAL_REG]')">+ السجل التجاري</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="insertHeaderTag('[BRANCH_PHONE]')">+ هاتف الفرع</button>
                </div>
            </div>
        </div>

        <!-- Section 2: Header Fields Matrix / رأس -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-secondary text-white py-2 d-flex justify-content-between align-items-center">
                <span class="fw-bold fs-6"><i class="bi bi-layout-text-window-reverse me-2"></i>خيارات الرأس (بيانات رأس الفاتورة / السند)</span>
                <div class="form-check form-check-inline text-white mb-0">
                    <input class="form-check-input" type="checkbox" name="apply_header_to_all" id="applyHeaderToAll" value="1" {{ !empty($template->apply_header_to_all) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-warning" for="applyHeaderToAll">تطبيق خيارات الرأس على كل السندات</label>
                </div>
            </div>
            <div class="card-body">
                @php
                    $h = $template->header_fields ?? [];
                @endphp
                <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-2">
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[company_logo]" id="h_logo" value="1" {{ !empty($h['company_logo']) ? 'checked' : '' }}><label class="form-check-label" for="h_logo">طباعة شعار الشركة</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[mobile]" id="h_mobile" value="1" {{ !empty($h['mobile']) ? 'checked' : '' }}><label class="form-check-label" for="h_mobile">موبايل</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[voucher_book]" id="h_book" value="1" {{ !empty($h['voucher_book']) ? 'checked' : '' }}><label class="form-check-label" for="h_book">دفتر السندات</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[tax_exchange_rate]" id="h_tax_rate" value="1" {{ !empty($h['tax_exchange_rate']) ? 'checked' : '' }}><label class="form-check-label" for="h_tax_rate">سعر الصرف الضريبة</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[e_default]" id="h_edef" value="1" {{ !empty($h['e_default']) ? 'checked' : '' }}><label class="form-check-label" for="h_edef">E- افتراضي</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[day]" id="h_day" value="1" {{ !empty($h['day']) ? 'checked' : '' }}><label class="form-check-label" for="h_day">اليوم</label></div></div>
                    
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[licensed_operator]" id="h_lic" value="1" {{ !empty($h['licensed_operator']) ? 'checked' : '' }}><label class="form-check-label" for="h_lic">مشتغل مرخص</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[vehicle_no_type]" id="h_veh" value="1" {{ !empty($h['vehicle_no_type']) ? 'checked' : '' }}><label class="form-check-label" for="h_veh">رقم / نوع المركبة</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[clearance_invoice]" id="h_clear" value="1" {{ !empty($h['clearance_invoice']) ? 'checked' : '' }}><label class="form-check-label" for="h_clear">فاتورة مقاصة</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[fax]" id="h_fax" value="1" {{ !empty($h['fax']) ? 'checked' : '' }}><label class="form-check-label" for="h_fax">فاكس</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[time]" id="h_time" value="1" {{ !empty($h['time']) ? 'checked' : '' }}><label class="form-check-label" for="h_time">الوقت</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[voucher_type]" id="h_type" value="1" {{ !empty($h['voucher_type']) ? 'checked' : '' }}><label class="form-check-label" for="h_type">نوع السند</label></div></div>

                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[date]" id="h_date" value="1" {{ !empty($h['date']) ? 'checked' : '' }}><label class="form-check-label" for="h_date">التاريخ</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[tax_classification]" id="h_tax_cls" value="1" {{ !empty($h['tax_classification']) ? 'checked' : '' }}><label class="form-check-label" for="h_tax_cls">التصنيف الضريبي</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[zero_tax_rate]" id="h_zero" value="1" {{ !empty($h['zero_tax_rate']) ? 'checked' : '' }}><label class="form-check-label" for="h_zero">خاضع لنسبة صفر</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[is_paid]" id="h_paid" value="1" {{ !empty($h['is_paid']) ? 'checked' : '' }}><label class="form-check-label" for="h_paid">مسدد</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[customer_no]" id="h_cno" value="1" {{ !empty($h['customer_no']) ? 'checked' : '' }}><label class="form-check-label" for="h_cno">رقم العميل</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[manual_date]" id="h_mdate" value="1" {{ !empty($h['manual_date']) ? 'checked' : '' }}><label class="form-check-label" for="h_mdate">التاريخ اليدوي</label></div></div>

                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[barcode_no]" id="h_bcode" value="1" {{ !empty($h['barcode_no']) ? 'checked' : '' }}><label class="form-check-label" for="h_bcode">رقم الباركود</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[income_tax_file_no]" id="h_incometax" value="1" {{ !empty($h['income_tax_file_no']) ? 'checked' : '' }}><label class="form-check-label" for="h_incometax">رقم ملف ضريبة الدخل</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[customer_levels]" id="h_clevels" value="1" {{ !empty($h['customer_levels']) ? 'checked' : '' }}><label class="form-check-label" for="h_clevels">مستويات العملاء</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[address]" id="h_addr" value="1" {{ !empty($h['address']) ? 'checked' : '' }}><label class="form-check-label" for="h_addr">العنوان</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[vat_calculation_date]" id="h_vatdate" value="1" {{ !empty($h['vat_calculation_date']) ? 'checked' : '' }}><label class="form-check-label" for="h_vatdate">تاريخ احتساب ض.م</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[voucher_no]" id="h_vno" value="1" {{ !empty($h['voucher_no']) ? 'checked' : '' }}><label class="form-check-label" for="h_vno">رقم السند</label></div></div>

                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[small_print_size]" id="h_small" value="1" {{ !empty($h['small_print_size']) ? 'checked' : '' }}><label class="form-check-label" for="h_small">حجم الطباعة صغير</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[customer_class]" id="h_ccls" value="1" {{ !empty($h['customer_class']) ? 'checked' : '' }}><label class="form-check-label" for="h_ccls">تصنيف العملاء</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[phone]" id="h_phone" value="1" {{ !empty($h['phone']) ? 'checked' : '' }}><label class="form-check-label" for="h_phone">هاتف</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[manual_voucher]" id="h_mvoucher" value="1" {{ !empty($h['manual_voucher']) ? 'checked' : '' }}><label class="form-check-label" for="h_mvoucher">سند يدوي</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[exchange_rate]" id="h_exrate" value="1" {{ !empty($h['exchange_rate']) ? 'checked' : '' }}><label class="form-check-label" for="h_exrate">سعر الصرف</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[licensed_operator_customers]" id="h_liccust" value="1" {{ !empty($h['licensed_operator_customers']) ? 'checked' : '' }}><label class="form-check-label" for="h_liccust">مشتغل مرخص-العملاء</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="header_fields[account_name]" id="h_accname" value="1" {{ !empty($h['account_name']) ? 'checked' : '' }}><label class="form-check-label" for="h_accname">اسم الحساب</label></div></div>
                </div>
            </div>
        </div>

        <!-- Section 3: Content / محتوى وجدول البنود -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-secondary text-white py-2 d-flex justify-content-between align-items-center">
                <span class="fw-bold fs-6"><i class="bi bi-table me-2"></i>خيارات المحتوى وجدول البنود (Line Items & Columns)</span>
                <div class="form-check form-check-inline text-white mb-0">
                    <input class="form-check-input" type="checkbox" name="apply_content_to_all" id="applyContentToAll" value="1" {{ !empty($template->apply_content_to_all) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-warning" for="applyContentToAll">تطبيق خيارات المحتوى على كل السندات</label>
                </div>
            </div>
            <div class="card-body">
                @php
                    $c = $template->content_fields ?? [];
                @endphp
                <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-2">
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[row_number]" id="c_row" value="1" {{ !empty($c['row_number']) ? 'checked' : '' }}><label class="form-check-label" for="c_row"># (الترقيم)</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[warehouse]" id="c_wh" value="1" {{ !empty($c['warehouse']) ? 'checked' : '' }}><label class="form-check-label" for="c_wh">المخزن</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[bonus]" id="c_bonus" value="1" {{ !empty($c['bonus']) ? 'checked' : '' }}><label class="form-check-label" for="c_bonus">بونس</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[total]" id="c_tot" value="1" {{ !empty($c['total']) ? 'checked' : '' }}><label class="form-check-label" for="c_tot">المجموع</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[main_category]" id="c_mcat" value="1" {{ !empty($c['main_category']) ? 'checked' : '' }}><label class="form-check-label" for="c_mcat">الصنف الرئيسي</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[tax_details_inclusive]" id="c_taxinc" value="1" {{ !empty($c['tax_details_inclusive']) ? 'checked' : '' }}><label class="form-check-label" for="c_taxinc">تفاصيل الضريبة في حال شامل</label></div></div>

                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[main_unit]" id="c_munit" value="1" {{ !empty($c['main_unit']) ? 'checked' : '' }}><label class="form-check-label" for="c_munit">الوحدة الرئيسية</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[categories]" id="c_cats" value="1" {{ !empty($c['categories']) ? 'checked' : '' }}><label class="form-check-label" for="c_cats">التصنيفات</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[avg_price]" id="c_avgp" value="1" {{ !empty($c['avg_price']) ? 'checked' : '' }}><label class="form-check-label" for="c_avgp">معدل السعر</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[factory_no]" id="c_fno" value="1" {{ !empty($c['factory_no']) ? 'checked' : '' }}><label class="form-check-label" for="c_fno">رقم المصنع</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[unit]" id="c_unit" value="1" {{ !empty($c['unit']) ? 'checked' : '' }}><label class="form-check-label" for="c_unit">الوحدة</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[date]" id="c_dt" value="1" {{ !empty($c['date']) ? 'checked' : '' }}><label class="form-check-label" for="c_dt">التاريخ</label></div></div>

                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[total_quantities]" id="c_tqty" value="1" {{ !empty($c['total_quantities']) ? 'checked' : '' }}><label class="form-check-label" for="c_tqty">مجموع الكميات</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[manufacturer_company]" id="c_mfg" value="1" {{ !empty($c['manufacturer_company']) ? 'checked' : '' }}><label class="form-check-label" for="c_mfg">الشركة المنتجة</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[length]" id="c_len" value="1" {{ !empty($c['length']) ? 'checked' : '' }}><label class="form-check-label" for="c_len">طول</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[discount_col]" id="c_dsc" value="1" {{ !empty($c['discount_col']) ? 'checked' : '' }}><label class="form-check-label" for="c_dsc">الخصم* ←</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[show_units_details]" id="c_unitdet" value="1" {{ !empty($c['show_units_details']) ? 'checked' : '' }}><label class="form-check-label" for="c_unitdet">إظهار تفاصيل الوحدات</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[total_quantities_arrow]" id="c_tqtyarr" value="1" {{ !empty($c['total_quantities_arrow']) ? 'checked' : '' }}><label class="form-check-label" for="c_tqtyarr">مجموع الكميات ←</label></div></div>

                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[original_no]" id="c_orig" value="1" {{ !empty($c['original_no']) ? 'checked' : '' }}><label class="form-check-label" for="c_orig">الرقم الأصلي</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[width]" id="c_wth" value="1" {{ !empty($c['width']) ? 'checked' : '' }}><label class="form-check-label" for="c_wth">عرض</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[price]" id="c_prc" value="1" {{ !empty($c['price']) ? 'checked' : '' }}><label class="form-check-label" for="c_prc">السعر</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[invoice_discount]" id="c_invdsc" value="1" {{ !empty($c['invoice_discount']) ? 'checked' : '' }}><label class="form-check-label" for="c_invdsc">الخصم في الفاتورة</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[trade_mark]" id="c_tm" value="1" {{ !empty($c['trade_mark']) ? 'checked' : '' }}><label class="form-check-label" for="c_tm">العلامة التجارية</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[rtl_item_name]" id="c_rtl" value="1" {{ !empty($c['rtl_item_name']) ? 'checked' : '' }}><label class="form-check-label" for="c_rtl">طباعة اسم الصنف من اليسار إلى اليمين</label></div></div>

                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[locations]" id="c_loc" value="1" {{ !empty($c['locations']) ? 'checked' : '' }}><label class="form-check-label" for="c_loc">الأماكن</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[price_before_tax]" id="c_pbtax" value="1" {{ !empty($c['price_before_tax']) ? 'checked' : '' }}><label class="form-check-label" for="c_pbtax">السعر قبل الضريبة</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[barcode]" id="c_barc" value="1" {{ !empty($c['barcode']) ? 'checked' : '' }}><label class="form-check-label" for="c_barc">باركود</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[thickness]" id="c_thk" value="1" {{ !empty($c['thickness']) ? 'checked' : '' }}><label class="form-check-label" for="c_thk">سماكة</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[discount]" id="c_dscval" value="1" {{ !empty($c['discount']) ? 'checked' : '' }}><label class="form-check-label" for="c_dscval">الخصم</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[invoice_discount_pct]" id="c_dscpct" value="1" {{ !empty($c['invoice_discount_pct']) ? 'checked' : '' }}><label class="form-check-label" for="c_dscpct">الخصم في الفاتورة %</label></div></div>

                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[model]" id="c_mdl" value="1" {{ !empty($c['model']) ? 'checked' : '' }}><label class="form-check-label" for="c_mdl">الموديل</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[tax_rate]" id="c_txr" value="1" {{ !empty($c['tax_rate']) ? 'checked' : '' }}><label class="form-check-label" for="c_txr">الضريبة*</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[discount_down_arrow]" id="c_dndsc" value="1" {{ !empty($c['discount_down_arrow']) ? 'checked' : '' }}><label class="form-check-label" for="c_dndsc">الخصم* ↓</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[measures]" id="c_meas" value="1" {{ !empty($c['measures']) ? 'checked' : '' }}><label class="form-check-label" for="c_meas">قياسات</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[item_no]" id="c_ino" value="1" {{ !empty($c['item_no']) ? 'checked' : '' }}><label class="form-check-label" for="c_ino">الرقم</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[count]" id="c_cnt" value="1" {{ !empty($c['count']) ? 'checked' : '' }}><label class="form-check-label" for="c_cnt">عدد</label></div></div>

                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[discount_pct]" id="c_dpct" value="1" {{ !empty($c['discount_pct']) ? 'checked' : '' }}><label class="form-check-label" for="c_dpct">الخصم %</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[tax]" id="c_tx" value="1" {{ !empty($c['tax']) ? 'checked' : '' }}><label class="form-check-label" for="c_tx">الضريبة</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[pattern]" id="c_ptrn" value="1" {{ !empty($c['pattern']) ? 'checked' : '' }}><label class="form-check-label" for="c_ptrn">الطراز</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[total_before_tax]" id="c_totbt" value="1" {{ !empty($c['total_before_tax']) ? 'checked' : '' }}><label class="form-check-label" for="c_totbt">المجموع قبل الضريبة</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[vat_amount]" id="c_vatamt" value="1" {{ !empty($c['vat_amount']) ? 'checked' : '' }}><label class="form-check-label" for="c_vatamt">قيمة الضريبة المضافة*</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[units_count]" id="c_ucnt" value="1" {{ !empty($c['units_count']) ? 'checked' : '' }}><label class="form-check-label" for="c_ucnt">عدد الوحدات</label></div></div>

                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[item_name]" id="c_name" value="1" {{ !empty($c['item_name']) ? 'checked' : '' }}><label class="form-check-label" for="c_name">الاسم</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[quantity]" id="c_qty" value="1" {{ !empty($c['quantity']) ? 'checked' : '' }}><label class="form-check-label" for="c_qty">الكمية</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[statement]" id="c_stmt" value="1" {{ !empty($c['statement']) ? 'checked' : '' }}><label class="form-check-label" for="c_stmt">البيان</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[grand_total]" id="c_gtot" value="1" {{ !empty($c['grand_total']) ? 'checked' : '' }}><label class="form-check-label" for="c_gtot">المجموع الكلي</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[color]" id="c_clr" value="1" {{ !empty($c['color']) ? 'checked' : '' }}><label class="form-check-label" for="c_clr">اللون</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[image]" id="c_img" value="1" {{ !empty($c['image']) ? 'checked' : '' }}><label class="form-check-label" for="c_img">الصورة</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[price_after_discount]" id="c_pad" value="1" {{ !empty($c['price_after_discount']) ? 'checked' : '' }}><label class="form-check-label" for="c_pad">السعر بعد الخصم</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="content_fields[show_barcode_image]" id="c_bimg" value="1" {{ !empty($c['show_barcode_image']) ? 'checked' : '' }}><label class="form-check-label" for="c_bimg">إظهار الباركود الصورة</label></div></div>
                </div>
            </div>
        </div>

        <!-- Section 4: Footer / تذييل -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-secondary text-white py-2 d-flex justify-content-between align-items-center">
                <span class="fw-bold fs-6"><i class="bi bi-layout-text-window me-2"></i>خيارات التذييل (بيانات أسفل الفاتورة والختام)</span>
                <div class="form-check form-check-inline text-white mb-0">
                    <input class="form-check-input" type="checkbox" name="apply_footer_to_all" id="applyFooterToAll" value="1" {{ !empty($template->apply_footer_to_all) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-warning" for="applyFooterToAll">تطبيق خيارات التذييل على كل السندات</label>
                </div>
            </div>
            <div class="card-body">
                @php
                    $f = $template->footer_fields ?? [];
                @endphp
                <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-2">
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[current_balance]" id="f_currbal" value="1" {{ !empty($f['current_balance']) ? 'checked' : '' }}><label class="form-check-label" for="f_currbal">الرصيد الحالي</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[print_serial_with_vouchers]" id="f_serial" value="1" {{ !empty($f['print_serial_with_vouchers']) ? 'checked' : '' }}><label class="form-check-label" for="f_serial">طباعة السيريال مع السندات</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[balance_to_date]" id="f_baltodt" value="1" {{ !empty($f['balance_to_date']) ? 'checked' : '' }}><label class="form-check-label" for="f_baltodt">الرصيد حتى تاريخه</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[qr_tlv]" id="f_qrtlv" value="1" {{ !empty($f['qr_tlv']) ? 'checked' : '' }}><label class="form-check-label text-success fw-bold" for="f_qrtlv">QR-TLV (الضريبة الأردنية / JoFotara)</label></div></div>
                    
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[print_internal_voucher_with_invoice]" id="f_invint" value="1" {{ !empty($f['print_internal_voucher_with_invoice']) ? 'checked' : '' }}><label class="form-check-label" for="f_invint">طباعة السند الداخلي مع الفاتورة</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[spell_out_amount]" id="f_spell" value="1" {{ !empty($f['spell_out_amount']) ? 'checked' : '' }}><label class="form-check-label fw-bold" for="f_spell">كتابة (تفقيط المبلغ بالحروف)</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[custom_footer]" id="f_custfoot" value="1" {{ !empty($f['custom_footer']) ? 'checked' : '' }}><label class="form-check-label" for="f_custfoot">تذييل</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[qr_link]" id="f_qrlink" value="1" {{ !empty($f['qr_link']) ? 'checked' : '' }}><label class="form-check-label" for="f_qrlink">QR-Link</label></div></div>

                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[show_representative]" id="f_rep" value="1" {{ !empty($f['show_representative']) ? 'checked' : '' }}><label class="form-check-label" for="f_rep">إظهار المندوب</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[employee_signature]" id="f_empsig" value="1" {{ !empty($f['employee_signature']) ? 'checked' : '' }}><label class="form-check-label" for="f_empsig">توقيع الموظف</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[align_center]" id="f_center" value="1" {{ !empty($f['align_center']) ? 'checked' : '' }}><label class="form-check-label" for="f_center">( وسط )</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[qr_workspace]" id="f_qrwork" value="1" {{ !empty($f['qr_workspace']) ? 'checked' : '' }}><label class="form-check-label" for="f_qrwork">مكان العمل QR</label></div></div>

                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[cost_center]" id="f_cc" value="1" {{ !empty($f['cost_center']) ? 'checked' : '' }}><label class="form-check-label" for="f_cc">مركز التكلفة</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[receiver_name_signature]" id="f_recsig" value="1" {{ !empty($f['receiver_name_signature']) ? 'checked' : '' }}><label class="form-check-label" for="f_recsig">اسم و توقيع المستلم</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[align_right]" id="f_right" value="1" {{ !empty($f['align_right']) ? 'checked' : '' }}><label class="form-check-label" for="f_right">يمين</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[deal]" id="f_deal" value="1" {{ !empty($f['deal']) ? 'checked' : '' }}><label class="form-check-label" for="f_deal">الصفقة</label></div></div>

                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[internal_voucher_summary]" id="f_intsum" value="1" {{ !empty($f['internal_voucher_summary']) ? 'checked' : '' }}><label class="form-check-label" for="f_intsum">ملخص السند الداخلي</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[align_left]" id="f_left" value="1" {{ !empty($f['align_left']) ? 'checked' : '' }}><label class="form-check-label" for="f_left">( يسار )</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[notes]" id="f_notes" value="1" {{ !empty($f['notes']) ? 'checked' : '' }}><label class="form-check-label" for="f_notes">ملاحظات</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[auto_notes]" id="f_autonotes" value="1" {{ !empty($f['auto_notes']) ? 'checked' : '' }}><label class="form-check-label" for="f_autonotes">ملاحظات آلية</label></div></div>
                    <div class="col"><div class="form-check"><input class="form-check-input" type="checkbox" name="footer_fields[user]" id="f_usr" value="1" {{ !empty($f['user']) ? 'checked' : '' }}><label class="form-check-label" for="f_usr">المستخدم</label></div></div>
                </div>
            </div>
        </div>

        <!-- Section 5: Document Notes & Automated Messaging / ملاحظات السند والرسائل -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-2 border-bottom">
                <span class="fw-bold text-dark fs-6"><i class="bi bi-chat-left-text me-2 text-primary"></i>ملاحظات السند وقوالب المراسلة التلقائية (SMS & Email)</span>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label small text-muted">ملاحظات السند المطبوعة أسفل الفاتورة (شروط البيع، سياسة التبديل والإرجاع 3 أيام، الضمان، شروط التحصيل):</label>
                    <textarea name="notes_html" id="notesHtml" class="form-control" rows="3" placeholder="البضاعة المباعة لا ترد ولا تستبدل بعد 3 أيام / يشترط وجود الفاتورة الأصلية...">{{ $template->notes_html ?? '' }}</textarea>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-light">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="send_sms" id="sendSms" value="1" {{ !empty($template->send_sms) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="sendSms"><i class="bi bi-chat-dots-fill text-success me-1"></i>إرسال رسالة قصيرة SMS تلقائياً للعميل عند الحفظ</label>
                            </div>
                            <div class="input-group mb-2">
                                <select class="form-select form-select-sm" id="smsInsertTag">
                                    <option value="[CUSTOMER_NAME]">اسم العميل</option>
                                    <option value="[INVOICE_NO]">رقم الفاتورة</option>
                                    <option value="[TOTAL_AMOUNT]">المبلغ الإجمالي</option>
                                    <option value="[DUE_DATE]">تاريخ الاستحقاق</option>
                                    <option value="[COMPANY_NAME]">اسم الشركة</option>
                                </select>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="insertTag('smsTemplate', 'smsInsertTag')">إدراج المتغير</button>
                            </div>
                            <textarea name="sms_template" id="smsTemplate" class="form-control" rows="2" placeholder="عزيزي [CUSTOMER_NAME]، تم إصدار الفاتورة رقم [INVOICE_NO] بقيمة [TOTAL_AMOUNT] دينار. شكراً لتعاملكم معنا.">{{ $template->sms_template ?? '' }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-light">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="send_email" id="sendEmail" value="1" {{ !empty($template->send_email) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="sendEmail"><i class="bi bi-envelope-fill text-primary me-1"></i>إرسال إيميل Email تلقائياً للعميل مع الفاتورة المرفقة</label>
                            </div>
                            <div class="input-group mb-2">
                                <select class="form-select form-select-sm" id="emailInsertTag">
                                    <option value="[CUSTOMER_NAME]">اسم العميل</option>
                                    <option value="[INVOICE_NO]">رقم الفاتورة</option>
                                    <option value="[TOTAL_AMOUNT]">المبلغ الإجمالي</option>
                                    <option value="[INVOICE_LINK]">رابط تحميل الفاتورة</option>
                                </select>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="insertTag('emailTemplate', 'emailInsertTag')">إدراج المتغير</button>
                            </div>
                            <textarea name="email_template" id="emailTemplate" class="form-control" rows="2" placeholder="مرحباً [CUSTOMER_NAME]، تجدون طياً تفاصيل الفاتورة رقم [INVOICE_NO] بمبلغ [TOTAL_AMOUNT] دينار.">{{ $template->email_template ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Live Print Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow border-0" style="direction: rtl; text-align: right;">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title fw-bold"><i class="bi bi-printer me-2"></i>معاينة شكل الفاتورة المطبوعة (A4 / Thermal)</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white" id="previewArea">
                <div class="border p-4 rounded" style="font-family: Arial, sans-serif; min-height: 400px; border-color: #cbd5e1 !important;">
                    <div id="modalHeaderPreview" class="text-center pb-3 mb-3 border-bottom"></div>
                    <div class="row mb-3 small" id="modalMetaPreview"></div>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-sm text-center small mb-0" id="modalTablePreview">
                            <thead class="table-light" id="modalTableHead"></thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>منتج تجريبي 1</td>
                                    <td>2</td>
                                    <td>10.000</td>
                                    <td>20.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top" id="modalFooterPreview">
                        <div>
                            <div class="fw-bold">المجموع الكلي: 20.000 د.أ</div>
                            <div class="text-muted small">فقط عشرون ديناراً أردنياً لا غير</div>
                        </div>
                        <div class="text-center p-2 border rounded bg-light" style="width: 80px; height: 80px;">
                            <i class="bi bi-qr-code fs-1 text-dark"></i>
                            <div style="font-size: 8px;">QR-TLV JoFotara</div>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted border-top pt-2" id="modalNotesPreview"></div>
                </div>
            </div>
            <div class="modal-footer py-1">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<script>
function changeDocType(type) {
    window.location.href = "{{ route('erp.settings.print_options') }}?type=" + type;
}

function insertHeaderTag(tag) {
    const el = document.getElementById('headerHtml');
    el.value += ' ' + tag;
}

function insertTag(targetId, selectId) {
    const el = document.getElementById(targetId);
    const sel = document.getElementById(selectId);
    el.value += ' ' + sel.value;
}

function previewPrint() {
    const headerHtml = document.getElementById('headerHtml').value || 'شركة الوثيق لأنظمة المحاسبة الذكية - المملكة الأردنية الهاشمية';
    const notesHtml = document.getElementById('notesHtml').value || 'البضاعة المباعة لا ترد ولا تستبدل بعد مرور 3 أيام من تاريخ الفاتورة.';
    
    document.getElementById('modalHeaderPreview').innerHTML = '<h5>' + headerHtml + '</h5>';
    document.getElementById('modalNotesPreview').innerText = notesHtml;
    
    // Build Table Headers dynamically based on checked content fields
    let theadHtml = '<tr>';
    theadHtml += '<th>#</th>';
    if (document.getElementById('c_name') && document.getElementById('c_name').checked) theadHtml += '<th>اسم الصنف</th>';
    if (document.getElementById('c_qty') && document.getElementById('c_qty').checked) theadHtml += '<th>الكمية</th>';
    if (document.getElementById('c_prc') && document.getElementById('c_prc').checked) theadHtml += '<th>السعر</th>';
    if (document.getElementById('c_tot') && document.getElementById('c_tot').checked) theadHtml += '<th>المجموع</th>';
    theadHtml += '</tr>';
    document.getElementById('modalTableHead').innerHTML = theadHtml;

    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}
</script>
@endsection
