<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$routes = [
    // 1. الواجهة الرئيسية
    '/' => 'الواجهة الرئيسية (Dashboard)',
    '/notepad' => 'المفكرة والمهام اليومية',
    '/phonebook' => 'دليل الهاتف وجهات الاتصال',

    // 2. تعريفات
    '/definitions' => 'مركز التعريفات والإعدادات',
    '/currencies' => 'العملات وأسعار الصرف',
    '/products' => 'بطاقة ودليل الأصناف والمخزون',
    '/products/create-editor' => 'إضافة صنف جديد',
    '/price-lists' => 'فئات سعر البيع',
    '/inventory/price-adjustment' => 'سند تعديل أسعار البيع',
    '/stock-count' => 'مطابقة وجرد المخزون',
    '/contacts' => 'بطاقة العملاء والموردين',
    '/contacts/create-editor' => 'إضافة عميل / مورد جديد',
    '/accounts' => 'شجرة ودليل الحسابات',
    '/fixed-assets' => 'بطاقة الأصول الثابتة والإهلاك',

    // 3. الفواتير والسندات
    '/invoicing-hub' => 'مركز الفواتير والسندات',
    '/invoices' => 'سجل فواتير المبيعات والمشتريات',
    '/invoices/create-editor' => 'تحرير فاتورة مبيعات',
    '/vouchers' => 'سجل سندات القبض والصرف',
    '/vouchers/create-editor' => 'محرر سند قبض مالي',
    '/vouchers/payment-editor' => 'محطة سند صرف مصروفات تفصيلية',
    '/journals/create-editor' => 'سند قيد يومية مزدوج',
    '/cheques' => 'حافظة الشيكات البنكية',
    '/dispatches' => 'الإرساليات ومذكرات التسليم',
    '/expenses' => 'سجل المصاريف التشغيلية',

    // 4. الضرائب والإقرار
    '/taxes' => 'مركز الضرائب والإقرار الفصلي',
    '/jofotara' => 'الفوترة الوطنية (JoFotara CTC)',

    // 5. الموارد البشرية (HR)
    '/hr' => 'سجل الموظفين والرواتب',
    '/hr/salary-editor' => 'محرر تعديل الراتب والبدلات والإضافي',
    '/hr/tax-exempt' => 'سجل الإعفاءات الضريبية والضمان',

    // 6. إدارة الفنادق
    '/hotels' => 'مخطط الغرف الفندقية (Room Rack)',
    '/hotels/guests' => 'سجل واستعلام النزلاء',

    // 7. نقاط البيع (POS)
    '/pos' => 'واجهة كاشير POS المباشرة',
    '/pos/hub' => 'مركز نقاط البيع',
    '/pos/shifts' => 'ورديات الكاشير وتسليم العهدة',
    '/pos/tables' => 'صالات وطاولات المطاعم والمقاهي',
    '/pos/campaigns' => 'الحملات الترويجية والعروض',

    // 8. التقارير المالية
    '/reports' => 'التقارير المالية والختامية',
    '/reports/export-excel' => 'تصدير التقارير إلى Excel',

    // 9. تخصيص الواجهة والإدارة
    '/admin' => 'لوحة الإدارة وتخصيص صلاحيات المستخدمين',
    '/customizer' => 'تخصيص بيانات وهوية المشترك والشركة'
];

echo "========================================================================\n";
echo "   WATHEEQ ERP - COMPLETE SUB-SCREENS & BUTTONS ROUTING AUDIT           \n";
echo "========================================================================\n";

$passCount = 0;
$failCount = 0;

$httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

foreach ($routes as $uri => $title) {
    $req = Illuminate\Http\Request::create($uri, 'GET');
    // Start session and set active user
    $session = $app->make('session')->driver();
    $session->setId('test-session-id');
    $session->start();
    $session->put('active_user_id', 1);
    $req->setLaravelSession($session);

    $res = $httpKernel->handle($req);
    $status = $res->getStatusCode();
    
    if ($status === 200 || $status === 302) {
        $passCount++;
        printf("  [PASS] %-35s -> %-38s (HTTP %d)\n", $uri, $title, $status);
    } else {
        $failCount++;
        printf("  [FAIL] %-35s -> %-38s (HTTP %d)\n", $uri, $title, $status);
    }
}

echo "========================================================================\n";
echo "  AUDIT RESULTS: TOTAL: " . count($routes) . " | PASSED: {$passCount} | FAILED: {$failCount}\n";
echo "========================================================================\n";
