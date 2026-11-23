<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tenant
        $tenantId = DB::table('tenants')->insertGetId([
            'name' => 'وثيق لإدارة الأعمال',
            'name_en' => 'Watheeq ERP Suite',
            'tax_number' => '1004829103',
            'commercial_record' => '29381-CR',
            'phone' => '',
            'address' => 'المملكة الأردنية الهاشمية',
            'currency' => 'JOD',
            'tax_rate' => 16.00,
            'primary_color' => '#1F2E4A',
            'accent_color' => '#343A40',
            'print_format' => 'A4',
            'invoice_notes' => 'شكراً لتعاملكم معنا.',
            'dictionary' => json_encode([
                'items' => 'الأصناف والمخزون',
                'customers' => 'العملاء والذمم المدينة',
                'suppliers' => 'الموردين والذمم الدائنة',
                'invoices' => 'المبيعات والفواتير',
                'vouchers' => 'سندات القبض والصرف',
                'warehouse' => 'المستودعات والمخازن',
                'pos' => 'نقطة البيع (POS)'
            ]),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 2. Cost Centers
        $cc1 = DB::table('cost_centers')->insertGetId([
            'tenant_id' => $tenantId,
            'code' => 'CC-01',
            'name' => 'الفرع الرئيسي - عمان',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $cc2 = DB::table('cost_centers')->insertGetId([
            'tenant_id' => $tenantId,
            'code' => 'CC-02',
            'name' => 'مشروع تمديدات شبكات المستشفى التخصصي',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 3. Fiscal Years
        DB::table('fiscal_years')->insert([
            [
                'tenant_id' => $tenantId,
                'title' => 'السنة المالية 2026',
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'status' => 'open',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // 4. Fixed Assets (الأصول الثابتة)
        DB::table('fixed_assets')->insert([
            [
                'tenant_id' => $tenantId,
                'asset_code' => 'AST-001',
                'name' => 'خادم شبكات وسيرفر رئيسي Dell PowerEdge',
                'purchase_date' => '2025-01-15',
                'purchase_cost' => 6500.00,
                'salvage_value' => 500.00,
                'depreciation_rate' => 20.00,
                'accumulated_depreciation' => 1200.00,
                'book_value' => 5300.00,
                'location' => 'غرفة السيرفرات - المقر الرئيسي',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tenant_id' => $tenantId,
                'asset_code' => 'AST-002',
                'name' => 'مركبة توزيع ونقل بضائع هيونداي فان',
                'purchase_date' => '2024-06-10',
                'purchase_cost' => 18000.00,
                'salvage_value' => 2000.00,
                'depreciation_rate' => 15.00,
                'accumulated_depreciation' => 3600.00,
                'book_value' => 14400.00,
                'location' => 'المستودع العام',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // 5. Chart of Accounts (دليل الحسابات المحاسبي الموحد)
        $accounts = [
            ['code' => '101', 'name' => 'الصندوق الرئيسي (النقدية)', 'type' => 'asset', 'balance' => 14850.50],
            ['code' => '102', 'name' => 'البنك العربي - الحساب الجاري', 'type' => 'asset', 'balance' => 65200.00],
            ['code' => '103', 'name' => 'شيكات برسم التحصيل (واردة)', 'type' => 'asset', 'balance' => 22400.00],
            ['code' => '104', 'name' => 'الذمم المدينة (العملاء)', 'type' => 'asset', 'balance' => 34100.00],
            ['code' => '105', 'name' => 'بضاعة أول المدة / المخزون العام', 'type' => 'asset', 'balance' => 88500.00],
            ['code' => '106', 'name' => 'الأصول الثابتة (أجهزة وسيارات)', 'type' => 'asset', 'balance' => 24500.00],
            ['code' => '201', 'name' => 'الذمم الدائنة (الموردين)', 'type' => 'liability', 'balance' => 41200.00],
            ['code' => '202', 'name' => 'شيكات مؤجلة الدفع (صادرة)', 'type' => 'liability', 'balance' => 15000.00],
            ['code' => '203', 'name' => 'ضريبة المبيعات المستحقة للدفع', 'type' => 'liability', 'balance' => 8400.00],
            ['code' => '301', 'name' => 'رأس المال المدفوع', 'type' => 'equity', 'balance' => 150000.00],
            ['code' => '401', 'name' => 'إيرادات المبيعات العامة', 'type' => 'revenue', 'balance' => 92400.00],
            ['code' => '501', 'name' => 'تكلفة البضاعة المباعة (COGS)', 'type' => 'expense', 'balance' => 54100.00],
            ['code' => '502', 'name' => 'مصاريف عمومية وإدارية', 'type' => 'expense', 'balance' => 12500.00],
            ['code' => '503', 'name' => 'مصاريف كهرباء ومياه', 'type' => 'expense', 'balance' => 1100.00],
            ['code' => '504', 'name' => 'مصاريف إيجار المقر', 'type' => 'expense', 'balance' => 6000.00],
            ['code' => '505', 'name' => 'مصاريف إهلاك الأصول الثابتة', 'type' => 'expense', 'balance' => 4800.00],
            ['code' => '506', 'name' => 'مصاريف رواتب وأجور الموظفين', 'type' => 'expense', 'balance' => 18400.00],
        ];

        foreach ($accounts as $acc) {
            DB::table('accounts')->insert([
                'tenant_id' => $tenantId,
                'code' => $acc['code'],
                'name' => $acc['name'],
                'type' => $acc['type'],
                'balance' => $acc['balance'],
                'currency' => 'JOD',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 6. Employees (حساباتي HR)
        DB::table('employees')->insert([
            [
                'tenant_id' => $tenantId,
                'emp_code' => 'EMP-101',
                'name' => 'أحمد يوسف الصالح',
                'job_title' => 'رئيس المحاسبين والمدير المالي',
                'department' => 'الإدارة المالية',
                'national_id' => '9901029384',
                'phone' => '0791112233',
                'basic_salary' => 1200.00,
                'allowances' => 200.00,
                'deductions' => 85.00, // اقتطاع ضمان اجتماعي
                'net_salary' => 1315.00,
                'hire_date' => '2023-03-01',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tenant_id' => $tenantId,
                'emp_code' => 'EMP-102',
                'name' => 'سارة إبراهيم القاسم',
                'job_title' => 'أخصائية موارد بشرية وشؤون موظفين',
                'department' => 'الموارد البشرية',
                'national_id' => '9942039485',
                'phone' => '0785556677',
                'basic_salary' => 850.00,
                'allowances' => 150.00,
                'deductions' => 65.00,
                'net_salary' => 935.00,
                'hire_date' => '2024-01-15',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tenant_id' => $tenantId,
                'emp_code' => 'EMP-103',
                'name' => 'طارق زياد خليل',
                'job_title' => 'أمين مستودع ومشرف حركة بضائع',
                'department' => 'المستودعات واللوجستيات',
                'national_id' => '9923049182',
                'phone' => '0779998811',
                'basic_salary' => 650.00,
                'allowances' => 100.00,
                'deductions' => 50.00,
                'net_salary' => 700.00,
                'hire_date' => '2024-05-10',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // 7. Hotel Rooms & Bookings (نظام الفنادق)
        $r1 = DB::table('hotel_rooms')->insertGetId([
            'tenant_id' => $tenantId,
            'room_number' => '101',
            'room_type' => 'غرفة مفردة قياسية (Deluxe Single)',
            'floor' => 1,
            'nightly_rate' => 45.00,
            'status' => 'occupied',
            'current_guest' => 'السيد حسام العبداللات',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $r2 = DB::table('hotel_rooms')->insertGetId([
            'tenant_id' => $tenantId,
            'room_number' => '102',
            'room_type' => 'غرفة مزدوجة إطلالة مدينة (Double Room)',
            'floor' => 1,
            'nightly_rate' => 65.00,
            'status' => 'available',
            'current_guest' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $r3 = DB::table('hotel_rooms')->insertGetId([
            'tenant_id' => $tenantId,
            'room_number' => '201',
            'room_type' => 'جناح ملكي تنفيذي (Royal Suite)',
            'floor' => 2,
            'nightly_rate' => 140.00,
            'status' => 'available',
            'current_guest' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('hotel_bookings')->insert([
            [
                'tenant_id' => $tenantId,
                'room_id' => $r1,
                'booking_no' => 'BKG-2026-004',
                'guest_name' => 'حسام العبداللات',
                'guest_phone' => '0796663322',
                'check_in' => '2026-11-10',
                'check_out' => '2026-11-13',
                'nights' => 3,
                'total_amount' => 135.00,
                'paid_amount' => 135.00,
                'status' => 'checked_in',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // 8. Products
        $products = [
            ['code' => 'ITM-001', 'barcode' => '625100293012', 'name' => 'وحدة طاقة احتياطية UPS 1500VA', 'category' => 'أجهزة ومعدات', 'cost_price' => 95.0, 'sale_price' => 140.0, 'quantity' => 45, 'min_quantity' => 10, 'unit' => 'قطعة'],
            ['code' => 'ITM-002', 'barcode' => '625100293029', 'name' => 'كابل شبكات Cat6 لفة 305 متر', 'category' => 'شبكات وتمديدات', 'cost_price' => 38.0, 'sale_price' => 55.0, 'quantity' => 120, 'min_quantity' => 25, 'unit' => 'لفة'],
            ['code' => 'ITM-003', 'barcode' => '625100293036', 'name' => 'شاشة مراقبة تفاعلية 27 بوصة IPS', 'category' => 'أجهزة ومعدات', 'cost_price' => 110.0, 'sale_price' => 165.0, 'quantity' => 18, 'min_quantity' => 5, 'unit' => 'شاشة'],
            ['code' => 'ITM-004', 'barcode' => '625100293043', 'name' => 'راوتر سيسكو احترافي Dual WAN', 'category' => 'شبكات وتمديدات', 'cost_price' => 220.0, 'sale_price' => 310.0, 'quantity' => 8, 'min_quantity' => 4, 'unit' => 'جهاز'],
        ];

        foreach ($products as $p) {
            DB::table('products')->insert([
                'tenant_id' => $tenantId,
                'code' => $p['code'],
                'barcode' => $p['barcode'],
                'name' => $p['name'],
                'category' => $p['category'],
                'cost_price' => $p['cost_price'],
                'sale_price' => $p['sale_price'],
                'quantity' => $p['quantity'],
                'min_quantity' => $p['min_quantity'],
                'unit' => $p['unit'],
                'warehouse' => 'المستودع الرئيسي',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 9. Contacts
        $c1 = DB::table('contacts')->insertGetId([
            'tenant_id' => $tenantId,
            'code' => 'CUST-101',
            'name' => 'شركة الأفق للتطوير الهندسي',
            'type' => 'customer',
            'phone' => '0795551234',
            'email' => 'info@alofoq-eng.jo',
            'tax_id' => '90023412',
            'credit_limit' => 15000,
            'balance' => 4200.50,
            'address' => 'عمان - مجمع الملك حسين للأعمال',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $c2 = DB::table('contacts')->insertGetId([
            'tenant_id' => $tenantId,
            'code' => 'CUST-102',
            'name' => 'مستشفى السلام التخصصي',
            'type' => 'customer',
            'phone' => '0788884321',
            'email' => 'procurement@alsalam-hosp.jo',
            'tax_id' => '90088712',
            'credit_limit' => 25000,
            'balance' => 11800.00,
            'address' => 'عمان - الشميساني',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 10. Invoices
        $inv1 = DB::table('invoices')->insertGetId([
            'tenant_id' => $tenantId,
            'invoice_no' => 'INV-2026-0089',
            'type' => 'sale',
            'contact_id' => $c1,
            'contact_name' => 'شركة الأفق للتطوير الهندسي',
            'date' => '2026-11-10',
            'due_date' => '2026-12-10',
            'status' => 'partial',
            'payment_method' => 'mixed',
            'subtotal' => 920.00,
            'tax_total' => 147.20,
            'discount_total' => 20.00,
            'grand_total' => 1047.20,
            'notes' => 'تسليم رسمي بموجب محضر استلام معتمد',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('invoice_items')->insert([
            ['invoice_id' => $inv1, 'product_id' => 1, 'item_name' => 'وحدة طاقة احتياطية UPS 1500VA', 'unit' => 'قطعة', 'quantity' => 4, 'unit_price' => 140.0, 'discount' => 20.0, 'tax' => 86.4, 'total' => 626.4, 'created_at' => now(), 'updated_at' => now()],
            ['invoice_id' => $inv1, 'product_id' => 3, 'item_name' => 'شاشة مراقبة تفاعلية 27 بوصة IPS', 'unit' => 'شاشة', 'quantity' => 2, 'unit_price' => 165.0, 'discount' => 0.0, 'tax' => 52.8, 'total' => 382.8, 'created_at' => now(), 'updated_at' => now()]
        ]);

        // 11. Vouchers
        DB::table('vouchers')->insert([
            [
                'tenant_id' => $tenantId,
                'voucher_no' => 'RV-2026-014',
                'type' => 'receipt',
                'date' => '2026-11-10',
                'contact_name' => 'شركة الأفق للتطوير الهندسي',
                'amount' => 500.00,
                'currency' => 'JOD',
                'payment_type' => 'cash',
                'bank_name' => null,
                'cheque_no' => null,
                'statement' => 'دفعة نقدية تحت حساب فاتورة مبيعات رقم INV-2026-0089',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tenant_id' => $tenantId,
                'voucher_no' => 'RV-2026-015',
                'type' => 'receipt',
                'date' => '2026-11-11',
                'contact_name' => 'مستشفى السلام التخصصي',
                'amount' => 719.20,
                'currency' => 'JOD',
                'payment_type' => 'cheque',
                'bank_name' => 'البنك العربي',
                'cheque_no' => '8849201',
                'statement' => 'سداد شيك بنكي مسحوب على البنك العربي برسم التحصيل',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // 12. Sample Balanced Double-Entry Journal Entry
        $jv1 = DB::table('journal_entries')->insertGetId([
            'tenant_id' => $tenantId,
            'entry_no' => 'JV-2026-001',
            'date' => '2026-11-11',
            'narration' => 'إثبات سداد مصاريف إيجار المقر والكهرباء عن شهر نوفمبر 2026 بموجب شيك',
            'total_debit' => 7100.00,
            'total_credit' => 7100.00,
            'status' => 'posted',
            'created_by' => 'رئيس المحاسبين',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('journal_entry_lines')->insert([
            [
                'journal_entry_id' => $jv1,
                'account_id' => 15,
                'cost_center_id' => $cc1,
                'debit' => 6000.00,
                'credit' => 0.00,
                'description' => 'إيجار المقر الإداري لشهر نوفمبر',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'journal_entry_id' => $jv1,
                'account_id' => 14,
                'cost_center_id' => $cc1,
                'debit' => 1100.00,
                'credit' => 0.00,
                'description' => 'فاتورة الكهرباء الشهرية للمستودع والمكاتب',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'journal_entry_id' => $jv1,
                'account_id' => 2,
                'cost_center_id' => null,
                'debit' => 0.00,
                'credit' => 7100.00,
                'description' => 'سداد بموجب شيك رقم #99102 مسحوب على البنك العربي',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // 13. Sample Employees for Hesabate HR
        DB::table('employees')->insert([
            [
                'tenant_id' => $tenantId,
                'emp_code' => 'EMP-001',
                'name' => 'أحمد عبد الله القضاة',
                'job_title' => 'رئيس المحاسبين والمدير المالي',
                'department' => 'الدائرة المالية',
                'national_id' => '9881023910',
                'phone' => '+962 7 9123 4567',
                'basic_salary' => 1200.00,
                'allowances' => 200.00,
                'deductions' => 90.00,
                'net_salary' => 1310.00,
                'hire_date' => '2023-01-15',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tenant_id' => $tenantId,
                'emp_code' => 'EMP-002',
                'name' => 'سارة محمود الرواشدة',
                'job_title' => 'محاسب عام وأمين صندوق',
                'department' => 'الدائرة المالية',
                'national_id' => '9922019482',
                'phone' => '+962 7 8876 5432',
                'basic_salary' => 650.00,
                'allowances' => 80.00,
                'deductions' => 48.75,
                'net_salary' => 681.25,
                'hire_date' => '2024-03-01',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tenant_id' => $tenantId,
                'emp_code' => 'EMP-003',
                'name' => 'محمد خالد العبادي',
                'job_title' => 'مدير مبيعات ونقاط البيع',
                'department' => 'إدارة المبيعات والتسويق',
                'national_id' => '9891004821',
                'phone' => '+962 7 7711 2233',
                'basic_salary' => 800.00,
                'allowances' => 150.00,
                'deductions' => 60.00,
                'net_salary' => 890.00,
                'hire_date' => '2023-08-10',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // 14. Sample Hotel Rooms & Bookings for Hesabate Hotels
        $r1 = DB::table('hotel_rooms')->insertGetId([
            'tenant_id' => $tenantId,
            'room_number' => '101',
            'room_type' => 'جناح تنفيذي مطل',
            'floor' => 1,
            'nightly_rate' => 120.00,
            'status' => 'occupied',
            'current_guest' => 'السيد طارق المعايطة',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $r2 = DB::table('hotel_rooms')->insertGetId([
            'tenant_id' => $tenantId,
            'room_number' => '102',
            'room_type' => 'غرفة ديلوكس مزدوجة',
            'floor' => 1,
            'nightly_rate' => 75.00,
            'status' => 'available',
            'current_guest' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $r3 = DB::table('hotel_rooms')->insertGetId([
            'tenant_id' => $tenantId,
            'room_number' => '201',
            'room_type' => 'جناح ملكي VIP',
            'floor' => 2,
            'nightly_rate' => 220.00,
            'status' => 'available',
            'current_guest' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $r4 = DB::table('hotel_rooms')->insertGetId([
            'tenant_id' => $tenantId,
            'room_number' => '202',
            'room_type' => 'غرفة ستاندرد مفردة',
            'floor' => 2,
            'nightly_rate' => 45.00,
            'status' => 'cleaning',
            'current_guest' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('hotel_bookings')->insert([
            [
                'tenant_id' => $tenantId,
                'room_id' => $r1,
                'booking_no' => 'HTL-2026-001',
                'guest_name' => 'السيد طارق المعايطة',
                'guest_phone' => '+962 7 9999 8888',
                'check_in' => '2026-11-10',
                'check_out' => '2026-11-14',
                'nights' => 4,
                'total_amount' => 480.00,
                'paid_amount' => 480.00,
                'status' => 'checked_in',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
