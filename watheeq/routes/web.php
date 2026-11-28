<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ErpController;
use App\Http\Controllers\AuthController;

// Authentication & Initial Setup Wizard
Route::get('/setup', [AuthController::class, 'showSetup'])->name('setup');
Route::post('/setup', [AuthController::class, 'saveSetup'])->name('setup.save');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Page 0: Dashboard
Route::get('/', [ErpController::class, 'dashboard'])->name('erp.dashboard');

// Definitions Hub & Sub-Screens
Route::get('/definitions', [ErpController::class, 'definitions'])->name('erp.definitions');
Route::get('/currencies', [ErpController::class, 'currencies'])->name('erp.currencies');
Route::post('/currencies', [ErpController::class, 'storeCurrency'])->name('erp.currencies.store');
Route::get('/price-lists', [ErpController::class, 'priceLists'])->name('erp.price_lists');
Route::post('/price-lists', [ErpController::class, 'updatePriceLists'])->name('erp.price_lists.update');
Route::get('/inventory/price-adjustment', [ErpController::class, 'priceAdjustmentEditor'])->name('erp.price_adjustment.editor');
Route::post('/inventory/price-adjustment', [ErpController::class, 'storePriceAdjustment'])->name('erp.price_adjustment.store');

// Page 2: Invoicing Hub & Operations
Route::get('/invoicing-hub', [ErpController::class, 'invoicingHub'])->name('erp.invoicing.hub');
Route::get('/invoices', [ErpController::class, 'invoices'])->name('erp.invoices');
Route::post('/invoices', [ErpController::class, 'storeInvoice'])->name('erp.invoices.store');
Route::get('/invoices/create-editor', [ErpController::class, 'invoiceEditor'])->name('erp.invoices.editor');
Route::get('/invoices/{id}/print', [ErpController::class, 'printInvoice'])->name('erp.invoices.print');

// Vouchers & Checks
Route::get('/vouchers', [ErpController::class, 'vouchers'])->name('erp.vouchers');
Route::post('/vouchers', [ErpController::class, 'storeVoucher'])->name('erp.vouchers.store');
Route::get('/vouchers/create-editor', [ErpController::class, 'voucherEditor'])->name('erp.vouchers.editor');
Route::get('/vouchers/payment-editor', [ErpController::class, 'paymentEditor'])->name('erp.vouchers.payment_editor');
Route::post('/vouchers/payment-editor', [ErpController::class, 'storePaymentEditor'])->name('erp.vouchers.payment_editor.store');

// Journals
Route::get('/accounts', [ErpController::class, 'accounts'])->name('erp.accounts');
Route::post('/accounts', [ErpController::class, 'storeAccount'])->name('erp.accounts.store');
Route::get('/journals/create-editor', [ErpController::class, 'journalEditor'])->name('erp.journals.editor');
Route::post('/journals', [ErpController::class, 'storeJournal'])->name('erp.journals.store');

// Stock Movements, Count & Transfers
Route::get('/products', [ErpController::class, 'products'])->name('erp.products');
Route::post('/products', [ErpController::class, 'storeProduct'])->name('erp.products.store');
Route::get('/products/create-editor', [ErpController::class, 'productEditor'])->name('erp.products.editor');
Route::get('/stock-count', [ErpController::class, 'stockCount'])->name('erp.stock_count');
Route::post('/stock-count', [ErpController::class, 'reconcileStockCount'])->name('erp.stock_count.reconcile');
Route::get('/dispatches', [ErpController::class, 'dispatches'])->name('erp.dispatches');
Route::post('/dispatches', [ErpController::class, 'storeDispatch'])->name('erp.dispatches.store');
Route::get('/transfers', [ErpController::class, 'stockTransfer'])->name('erp.transfers');
Route::post('/transfers', [ErpController::class, 'storeStockTransfer'])->name('erp.transfers.store');
Route::get('/manufacturing', [ErpController::class, 'manufacturingVoucher'])->name('erp.manufacturing');
Route::post('/manufacturing', [ErpController::class, 'storeManufacturingVoucher'])->name('erp.manufacturing.store');

// Contacts & Statement
Route::get('/contacts', [ErpController::class, 'contacts'])->name('erp.contacts');
Route::post('/contacts', [ErpController::class, 'storeContact'])->name('erp.contacts.store');
Route::get('/contacts/create-editor', [ErpController::class, 'contactEditor'])->name('erp.contacts.editor');
Route::get('/contacts/{id}/statement', [ErpController::class, 'customerStatement'])->name('erp.contacts.statement');

// Cheques Portfolio
Route::get('/cheques', [ErpController::class, 'cheques'])->name('erp.cheques');
Route::post('/cheques', [ErpController::class, 'storeCheque'])->name('erp.cheques.store');

