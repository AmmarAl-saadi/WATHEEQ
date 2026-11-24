<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Primary Tenant (منشأة وثيق)
        $tenantId = DB::table('tenants')->insertGetId([
            'name' => 'شركة وثيق لحلول الأعمال والتقنية',
            'name_en' => 'Watheeq Business & Tech Solutions Ltd',
            'tax_number' => '1004829103',
            'commercial_record' => '29381-CR',
            'phone' => '+962 6 555 1234',
            'address' => 'المملكة الأردنية الهاشمية - عمان - شارع مكة',
            'currency' => 'JOD',
            'tax_rate' => 16.00,
            'primary_color' => '#1F2E4A',
            'accent_color' => '#343A40',
            'print_format' => 'A4',
            'invoice_notes' => 'شكراً لتعاملكم مع منظومة وثيق. البضاعة المباعة ترد وتستبدل حسب الشروط.',
            'dictionary' => json_encode([
                'items' => 'الأصناف والخدمات',
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

        // 2. Cost Centers (5 مراكز تكلفة ومشاريع)
        $costCenters = [
            ['code' => 'CC-01', 'name' => 'الفرع الرئيسي - عمان (مبنى الإدارة)'],
            ['code' => 'CC-02', 'name' => 'مشروع أتمتة مستشفى السلام الدولي'],
            ['code' => 'CC-03', 'name' => 'فرع إربد - مركز الخدمات اللوجستية'],
            ['code' => 'CC-04', 'name' => 'مشروع تطوير الشبكات - جامعة الشرق'],
            ['code' => 'CC-05', 'name' => 'فرع العقبة - مستودع المنطقة الاقتصادية'],
        ];
        $ccIds = [];
        foreach ($costCenters as $cc) {
            $ccIds[] = DB::table('cost_centers')->insertGetId([
                'tenant_id' => $tenantId,
                'code' => $cc['code'],
                'name' => $cc['name'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 3. Fiscal Year
        DB::table('fiscal_years')->insert([
            'tenant_id' => $tenantId,
            'title' => 'السنة المالية 2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 4. Fixed Assets (5 أصول ثابتة متنوعة)
        $fixedAssets = [
            ['code' => 'AST-001', 'name' => 'خادم سحابي مركزي Dell PowerEdge R750', 'cost' => 6500.00, 'salvage' => 500.00, 'rate' => 20.00, 'acc' => 1200.00, 'book' => 5300.00, 'loc' => 'غرفة السيرفرات - المقر الرئيسي'],
            ['code' => 'AST-002', 'name' => 'مركبة توزيع ونقل بضائع Hyundai H-1 Van', 'cost' => 19500.00, 'salvage' => 2500.00, 'rate' => 15.00, 'acc' => 3800.00, 'book' => 15700.00, 'loc' => 'كراج السيارات - عمان'],
            ['code' => 'AST-003', 'name' => 'أجهزة ونقاط بيع لمس POS Touch All-in-One (طقم 5 أجهزة)', 'cost' => 3200.00, 'salvage' => 200.00, 'rate' => 25.00, 'acc' => 600.00, 'book' => 2600.00, 'loc' => 'صالات المبيعات والفروع'],
            ['code' => 'AST-004', 'name' => 'أثاث وتجهيزات مكتبية تنفيذية للمكاتب الإدارية', 'cost' => 8400.00, 'salvage' => 400.00, 'rate' => 10.00, 'acc' => 1100.00, 'book' => 7300.00, 'loc' => 'مكاتب الإدارة العامة - الطابق 3'],
            ['code' => 'AST-005', 'name' => 'نظام تكييف مركزي متطور إنفيرتر VRF', 'cost' => 12000.00, 'salvage' => 1000.00, 'rate' => 12.00, 'acc' => 2200.00, 'book' => 9800.00, 'loc' => 'مبنى الإدارة ومستودع البضائع'],
        ];
        foreach ($fixedAssets as $fa) {
            DB::table('fixed_assets')->insert([
                'tenant_id' => $tenantId,
                'asset_code' => $fa['code'],
                'name' => $fa['name'],
                'purchase_date' => '2025-01-15',
                'purchase_cost' => $fa['cost'],
                'salvage_value' => $fa['salvage'],
                'depreciation_rate' => $fa['rate'],
                'accumulated_depreciation' => $fa['acc'],
                'book_value' => $fa['book'],
                'location' => $fa['loc'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 5. Chart of Accounts (دليل الحسابات الموحد)
        $accounts = [
            ['code' => '101', 'name' => 'الصندوق الرئيسي (نقدية بالصندوق)', 'type' => 'asset', 'balance' => 12450.00],
            ['code' => '102', 'name' => 'البنك العربي - الحساب الجاري التشغيلي', 'type' => 'asset', 'balance' => 48900.00],
            ['code' => '103', 'name' => 'شيكات برسم التحصيل (شيكات واردة)', 'type' => 'asset', 'balance' => 16300.00],
            ['code' => '104', 'name' => 'الذمم المدينة (العملاء التجاريون)', 'type' => 'asset', 'balance' => 29400.00],
            ['code' => '105', 'name' => 'بضاعة المخزون والمستودع العام', 'type' => 'asset', 'balance' => 72100.00],
            ['code' => '201', 'name' => 'الذمم الدائنة (الموردين والشركاء)', 'type' => 'liability', 'balance' => 31800.00],
            ['code' => '202', 'name' => 'شيكات مؤجلة الدفع (شيكات صادرة)', 'type' => 'liability', 'balance' => 12500.00],
            ['code' => '203', 'name' => 'أمانات ضريبة المبيعات المستحقة (ISTD)', 'type' => 'liability', 'balance' => 6400.00],
            ['code' => '301', 'name' => 'رأس المال المدفوع والاحتياطي', 'type' => 'equity', 'balance' => 110000.00],
            ['code' => '401', 'name' => 'إيرادات المبيعات والخدمات', 'type' => 'revenue', 'balance' => 84200.00],
            ['code' => '501', 'name' => 'تكلفة البضاعة والخدمات المباعة', 'type' => 'expense', 'balance' => 41500.00],
            ['code' => '502', 'name' => 'مصاريف الرواتب والأجور الشهرية', 'type' => 'expense', 'balance' => 14200.00],
            ['code' => '503', 'name' => 'مصاريف إيجار المقار والمستودعات', 'type' => 'expense', 'balance' => 5500.00],
            ['code' => '504', 'name' => 'مصاريف كهرباء ومياه واتصالات', 'type' => 'expense', 'balance' => 1350.00],
            ['code' => '505', 'name' => 'مصاريف تسويق وضيافة عامة', 'type' => 'expense', 'balance' => 2100.00],
        ];
        $accMap = [];
        foreach ($accounts as $acc) {
            $id = DB::table('accounts')->insertGetId([
                'tenant_id' => $tenantId,
                'code' => $acc['code'],
                'name' => $acc['name'],
                'type' => $acc['type'],
                'balance' => $acc['balance'],
                'currency' => 'JOD',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $accMap[$acc['code']] = $id;
        }

        // 6. Employees (5 موظفين في الموارد البشرية HR)
        $employees = [
            ['code' => 'EMP-101', 'name' => 'أحمد يوسف الصالح', 'title' => 'رئيس المحاسبين والمدير المالي', 'dept' => 'الإدارة المالية', 'nat' => '9901029384', 'phone' => '0791112233', 'basic' => 1250.00, 'allow' => 250.00, 'ded' => 97.50, 'net' => 1402.50],
            ['code' => 'EMP-102', 'name' => 'سارة إبراهيم القاسم', 'title' => 'مسؤولة شؤون الموظفين واستقطاب الكفاءات', 'dept' => 'الموارد البشرية (HR)', 'nat' => '9942039485', 'phone' => '0785556677', 'basic' => 900.00, 'allow' => 150.00, 'ded' => 70.20, 'net' => 979.80],
            ['code' => 'EMP-103', 'name' => 'طارق زياد خليل', 'title' => 'مسؤول كاشير ونقاط البيع POS', 'dept' => 'إدارة المبيعات والتوزيع', 'nat' => '9923049182', 'phone' => '0779998811', 'basic' => 550.00, 'allow' => 100.00, 'ded' => 42.90, 'net' => 607.10],
            ['code' => 'EMP-104', 'name' => 'م. ليلى عبد الرؤوف النجار', 'title' => 'مهندسة برمجيات ودعم فني أول', 'dept' => 'تطوير الأنظمة وتكنولوجيا المعلومات', 'nat' => '9951092837', 'phone' => '0792224488', 'basic' => 1400.00, 'allow' => 300.00, 'ded' => 109.20, 'net' => 1590.80],
            ['code' => 'EMP-105', 'name' => 'عمر سامي الجعفري', 'title' => 'مشرف حركة بضائع وأمين مستودع', 'dept' => 'المستودعات واللوجستيات', 'nat' => '9914029184', 'phone' => '0781119955', 'basic' => 650.00, 'allow' => 120.00, 'ded' => 50.70, 'net' => 719.30],
        ];
        foreach ($employees as $emp) {
            DB::table('employees')->insert([
                'tenant_id' => $tenantId,
                'emp_code' => $emp['code'],
                'name' => $emp['name'],
                'job_title' => $emp['title'],
                'department' => $emp['dept'],
                'national_id' => $emp['nat'],
                'phone' => $emp['phone'],
                'basic_salary' => $emp['basic'],
                'allowances' => $emp['allow'],
                'deductions' => $emp['ded'],
                'net_salary' => $emp['net'],
                'hire_date' => '2024-01-10',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 7. Hotel Rooms & Bookings (5 غرف فندقية)
        $hotelRooms = [
            ['room_no' => '101', 'type' => 'جناح ديلوكس تنفيذي (Deluxe Suite)', 'floor' => 1, 'rate' => 110.00, 'status' => 'occupied', 'guest' => 'السيد معتز الخوالدة'],
            ['room_no' => '102', 'type' => 'غرفة مزدوجة إطلالة جبلية (Double Room)', 'floor' => 1, 'rate' => 65.00, 'status' => 'available', 'guest' => null],
            ['room_no' => '201', 'type' => 'جناح ملكي رئاسي (Royal VIP Suite)', 'floor' => 2, 'rate' => 220.00, 'status' => 'occupied', 'guest' => 'د. فراس التميمي'],
            ['room_no' => '202', 'type' => 'غرفة مفردة رجال أعمال (Single Room)', 'floor' => 2, 'rate' => 45.00, 'status' => 'cleaning', 'guest' => null],
            ['room_no' => '301', 'type' => 'استوديو عائلي فندقي (Family Studio)', 'floor' => 3, 'rate' => 95.00, 'status' => 'available', 'guest' => null],
        ];
        $roomIds = [];
        foreach ($hotelRooms as $hr) {
            $roomIds[] = DB::table('hotel_rooms')->insertGetId([
                'tenant_id' => $tenantId,
                'room_number' => $hr['room_no'],
                'room_type' => $hr['type'],
                'floor' => $hr['floor'],
                'nightly_rate' => $hr['rate'],
                'status' => $hr['status'],
                'current_guest' => $hr['guest'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 5 حجوزات فندقية
        $bookings = [
            ['room_id' => $roomIds[0], 'no' => 'BKG-2026-001', 'guest' => 'معتز الخوالدة', 'phone' => '0798881122', 'in' => '2026-11-20', 'out' => '2026-11-25', 'nights' => 5, 'total' => 550.00, 'paid' => 550.00, 'status' => 'checked_in'],
            ['room_id' => $roomIds[2], 'no' => 'BKG-2026-002', 'guest' => 'د. فراس التميمي', 'phone' => '0789994433', 'in' => '2026-11-22', 'out' => '2026-11-26', 'nights' => 4, 'total' => 880.00, 'paid' => 880.00, 'status' => 'checked_in'],
            ['room_id' => $roomIds[1], 'no' => 'BKG-2026-003', 'guest' => 'عبدالله الكردي', 'phone' => '0775553311', 'in' => '2026-11-27', 'out' => '2026-11-29', 'nights' => 2, 'total' => 130.00, 'paid' => 50.00, 'status' => 'confirmed'],
            ['room_id' => $roomIds[4], 'no' => 'BKG-2026-004', 'guest' => 'عائلة م. خالد النابلسي', 'phone' => '0791238899', 'in' => '2026-12-01', 'out' => '2026-12-04', 'nights' => 3, 'total' => 285.00, 'paid' => 100.00, 'status' => 'confirmed'],
            ['room_id' => $roomIds[3], 'no' => 'BKG-2026-005', 'guest' => 'جون أندرسون', 'phone' => '+447911122334', 'in' => '2026-11-15', 'out' => '2026-11-18', 'nights' => 3, 'total' => 135.00, 'paid' => 135.00, 'status' => 'checked_out'],
        ];
        foreach ($bookings as $b) {
            DB::table('hotel_bookings')->insert([
                'tenant_id' => $tenantId,
                'room_id' => $b['room_id'],
                'booking_no' => $b['no'],
                'guest_name' => $b['guest'],
                'guest_phone' => $b['phone'],
                'check_in' => $b['in'],
                'check_out' => $b['out'],
                'nights' => $b['nights'],
                'total_amount' => $b['total'],
                'paid_amount' => $b['paid'],
                'status' => $b['status'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 8. Products & Services (5 أصناف رئيسية في المخزون)
        $products = [
            ['code' => 'ITM-001', 'barcode' => '625100293012', 'name' => 'وحدة طاقة احتياطية ذكية Smart UPS 1500VA', 'category' => 'أجهزة ومعدات شبكات', 'cost' => 88.00, 'sale' => 135.00, 'qty' => 45, 'min' => 10, 'unit' => 'قطعة'],
            ['code' => 'ITM-002', 'barcode' => '625100293029', 'name' => 'كيبل شبكات مجدول Cat6 UTP أصلي لفة 305 متر', 'category' => 'كوابل وتمديدات', 'cost' => 36.00, 'sale' => 54.00, 'qty' => 150, 'min' => 25, 'unit' => 'لفة'],
            ['code' => 'ITM-003', 'barcode' => '625100293036', 'name' => 'شاشة لمس تفاعلية لنقاط البيع Touch POS Display 15.6"', 'category' => 'أجهزة ومعدات نقاط البيع', 'cost' => 110.00, 'sale' => 165.00, 'qty' => 24, 'min' => 5, 'unit' => 'شاشة'],
            ['code' => 'ITM-004', 'barcode' => '625100293043', 'name' => 'راوتر احترافي متعدد المنافذ Cisco Dual-WAN Gigabit', 'category' => 'أجهزة ومعدات شبكات', 'cost' => 195.00, 'sale' => 280.00, 'qty' => 16, 'min' => 4, 'unit' => 'جهاز'],
            ['code' => 'ITM-005', 'barcode' => '625100293050', 'name' => 'طابعة فواتير حرارية وسندات حرارية Thermal Receipt USB/LAN', 'category' => 'طابعات وملحقات كاشير', 'cost' => 48.00, 'sale' => 75.00, 'qty' => 38, 'min' => 8, 'unit' => 'طابعة'],
        ];
        $prodIds = [];
        foreach ($products as $p) {
            $prodIds[] = DB::table('products')->insertGetId([
                'tenant_id' => $tenantId,
                'code' => $p['code'],
                'barcode' => $p['barcode'],
                'name' => $p['name'],
                'category' => $p['category'],
                'cost_price' => $p['cost'],
                'sale_price' => $p['sale'],
                'quantity' => $p['qty'],
                'min_quantity' => $p['min'],
                'unit' => $p['unit'],
                'warehouse' => 'المستودع الرئيسي - عمان',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 9. Contacts (5 عملاء وموردين)
        $contacts = [
            ['code' => 'CUST-101', 'name' => 'شركة الأفق للاستشارات الهندسية', 'type' => 'customer', 'phone' => '0795551234', 'email' => 'info@alofoq-eng.jo', 'tax_id' => '90023412', 'limit' => 20000, 'bal' => 4200.00, 'addr' => 'عمان - مجمع الملك حسين للأعمال'],
            ['code' => 'CUST-102', 'name' => 'مستشفى السلام الدولي التخصصي', 'type' => 'customer', 'phone' => '0788884321', 'email' => 'procurement@alsalam-hosp.jo', 'tax_id' => '90088712', 'limit' => 35000, 'bal' => 11450.00, 'addr' => 'عمان - الشميساني'],
            ['code' => 'CUST-103', 'name' => 'جامعة الشرق الحديثة للعلوم والتكنولوجيا', 'type' => 'customer', 'phone' => '0774449911', 'email' => 'finance@mashreq-edu.jo', 'tax_id' => '90045678', 'limit' => 50000, 'bal' => 18900.00, 'addr' => 'إربد - شارع الجامعة'],
            ['code' => 'SUPP-201', 'name' => 'مؤسسة الدلتا الدولية للأجهزة والتقنية', 'type' => 'supplier', 'phone' => '0793332211', 'email' => 'orders@delta-tech.jo', 'tax_id' => '80012399', 'limit' => 40000, 'bal' => 14800.00, 'addr' => 'عمان - تلاع العلي'],
            ['code' => 'SUPP-202', 'name' => 'شركة البشير لاستيراد وتوزيع الكوابل والشبكات', 'type' => 'supplier', 'phone' => '0782226655', 'email' => 'sales@basheer-cables.com', 'tax_id' => '80098765', 'limit' => 25000, 'bal' => 6200.00, 'addr' => 'الزرقاء - المنطقة الحرة'],
        ];
        $contactIds = [];
        foreach ($contacts as $c) {
            $contactIds[] = DB::table('contacts')->insertGetId([
                'tenant_id' => $tenantId,
                'code' => $c['code'],
                'name' => $c['name'],
                'type' => $c['type'],
                'phone' => $c['phone'],
                'email' => $c['email'],
                'tax_id' => $c['tax_id'],
                'credit_limit' => $c['limit'],
                'balance' => $c['bal'],
                'address' => $c['addr'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 10. Invoices (5 فواتير مبيعات ومشتريات)
        $invoices = [
            ['no' => 'INV-2026-001', 'type' => 'sale', 'cid' => $contactIds[0], 'name' => 'شركة الأفق للاستشارات الهندسية', 'date' => '2026-11-05', 'due' => '2026-12-05', 'sub' => 680.00, 'tax' => 108.80, 'disc' => 20.00, 'grand' => 768.80, 'status' => 'paid', 'method' => 'cash', 'notes' => 'توريد أجهزة شبكات بموجب أمر الشراء المباشر'],
            ['no' => 'INV-2026-002', 'type' => 'sale', 'cid' => $contactIds[1], 'name' => 'مستشفى السلام الدولي التخصصي', 'date' => '2026-11-12', 'due' => '2026-12-12', 'sub' => 1420.00, 'tax' => 227.20, 'disc' => 50.00, 'grand' => 1597.20, 'status' => 'partial', 'method' => 'mixed', 'notes' => 'توريد شاشات وأجهزة UPS لقسم العناية المركزة'],
            ['no' => 'INV-2026-003', 'type' => 'sale', 'cid' => $contactIds[2], 'name' => 'جامعة الشرق الحديثة للعلوم والتكنولوجيا', 'date' => '2026-11-18', 'due' => '2026-12-18', 'sub' => 2850.00, 'tax' => 456.00, 'disc' => 100.00, 'grand' => 3206.00, 'status' => 'unpaid', 'method' => 'credit', 'notes' => 'مشروع ربط مختبرات الحاسوب بالمبنى الأكاديمي'],
            ['no' => 'BILL-2026-001', 'type' => 'purchase', 'cid' => $contactIds[3], 'name' => 'مؤسسة الدلتا الدولية للأجهزة والتقنية', 'date' => '2026-11-02', 'due' => '2026-12-02', 'sub' => 3200.00, 'tax' => 512.00, 'disc' => 0.00, 'grand' => 3712.00, 'status' => 'paid', 'method' => 'card', 'notes' => 'استيراد وشراء دفعة خوادم وأجهزة راوترات'],
            ['no' => 'BILL-2026-002', 'type' => 'purchase', 'cid' => $contactIds[4], 'name' => 'شركة البشير لاستيراد وتوزيع الكوابل والشبكات', 'date' => '2026-11-15', 'due' => '2026-12-15', 'sub' => 1800.00, 'tax' => 288.00, 'disc' => 50.00, 'grand' => 2038.00, 'status' => 'partial', 'method' => 'cheque', 'notes' => 'شراء كوابل Cat6 وإكسسوارات تمديد'],
        ];
        foreach ($invoices as $inv) {
            $invId = DB::table('invoices')->insertGetId([
                'tenant_id' => $tenantId,
                'invoice_no' => $inv['no'],
                'type' => $inv['type'],
                'contact_id' => $inv['cid'],
                'contact_name' => $inv['name'],
                'date' => $inv['date'],
                'due_date' => $inv['due'],
                'status' => $inv['status'],
                'payment_method' => $inv['method'],
                'subtotal' => $inv['sub'],
                'tax_total' => $inv['tax'],
                'discount_total' => $inv['disc'],
                'grand_total' => $inv['grand'],
                'notes' => $inv['notes'],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('invoice_items')->insert([
                ['invoice_id' => $invId, 'product_id' => $prodIds[0], 'item_name' => 'وحدة طاقة احتياطية ذكية Smart UPS 1500VA', 'unit' => 'قطعة', 'quantity' => 2, 'unit_price' => 135.0, 'discount' => 10.0, 'tax' => 41.6, 'total' => 301.6, 'created_at' => now(), 'updated_at' => now()],
                ['invoice_id' => $invId, 'product_id' => $prodIds[1], 'item_name' => 'كيبل شبكات مجدول Cat6 UTP أصلي لفة 305 متر', 'unit' => 'لفة', 'quantity' => 5, 'unit_price' => 54.0, 'discount' => 0.0, 'tax' => 43.2, 'total' => 313.2, 'created_at' => now(), 'updated_at' => now()]
            ]);
        }

        // 11. Vouchers (5 سندات قبض وصرف مالي)
        $vouchers = [
            ['no' => 'RV-2026-001', 'type' => 'receipt', 'date' => '2026-11-05', 'name' => 'شركة الأفق للاستشارات الهندسية', 'amount' => 768.80, 'pay' => 'cash', 'bank' => null, 'cheque' => null, 'notes' => 'سداد نقدي كامل لفاتورة مبيعات رقم INV-2026-001'],
            ['no' => 'RV-2026-002', 'type' => 'receipt', 'date' => '2026-11-13', 'name' => 'مستشفى السلام الدولي التخصصي', 'amount' => 1000.00, 'pay' => 'cheque', 'bank' => 'البنك العربي', 'cheque' => '994012', 'notes' => 'شيك بنكي دفعة تحت حساب فاتورة رقم INV-2026-002'],
            ['no' => 'RV-2026-003', 'type' => 'receipt', 'date' => '2026-11-20', 'name' => 'معتز الخوالدة (نزيل فندقي)', 'amount' => 550.00, 'pay' => 'cash', 'bank' => null, 'cheque' => null, 'notes' => 'سداد نقدي عن حجز جناح رقم 101 لمدة 5 ليال'],
            ['no' => 'PV-2026-001', 'type' => 'payment', 'date' => '2026-11-03', 'name' => 'مؤسسة الدلتا الدولية للأجهزة والتقنية', 'amount' => 3712.00, 'pay' => 'bank_transfer', 'bank' => 'البنك العربي', 'cheque' => null, 'notes' => 'تحويل بنكي لسداد فاتورة مشتريات رقم BILL-2026-001'],
            ['no' => 'PV-2026-002', 'type' => 'payment', 'date' => '2026-11-16', 'name' => 'شركة البشير للكوابل والشبكات', 'amount' => 1000.00, 'pay' => 'cheque', 'bank' => 'بنك الاتحاد', 'cheque' => '332019', 'notes' => 'دفعة شيك مؤجل سداد من فاتورة توريد كوابل رقم BILL-2026-002'],
        ];
        foreach ($vouchers as $v) {
            DB::table('vouchers')->insert([
                'tenant_id' => $tenantId,
                'voucher_no' => $v['no'],
                'type' => $v['type'],
                'date' => $v['date'],
                'contact_name' => $v['name'],
                'amount' => $v['amount'],
                'currency' => 'JOD',
                'payment_type' => $v['pay'],
                'bank_name' => $v['bank'],
                'cheque_no' => $v['cheque'],
                'statement' => $v['notes'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 12. Journal Entries (5 قيود يومية محاسبية متوازنة Debit = Credit)
        $journals = [
            ['no' => 'JV-2026-001', 'date' => '2026-11-01', 'narration' => 'إثبات سداد مصاريف إيجار المقر الإداري لشهر نوفمبر 2026 بشيك بنكي', 'amt' => 2500.00, 'd_acc' => $accMap['503'], 'c_acc' => $accMap['102']],
            ['no' => 'JV-2026-002', 'date' => '2026-11-08', 'narration' => 'سداد فواتير الكهرباء والمياه والإنترنت المركزي نقداً من الصندوق', 'amt' => 650.00, 'd_acc' => $accMap['504'], 'c_acc' => $accMap['101']],
            ['no' => 'JV-2026-003', 'date' => '2026-11-15', 'narration' => 'إثبات قيد استهلاك الأصول الثابتة الشهري للمركبات والخوادم', 'amt' => 450.00, 'd_acc' => $accMap['501'], 'c_acc' => $accMap['105']],
            ['no' => 'JV-2026-004', 'date' => '2026-11-20', 'narration' => 'تسوية وصرف عهدة نثريات وضيافة المكاتب للفروع نقداً', 'amt' => 320.00, 'd_acc' => $accMap['505'], 'c_acc' => $accMap['101']],
            ['no' => 'JV-2026-005', 'date' => '2026-11-23', 'narration' => 'تحويل وتغذية سيولة من الحساب الجاري بالبنك العربي إلى الصندوق الرئيسي', 'amt' => 3000.00, 'd_acc' => $accMap['101'], 'c_acc' => $accMap['102']],
        ];
        foreach ($journals as $j) {
            $jId = DB::table('journal_entries')->insertGetId([
                'tenant_id' => $tenantId,
                'entry_no' => $j['no'],
                'date' => $j['date'],
                'narration' => $j['narration'],
                'total_debit' => $j['amt'],
                'total_credit' => $j['amt'],
                'status' => 'posted',
                'created_by' => 'رئيس المحاسبين',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('journal_entry_lines')->insert([
                ['journal_entry_id' => $jId, 'account_id' => $j['d_acc'], 'cost_center_id' => $ccIds[0], 'debit' => $j['amt'], 'credit' => 0.00, 'description' => $j['narration'], 'created_at' => now(), 'updated_at' => now()],
                ['journal_entry_id' => $jId, 'account_id' => $j['c_acc'], 'cost_center_id' => null, 'debit' => 0.00, 'credit' => $j['amt'], 'description' => $j['narration'], 'created_at' => now(), 'updated_at' => now()]
            ]);
        }

        // 13. Cheques Portfolio (5 شيكات واردة وصادرة)
        $cheques = [
            ['no' => 'CHQ-88201', 'type' => 'received', 'bank' => 'البنك العربي', 'branch' => 'الشميساني', 'drawer' => 'مستشفى السلام الدولي', 'ben' => 'شركة وثيق', 'amt' => 1000.00, 'due' => '2026-11-28', 'st' => 'under_collection', 'notes' => 'شيك وارد دفعة تحت التحصيل'],
            ['no' => 'CHQ-88202', 'type' => 'received', 'bank' => 'بنك الإسكان', 'branch' => 'مكة', 'drawer' => 'شركة الأفق الهندسية', 'ben' => 'شركة وثيق', 'amt' => 2500.00, 'due' => '2026-12-05', 'st' => 'under_collection', 'notes' => 'سداد فاتورة مشاريع'],
            ['no' => 'CHQ-88203', 'type' => 'received', 'bank' => 'بنك القاهرة عمان', 'branch' => 'العبدلي', 'drawer' => 'فندق المروج السياحي', 'ben' => 'شركة وثيق', 'amt' => 1400.00, 'due' => '2026-11-10', 'st' => 'cleared', 'notes' => 'تم تحصيله وإيداعه في البنك العربي'],
            ['no' => 'CHQ-55101', 'type' => 'issued', 'bank' => 'البنك العربي', 'branch' => 'عمان', 'drawer' => 'شركة وثيق', 'ben' => 'شركة البشير للكوابل', 'amt' => 1000.00, 'due' => '2026-12-10', 'st' => 'under_collection', 'notes' => 'شيك مؤجل الدفع للمورد'],
            ['no' => 'CHQ-55102', 'type' => 'issued', 'bank' => 'البنك العربي', 'branch' => 'عمان', 'drawer' => 'شركة وثيق', 'ben' => 'شركة إعمار العقارية', 'amt' => 2500.00, 'due' => '2026-11-01', 'st' => 'cleared', 'notes' => 'شيك إيجار المقر تم صرفه'],
        ];
        foreach ($cheques as $chq) {
            DB::table('cheques')->insert([
                'tenant_id' => $tenantId,
                'cheque_no' => $chq['no'],
                'type' => $chq['type'],
                'bank_name' => $chq['bank'],
                'branch' => $chq['branch'],
                'drawer_name' => $chq['drawer'],
                'beneficiary' => $chq['ben'],
                'amount' => $chq['amt'],
                'due_date' => $chq['due'],
                'status' => $chq['st'],
                'notes' => $chq['notes'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 14. Logistics & Dispatches (5 إرساليات نقل وتسليم بضاعة)
        $dispatches = [
            ['no' => 'DSP-2026-001', 'type' => 'delivery_out', 'party' => 'شركة الأفق للاستشارات', 'date' => '2026-11-05', 'driver' => 'عمر الجعفري', 'veh' => '54-89210', 'src' => 'المستودع الرئيسي - عمان', 'dest' => 'موقع مجمع الملك حسين', 'st' => 'delivered', 'qty' => 7, 'notes' => 'تسليم أجهزة ومعدات شبكات'],
            ['no' => 'DSP-2026-002', 'type' => 'delivery_out', 'party' => 'مستشفى السلام الدولي', 'date' => '2026-11-12', 'driver' => 'خالد السعدي', 'veh' => '12-34901', 'src' => 'المستودع الرئيسي - عمان', 'dest' => 'الشميساني - مستودع المستشفى', 'st' => 'delivered', 'qty' => 12, 'notes' => 'تسليم وحدات طاقة UPS وشاشات'],
            ['no' => 'DSP-2026-003', 'type' => 'branch_transfer', 'party' => 'فرع إربد اللوجستي', 'date' => '2026-11-18', 'driver' => 'عمر الجعفري', 'veh' => '54-89210', 'src' => 'المستودع الرئيسي - عمان', 'dest' => 'مستودع فرع إربد', 'st' => 'in_transit', 'qty' => 50, 'notes' => 'تحويل بضاعة كوابل وشاشات لمستودع الشمال'],
            ['no' => 'DSP-2026-004', 'type' => 'receipt_in', 'party' => 'مؤسسة الدلتا الدولية', 'date' => '2026-11-02', 'driver' => 'سائق التوريد الخارجي', 'veh' => '88-12904', 'src' => 'مستودع المورد', 'dest' => 'المستودع الرئيسي - عمان', 'st' => 'received', 'qty' => 20, 'notes' => 'محضر استلام بضاعة جديدة واردة'],
            ['no' => 'DSP-2026-005', 'type' => 'sales_order', 'party' => 'جامعة الشرق الحديثة', 'date' => '2026-11-24', 'driver' => 'لم يحدد بعد', 'veh' => null, 'src' => 'المستودع الرئيسي - عمان', 'dest' => 'حرم جامعة الشرق', 'st' => 'pending', 'qty' => 35, 'notes' => 'طلبية مبيعات قيد التجهيز والتغليف'],
        ];
        foreach ($dispatches as $d) {
            DB::table('dispatches')->insert([
                'tenant_id' => $tenantId,
                'dispatch_no' => $d['no'],
                'type' => $d['type'],
                'party_name' => $d['party'],
                'date' => $d['date'],
                'driver_name' => $d['driver'],
                'vehicle_no' => $d['veh'],
                'source_warehouse' => $d['src'],
                'dest_warehouse' => $d['dest'],
                'status' => $d['st'],
                'total_qty' => $d['qty'],
                'notes' => $d['notes'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 15. General Expenses (5 سندات ومصاريف تشغيلية)
        $expenses = [
            ['no' => 'EXP-2026-001', 'cat' => 'إيجار المقار والمكاتب', 'amt' => 2500.00, 'date' => '2026-11-01', 'to' => 'شركة إعمار العقارية', 'method' => 'شيك بنكي من البنك العربي', 'desc' => 'سداد دفعة إيجار المقر الرئيسي لشهر نوفمبر'],
            ['no' => 'EXP-2026-002', 'cat' => 'كهرباء ومياه وطاقة', 'amt' => 450.00, 'date' => '2026-11-08', 'to' => 'شركة الكهرباء الأردنية', 'method' => 'دفع إلكتروني إي فواتيركم', 'desc' => 'فاتورة استهلاك الكهرباء والمكيفات للمكاتب'],
            ['no' => 'EXP-2026-003', 'cat' => 'ضيافة وبوفيه واستقبال عملاء', 'amt' => 125.00, 'date' => '2026-11-11', 'to' => 'سوبرماركت وفرع التموين', 'method' => 'نقد من الصندوق الرئيسي', 'desc' => 'نثريات وضيافة لاجتماع مجلس الإدارة'],
            ['no' => 'EXP-2026-004', 'cat' => 'اتصالات واشتراك إنترنت ألياف ضوئية', 'amt' => 180.00, 'date' => '2026-11-15', 'to' => 'شركة أورنج للاتصالات', 'method' => 'دفع إلكتروني إي فواتيركم', 'desc' => 'فاتورة الإنترنت السريع المركزي والخطوط الثابتة'],
            ['no' => 'EXP-2026-005', 'cat' => 'صيانة مركبات ووقود وبنزين', 'amt' => 210.00, 'date' => '2026-11-20', 'to' => 'محطة توتال المركزية', 'method' => 'بطاقة وقود مسبقة الدفع', 'desc' => 'تعبئة وقود وتغيير زيوت فان التوزيع'],
        ];
        foreach ($expenses as $exp) {
            DB::table('expenses')->insert([
                'tenant_id' => $tenantId,
                'expense_no' => $exp['no'],
                'category' => $exp['cat'],
                'amount' => $exp['amt'],
                'date' => $exp['date'],
                'paid_to' => $exp['to'],
                'payment_method' => $exp['method'],
                'description' => $exp['desc'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 16. POS Promotions & Campaigns (5 حملات تسويقية)
        $campaigns = [
            ['title' => 'عروض الجمعة البيضاء الشاملة', 'type' => 'general', 'rate' => 15.00, 'mult' => 1, 's' => '2026-11-20', 'e' => '2026-11-30', 'st' => 'active', 'notes' => 'خصم 15% على جميع الأصناف'],
            ['title' => 'مضاعفة نقاط الولاء للعملاء المميزين', 'type' => 'points', 'rate' => 0.00, 'mult' => 3, 's' => '2026-11-01', 'e' => '2026-12-31', 'st' => 'active', 'notes' => '3 نقاط لكل دينار مشتريات'],
            ['title' => 'خصم كوابل وإكسسوارات الشبكات', 'type' => 'period_discount', 'rate' => 10.00, 'mult' => 1, 's' => '2026-11-15', 'e' => '2026-11-25', 'st' => 'active', 'notes' => 'خصم فوري عند شراء لفتين فأكثر'],
            ['title' => 'حملة العودة للمدارس والجامعات', 'type' => 'cards_prices', 'rate' => 12.00, 'mult' => 2, 's' => '2026-09-01', 'e' => '2026-10-15', 'st' => 'expired', 'notes' => 'حملة موسمية منتهية'],
            ['title' => 'عروض نهاية العام ورأس السنة 2027', 'type' => 'general', 'rate' => 20.00, 'mult' => 2, 's' => '2026-12-20', 'e' => '2027-01-05', 'st' => 'scheduled', 'notes' => 'حملة مجدولة قادمة'],
        ];
        foreach ($campaigns as $camp) {
            DB::table('pos_campaigns')->insert([
                'tenant_id' => $tenantId,
                'title' => $camp['title'],
                'type' => $camp['type'],
                'discount_rate' => $camp['rate'],
                'points_multiplier' => $camp['mult'],
                'start_date' => $camp['s'],
                'end_date' => $camp['e'],
                'status' => $camp['st'],
                'notes' => $camp['notes'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 17. POS Tables & Dining (5 طاولات وقاعات POS)
        $tables = [
            ['no' => 'T-01', 'sec' => 'الصالة الرئيسية', 'cap' => 4, 'st' => 'occupied', 'wtr' => 'طارق كاشير', 'total' => 45.50],
            ['no' => 'T-02', 'sec' => 'الصالة الرئيسية', 'cap' => 6, 'st' => 'available', 'wtr' => null, 'total' => 0.00],
            ['no' => 'T-03', 'sec' => 'تراس الهواء الطلق', 'cap' => 2, 'st' => 'reserved', 'wtr' => 'عمر الجعفري', 'total' => 0.00],
            ['no' => 'T-04', 'sec' => 'صالة العائلات VIP', 'cap' => 8, 'st' => 'available', 'wtr' => null, 'total' => 0.00],
            ['no' => 'T-05', 'sec' => 'التراس الخارجي', 'cap' => 4, 'st' => 'available', 'wtr' => null, 'total' => 0.00],
        ];
        foreach ($tables as $t) {
            DB::table('pos_tables')->insert([
                'tenant_id' => $tenantId,
                'table_number' => $t['no'],
                'section' => $t['sec'],
                'capacity' => $t['cap'],
                'status' => $t['st'],
                'current_waiter' => $t['wtr'],
                'current_order_total' => $t['total'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 18. POS Shifts (5 ورديات كاشير)
        $shifts = [
            ['no' => 'SHF-001', 'cashier' => 'طارق كاشير', 'open' => '2026-11-20 08:00:00', 'close' => '2026-11-20 16:00:00', 'init' => 100.00, 'c_sale' => 680.00, 'v_sale' => 420.00, 'exp' => 780.00, 'act' => 780.00, 'diff' => 0.00, 'st' => 'closed', 'notes' => 'وردية صباحية متطابقة بالكامل'],
            ['no' => 'SHF-002', 'cashier' => 'أحمد العبادي', 'open' => '2026-11-20 16:00:00', 'close' => '2026-11-20 23:30:00', 'init' => 100.00, 'c_sale' => 950.00, 'v_sale' => 610.00, 'exp' => 1050.00, 'act' => 1050.00, 'diff' => 0.00, 'st' => 'closed', 'notes' => 'وردية مسائية مطابقة'],
            ['no' => 'SHF-003', 'cashier' => 'طارق كاشير', 'open' => '2026-11-21 08:00:00', 'close' => '2026-11-21 16:00:00', 'init' => 100.00, 'c_sale' => 540.00, 'v_sale' => 380.00, 'exp' => 640.00, 'act' => 640.00, 'diff' => 0.00, 'st' => 'closed', 'notes' => 'تسليم عهدة نظامي'],
            ['no' => 'SHF-004', 'cashier' => 'أحمد العبادي', 'open' => '2026-11-21 16:00:00', 'close' => '2026-11-21 23:45:00', 'init' => 100.00, 'c_sale' => 820.00, 'v_sale' => 510.00, 'exp' => 920.00, 'act' => 920.00, 'diff' => 0.00, 'st' => 'closed', 'notes' => 'وردية مسائية مكتملة'],
            ['no' => 'SHF-005', 'cashier' => 'طارق كاشير', 'open' => '2026-11-24 08:30:00', 'close' => null, 'init' => 100.00, 'c_sale' => 310.00, 'v_sale' => 190.00, 'exp' => 410.00, 'act' => null, 'diff' => 0.00, 'st' => 'open', 'notes' => 'الوردية الحالية المفتوحة اليوم'],
        ];
        foreach ($shifts as $s) {
            DB::table('pos_shifts')->insert([
                'tenant_id' => $tenantId,
                'shift_no' => $s['no'],
                'cashier_name' => $s['cashier'],
                'opened_at' => $s['open'],
                'closed_at' => $s['close'],
                'opening_cash' => $s['init'],
                'cash_sales' => $s['c_sale'],
                'card_sales' => $s['v_sale'],
                'expected_cash' => $s['exp'],
                'actual_cash' => $s['act'],
                'difference' => $s['diff'],
                'status' => $s['st'],
                'notes' => $s['notes'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 19. Phonebook (5 جهات اتصال في دليل الهاتف)
        $phonebook = [
            ['name' => 'البنك العربي - فرع الشميساني التجاري', 'cat' => 'بنك', 'phone' => '065600000', 'mobile' => '0799990001', 'company' => 'Arab Bank plc', 'city' => 'عمان', 'notes' => 'مسؤول الحسابات التجارية: السيد أيمن'],
            ['name' => 'دائرة ضريبة الدخل والمبيعات (ISTD)', 'cat' => 'جهة حكومية', 'phone' => '064603300', 'mobile' => '0799990002', 'company' => 'وزارة المالية الأردنية', 'city' => 'عمان', 'notes' => 'قسم الفوترة الإلكترونية والدعم الفني'],
            ['name' => 'شركة أورنج الأردن - خدمات الأعمال', 'cat' => 'اتصالات', 'phone' => '065500000', 'mobile' => '0777770003', 'company' => 'Orange Jordan', 'city' => 'عمان', 'notes' => 'الدعم الفني لخطوط الفايبر المستأجرة'],
            ['name' => 'م. لؤي الشريف - مستشار نظم أمن معلومات', 'cat' => 'شريك', 'phone' => '065820000', 'mobile' => '0788880004', 'company' => 'سايبر تك للحلول الأمنية', 'city' => 'عمان', 'notes' => 'مستشار تدقيق أمني للأنظمة السحابية'],
            ['name' => 'شركة التأمين الوطنية للضمان الصحي', 'cat' => 'تأمين', 'phone' => '065670000', 'mobile' => '0795550005', 'company' => 'National Insurance', 'city' => 'عمان', 'notes' => 'مسؤولة بوالص التأمين لموظفي الشركة'],
        ];
        foreach ($phonebook as $pb) {
            DB::table('phonebook')->insert([
                'tenant_id' => $tenantId,
                'name' => $pb['name'],
                'category' => $pb['cat'],
                'phone' => $pb['phone'],
                'mobile' => $pb['mobile'],
                'company' => $pb['company'],
                'city' => $pb['city'],
                'notes' => $pb['notes'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 20. Notepad Reminders (5 مهام وملاحظات في المفكرة)
        $notes = [
            ['title' => 'تقديم إقرار ضريبة المبيعات للشهر الحالي', 'prio' => 'urgent', 'due' => '2026-11-30', 'content' => 'مراجعة فواتير المبيعات والمشتريات وتطابق مع JoFotara قبل نهاية المهلة القانونية.', 'comp' => false],
            ['title' => 'جرد مستودع فرع إربد اللوجستي', 'prio' => 'high', 'due' => '2026-12-02', 'content' => 'مطابقة الأرصدة الفعلية للكوابل والشاشات مع السجلات الدفترية.', 'comp' => false],
            ['title' => 'تجديد وثيقة التأمين الصحي للموظفين', 'prio' => 'normal', 'due' => '2026-12-15', 'content' => 'إرسال قائمة الكادر المحدثة لشركة التأمين الوطنية لاعتماد الفئات.', 'comp' => false],
            ['title' => 'صيانة وفحص بطاريات خوادم الداتا سنتر UPS', 'prio' => 'normal', 'due' => '2026-12-05', 'content' => 'القيام بالفحص الدوري لنظام الطاقة البديلة ومولد الطوارئ.', 'comp' => true],
            ['title' => 'صرف رواتب الموظفين لشهر نوفمبر 2026', 'prio' => 'high', 'due' => '2026-11-28', 'content' => 'تدقيق ساعات الدوام والإضافي وتصدير ملف تحويل الرواتب للبنك.', 'comp' => false],
        ];
        foreach ($notes as $n) {
            DB::table('notepad_notes')->insert([
                'tenant_id' => $tenantId,
                'title' => $n['title'],
                'priority' => $n['prio'],
                'due_date' => $n['due'],
                'content' => $n['content'],
                'is_completed' => $n['comp'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 21. Hotel Guests Directory (5 نزلاء مسجلين في دليل النزلاء)
        $guests = [
            ['no' => 'GST-001', 'name' => 'معتز إبراهيم الخوالدة', 'nat' => '9851029381', 'bday' => '1985-04-12', 'addr' => 'إربد - الحي الشرقي', 'phone' => '0798881122', 'bl' => false, 'notes' => 'نزيل VIP دائم'],
            ['no' => 'GST-002', 'name' => 'د. فراس فهد التميمي', 'nat' => '9802049182', 'bday' => '1980-08-20', 'addr' => 'عمان - دابوق', 'phone' => '0789994433', 'bl' => false, 'notes' => 'طبيب زائر ومحاضر جامعي'],
            ['no' => 'GST-003', 'name' => 'عبدالله سمير الكردي', 'nat' => '9913049185', 'bday' => '1991-11-05', 'addr' => 'الزرقاء - الزرقاء الجديدة', 'phone' => '0775553311', 'bl' => false, 'notes' => 'حجز عبر الموقع'],
            ['no' => 'GST-004', 'name' => 'م. خالد فؤاد النابلسي', 'nat' => '9781039482', 'bday' => '1978-02-18', 'addr' => 'العقبة - الخامسة', 'phone' => '0791238899', 'bl' => false, 'notes' => 'عائلة مكونة من 4 أفراد'],
            ['no' => 'GST-005', 'name' => 'جون أندرسون (John Anderson)', 'nat' => 'PASSPORT-UK-88210', 'bday' => '1988-06-25', 'addr' => 'London, United Kingdom', 'phone' => '+447911122334', 'bl' => false, 'notes' => 'سائح أجنبي'],
        ];
        foreach ($guests as $g) {
            DB::table('hotel_guests')->insert([
                'guest_no' => $g['no'],
                'name' => $g['name'],
                'national_id' => $g['nat'],
                'birth_date' => $g['bday'],
                'address' => $g['addr'],
                'phone' => $g['phone'],
                'is_blacklisted' => $g['bl'],
                'notes' => $g['notes'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 22. Salary Adjustments (5 سندات تعديل وتحديد رواتب HR)
        $salAdjust = [
            ['no' => 'SAL-2026-001', 'date' => '2026-01-10', 'code' => 'EMP-101', 'name' => 'أحمد يوسف الصالح', 'm' => 1250.00, 'd' => 41.667, 'h' => 5.208, 'ot' => 7.812, 'dept' => 'الإدارة المالية'],
            ['no' => 'SAL-2026-002', 'date' => '2026-01-10', 'code' => 'EMP-102', 'name' => 'سارة إبراهيم القاسم', 'm' => 900.00, 'd' => 30.000, 'h' => 3.750, 'ot' => 5.625, 'dept' => 'الموارد البشرية (HR)'],
            ['no' => 'SAL-2026-003', 'date' => '2026-01-10', 'code' => 'EMP-103', 'name' => 'طارق زياد خليل', 'm' => 550.00, 'd' => 18.333, 'h' => 2.292, 'ot' => 3.438, 'dept' => 'إدارة المبيعات والتوزيع'],
            ['no' => 'SAL-2026-004', 'date' => '2026-01-10', 'code' => 'EMP-104', 'name' => 'م. ليلى النجار', 'm' => 1400.00, 'd' => 46.667, 'h' => 5.833, 'ot' => 8.750, 'dept' => 'تطوير الأنظمة'],
            ['no' => 'SAL-2026-005', 'date' => '2026-01-10', 'code' => 'EMP-105', 'name' => 'عمر الجعفري', 'm' => 650.00, 'd' => 21.667, 'h' => 2.708, 'ot' => 4.062, 'dept' => 'المستودعات واللوجستيات'],
        ];
        foreach ($salAdjust as $sa) {
            DB::table('salary_adjustments')->insert([
                'voucher_no' => $sa['no'],
                'date' => $sa['date'],
                'employee_code' => $sa['code'],
                'employee_name' => $sa['name'],
                'monthly_salary' => $sa['m'],
                'daily_salary' => $sa['d'],
                'hourly_salary' => $sa['h'],
                'overtime_rate' => $sa['ot'],
                'sales_commission' => 1.5,
                'collection_commission' => 0.5,
                'department' => $sa['dept'],
                'currency' => 'JOD',
                'weekend_day_1' => 'الجمعة',
                'weekend_day_2' => 'السبت',
                'notes' => 'سند اعتماد وبداية احتساب الراتب السنوي',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 23. Price Adjustment Vouchers (5 سندات تعديل أسعار بيع)
        $priceAdjust = [
            ['no' => 'PRC-2026-001', 'from' => '2026-11-01', 'to' => '2026-11-30', 'cat' => 'أجهزة ومعدات شبكات', 'brand' => 'Smart UPS', 'model' => '1500VA Pro', 'mfg' => 'APC / Schneider', 'st' => 'معتمد', 'notes' => 'تعديل أسعار بيع وحدات UPS بنسبة +5%'],
            ['no' => 'PRC-2026-002', 'from' => '2026-11-05', 'to' => '2026-12-31', 'cat' => 'كوابل وتمديدات', 'brand' => 'Cat6 UTP', 'model' => 'Gigabit Roll', 'mfg' => 'D-Link / Belden', 'st' => 'معتمد', 'notes' => 'تخفيض سعر الجملة للشركات'],
            ['no' => 'PRC-2026-003', 'from' => '2026-11-10', 'to' => '2026-12-15', 'cat' => 'أجهزة ومعدات نقاط البيع', 'brand' => 'Touch POS', 'model' => 'All-in-One 15.6', 'mfg' => 'Sunmi / Posiflex', 'st' => 'معتمد', 'notes' => 'عرض تشجيعي مع طابعة مجانية'],
            ['no' => 'PRC-2026-004', 'from' => '2026-11-15', 'to' => '2026-12-31', 'cat' => 'طابعات وملحقات كاشير', 'brand' => 'Thermal Printer', 'model' => '80mm USB/LAN', 'mfg' => 'Epson / Xprinter', 'st' => 'معتمد', 'notes' => 'تثبيت أسعار طابعات الإيصالات'],
            ['no' => 'PRC-2026-005', 'from' => '2026-11-20', 'to' => '2026-12-31', 'cat' => 'أجهزة ومعدات شبكات', 'brand' => 'Cisco Router', 'model' => 'Gigabit Dual-WAN', 'mfg' => 'Cisco Systems', 'st' => 'قيد المراجعة', 'notes' => 'تحديث تسعيرة المناقصات الحكومية'],
        ];
        foreach ($priceAdjust as $pa) {
            DB::table('price_adjustment_vouchers')->insert([
                'voucher_no' => $pa['no'],
                'from_date' => $pa['from'],
                'to_date' => $pa['to'],
                'category' => $pa['cat'],
                'brand' => $pa['brand'],
                'model' => $pa['model'],
                'manufacturer' => $pa['mfg'],
                'status' => $pa['st'],
                'notes' => $pa['notes'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
