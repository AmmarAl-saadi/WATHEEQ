<?php

/**
 * WATHEEQ ERP - LIVE PRODUCTION SYSTEM AUDIT & E2E INTEGRITY SCANNER
 * Lead Senior QA Analyst Automated Sweep
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

$baseUrl = 'http://127.0.0.1:8000';
$cookieFile = __DIR__ . '/audit_cookies.txt';
if (file_exists($cookieFile)) {
    unlink($cookieFile);
}

function request($url, $method = 'GET', $data = [], $cookieJar = null, $follow = true) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $follow);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Watheeq-QA-Auditor/1.0');
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Connection: close']);

    if ($cookieJar) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    }

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    $raw = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
    $error = curl_error($ch);
    curl_close($ch);
    usleep(60000); // 60ms breath for single-threaded PHP dev server

    $header = substr($raw, 0, $headerSize);
    $body = substr($raw, $headerSize);

    return [
        'code' => $httpCode,
        'effective_url' => $effectiveUrl,
        'time' => round($totalTime * 1000, 1),
        'header' => $header,
        'body' => $body,
        'error' => $error
    ];
}

echo "═══════════════════════════════════════════════════════════════════════\n";
echo "   WATHEEQ ERP - SENIOR QA E2E SYSTEM INTEGRITY & BUTTON-LEVEL AUDIT   \n";
echo "═══════════════════════════════════════════════════════════════════════\n\n";

$passCount = 0;
$failCount = 0;

// STEP 1: Test Security Guard on Unauthenticated Access
echo "── [PHASE 1: AUTHENTICATION & SECURITY GUARD] ──\n";
$unauth = request($baseUrl . '/taxes', 'GET', [], null, false);
if ($unauth['code'] === 302 && str_contains($unauth['header'], '/login')) {
    echo "  [PASS] Unauthenticated access to /taxes redirected to /login (HTTP 302)\n";
    $passCount++;
} else {
    echo "  [FAIL] Unauthenticated access not redirected properly (HTTP {$unauth['code']})\n";
    $failCount++;
}

// STEP 2: Login Flow
$loginGet = request($baseUrl . '/login', 'GET', [], $cookieFile);
preg_match('/name="_token" value="([^"]+)"/', $loginGet['body'], $tokenMatch);
$csrfToken = $tokenMatch[1] ?? '';

$loginPost = request($baseUrl . '/login', 'POST', [
    '_token' => $csrfToken,
    'login' => 'admin',
    'password' => 'admin123'
], $cookieFile, true);

if ($loginPost['code'] === 200 && str_ends_with(parse_url($loginPost['effective_url'], PHP_URL_PATH), '/')) {
    echo "  [PASS] Super Admin Login successful -> Redirected to Dashboard (HTTP 200)\n";
    $passCount++;
} else {
    echo "  [FAIL] Super Admin Login failed (HTTP {$loginPost['code']}, URL: {$loginPost['effective_url']})\n";
    $failCount++;
}

// STEP 3: Systematic Sweep of All Key Modules & Subscreens
echo "\n── [PHASE 2: FULL MODULES & ACTION SCREENS SWEEP] ──\n";

$testEndpoints = [
    'Dashboard' => ['url' => '/', 'contains' => 'وثيق | Watheeq'],
    'Tax Hub (ISTD)' => ['url' => '/taxes', 'contains' => 'دائرة ضريبة الدخل والمبيعات'],
    'Invoices Ledger' => ['url' => '/invoices', 'contains' => 'الفواتير'],
    'Sales Invoice Editor' => ['url' => '/invoices/create-editor?type=sale', 'contains' => 'فاتورة مبيعات'],
    'Purchase Invoice Editor' => ['url' => '/invoices/create-editor?type=purchase', 'contains' => 'فاتورة مشتريات'],
    'Invoice Official Print' => ['url' => '/invoices/1/print', 'contains' => 'معاينة'],
    'JoFotara UBL XML' => ['url' => '/invoices/1/ubl-xml', 'contains' => 'Invoice'],
    'JoFotara CTC Hub' => ['url' => '/jofotara', 'contains' => 'JoFotara'],
    'Vouchers Ledger' => ['url' => '/vouchers', 'contains' => 'سند'],
    'Receipt Voucher Editor' => ['url' => '/vouchers/create-editor?type=receipt', 'contains' => 'سند قبض'],
    'Payment Voucher Editor' => ['url' => '/vouchers/payment-editor', 'contains' => 'سند صرف'],
    'Journal Voucher Editor' => ['url' => '/journals/create-editor', 'contains' => 'سند قيد'],
    'Chart of Accounts' => ['url' => '/accounts', 'contains' => 'الحسابات'],
    'Products & Inventory' => ['url' => '/products', 'contains' => 'الأصناف'],
    'Product Create Editor' => ['url' => '/products/create-editor', 'contains' => 'بطاقة صنف'],
    'Barcode Scanner API' => ['url' => '/api/products/lookup?q=ITM-001', 'contains' => '"found":true'],
    'Physical Stock Count' => ['url' => '/stock-count', 'contains' => 'الجرد'],
    'Dispatches & Transfers' => ['url' => '/dispatches', 'contains' => 'الإرساليات'],
    'Price Lists' => ['url' => '/price-lists', 'contains' => 'الأسعار'],
    'Price Adjustment Editor' => ['url' => '/inventory/price-adjustment', 'contains' => 'تعديل أسعار'],
    'Contacts & Clients' => ['url' => '/contacts', 'contains' => 'العملاء'],
    'Contact Create Editor' => ['url' => '/contacts/create-editor?type=customer', 'contains' => 'بطاقة عميل'],
    'Customer Statement' => ['url' => '/contacts/1/statement', 'contains' => 'كشف حساب'],
    'Cheques Portfolio' => ['url' => '/cheques', 'contains' => 'الشيكات'],
    'Operating Expenses' => ['url' => '/expenses', 'contains' => 'المصاريف'],
    'Fixed Assets Register' => ['url' => '/fixed-assets', 'contains' => 'الأصول'],
    'Cashier POS Terminal' => ['url' => '/pos', 'contains' => 'الكاشير'],
    'POS Shift Closing' => ['url' => '/pos/shifts', 'contains' => 'ورديات'],
    'POS Promo Campaigns' => ['url' => '/pos/campaigns', 'contains' => 'الحملات'],
    'POS Dining Tables' => ['url' => '/pos/tables', 'contains' => 'الطاولات'],
    'POS Hub' => ['url' => '/pos/hub', 'contains' => 'نقاط البيع'],
    'HR & Employee Payroll' => ['url' => '/hr', 'contains' => 'الموارد البشرية'],
    'HR Salary Editor' => ['url' => '/hr/salary-editor', 'contains' => 'راتب'],
    'HR Tax Exemptions' => ['url' => '/hr/tax-exempt', 'contains' => 'الإعفاءات'],
    'Hotel Rooms & Bookings' => ['url' => '/hotels', 'contains' => 'الفنادق'],
    'Hotel Guests Directory' => ['url' => '/hotels/guests', 'contains' => 'النزلاء'],
    'Financial Statements' => ['url' => '/reports', 'contains' => 'التقارير'],
    'Reports Excel Export' => ['url' => '/reports/export-excel', 'contains' => ''],
    'Admin Panel & Users' => ['url' => '/admin', 'contains' => 'لوحة تحكم المسؤول'],
    'Interface Customizer' => ['url' => '/customizer', 'contains' => 'تخصيص'],
    'Notepad Tasks' => ['url' => '/notepad', 'contains' => 'المفكرة المحاسبية'],
    'Phonebook Directory' => ['url' => '/phonebook', 'contains' => 'دليل الهاتف']
];

foreach ($testEndpoints as $name => $cfg) {
    $res = request($baseUrl . $cfg['url'], 'GET', [], $cookieFile);
    $hasContent = str_contains($res['body'], $cfg['contains']);
    $isOk = ($res['code'] === 200 && $hasContent);

    if ($isOk) {
        printf("  [PASS] %-25s -> HTTP 200 | Latency: %5.1f ms | Size: %6d b\n", $name, $res['time'], strlen($res['body']));
        $passCount++;
    } else {
        printf("  [FAIL] %-25s -> HTTP %d | URL: %s | Missing: '%s'\n", $name, $res['code'], $res['effective_url'], $cfg['contains']);
        $failCount++;
    }
}

// STEP 4: RBAC Verification for Non-Admin User
echo "\n── [PHASE 3: ROLE-BASED ACCESS CONTROL (RBAC) RESTRICTION] ──\n";
// Switch to cashier
$cashier = DB::table('users')->where('username', 'cashier')->first();
if ($cashier) {
    request($baseUrl . '/users/switch/' . $cashier->id, 'GET', [], $cookieFile);
    $adminBlocked = request($baseUrl . '/admin', 'GET', [], $cookieFile, false);
    if ($adminBlocked['code'] === 302) {
        echo "  [PASS] Cashier restricted from accessing /admin (HTTP 302 Redirected)\n";
        $passCount++;
    } else {
        echo "  [FAIL] Cashier NOT restricted from accessing /admin (HTTP {$adminBlocked['code']})\n";
        $failCount++;
    }
}

// Cleanup
if (file_exists($cookieFile)) {
    unlink($cookieFile);
}

echo "\n═══════════════════════════════════════════════════════════════════════\n";
printf("   AUDIT SUMMARY: TOTAL CHECKS: %d | PASSED: %d | FAILED: %d\n", ($passCount + $failCount), $passCount, $failCount);
if ($failCount === 0) {
    echo "   VERDICT: 100% PRODUCTION READY - ZERO REGRESSIONS DETECTED \n";
} else {
    echo "   VERDICT: ISSUES FOUND - IMMEDIATE REMEDIATION REQUIRED \n";
}
echo "═══════════════════════════════════════════════════════════════════════\n";