// Expenses
Route::get('/expenses', [ErpController::class, 'expenses'])->name('erp.expenses');
Route::post('/expenses', [ErpController::class, 'storeExpense'])->name('erp.expenses.store');

// Fixed Assets
Route::get('/fixed-assets', [ErpController::class, 'fixedAssets'])->name('erp.fixed_assets');
Route::post('/fixed-assets', [ErpController::class, 'storeFixedAsset'])->name('erp.fixed_assets.store');

// Page 3: Hesabate HR & Sub-Screens
Route::get('/hr', [ErpController::class, 'hr'])->name('erp.hr');
Route::post('/hr/employees', [ErpController::class, 'storeEmployee'])->name('erp.hr.store');
Route::get('/hr/employee-editor/{id?}', [ErpController::class, 'employeeEditor'])->name('erp.hr.employee_editor');
Route::post('/hr/employee-editor', [ErpController::class, 'storeEmployeeEditor'])->name('erp.hr.employee_editor.store');
Route::get('/hr/vouchers/{type?}', [ErpController::class, 'employeeVoucher'])->name('erp.hr.employee_voucher');
Route::post('/hr/vouchers', [ErpController::class, 'storeEmployeeVoucher'])->name('erp.hr.employee_voucher.store');
Route::get('/hr/settings', [ErpController::class, 'hrSettings'])->name('erp.hr.settings');
Route::post('/hr/settings', [ErpController::class, 'storeHrSettings'])->name('erp.hr.settings.store');
Route::get('/hr/termination', [ErpController::class, 'employeeTermination'])->name('erp.hr.termination');
Route::post('/hr/termination', [ErpController::class, 'storeEmployeeTermination'])->name('erp.hr.termination.store');
Route::get('/hr/salary-editor', [ErpController::class, 'salaryEditor'])->name('erp.hr.salary_editor');
Route::post('/hr/salary-editor', [ErpController::class, 'storeSalaryEditor'])->name('erp.hr.salary_editor.store');
Route::get('/hr/tax-exempt', [ErpController::class, 'taxExemptEditor'])->name('erp.hr.tax_exempt');
Route::post('/hr/tax-exempt', [ErpController::class, 'storeTaxExempt'])->name('erp.hr.tax_exempt.store');

// Page 4: Hotels & Sub-Screens
Route::get('/hotels', [ErpController::class, 'hotels'])->name('erp.hotels');
Route::post('/hotels/bookings', [ErpController::class, 'storeBooking'])->name('erp.hotels.store');
Route::get('/hotels/guests', [ErpController::class, 'hotelGuests'])->name('erp.hotels.guests');
Route::post('/hotels/guests', [ErpController::class, 'storeHotelGuest'])->name('erp.hotels.guests.store');

// Page 5: POS & Sub-Screens
Route::get('/pos', [ErpController::class, 'pos'])->name('erp.pos');
Route::get('/pos/hub', [ErpController::class, 'posHub'])->name('erp.pos.hub');
Route::get('/pos/campaigns', [ErpController::class, 'posCampaigns'])->name('erp.pos.campaigns');
Route::post('/pos/campaigns', [ErpController::class, 'storePosCampaign'])->name('erp.pos.campaigns.store');
Route::get('/pos/tables', [ErpController::class, 'posTables'])->name('erp.pos.tables');
Route::post('/pos/tables', [ErpController::class, 'storePosTable'])->name('erp.pos.tables.store');
Route::get('/pos/shifts', [ErpController::class, 'posShifts'])->name('erp.pos.shifts');
Route::post('/pos/shifts/close', [ErpController::class, 'closePosShift'])->name('erp.pos.shifts.close');
Route::get('/pos/options', [ErpController::class, 'posOptions'])->name('erp.pos.options');
Route::post('/pos/options', [ErpController::class, 'storePosOptions'])->name('erp.pos.options.store');
Route::get('/pos/reports', [ErpController::class, 'posReports'])->name('erp.pos.reports');
Route::get('/admin/permissions', [ErpController::class, 'adminPermissions'])->name('erp.admin.permissions');
Route::post('/admin/permissions', [ErpController::class, 'storeAdminPermissions'])->name('erp.admin.permissions.store');

// Page 6: Reports
Route::get('/reports', [ErpController::class, 'reports'])->name('erp.reports');
Route::get('/reports/customer-statement', [ErpController::class, 'reportCustomerStatement'])->name('erp.reports.customer_statement');
Route::get('/reports/stock-ledger', [ErpController::class, 'reportStockLedger'])->name('erp.reports.stock_ledger');
Route::get('/reports/cheques', [ErpController::class, 'reportCheques'])->name('erp.reports.cheques');
Route::get('/reports/export-excel', [ErpController::class, 'exportExcel'])->name('erp.reports.excel');

