<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class WatheeqEndToEndFullSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $cashierUser;
    protected $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed base database (tenants, accounts, products, rooms)
        $this->seed(DatabaseSeeder::class);

        // Fake external ISTD / JoFotara API calls to prevent network timeouts during testing
        Http::fake([
            'https://*jofotara.gov.jo/*' => Http::response([
                'EINV_STATUS' => 'SUBMITTED',
                'EINV_INV_UUID' => 'test-jofotara-uuid-9988',
                'EINV_QR' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='
            ], 200)
        ]);

        $this->tenant = DB::table('tenants')->first();

        // Seed default Admin & Cashier users for testing
        $adminId = DB::table('users')->insertGetId([
            'name' => 'المدير العام (Super Admin)',
            'username' => 'admin',
            'email' => 'admin@watheeq.erp',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'role_name_ar' => 'مدير النظام',
            'enabled_modules' => json_encode(['dashboard', 'definitions', 'invoices', 'vouchers', 'hr', 'hotels', 'pos', 'reports', 'jofotara', 'taxes', 'admin']),
            'enabled_tools' => json_encode(['jofotara', 'taxes', 'sales_invoice', 'purchase_invoice', 'receipt_voucher', 'payment_voucher', 'journal_voucher', 'price_adjustment', 'employee_salary', 'guest_booking', 'cheque_portfolio', 'dispatches', 'phonebook', 'notepad', 'pos', 'reports']),
            'is_active' => true,
            'avatar_color' => '#1F2E4A',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $cashierId = DB::table('users')->insertGetId([
            'name' => 'كاشير الفرع الرئيسي',
            'username' => 'cashier',
            'email' => 'cashier@watheeq.erp',
            'password' => Hash::make('cashier123'),
            'role' => 'cashier',
            'role_name_ar' => 'أمين الصندوق (كاشير)',
            'enabled_modules' => json_encode(['pos', 'invoices', 'vouchers']),
            'enabled_tools' => json_encode(['pos', 'sales_invoice', 'receipt_voucher']),
            'is_active' => true,
            'avatar_color' => '#2B4C7E',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->adminUser = DB::table('users')->where('id', $adminId)->first();
        $this->cashierUser = DB::table('users')->where('id', $cashierId)->first();
    }

    /** Helper to authenticate as Admin */
    protected function asAdmin()
    {
        return $this->withSession(['active_user_id' => $this->adminUser->id]);
    }

    /** Helper to authenticate as Cashier */
    protected function asCashier()
    {
        return $this->withSession(['active_user_id' => $this->cashierUser->id]);
    }

    /* ══════════════════════════════════════════════════════════════════
       1. AUTHENTICATION & ROLE-BASED ACCESS CONTROL (RBAC)
       ══════════════════════════════════════════════════════════════════ */

    public function test_unauthenticated_requests_redirect_to_login()
    {
        $response = $this->get('/invoices');
        $response->assertRedirect('/login');
    }

    public function test_setup_wizard_redirects_to_login_if_already_initialized()
    {
        $response = $this->get('/setup');
        $response->assertRedirect('/login');
    }

    public function test_login_fails_with_invalid_credentials()
    {
        $response = $this->post('/login', [
            'login' => 'admin',
            'password' => 'wrongpassword'
        ]);
        $response->assertSessionHas('error');
    }

    public function test_login_succeeds_with_valid_credentials()
    {
        $response = $this->post('/login', [
            'login' => 'admin',
            'password' => 'admin123'
        ]);
        $response->assertRedirect('/');
        $response->assertSessionHas('active_user_id', $this->adminUser->id);
    }

    public function test_non_admin_is_forbidden_from_admin_and_customizer()
    {
        $response = $this->asCashier()->get('/admin');
        $response->assertRedirect('/');
        $response->assertSessionHas('error');

        $responseCustomizer = $this->asCashier()->get('/customizer');
        $responseCustomizer->assertRedirect('/');
    }

    public function test_admin_can_access_admin_panel()
    {
        $response = $this->asAdmin()->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('لوحة تحكم المسؤول');
    }

    public function test_user_switch_and_logout_flow()
    {
        $response = $this->asAdmin()->get('/users/switch/' . $this->cashierUser->id);
        $response->assertRedirect('/');
        $this->assertEquals($this->cashierUser->id, session('active_user_id'));

        $logoutResponse = $this->asCashier()->post('/logout');
        $logoutResponse->assertRedirect('/login');
        $this->assertNull(session('active_user_id'));
    }

    /* ══════════════════════════════════════════════════════════════════
       2. CORPORATE DASHBOARD & LIVE KPIS
       ══════════════════════════════════════════════════════════════════ */

    public function test_dashboard_renders_live_metrics_and_navigation()
    {
        $response = $this->asAdmin()->get('/');
        $response->assertStatus(200);
        $response->assertSee('وثيق | Watheeq');
        $response->assertSee('المؤشر المالي العام');
        $response->assertSee('السيولة النقدية والبنكية');
    }

    /* ══════════════════════════════════════════════════════════════════
       3. PRODUCTS, INVENTORY & HARDWARE BARCODE LOOKUP
       ══════════════════════════════════════════════════════════════════ */

    public function test_products_catalog_loads_and_creates_new_product()
    {
        $response = $this->asAdmin()->get('/products');
        $response->assertStatus(200);

        $productData = [
            'code' => 'TEST-SKU-99',
            'barcode' => '6281000998877',
            'name' => 'طابعة إيصالات حرارية 80mm إبسون',
            'category' => 'أجهزة ونقاط بيع',
            'warehouse' => 'المستودع الرئيسي',
            'cost_price' => 75.00,
            'sale_price' => 110.00,
            'quantity' => 15,
            'min_quantity' => 3,
            'unit' => 'جهاز'
        ];

        $postResponse = $this->asAdmin()->post('/products', $productData);
        $postResponse->assertRedirect('/products');

        $this->assertDatabaseHas('products', [
            'barcode' => '6281000998877',
            'name' => 'طابعة إيصالات حرارية 80mm إبسون'
        ]);
    }

    public function test_product_barcode_scanner_api_lookup()
    {
        $product = DB::table('products')->first();
        $this->assertNotNull($product);

        $barcode = $product->barcode ?: $product->code;
        $response = $this->get('/api/products/lookup?q=' . urlencode($barcode));
        $response->assertStatus(200);
        $response->assertJson(['found' => true]);
    }

    public function test_physical_stock_counting_and_reconciliation()
    {
        $response = $this->asAdmin()->get('/stock-count');
        $response->assertStatus(200);

        $product = DB::table('products')->first();

        $reconcileData = [
            'counted_qty' => [
                $product->id => 50
            ],
            'notes' => 'جرد فعلي للمستودع الرئيسي لنهاية الدورة'
        ];

        $postResponse = $this->asAdmin()->post('/stock-count', $reconcileData);
        $postResponse->assertRedirect('/stock-count');
    }

    public function test_dispatches_and_transfers_scenario()
    {
        $response = $this->asAdmin()->get('/dispatches');
        $response->assertStatus(200);

        $dispatchData = [
            'type' => 'delivery_out',
            'party_name' => 'شركة الأفق للاستشارات',
            'date' => date('Y-m-d'),
            'source_warehouse' => 'المستودع الرئيسي - عمان',
            'dest_warehouse' => 'فرع العقبة',
            'total_qty' => 10,
            'driver_name' => 'خالد السائق',
            'vehicle_no' => '22-8877',
            'notes' => 'إرسالية تجريبية'
        ];

        $postResponse = $this->asAdmin()->post('/dispatches', $dispatchData);
        $postResponse->assertRedirect('/dispatches');

        $this->assertDatabaseHas('dispatches', [
            'party_name' => 'شركة الأفق للاستشارات',
            'driver_name' => 'خالد السائق'
        ]);
    }

    /* ══════════════════════════════════════════════════════════════════
       4. CONTACTS, CLIENTS, SUPPLIERS & STATEMENTS
       ══════════════════════════════════════════════════════════════════ */

    public function test_contacts_directory_and_customer_creation()
    {
        $response = $this->asAdmin()->get('/contacts');
        $response->assertStatus(200);

        $contactData = [
            'name' => 'شركة الأفق للاستشارات الهندسية',
            'type' => 'customer',
            'phone' => '+962 7 9000 1111',
            'tax_id' => '200192837',
            'credit_limit' => 5000.00,
            'balance' => 0.00,
            'address' => 'عمان - شارع وصفي التل'
        ];

        $postResponse = $this->asAdmin()->post('/contacts', $contactData);
        $postResponse->assertRedirect('/contacts');

        $this->assertDatabaseHas('contacts', [
            'name' => 'شركة الأفق للاستشارات الهندسية'
        ]);
    }

    public function test_customer_account_statement_renders_cleanly()
    {
        $contact = DB::table('contacts')->first();
        $this->assertNotNull($contact);

        $response = $this->asAdmin()->get('/contacts/' . $contact->id . '/statement');
        $response->assertStatus(200);
        $response->assertSee('كشف حساب مفصل لحركات الذمم');
    }

    /* ══════════════════════════════════════════════════════════════════
       5. INVOICES, SALES/PURCHASES & JOFOTARA E-INVOICING
       ══════════════════════════════════════════════════════════════════ */

    public function test_invoices_ledger_and_creation_scenario()
    {
        $response = $this->asAdmin()->get('/invoices');
        $response->assertStatus(200);

        $contact = DB::table('contacts')->first();
        $product = DB::table('products')->first();

        $invoiceData = [
            'contact_id' => $contact->id,
            'type' => 'sale',
            'payment_method' => 'cash',
            'subtotal' => 200.00,
            'tax_total' => 32.00,
            'grand_total' => 232.00,
            'items' => [
                [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'unit' => 'قطعة',
                    'qty' => 2,
                    'price' => 100.00
                ]
            ],
            'notes' => 'فاتورة ضريبية رسمية 16% مؤتمتة'
        ];

        $postResponse = $this->asAdmin()->post('/invoices', $invoiceData);
        $postResponse->assertRedirect('/invoices');

        $this->assertDatabaseHas('invoices', [
            'notes' => 'فاتورة ضريبية رسمية 16% مؤتمتة',
            'grand_total' => 232.00
        ]);
    }

    public function test_invoice_print_and_ubl_xml_preview()
    {
        $invoice = DB::table('invoices')->first();
        $this->assertNotNull($invoice);

        $printResponse = $this->asAdmin()->get('/invoices/' . $invoice->id . '/print');
        $printResponse->assertStatus(200);
        $printResponse->assertSee($invoice->invoice_no);

        $xmlResponse = $this->asAdmin()->get('/invoices/' . $invoice->id . '/ubl-xml');
        $xmlResponse->assertStatus(200);
        $xmlResponse->assertSee('UBL');
    }

    public function test_jofotara_hub_and_settings()
    {
        $response = $this->asAdmin()->get('/jofotara');
        $response->assertStatus(200);
        $response->assertSee('نظام الفوترة الوطني الأردني (JoFotara)');

        $settingsData = [
            'jofotara_client_id' => 'CLIENT_TEST_KEY',
            'jofotara_secret_key' => 'SECRET_TEST_KEY',
            'jofotara_activity_no' => 'ACT-9988',
            'jofotara_environment' => 'sandbox'
        ];

        $postSettings = $this->asAdmin()->post('/jofotara/settings', $settingsData);
        $postSettings->assertRedirect('/jofotara');

        $batchResponse = $this->asAdmin()->post('/jofotara/batch-submit');
        $batchResponse->assertRedirect('/jofotara');
    }

    /* ══════════════════════════════════════════════════════════════════
       6. POS (POINT OF SALE) & CASHIER WORKFLOWS
       ══════════════════════════════════════════════════════════════════ */

    public function test_pos_terminal_and_shifts()
    {
        $response = $this->asCashier()->get('/pos');
        $response->assertStatus(200);
        $response->assertSee('شاشة الكاشير السريعة الاحترافية');

        $shiftsResponse = $this->asAdmin()->get('/pos/shifts');
        $shiftsResponse->assertStatus(200);

        $closeShiftData = [
            'actual_cash' => 250.00,
            'closing_notes' => 'تسليم وردية الصندوق المسائي'
        ];

        $postClose = $this->asAdmin()->post('/pos/shifts/close', $closeShiftData);
        $postClose->assertRedirect('/pos/shifts');
    }

    /* ══════════════════════════════════════════════════════════════════
       7. FINANCIAL VOUCHERS, CHEQUES & EXPENSES
       ══════════════════════════════════════════════════════════════════ */

    public function test_financial_vouchers_and_journals()
    {
        $response = $this->asAdmin()->get('/vouchers');
        $response->assertStatus(200);

        $contact = DB::table('contacts')->first();

        // 1. Receipt voucher
        $voucherData = [
            'type' => 'receipt',
            'contact_name' => $contact->name,
            'amount' => 150.00,
            'payment_type' => 'cash',
            'statement' => 'دفعة نقدية تحت الحساب'
        ];

        $postVoucher = $this->asAdmin()->post('/vouchers', $voucherData);
        $postVoucher->assertRedirect('/vouchers');

        $this->assertDatabaseHas('vouchers', [
            'contact_name' => $contact->name,
            'amount' => 150.00
        ]);

        // 2. Journal Entry
        $accCash = DB::table('accounts')->where('code', '1101')->first() ?? DB::table('accounts')->first();
        $accSales = DB::table('accounts')->where('code', '4101')->first() ?? DB::table('accounts')->skip(1)->first();

        $journalData = [
            'entry_no' => 'JRN-TEST-01',
            'date' => date('Y-m-d'),
            'narration' => 'قيد تسوية واعتراف إيرادات دورية',
            'lines' => [
                [
                    'account_id' => $accCash->id,
                    'debit' => 300.00,
                    'credit' => 0.00,
                    'line_desc' => 'حساب الصندوق الرئيسي'
                ],
                [
                    'account_id' => $accSales->id,
                    'debit' => 0.00,
                    'credit' => 300.00,
                    'line_desc' => 'حساب إيراد المبيعات'
                ]
            ]
        ];

        $postJournal = $this->asAdmin()->post('/journals', $journalData);
        $postJournal->assertRedirect('/accounts');
    }

    public function test_cheques_and_expenses_portfolio()
    {
        $responseCheques = $this->asAdmin()->get('/cheques');
        $responseCheques->assertStatus(200);

        $chequeData = [
            'cheque_no' => 'CHQ-889900',
            'type' => 'received',
            'bank_name' => 'البنك العربي - فرع مكة',
            'drawer_name' => 'شركة الأفق الهندسية',
            'beneficiary' => 'المؤسسة الوطنية للتجارة',
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'amount' => 1250.00,
            'status' => 'under_collection',
            'notes' => 'شيك تحت التحصيل'
        ];

        $postCheque = $this->asAdmin()->post('/cheques', $chequeData);
        $postCheque->assertRedirect('/cheques');

        $this->assertDatabaseHas('cheques', [
            'cheque_no' => 'CHQ-889900',
            'drawer_name' => 'شركة الأفق الهندسية'
        ]);

        // Expenses
        $responseExp = $this->asAdmin()->get('/expenses');
        $responseExp->assertStatus(200);

        $expenseData = [
            'expense_no' => 'EXP-TEST-01',
            'title' => 'فاتورة الكهرباء والإنترنت للمقر',
            'category' => 'مصاريف تشغيلية',
            'amount' => 180.00,
            'date' => date('Y-m-d'),
            'payment_method' => 'cash',
            'notes' => 'سداد نقدي من الصندوق'
        ];

        $postExp = $this->asAdmin()->post('/expenses', $expenseData);
        $postExp->assertRedirect('/expenses');
    }

    /* ══════════════════════════════════════════════════════════════════
       8. HR, PAYROLL & SOCIAL SECURITY
       ══════════════════════════════════════════════════════════════════ */

    public function test_hr_management_and_employees()
    {
        $response = $this->asAdmin()->get('/hr');
        $response->assertStatus(200);

        $empData = [
            'name' => 'م. أحمد الشواقفة',
            'job_title' => 'مدير مالي ومحاسب قانوني',
            'department' => 'الإدارة المالية',
            'national_id' => '9901029384',
            'phone' => '+962 7 8888 2222',
            'base_salary' => 1100.00,
            'social_security_opt' => true
        ];

        $postEmp = $this->asAdmin()->post('/hr/employees', $empData);
        $postEmp->assertRedirect('/hr');

        $this->assertDatabaseHas('employees', [
            'national_id' => '9901029384'
        ]);
    }

    /* ══════════════════════════════════════════════════════════════════
       9. HOSPITALITY & HOTEL PROPERTY MANAGEMENT
       ══════════════════════════════════════════════════════════════════ */

    public function test_hotels_room_rack_and_bookings()
    {
        $response = $this->asAdmin()->get('/hotels');
        $response->assertStatus(200);

        $room = DB::table('hotel_rooms')->first();
        $this->assertNotNull($room);

        $bookingData = [
            'room_id' => $room->id,
            'guest_name' => 'د. سمير الخطيب',
            'guest_phone' => '+962 7 9123 4567',
            'check_in' => date('Y-m-d'),
            'check_out' => date('Y-m-d', strtotime('+3 days')),
            'nights' => 3,
            'total_amount' => 240.00,
            'paid_amount' => 240.00
        ];

        $postBooking = $this->asAdmin()->post('/hotels/bookings', $bookingData);
        $postBooking->assertRedirect('/hotels');

        $this->assertDatabaseHas('hotel_bookings', [
            'guest_name' => 'د. سمير الخطيب'
        ]);
    }

    /* ══════════════════════════════════════════════════════════════════
       10. FINANCIAL REPORTS & ISTD TAX DECLARATION HUB
       ══════════════════════════════════════════════════════════════════ */

    public function test_reports_and_excel_export()
    {
        $response = $this->asAdmin()->get('/reports');
        $response->assertStatus(200);
        $response->assertSee('التقارير المحاسبية، كشوفات الحسابات والقوائم الختامية');

        $excelResponse = $this->asAdmin()->get('/reports/export-excel');
        $excelResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $excelResponse->headers->get('Content-Type') ?? '');
    }

    public function test_tax_center_and_istd_declaration_form()
    {
        $response = $this->asAdmin()->get('/taxes');
        $response->assertStatus(200);
        $response->assertSee('دائرة ضريبة الدخل والمبيعات (ISTD)');
        $response->assertSee('كشف الإقرار الضريبي الدوري للضريبة العامة على المبيعات');

        $taxSettings = [
            'tax_number' => '1004829103',
            'commercial_register' => '29381-CR',
            'tax_rate' => 16.00,
            'tax_period' => 'شهري (Monthly)'
        ];

        $postTax = $this->asAdmin()->post('/taxes/settings', $taxSettings);
        $postTax->assertRedirect('/taxes');

        $this->assertDatabaseHas('tenants', [
            'tax_number' => '1004829103',
            'tax_rate' => 16.00
        ]);
    }

    /* ══════════════════════════════════════════════════════════════════
       11. ADMIN USER MANAGEMENT & INTERFACE CUSTOMIZATION
       ══════════════════════════════════════════════════════════════════ */

    public function test_admin_creates_and_manages_custom_user_accounts()
    {
        $newUserData = [
            'name' => 'مستودعي الفرع الشمالي',
            'username' => 'storekeeper',
            'email' => 'store@watheeq.erp',
            'password' => 'pass1234',
            'role' => 'accountant',
            'role_name_ar' => 'أمين مستودع',
            'modules' => ['products', 'stock_count', 'dispatches'],
            'tools' => ['dispatches', 'stock_count']
        ];

        $postUser = $this->asAdmin()->post('/admin/users', $newUserData);
        $postUser->assertRedirect('/admin');

        $createdUser = DB::table('users')->where('username', 'storekeeper')->first();
        $this->assertNotNull($createdUser);
        $this->assertEquals('أمين مستودع', $createdUser->role_name_ar);

        // Update user
        $updateData = [
            'name' => 'أمين مستودعات رئيسي',
            'role_name_ar' => 'مدير المستودعات',
            'modules' => ['products', 'stock_count', 'dispatches', 'invoices'],
            'tools' => ['dispatches']
        ];

        $updateResponse = $this->asAdmin()->post('/admin/users/' . $createdUser->id, $updateData);
        $updateResponse->assertRedirect('/admin');

        $this->assertDatabaseHas('users', [
            'id' => $createdUser->id,
            'role_name_ar' => 'مدير المستودعات'
        ]);

        // Delete user
        $deleteResponse = $this->asAdmin()->delete('/admin/users/' . $createdUser->id);
        $deleteResponse->assertRedirect('/admin');

        $this->assertDatabaseMissing('users', [
            'id' => $createdUser->id
        ]);
    }

    /* ══════════════════════════════════════════════════════════════════
       12. PRODUCTIVITY SMART TOOLS (NOTEPAD & PHONEBOOK)
       ══════════════════════════════════════════════════════════════════ */

    public function test_notepad_and_phonebook_smart_tools()
    {
        $responseNote = $this->asAdmin()->get('/notepad');
        $responseNote->assertStatus(200);

        $noteData = [
            'title' => 'مراجعة تسوية ضريبة المبيعات لشهر تشرين الثاني',
            'priority' => 'high',
            'due_date' => date('Y-m-d'),
            'content' => 'مراجعة الحسابات والفواتير الصادرة قبل نهاية الشهر'
        ];

        $postNote = $this->asAdmin()->post('/notepad', $noteData);
        $postNote->assertRedirect('/notepad');

        $note = DB::table('notepad_notes')->where('title', 'مراجعة تسوية ضريبة المبيعات لشهر تشرين الثاني')->first();
        $this->assertNotNull($note);

        // Toggle note
        $toggleResponse = $this->asAdmin()->post('/notepad/' . $note->id . '/toggle');
        $toggleResponse->assertRedirect('/notepad');

        // Phonebook
        $responsePhone = $this->asAdmin()->get('/phonebook');
        $responsePhone->assertStatus(200);

        $phoneData = [
            'name' => 'دائرة ضريبة الدخل والمبيعات - مديرية كبار المكلفين',
            'phone' => '+962 6 464 4444',
            'company' => 'دائرة الضريبة الحكومية',
            'category' => 'دوائر حكومية'
        ];

        $postPhone = $this->asAdmin()->post('/phonebook', $phoneData);
        $postPhone->assertRedirect('/phonebook');

        $this->assertDatabaseHas('phonebook', [
            'name' => 'دائرة ضريبة الدخل والمبيعات - مديرية كبار المكلفين'
        ]);
    }
}
