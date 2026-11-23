<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;

class ErpSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Http::fake([
            'sandbox.jofotara.gov.jo/*' => \Illuminate\Support\Facades\Http::response(['status' => 'SUCCESS', 'message' => 'Simulated test response'], 200),
            'api.qrserver.com/*' => \Illuminate\Support\Facades\Http::response('dummy-qr-content', 200),
            '*' => \Illuminate\Support\Facades\Http::response([], 200)
        ]);
        $this->seed(DatabaseSeeder::class);

        $adminId = \Illuminate\Support\Facades\DB::table('users')->insertGetId([
            'name' => 'المدير العام (Super Admin)',
            'username' => 'admin',
            'email' => 'admin@watheeq.erp',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            'role' => 'admin',
            'role_name_ar' => 'مدير النظام',
            'enabled_modules' => json_encode(['dashboard', 'definitions', 'invoices', 'vouchers', 'hr', 'hotels', 'pos', 'reports', 'jofotara', 'taxes', 'admin']),
            'enabled_tools' => json_encode(['jofotara', 'taxes', 'sales_invoice', 'purchase_invoice', 'receipt_voucher', 'payment_voucher', 'journal_voucher', 'price_adjustment', 'employee_salary', 'guest_booking', 'cheque_portfolio', 'dispatches', 'phonebook', 'notepad', 'pos', 'reports']),
            'is_active' => true,
            'avatar_color' => '#1F2E4A',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->withSession(['active_user_id' => $adminId]);
    }

    /** @test Scenario 1: Dashboard loads with Jordan SSC styling and metrics */
    public function test_dashboard_renders_successfully_with_correct_metrics()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('وثيق لإدارة الأعمال');
        $response->assertSee('إجمالي المبيعات المكتملة');
    }

    /** @test Scenario 2: Invoices page loads and allows creating a new invoice */
    public function test_invoice_creation_scenario()
    {
        $response = $this->get('/invoices');
        $response->assertStatus(200);

        $postData = [
            'contact_id' => 1,
            'type' => 'sale',
            'payment_method' => 'cash',
            'subtotal' => 200.00,
            'tax_total' => 32.00,
            'grand_total' => 232.00,
            'items' => [
                [
                    'product_id' => 1,
                    'name' => 'وحدة طاقة احتياطية UPS 1500VA',
                    'unit' => 'قطعة',
                    'qty' => 1,
                    'price' => 200.00
                ]
            ],
            'notes' => 'فاتورة اختبار مؤتمتة'
        ];

        $storeResponse = $this->post('/invoices', $postData);
        $storeResponse->assertRedirect('/invoices');

        $this->assertDatabaseHas('invoices', [
            'notes' => 'فاتورة اختبار مؤتمتة',
            'grand_total' => 232.00
        ]);
    }

    /** @test Scenario 3: Tenant Customizer allows white-label modifications */
    public function test_tenant_customizer_scenario()
    {
        $updateData = [
            'name' => 'شركة اليرموك للتجارة الدولية',
            'name_en' => 'Yarmouk Global Trading',
            'tax_number' => '99887766',
            'commercial_record' => 'CR-5544',
            'currency' => 'JOD',
            'tax_rate' => 16.00,
            'phone' => '+962 6 555 1212',
            'address' => 'عمان - الأردن',
            'print_format' => 'Thermal80mm',
            'invoice_notes' => 'شروط جديدة مخصصة'
        ];

        $response = $this->post('/customizer', $updateData);
        $response->assertRedirect('/customizer');

        $this->assertDatabaseHas('tenants', [
            'name' => 'شركة اليرموك للتجارة الدولية',
            'print_format' => 'Thermal80mm'
        ]);
    }

    /** @test Scenario 4: Performance & GZip headers verification */
    public function test_gzip_compression_header_presence()
    {
        $response = $this->withHeaders([
            'Accept-Encoding' => 'gzip, deflate'
        ])->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    /** @test Scenario 5: Financial integrity (Accounts & Reports load) */
    public function test_reports_and_chart_of_accounts_load()
    {
        $accountsResp = $this->get('/accounts');
        $accountsResp->assertStatus(200);
        $accountsResp->assertSee('الصندوق الرئيسي (النقدية)');

        $reportsResp = $this->get('/reports');
        $reportsResp->assertStatus(200);
        $reportsResp->assertSee('إجمالي الأصول (الموجودات)');
    }

    /** @test Scenario 6: Hesabate HR & Payroll execution */
    public function test_hr_and_payroll_scenario()
    {
        $response = $this->get('/hr');
        $response->assertStatus(200);
        $response->assertSee('أحمد عبد الله القضاة');

        $empData = [
            'emp_code' => 'EMP-777',
            'name' => 'بلال خالد المصري',
            'job_title' => 'محاسب تكاليف',
            'department' => 'الدائرة المالية',
            'national_id' => '9901002233',
            'phone' => '+962 7 9000 1122',
            'basic_salary' => 900.00,
            'allowances' => 100.00,
            'deductions' => 75.00,
            'hire_date' => '2026-02-01',
            'status' => 'active'
        ];

        $postResp = $this->post('/hr/employees', $empData);
        $postResp->assertRedirect('/hr');

        $this->assertDatabaseHas('employees', [
            'emp_code' => 'EMP-777',
            'net_salary' => 925.00
        ]);
    }

    /** @test Scenario 7: Hesabate Hotels & Room bookings execution */
    public function test_hotel_rooms_and_bookings_scenario()
    {
        $response = $this->get('/hotels');
        $response->assertStatus(200);
        $response->assertSee('جناح تنفيذي مطل');

        $bookingData = [
            'room_id' => 2, // Room 102 (available)
            'guest_name' => 'الدكتور سمير النجار',
            'guest_phone' => '+962 7 8888 1111',
            'check_in' => '2026-11-15',
            'check_out' => '2026-11-18',
            'paid_amount' => 225.00
        ];

        $postResp = $this->post('/hotels/bookings', $bookingData);
        $postResp->assertRedirect('/hotels');

        $this->assertDatabaseHas('hotel_bookings', [
            'guest_name' => 'الدكتور سمير النجار',
            'nights' => 3
        ]);

        $this->assertDatabaseHas('hotel_rooms', [
            'id' => 2,
            'status' => 'occupied',
            'current_guest' => 'الدكتور سمير النجار'
        ]);
    }
}