// Utilities: Notepad, Phonebook, Customizer
Route::get('/phonebook', [ErpController::class, 'phonebook'])->name('erp.phonebook');
Route::post('/phonebook', [ErpController::class, 'storePhonebook'])->name('erp.phonebook.store');
Route::get('/notepad', [ErpController::class, 'notepad'])->name('erp.notepad');
Route::post('/notepad', [ErpController::class, 'storeNotepad'])->name('erp.notepad.store');
Route::post('/notepad/{id}/toggle', [ErpController::class, 'toggleNotepad'])->name('erp.notepad.toggle');
Route::get('/customizer', [ErpController::class, 'admin'])->name('erp.customizer');
Route::post('/customizer', [ErpController::class, 'updateCustomizer'])->name('erp.customizer.update');
Route::get('/stream', [ErpController::class, 'stream'])->name('erp.stream');

// Jordan National E-Invoicing System (JoFotara / ISTD)
Route::get('/jofotara', [ErpController::class, 'jofotaraHub'])->name('erp.jofotara.hub');
Route::post('/jofotara/settings', [ErpController::class, 'updateJoFotaraSettings'])->name('erp.jofotara.settings.update');
Route::post('/invoices/{id}/jofotara-submit', [ErpController::class, 'submitInvoiceToJoFotara'])->name('erp.invoices.jofotara.submit');
Route::post('/jofotara/batch-submit', [ErpController::class, 'batchSubmitJoFotara'])->name('erp.jofotara.batch.submit');
Route::get('/invoices/{id}/ubl-xml', [ErpController::class, 'previewInvoiceUblXml'])->name('erp.invoices.ubl.xml');

// Tax Center & ISTD Sales Tax Declarations Hub
Route::get('/taxes', [ErpController::class, 'taxCenter'])->name('erp.taxes');
Route::post('/taxes/settings', [ErpController::class, 'updateTaxSettings'])->name('erp.taxes.settings');

// Watheeq Admin Panel & User Interface Customization Engine
Route::get('/admin', [ErpController::class, 'admin'])->name('erp.admin');
Route::get('/users/switch/{id}', [ErpController::class, 'switchUser'])->name('erp.users.switch');
Route::post('/admin/users', [ErpController::class, 'storeUser'])->name('erp.admin.users.store');
Route::post('/admin/users/{id}', [ErpController::class, 'updateUser'])->name('erp.admin.users.update');
Route::delete('/admin/users/{id}', [ErpController::class, 'deleteUser'])->name('erp.admin.users.delete');

// Seamless Hardware Barcode & QR Code Real-Time Lookup Engine
Route::get('/api/products/lookup', [ErpController::class, 'lookupProductByBarcode'])->name('erp.products.lookup');

// Video 7: Print Options Customizer (14_6i.php), System Preferences (14_3i.php), and Installments (6_5_1i.php)
Route::get('/settings/print-options', [ErpController::class, 'printOptions'])->name('erp.settings.print_options');
Route::post('/settings/print-options', [ErpController::class, 'savePrintOptions'])->name('erp.settings.print_options.save');
Route::get('/settings/preferences', [ErpController::class, 'preferences'])->name('erp.settings.preferences');
Route::post('/settings/preferences', [ErpController::class, 'savePreferences'])->name('erp.settings.preferences.save');
Route::get('/vouchers/installments', [ErpController::class, 'installmentVouchers'])->name('erp.vouchers.installments');
Route::get('/vouchers/installments/create', [ErpController::class, 'createInstallmentVoucher'])->name('erp.vouchers.installments.create');
Route::post('/vouchers/installments', [ErpController::class, 'storeInstallmentVoucher'])->name('erp.vouchers.installments.store');

// Enterprise & Government-Grade Extensions (PDF Specification Baseline)
Route::get('/projects', [ErpController::class, 'projects'])->name('erp.projects');
Route::post('/projects', [ErpController::class, 'storeProject'])->name('erp.projects.store');
Route::get('/procurement/matching', [ErpController::class, 'threeWayMatching'])->name('erp.procurement.matching');
Route::post('/procurement/po', [ErpController::class, 'storePo'])->name('erp.procurement.po.store');
Route::post('/procurement/grn', [ErpController::class, 'storeGrn'])->name('erp.procurement.grn.store');
Route::get('/assets', [ErpController::class, 'fixedAssetsHub'])->name('erp.assets.hub');
Route::post('/assets/depreciate', [ErpController::class, 'runAssetDepreciation'])->name('erp.assets.depreciate.run');
// Route::get('/hr/biometric', [ErpController::class, 'biometricAttendance'])->name('erp.hr.biometric');
// Route::post('/hr/biometric/import', [ErpController::class, 'importBiometricPunch'])->name('erp.hr.biometric.import');
Route::get('/ai/assistant', [ErpController::class, 'aiAssistant'])->name('erp.ai.assistant');


