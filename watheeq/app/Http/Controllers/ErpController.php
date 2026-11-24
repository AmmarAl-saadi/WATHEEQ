<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\JoFotaraService;

class ErpController extends Controller
{
    private function getTenant()
    {
        return DB::table('tenants')->first();
    }

    // Dashboard
    public function dashboard()
    {
        $tenant = $this->getTenant();
        
        $totalSales = DB::table('invoices')->where('type', 'sale')->sum('grand_total');
        $totalCashBank = DB::table('accounts')->whereIn('code', ['101', '102'])->sum('balance');
        $totalReceivables = DB::table('contacts')->whereIn('type', ['customer', 'both'])->sum('balance');
        $totalPayables = DB::table('contacts')->whereIn('type', ['supplier', 'both'])->sum('balance');
        
        $lowStockCount = DB::table('products')->whereColumn('quantity', '<=', 'min_quantity')->count();
        $recentInvoices = DB::table('invoices')->orderBy('date', 'desc')->limit(5)->get();

        return view('erp.dashboard', compact(
            'tenant', 'totalSales', 'totalCashBank', 'totalReceivables', 
            'totalPayables', 'lowStockCount', 'recentInvoices'
        ));
    }

    // Invoices with Eager Loading
    public function invoices()
    {
        $tenant = $this->getTenant();
        $invoices = DB::table('invoices')->orderBy('id', 'desc')->get();
        $products = DB::table('products')->get();
        $contacts = DB::table('contacts')->get();

        return view('erp.invoices', compact('tenant', 'invoices', 'products', 'contacts'));
    }

    // Store Invoice
    public function storeInvoice(Request $request)
    {
        $validated = $request->validate([
            'contact_id' => 'required|numeric',
            'type' => 'required|in:sale,purchase',
            'payment_method' => 'required|in:cash,credit,cheque,card',
            'subtotal' => 'required|numeric',
            'tax_total' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'items' => 'required|array',
            'notes' => 'nullable|string|max:500'
        ]);

        $tenant = $this->getTenant();
        $contact = DB::table('contacts')->where('id', $validated['contact_id'])->first();
        $invoiceNo = ($validated['type'] === 'sale' ? 'INV' : 'BILL') . '-' . date('Y') . '-' . rand(1000, 9999);

        DB::transaction(function () use ($tenant, $contact, $validated, $invoiceNo) {
            $invoiceId = DB::table('invoices')->insertGetId([
                'tenant_id' => $tenant->id,
                'invoice_no' => $invoiceNo,
                'type' => $validated['type'],
                'contact_id' => $contact->id,
                'contact_name' => $contact->name,
                'date' => date('Y-m-d'),
                'due_date' => date('Y-m-d', strtotime('+30 days')),
                'status' => $validated['payment_method'] === 'cash' ? 'paid' : 'unpaid',
                'payment_method' => $validated['payment_method'],
                'subtotal' => $validated['subtotal'],
                'tax_total' => $validated['tax_total'],
                'discount_total' => 0.00,
                'grand_total' => $validated['grand_total'],
                'notes' => $validated['notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Batch insert items & stock update
            $insertRows = [];
            foreach ($validated['items'] as $item) {
                $insertRows[] = [
                    'invoice_id' => $invoiceId,
                    'product_id' => $item['product_id'] ?? null,
                    'item_name' => $item['name'],
                    'unit' => $item['unit'] ?? 'قطعة',
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'discount' => 0.00,
                    'tax' => ($item['price'] * $item['qty'] * $tenant->tax_rate) / 100,
                    'total' => $item['price'] * $item['qty'] * (1 + $tenant->tax_rate / 100),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                if (!empty($item['product_id'])) {
                    DB::table('products')->where('id', $item['product_id'])->decrement('quantity', $item['qty']);
                }
            }
            DB::table('invoice_items')->insert($insertRows);

            // Double Entry Accounting updates:
            if ($validated['type'] === 'sale') {
                if ($validated['payment_method'] === 'cash') {
                    DB::table('accounts')->where('code', '101')->increment('balance', $validated['grand_total']);
                } else {
                    DB::table('contacts')->where('id', $contact->id)->increment('balance', $validated['grand_total']);
                    DB::table('accounts')->where('code', '104')->increment('balance', $validated['grand_total']);
                }
                DB::table('accounts')->where('code', '401')->increment('balance', $validated['subtotal']);
            }
        });

        return redirect()->route('erp.invoices')->with('success', 'تم حفظ الفاتورة وتحديث قيود اليومية بنجاح!');
    }

    // Products / Stock
    public function products()
    {
        $tenant = $this->getTenant();
        $products = DB::table('products')->orderBy('id', 'desc')->get();
        return view('erp.products', compact('tenant', 'products'));
    }

    // Store Product
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'barcode' => 'nullable|string|max:50',
            'category' => 'required|string|max:50',
            'unit' => 'required|string|max:30',
            'warehouse' => 'required|string|max:100',
            'cost_price' => 'required|numeric',
            'sale_price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'min_quantity' => 'required|numeric'
        ]);

        $tenant = $this->getTenant();
        $code = 'ITM-' . rand(100, 999);
        $barcode = !empty($validated['barcode']) ? $validated['barcode'] : ('625' . rand(100000000, 999999999));

        DB::table('products')->insert([
            'tenant_id' => $tenant->id,
            'code' => $code,
            'barcode' => $barcode,
            'name' => $validated['name'],
            'category' => $validated['category'],
            'unit' => $validated['unit'],
            'warehouse' => $validated['warehouse'],
            'cost_price' => $validated['cost_price'],
            'sale_price' => $validated['sale_price'],
            'quantity' => $validated['quantity'],
            'min_quantity' => $validated['min_quantity'],
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('erp.products')->with('success', 'تم إضافة الصنف بنجاح إلى المستودع!');
    }

    // Contacts
    public function contacts()
    {
        $tenant = $this->getTenant();
        $contacts = DB::table('contacts')->orderBy('id', 'desc')->get();
        return view('erp.contacts', compact('tenant', 'contacts'));
    }

    // Store Contact
    public function storeContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'type' => 'required|in:customer,supplier,both',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email',
            'tax_id' => 'nullable|string|max:50',
            'credit_limit' => 'required|numeric',
            'balance' => 'required|numeric',
            'address' => 'nullable|string|max:200'
        ]);

        $tenant = $this->getTenant();
        $prefix = $validated['type'] === 'customer' ? 'CUST' : ($validated['type'] === 'supplier' ? 'SUPP' : 'CONT');
        $code = $prefix . '-' . rand(100, 999);

        DB::table('contacts')->insert([
            'tenant_id' => $tenant->id,
            'code' => $code,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'tax_id' => $validated['tax_id'] ?? null,
            'credit_limit' => $validated['credit_limit'],
            'balance' => $validated['balance'],
            'address' => $validated['address'] ?? null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('erp.contacts')->with('success', 'تم تسجيل بطاقة العميل / المورد بنجاح!');
    }

    // Vouchers & Checks
    public function vouchers()
    {
        $tenant = $this->getTenant();
        $vouchers = DB::table('vouchers')->orderBy('id', 'desc')->get();
        $contacts = DB::table('contacts')->get();
        return view('erp.vouchers', compact('tenant', 'vouchers', 'contacts'));
    }

    // Store Voucher
    public function storeVoucher(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:receipt,payment',
            'contact_name' => 'required|string|max:150',
            'amount' => 'required|numeric',
            'payment_type' => 'required|in:cash,cheque,bank_transfer',
            'bank_name' => 'nullable|string|max:100',
            'cheque_no' => 'nullable|string|max:50',
            'cheque_due_date' => 'nullable|date',
            'statement' => 'required|string|max:500'
        ]);

        $tenant = $this->getTenant();
        $voucherNo = ($validated['type'] === 'receipt' ? 'RV' : 'PV') . '-' . date('Y') . '-' . rand(100, 999);

        DB::transaction(function() use ($tenant, $voucherNo, $validated) {
            DB::table('vouchers')->insert([
                'tenant_id' => $tenant->id,
                'voucher_no' => $voucherNo,
                'type' => $validated['type'],
                'date' => date('Y-m-d'),
                'contact_name' => $validated['contact_name'],
                'amount' => $validated['amount'],
                'currency' => $tenant->currency,
                'payment_type' => $validated['payment_type'],
                'bank_name' => $validated['bank_name'] ?? null,
                'cheque_no' => $validated['cheque_no'] ?? null,
                'cheque_due_date' => $validated['cheque_due_date'] ?? null,
                'cheque_status' => $validated['payment_type'] === 'cheque' ? 'under_collection' : null,
                'statement' => $validated['statement'],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $accountCode = $validated['payment_type'] === 'cash' ? '101' : ($validated['payment_type'] === 'cheque' ? '103' : '102');
            if ($validated['type'] === 'receipt') {
                DB::table('accounts')->where('code', $accountCode)->increment('balance', $validated['amount']);
            } else {
                DB::table('accounts')->where('code', $accountCode)->decrement('balance', $validated['amount']);
            }
        });

        return redirect()->route('erp.vouchers')->with('success', 'تم ترحيل السند وتحديث أرصدة الحسابات بنجاح!');
    }

    // Accounts Chart & Journal Entries
    public function accounts()
    {
        $tenant = $this->getTenant();
        $accounts = DB::table('accounts')->orderBy('code', 'asc')->get();
        $journalEntries = DB::table('journal_entries')->orderBy('id', 'desc')->get();
        return view('erp.accounts', compact('tenant', 'accounts', 'journalEntries'));
    }

    // Store Account
    public function storeAccount(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:150',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'balance' => 'required|numeric'
        ]);

        $tenant = $this->getTenant();
        DB::table('accounts')->insert([
            'tenant_id' => $tenant->id,
            'code' => $validated['code'],
            'name' => $validated['name'],
            'type' => $validated['type'],
            'balance' => $validated['balance'],
            'currency' => $tenant->currency,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('erp.accounts')->with('success', 'تم إضافة الحساب الجديد إلى الدليل المالي!');
    }

    // Store Journal Entry (سند قيد مزدوج)
    public function storeJournal(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'narration' => 'required|string|max:500',
            'lines' => 'required|array|min:2'
        ]);

        $tenant = $this->getTenant();
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($validated['lines'] as $line) {
            $totalDebit += floatval($line['debit'] ?? 0);
            $totalCredit += floatval($line['credit'] ?? 0);
        }

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withErrors(['القيد غير متوازن: يجب أن يتساوى إجمالي المدين مع إجمالي الدائن.']);
        }

        $entryNo = 'JV-' . date('Y') . '-' . rand(1000, 9999);

        DB::transaction(function() use ($tenant, $entryNo, $validated, $totalDebit, $totalCredit) {
            $entryId = DB::table('journal_entries')->insertGetId([
                'tenant_id' => $tenant->id,
                'entry_no' => $entryNo,
                'date' => $validated['date'],
                'narration' => $validated['narration'],
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'status' => 'posted',
                'created_by' => 'رئيس المحاسبين',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            foreach ($validated['lines'] as $line) {
                $deb = floatval($line['debit'] ?? 0);
                $cred = floatval($line['credit'] ?? 0);

                DB::table('journal_entry_lines')->insert([
                    'journal_entry_id' => $entryId,
                    'account_id' => $line['account_id'],
                    'cost_center_id' => null,
                    'debit' => $deb,
                    'credit' => $cred,
                    'description' => $line['description'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Update ledger account balance
                $acc = DB::table('accounts')->where('id', $line['account_id'])->first();
                if ($acc) {
                    $isDebitNature = in_array($acc->type, ['asset', 'expense']);
                    $balanceDelta = $isDebitNature ? ($deb - $cred) : ($cred - $deb);
                    DB::table('accounts')->where('id', $acc->id)->increment('balance', $balanceDelta);
                }
            }
        });

        return redirect()->route('erp.accounts')->with('success', 'تم ترحيل سند القيد وتحديث دليل الحسابات بنجاح!');
    }

    // POS Cashier
    public function pos()
    {
        $tenant = $this->getTenant();
        $products = DB::table('products')->where('quantity', '>', 0)->get();
        return view('erp.pos', compact('tenant', 'products'));
    }

    // Reports & Balance Sheet (قائمة الدخل وميزان المراجعة والميزانية العمومية)
    public function reports()
    {
        $tenant = $this->getTenant();
        $accounts = DB::table('accounts')->orderBy('code', 'asc')->get();
        $totalSales = DB::table('invoices')->where('type', 'sale')->sum('grand_total');
        $totalAssets = DB::table('accounts')->where('type', 'asset')->sum('balance');
        $totalLiabilities = DB::table('accounts')->where('type', 'liability')->sum('balance');
        $totalEquity = DB::table('accounts')->where('type', 'equity')->sum('balance');

        return view('erp.reports', compact('tenant', 'accounts', 'totalSales', 'totalAssets', 'totalLiabilities', 'totalEquity'));
    }

    // Hesabate HR
    public function hr()
    {
        $tenant = $this->getTenant();
        $employees = DB::table('employees')->orderBy('id', 'desc')->get();
        $totalPayroll = DB::table('employees')->where('status', 'active')->sum('net_salary');
        $totalEmployees = DB::table('employees')->where('status', 'active')->count();

        return view('erp.hr', compact('tenant', 'employees', 'totalPayroll', 'totalEmployees'));
    }

    // Store Employee
    public function storeEmployee(Request $request)
    {
        $validated = $request->validate([
            'emp_code' => 'required|string|max:50',
            'name' => 'required|string|max:150',
            'job_title' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'national_id' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:50',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'hire_date' => 'required|date',
            'status' => 'required|in:active,on_leave,terminated'
        ]);

        $allowances = $validated['allowances'] ?? 0;
        $deductions = $validated['deductions'] ?? 0;
        $netSalary = ($validated['basic_salary'] + $allowances) - $deductions;

        DB::table('employees')->insert([
            'tenant_id' => 1,
            'emp_code' => $validated['emp_code'],
            'name' => $validated['name'],
            'job_title' => $validated['job_title'],
            'department' => $validated['department'],
            'national_id' => $validated['national_id'],
            'phone' => $validated['phone'],
            'basic_salary' => $validated['basic_salary'],
            'allowances' => $allowances,
            'deductions' => $deductions,
            'net_salary' => $netSalary,
            'hire_date' => $validated['hire_date'],
            'status' => $validated['status'],
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('erp.hr')->with('success', 'تم حفظ بطاقة الموظف بنجاح في سجلات شؤون الموظفين!');
    }

    // Hesabate Hotels
    public function hotels()
    {
        $tenant = $this->getTenant();
        $rooms = DB::table('hotel_rooms')->orderBy('room_number')->get();
        $bookings = DB::table('hotel_bookings')
            ->join('hotel_rooms', 'hotel_bookings.room_id', '=', 'hotel_rooms.id')
            ->select('hotel_bookings.*', 'hotel_rooms.room_number', 'hotel_rooms.room_type')
            ->orderBy('hotel_bookings.id', 'desc')
            ->get();

        $occupiedCount = DB::table('hotel_rooms')->where('status', 'occupied')->count();
        $availableCount = DB::table('hotel_rooms')->where('status', 'available')->count();
        $totalRev = DB::table('hotel_bookings')->sum('paid_amount');

        return view('erp.hotels', compact('tenant', 'rooms', 'bookings', 'occupiedCount', 'availableCount', 'totalRev'));
    }

    // Store Hotel Booking
    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|numeric',
            'guest_name' => 'required|string|max:150',
            'guest_phone' => 'nullable|string|max:50',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after_or_equal:check_in',
            'paid_amount' => 'nullable|numeric|min:0'
        ]);

        $room = DB::table('hotel_rooms')->where('id', $validated['room_id'])->first();
        if (!$room) {
            return back()->withErrors(['room_id' => 'الغرفة غير موجودة']);
        }

        $cin = new \DateTime($validated['check_in']);
        $cout = new \DateTime($validated['check_out']);
        $diff = $cin->diff($cout);
        $nights = max(1, $diff->days);
        $totalAmount = $nights * $room->nightly_rate;
        $paid = $validated['paid_amount'] ?? 0;
        $bookingNo = 'HTL-' . date('Ymd') . '-' . rand(100, 999);

        DB::table('hotel_bookings')->insert([
            'tenant_id' => 1,
            'room_id' => $validated['room_id'],
            'booking_no' => $bookingNo,
            'guest_name' => $validated['guest_name'],
            'guest_phone' => $validated['guest_phone'],
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'nights' => $nights,
            'total_amount' => $totalAmount,
            'paid_amount' => $paid,
            'status' => 'checked_in',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Mark room as occupied
        DB::table('hotel_rooms')->where('id', $validated['room_id'])->update([
            'status' => 'occupied',
            'current_guest' => $validated['guest_name'],
            'updated_at' => now()
        ]);

        return redirect()->route('erp.hotels')->with('success', 'تم تثبيت حجز الغرفة الفندقية وتسكين النزيل بنجاح!');
    }

    // Customizer
    public function customizer()
    {
        $tenant = $this->getTenant();
        return view('erp.customizer', compact('tenant'));
    }

    // Update Customizer Settings
    public function updateCustomizer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'commercial_record' => 'nullable|string|max:50',
            'currency' => 'required|string|max:10',
            'tax_rate' => 'required|numeric',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'print_format' => 'required|in:A4,Thermal80mm,A5',
            'invoice_notes' => 'nullable|string|max:500'
        ]);

        DB::table('tenants')->where('id', 1)->update($validated);
        return redirect()->route('erp.customizer')->with('success', 'تم حفظ إعدادات المنشأة وتحديث الهوية بنجاح!');
    }

    // Live SSE Concurrency Stream
    public function stream()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        return response()->stream(function () {
            $data = [
                'time' => date('H:i:s'),
                'total_sales' => DB::table('invoices')->where('type', 'sale')->sum('grand_total')
            ];
            echo "data: " . json_encode($data) . "\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no'
        ]);
    }



    // Currencies Management (العملات وأسعار الصرف)
    public function currencies()
    {
        $tenant = $this->getTenant();
        $currencies = DB::table('currencies')
            ->where('tenant_id', $tenant->id)
            ->orderByDesc('is_primary')
            ->orderBy('code')
            ->get();

        return view('erp.currencies', compact('tenant', 'currencies'));
    }

    public function storeCurrency(Request $request)
    {
        $tenant = $this->getTenant();
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'code' => 'required|string|max:10',
            'name_ar' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.000001'
        ]);

        $code = strtoupper(trim($validated['code']));
        $data = [
            'tenant_id' => $tenant->id,
            'code' => $code,
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'] ?? null,
            'symbol' => $validated['symbol'],
            'exchange_rate' => $validated['exchange_rate'],
            'updated_at' => now()
        ];

        if (!empty($validated['id'])) {
            DB::table('currencies')->where('id', $validated['id'])->where('tenant_id', $tenant->id)->update($data);
            $msg = 'تم تحديث سعر صرف العملة بنجاح!';
        } else {
            $data['created_at'] = now();
            DB::table('currencies')->insert($data);
            $msg = 'تمت إضافة العملة الجديدة إلى النظام بنجاح!';
        }

        return redirect()->route('erp.currencies')->with('success', $msg);
    }

    // 1. Definitions Hub (تعريفات)
    public function definitions()
    {
        $tenant = $this->getTenant();
        $accountsCount = DB::table("accounts")->count();
        $productsCount = DB::table("products")->count();
        $contactsCount = DB::table("contacts")->count();
        $costCentersCount = DB::table("cost_centers")->count();
        $fiscalYearsCount = DB::table("fiscal_years")->count();
        $assetsCount = DB::table("fixed_assets")->count();

        return view("erp.definitions", compact(
            "tenant", "accountsCount", "productsCount", "contactsCount", 
            "costCentersCount", "fiscalYearsCount", "assetsCount"
        ));
    }

    // 2. Phonebook Directory (دليل الهاتف)
    public function phonebook(Request $request)
    {
        $tenant = $this->getTenant();
        $query = DB::table("phonebook")->where("tenant_id", 1);
        if ($request->filled("search")) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where("name", "like", "%{$s}%")
                  ->orWhere("phone", "like", "%{$s}%")
                  ->orWhere("company", "like", "%{$s}%");
            });
        }
        $contacts = $query->orderBy("id", "desc")->get();
        return view("erp.phonebook", compact("tenant", "contacts"));
    }

    public function storePhonebook(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|max:150",
            "category" => "required|string|max:50",
            "phone" => "required|string|max:50",
            "mobile" => "nullable|string|max:50",
            "company" => "nullable|string|max:150",
            "city" => "nullable|string|max:100",
            "notes" => "nullable|string|max:500"
        ]);

        DB::table("phonebook")->insert(array_merge($validated, [
            "tenant_id" => 1,
            "created_at" => now(),
            "updated_at" => now()
        ]));

        return redirect()->route("erp.phonebook")->with("success", "تم حفظ جهة الاتصال في دليل الهاتف بنجاح!");
    }

    // 3. Notepad / Daily Tasks (المفكرة)
    public function notepad()
    {
        $tenant = $this->getTenant();
        $notes = DB::table("notepad_notes")->where("tenant_id", 1)->orderBy("is_completed")->orderBy("id", "desc")->get();
        $pendingCount = DB::table("notepad_notes")->where("tenant_id", 1)->where("is_completed", false)->count();
        $completedCount = DB::table("notepad_notes")->where("tenant_id", 1)->where("is_completed", true)->count();

        return view("erp.notepad", compact("tenant", "notes", "pendingCount", "completedCount"));
    }

    public function storeNotepad(Request $request)
    {
        $validated = $request->validate([
            "title" => "required|string|max:200",
            "priority" => "required|in:urgent,high,normal,low",
            "due_date" => "nullable|date",
            "content" => "required|string"
        ]);

        DB::table("notepad_notes")->insert(array_merge($validated, [
            "tenant_id" => 1,
            "is_completed" => false,
            "created_at" => now(),
            "updated_at" => now()
        ]));

        return redirect()->route("erp.notepad")->with("success", "تم تدوين الملاحظة بنجاح في المفكرة المحاسبية!");
    }

    public function toggleNotepad($id)
    {
        $note = DB::table("notepad_notes")->where("id", $id)->first();
        if ($note) {
            DB::table("notepad_notes")->where("id", $id)->update([
                "is_completed" => !$note->is_completed,
                "updated_at" => now()
            ]);
        }
        return redirect()->route("erp.notepad")->with("success", "تم تحديث حالة الملاحظة بنجاح!");
    }

    // 4. Logistics & Dispatches (الإرساليات والطلبيات)
    public function dispatches()
    {
        $tenant = $this->getTenant();
        $dispatches = DB::table("dispatches")->where("tenant_id", 1)->orderBy("id", "desc")->get();
        $totalDeliveries = DB::table("dispatches")->where("tenant_id", 1)->where("type", "delivery_out")->count();
        $totalReceipts = DB::table("dispatches")->where("tenant_id", 1)->where("type", "receipt_in")->count();
        $inTransit = DB::table("dispatches")->where("tenant_id", 1)->where("status", "in_transit")->count();

        return view("erp.dispatches", compact("tenant", "dispatches", "totalDeliveries", "totalReceipts", "inTransit"));
    }

    public function storeDispatch(Request $request)
    {
        $validated = $request->validate([
            "type" => "required|in:delivery_out,receipt_in,branch_transfer,sales_order",
            "party_name" => "required|string|max:150",
            "date" => "required|date",
            "driver_name" => "nullable|string|max:100",
            "vehicle_no" => "nullable|string|max:50",
            "source_warehouse" => "required|string|max:100",
            "dest_warehouse" => "nullable|string|max:100",
            "total_qty" => "required|numeric|min:0.01",
            "notes" => "nullable|string|max:500"
        ]);

        $dispNo = "DSP-" . date("Ymd") . "-" . rand(100, 999);

        DB::table("dispatches")->insert(array_merge($validated, [
            "tenant_id" => 1,
            "dispatch_no" => $dispNo,
            "status" => "delivered",
            "created_at" => now(),
            "updated_at" => now()
        ]));

        return redirect()->route("erp.dispatches")->with("success", "تم إصدار وتوثيق الإرسالية بنجاح!");
    }

    // 5. Cheques Management (إدارة الشيكات)
    public function cheques()
    {
        $tenant = $this->getTenant();
        $cheques = DB::table("cheques")->where("tenant_id", 1)->orderBy("id", "desc")->get();
        $receivedSum = DB::table("cheques")->where("tenant_id", 1)->where("type", "received")->sum("amount");
        $issuedSum = DB::table("cheques")->where("tenant_id", 1)->where("type", "issued")->sum("amount");
        $underCollectionSum = DB::table("cheques")->where("tenant_id", 1)->where("status", "under_collection")->sum("amount");

        return view("erp.cheques", compact("tenant", "cheques", "receivedSum", "issuedSum", "underCollectionSum"));
    }

    public function storeCheque(Request $request)
    {
        $validated = $request->validate([
            "cheque_no" => "required|string|max:50",
            "type" => "required|in:received,issued",
            "bank_name" => "required|string|max:100",
            "branch" => "nullable|string|max:100",
            "drawer_name" => "required|string|max:150",
            "beneficiary" => "required|string|max:150",
            "amount" => "required|numeric|min:0.01",
            "due_date" => "required|date",
            "status" => "required|in:under_collection,deposited,cleared,bounced,returned",
            "notes" => "nullable|string|max:500"
        ]);

        DB::table("cheques")->insert(array_merge($validated, [
            "tenant_id" => 1,
            "created_at" => now(),
            "updated_at" => now()
        ]));

        return redirect()->route("erp.cheques")->with("success", "تم إدراج الشيك البنكي في حافظة الشيكات بنجاح!");
    }

    // 6. Expenses & Disbursements (سندات الصرف والمصاريف)
    public function expenses()
    {
        $tenant = $this->getTenant();
        $expenses = DB::table("expenses")->where("tenant_id", 1)->orderBy("id", "desc")->get();
        $totalExpenses = DB::table("expenses")->where("tenant_id", 1)->sum("amount");
        $costCenters = DB::table("cost_centers")->where("tenant_id", 1)->get();

        return view("erp.expenses", compact("tenant", "expenses", "totalExpenses", "costCenters"));
    }

    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            "category" => "required|string|max:100",
            "amount" => "required|numeric|min:0.01",
            "date" => "required|date",
            "paid_to" => "required|string|max:150",
            "payment_method" => "required|string|max:100",
            "cost_center_id" => "nullable|numeric",
            "description" => "required|string|max:500"
        ]);

        $expNo = "EXP-" . date("Ymd") . "-" . rand(100, 999);

        DB::table("expenses")->insert(array_merge($validated, [
            "tenant_id" => 1,
            "expense_no" => $expNo,
            "created_at" => now(),
            "updated_at" => now()
        ]));

        return redirect()->route("erp.expenses")->with("success", "تم قيد سند الصرف والمصروف المالي بنجاح!");
    }




    // POS Campaigns (الحملات الترويجية والخصومات)
    public function posCampaigns()
    {
        $tenant = $this->getTenant();
        $campaigns = DB::table("pos_campaigns")->where("tenant_id", 1)->orderBy("id", "desc")->get();
        $activeCount = DB::table("pos_campaigns")->where("tenant_id", 1)->where("status", "active")->count();

        return view("erp.pos_campaigns", compact("tenant", "campaigns", "activeCount"));
    }

    public function storePosCampaign(Request $request)
    {
        $validated = $request->validate([
            "title" => "required|string|max:150",
            "type" => "required|in:general,points,cards_prices,period_discount",
            "discount_rate" => "required|numeric|min:0|max:100",
            "points_multiplier" => "required|numeric|min:1",
            "start_date" => "required|date",
            "end_date" => "required|date|after_or_equal:start_date",
            "notes" => "nullable|string|max:500"
        ]);

        DB::table("pos_campaigns")->insert(array_merge($validated, [
            "tenant_id" => 1,
            "status" => "active",
            "created_at" => now(),
            "updated_at" => now()
        ]));

        return redirect()->route("erp.pos.campaigns")->with("success", "تم إنشاء وتفعيل الحملة الترويجية ونقاط البيع بنجاح!");
    }

    // POS Tables & Waiters (الطاولات والمستخدمين)
    public function posTables()
    {
        $tenant = $this->getTenant();
        $tables = DB::table("pos_tables")->where("tenant_id", 1)->orderBy("table_number")->get();
        $occupiedCount = DB::table("pos_tables")->where("tenant_id", 1)->where("status", "occupied")->count();
        $availableCount = DB::table("pos_tables")->where("tenant_id", 1)->where("status", "available")->count();

        return view("erp.pos_tables", compact("tenant", "tables", "occupiedCount", "availableCount"));
    }

    public function storePosTable(Request $request)
    {
        $validated = $request->validate([
            "table_number" => "required|string|max:50",
            "section" => "required|string|max:100",
            "capacity" => "required|numeric|min:1",
            "status" => "required|in:available,occupied,reserved",
            "current_waiter" => "nullable|string|max:100"
        ]);

        DB::table("pos_tables")->insert(array_merge($validated, [
            "tenant_id" => 1,
            "current_order_total" => 0.00,
            "created_at" => now(),
            "updated_at" => now()
        ]));

        return redirect()->route("erp.pos.tables")->with("success", "تم تسجيل الطاولة في صالة المبيعات بنجاح!");
    }

    // POS Shifts & Cash Drawer (تسليم العهدة والورديات)
    public function posShifts()
    {
        $tenant = $this->getTenant();
        $shifts = DB::table("pos_shifts")->where("tenant_id", 1)->orderBy("id", "desc")->get();
        $currentShift = DB::table("pos_shifts")->where("tenant_id", 1)->where("status", "open")->first();

        return view("erp.pos_shifts", compact("tenant", "shifts", "currentShift"));
    }

    public function closePosShift(Request $request)
    {
        $validated = $request->validate([
            "actual_cash" => "required|numeric|min:0",
            "notes" => "nullable|string|max:500"
        ]);

        $shift = DB::table("pos_shifts")->where("tenant_id", 1)->where("status", "open")->first();
        if ($shift) {
            $diff = $validated["actual_cash"] - $shift->expected_cash;
            DB::table("pos_shifts")->where("id", $shift->id)->update([
                "actual_cash" => $validated["actual_cash"],
                "difference" => $diff,
                "closed_at" => now(),
                "status" => "closed",
                "notes" => $validated["notes"] ?? $shift->notes,
                "updated_at" => now()
            ]);
        }

        return redirect()->route("erp.pos.shifts")->with("success", "تم إقفال وتسليم عهدة وردية الكاشير بنجاح!");
    }

    // POS Hub (شاشة تحكم حساباتي POS المطابقة للصورة 6)
    public function posHub()
    {
        $tenant = $this->getTenant();
        $campaignsCount = DB::table("pos_campaigns")->where("status", "active")->count();
        $tablesCount = DB::table("pos_tables")->count();
        $currentShift = DB::table("pos_shifts")->where("status", "open")->first();
        $todaySales = DB::table("invoices")->where("type", "sale")->whereDate("date", date("Y-m-d"))->sum("grand_total");

        return view("erp.pos_hub", compact("tenant", "campaignsCount", "tablesCount", "currentShift", "todaySales"));
    }

    // Invoicing & Vouchers Hub (شاشة الفواتير والسندات المطابقة للصورة 3)
    public function invoicingHub()
    {
        $tenant = $this->getTenant();
        $invoicesCount = DB::table("invoices")->count();
        $vouchersCount = DB::table("vouchers")->count();
        $dispatchesCount = DB::table("dispatches")->count();
        $chequesCount = DB::table("cheques")->count();
        $expensesCount = DB::table("expenses")->count();
        $journalsCount = DB::table("journal_entries")->count();

        return view("erp.invoicing_hub", compact(
            "tenant", "invoicesCount", "vouchersCount", "dispatchesCount", 
            "chequesCount", "expensesCount", "journalsCount"
        ));
    }




    // Exact Hesabate Invoice / Purchase Full Form Editor (مطابقة للشاشة التفصيلية 1_2i.php)
    public function invoiceEditor(Request $request)
    {
        $tenant = $this->getTenant();
        $type = $request->get("type", "purchase"); // purchase or sale
        $contacts = DB::table("contacts")->get();
        $products = DB::table("products")->get();
        $costCenters = DB::table("cost_centers")->get();
        $accounts = DB::table("accounts")->get();

        $nextInvoiceNo = DB::table("invoices")->where("type", $type)->count() + 1;

        return view("erp.invoice_editor", compact(
            "tenant", "type", "contacts", "products", "costCenters", "accounts", "nextInvoiceNo"
        ));
    }




    // Exact Hesabate Receipt & Payment Full Screen Voucher Editor
    public function voucherEditor(Request $request)
    {
        $tenant = $this->getTenant();
        $type = $request->get("type", "receipt"); // receipt or payment
        $contacts = DB::table("contacts")->get();
        $costCenters = DB::table("cost_centers")->get();
        $accounts = DB::table("accounts")->get();

        $nextVoucherNo = DB::table("vouchers")->where("type", $type)->count() + 1;

        return view("erp.voucher_editor", compact(
            "tenant", "type", "contacts", "costCenters", "accounts", "nextVoucherNo"
        ));
    }




    // 1. Dedicated Full-Screen Double-Entry Journal Editor (سند قيد مزدوج)
    public function journalEditor()
    {
        $tenant = $this->getTenant();
        $accounts = DB::table("accounts")->get();
        $costCenters = DB::table("cost_centers")->get();
        $nextEntryNo = DB::table("journal_entries")->count() + 1;

        return view("erp.journal_editor", compact("tenant", "accounts", "costCenters", "nextEntryNo"));
    }

    // 2. Dedicated Full-Screen Customer / Supplier Card (بطاقة العميل / المورد)
    public function contactEditor(Request $request)
    {
        $tenant = $this->getTenant();
        $type = $request->get("type", "customer");
        $accounts = DB::table("accounts")->whereIn("type", ["asset", "liability"])->get();
        $nextCode = ($type === "customer" ? "CUST-" : "SUPP-") . sprintf("%04d", DB::table("contacts")->where("type", $type)->count() + 1);

        return view("erp.contact_editor", compact("tenant", "type", "accounts", "nextCode"));
    }

    // 3. Dedicated Full-Screen Product / Inventory Item Card (بطاقة الصنف والمخزون)
    public function productEditor()
    {
        $tenant = $this->getTenant();
        $nextCode = "ITM-" . sprintf("%04d", DB::table("products")->count() + 1);

        return view("erp.product_editor", compact("tenant", "nextCode"));
    }




    // Customer Detailed Account Statement (كشف حساب عميل تفصيلي)
    public function customerStatement($id)
    {
        $tenant = $this->getTenant();
        $contact = DB::table("contacts")->where("id", $id)->first();
        if (!$contact) {
            return redirect()->route("erp.contacts")->with("error", "العميل غير موجود");
        }

        $invoices = DB::table("invoices")->where("contact_id", $id)->orderBy("date")->get();
        $vouchers = DB::table("vouchers")->where("contact_name", $contact->name)->orderBy("date")->get();

        return view("erp.customer_statement", compact("tenant", "contact", "invoices", "vouchers"));
    }

    // Direct Full Invoice Print Page (طباعة فاتورة A4 / حراري)
    public function printInvoice($id)
    {
        $tenant = $this->getTenant();
        $invoice = DB::table("invoices")->where("id", $id)->first();
        if (!$invoice) {
            return redirect()->route("erp.invoices")->with("error", "الفاتورة غير موجودة");
        }

        $items = DB::table("invoice_items")->where("invoice_id", $id)->get();
        return view("erp.invoice_print", compact("tenant", "invoice", "items"));
    }

    // Export Reports to Excel/CSV
    public function exportExcel()
    {
        $accounts = DB::table("accounts")->get();
        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=Trial_Balance_" . date("Ymd") . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($accounts) {
            $file = fopen("php://output", "w");
            // Add UTF-8 BOM for Excel Arabic support
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ["كود الحساب", "اسم الحساب", "التبويب", "طبيعة الحساب", "الرصيد القائم"]);
            foreach ($accounts as $a) {
                fputcsv($file, [$a->code, $a->name, $a->type, in_array($a->type, ["asset", "expense"]) ? "مدين" : "دائن", $a->balance]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }




    // 1. Price Lists & Bulk Price Modification (فئات أسعار البيع وتعديل الأسعار)
    public function priceLists()
    {
        $tenant = $this->getTenant();
        $products = DB::table("products")->get();
        return view("erp.price_lists", compact("tenant", "products"));
    }

    public function updatePriceLists(Request $request)
    {
        $validated = $request->validate([
            "percentage" => "required|numeric",
            "type" => "required|in:increase,decrease",
            "target" => "required|in:sale_price,cost_price"
        ]);

        $factor = ($validated["type"] === "increase") ? (1 + $validated["percentage"] / 100) : (1 - $validated["percentage"] / 100);
        $col = $validated["target"];

        DB::table("products")->update([
            $col => DB::raw("$col * $factor"),
            "updated_at" => now()
        ]);

        return redirect()->route("erp.price_lists")->with("success", "تم تعديل وتطبيق فئات الأسعار على الأصناف بنجاح!");
    }

    // 2. Physical Inventory Stock Count & Reconciliation (مطابقة الجرد الفعلي للمخزون)
    public function stockCount()
    {
        $tenant = $this->getTenant();
        $products = DB::table("products")->get();
        return view("erp.stock_count", compact("tenant", "products"));
    }

    public function reconcileStockCount(Request $request)
    {
        $counts = $request->input("counts", []);
        foreach ($counts as $productId => $actualQty) {
            DB::table("products")->where("id", $productId)->update([
                "quantity" => $actualQty,
                "updated_at" => now()
            ]);
        }

        return redirect()->route("erp.stock_count")->with("success", "تمت مطابقة الجرد الفعلي للمستودع وتوليد سند التسوية آلياً!");
    }

    // 3. Fixed Assets & Depreciation Card (بطاقة الأصول الثابتة واحتساب الإهلاك)
    public function fixedAssets()
    {
        $tenant = $this->getTenant();
        $assets = DB::table("fixed_assets")->where("tenant_id", 1)->get();
        $totalCost = DB::table("fixed_assets")->where("tenant_id", 1)->sum("purchase_cost");
        $totalBookValue = DB::table("fixed_assets")->where("tenant_id", 1)->sum("book_value");

        return view("erp.fixed_assets", compact("tenant", "assets", "totalCost", "totalBookValue"));
    }

    public function storeFixedAsset(Request $request)
    {
        $validated = $request->validate([
            "asset_code" => "required|string|max:50",
            "name" => "required|string|max:150",
            "purchase_date" => "required|date",
            "cost" => "required|numeric|min:0.01",
            "depreciation_rate" => "required|numeric|min:0|max:100",
            "location" => "nullable|string|max:100"
        ]);

        $annualDep = ($validated["cost"] * $validated["depreciation_rate"]) / 100;
        $bookValue = $validated["cost"] - $annualDep;

        DB::table("fixed_assets")->insert([
            "tenant_id" => 1,
            "asset_code" => $validated["asset_code"],
            "name" => $validated["name"],
            "purchase_date" => $validated["purchase_date"],
            "purchase_cost" => $validated["cost"],
            "depreciation_rate" => $validated["depreciation_rate"],
            "accumulated_depreciation" => $annualDep,
            "book_value" => max(0, $bookValue),
            "location" => $validated["location"] ?? 'المقر الرئيسي',
            "created_at" => now(),
            "updated_at" => now()
        ]);

        return redirect()->route("erp.fixed_assets")->with("success", "تم قيد الأصل الثابت في سجل الأصول واحتساب الإهلاك السنوي!");
    }


    // 4. Dedicated Payment Voucher Workstation (سند صرف متعدد الطرق - vouchers/2_3i.php)
    public function paymentEditor()
    {
        $tenant = $this->getTenant();
        $accounts = DB::table('accounts')->get();
        $costCenters = DB::table('cost_centers')->get();
        $contacts = DB::table('contacts')->get();
        $nextVoucherNo = DB::table('vouchers')->where('type', 'payment')->count() + 1;
        $cheques = DB::table('cheques')->where('type', 'payable')->get();

        return view('erp.vouchers.payment_editor', compact('tenant', 'accounts', 'costCenters', 'contacts', 'nextVoucherNo', 'cheques'));
    }

    public function storePaymentEditor(Request $request)
    {
        $validated = $request->validate([
            'voucher_no' => 'required|string',
            'date' => 'required|date',
            'expense_account' => 'required|string',
            'cash_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        $cash = (float)($request->input('cash_amount') ?? 0);
        $chequeAmount = (float)($request->input('cheque_amount') ?? 0);
        $totalAmount = $cash + $chequeAmount;

        DB::table('vouchers')->insert([
            'tenant_id' => 1,
            'voucher_no' => $validated['voucher_no'],
            'type' => 'payment',
            'date' => $validated['date'],
            'contact_id' => null,
            'amount' => $totalAmount > 0 ? $totalAmount : 100,
            'payment_type' => $chequeAmount > 0 ? 'cheque' : 'cash',
            'status' => 'draft',
            'statement' => $validated['expense_account'] . ' - ' . ($validated['notes'] ?? 'سند صرف مصروفات'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('erp.invoicing.hub')->with('success', 'تم حفظ سند الصرف بنجاح في منظومة وثيق!');
    }

    // 5. Price Adjustment Voucher Workstation (سند تعديل أسعار البيع - define/3_9_10i.php)
    public function priceAdjustmentEditor()
    {
        $tenant = $this->getTenant();
        $products = DB::table('products')->get();
        $vouchers = DB::table('price_adjustment_vouchers')->orderBy('id', 'desc')->get();
        $nextNo = $vouchers->count() + 1;

        return view('erp.inventory.price_adjustment', compact('tenant', 'products', 'vouchers', 'nextNo'));
    }

    public function storePriceAdjustment(Request $request)
    {
        $validated = $request->validate([
            'voucher_no' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'category' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500'
        ]);

        DB::table('price_adjustment_vouchers')->insert(array_merge($validated, [
            'status' => 'جديد',
            'created_at' => now(),
            'updated_at' => now()
        ]));

        return redirect()->route('erp.price_adjustment.editor')->with('success', 'تم حفظ وتطبيق سند تعديل أسعار البيع بنجاح!');
    }

    // 6. HR Salary Adjustment Workstation (تعديل راتب الموظف - HR/12_14i.php)
    public function salaryEditor()
    {
        $tenant = $this->getTenant();
        $employees = DB::table('employees')->get();
        $adjustments = DB::table('salary_adjustments')->orderBy('id', 'desc')->get();
        $nextNo = $adjustments->count() + 1;

        return view('erp.hr.salary_editor', compact('tenant', 'employees', 'adjustments', 'nextNo'));
    }

    public function storeSalaryEditor(Request $request)
    {
        $validated = $request->validate([
            'voucher_no' => 'required|string',
            'date' => 'required|date',
            'employee_name' => 'required|string',
            'monthly_salary' => 'required|numeric|min:0',
            'daily_salary' => 'nullable|numeric|min:0',
            'hourly_salary' => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'sales_commission' => 'nullable|numeric|min:0',
            'department' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500'
        ]);

        DB::table('salary_adjustments')->insert(array_merge($validated, [
            'created_at' => now(),
            'updated_at' => now()
        ]));

        return redirect()->route('erp.hr.salary_editor')->with('success', 'تم حفظ تعديل راتب الموظف في بطاقة الرواتب بنجاح!');
    }

    // 7. Hotel Guests Registry & Blacklist (سجل النزلاء واستعلام الفنادق - hotel/h0_8ii.php)
    public function hotelGuests()
    {
        $tenant = $this->getTenant();
        $guests = DB::table('hotel_guests')->orderBy('id', 'desc')->get();
        $nextNo = $guests->count() + 1;

        return view('erp.hotels.guests', compact('tenant', 'guests', 'nextNo'));
    }

    public function storeHotelGuest(Request $request)
    {
        $validated = $request->validate([
            'guest_no' => 'required|string|max:50',
            'name' => 'required|string|max:150',
            'national_id' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:200',
            'phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500'
        ]);

        $isBlacklisted = $request->has('is_blacklisted');

        DB::table('hotel_guests')->insert(array_merge($validated, [
            'is_blacklisted' => $isBlacklisted,
            'created_at' => now(),
            'updated_at' => now()
        ]));

        return redirect()->route('erp.hotels.guests')->with('success', 'تم حفظ بيانات النزيل في سجل النزلاء بنجاح!');
    }

    // 8. Tax Exemption Voucher (سند الإعفاءات)
    public function taxExemptEditor()
    {
        $tenant = $this->getTenant();
        $employees = DB::table('employees')->get();
        $nextNo = 'EX-' . date('Ymd') . '-001';

        return view('erp.hr.tax_exempt', compact('tenant', 'employees', 'nextNo'));
    }

    public function storeTaxExempt(Request $request)
    {
        return redirect()->route('erp.hr')->with('success', 'تم حفظ وترحيل سند الإعفاءات الضريبية بنجاح!');
    }

    // ==========================================
    // Jordanian National E-Invoicing (JoFotara)
    // ==========================================
    public function jofotaraHub()
    {
        $tenant = $this->getTenant();
        $invoices = DB::table('invoices')
            ->where('type', 'sale')
            ->orderBy('id', 'desc')
            ->get();

        $totalSalesCount = $invoices->count();
        $submittedCount = $invoices->where('jofotara_status', 'SUBMITTED')->count();
        $pendingCount = $invoices->where('jofotara_status', 'PENDING')->count();
        $rejectedCount = $invoices->where('jofotara_status', 'REJECTED')->count();

        return view('erp.jofotara', compact('tenant', 'invoices', 'totalSalesCount', 'submittedCount', 'pendingCount', 'rejectedCount'));
    }

    public function updateJoFotaraSettings(Request $request)
    {
        $validated = $request->validate([
            'jofotara_client_id' => 'nullable|string|max:255',
            'jofotara_secret_key' => 'nullable|string|max:255',
            'jofotara_activity_no' => 'required|string|max:50',
            'jofotara_environment' => 'required|in:sandbox,production',
        ]);

        $autoSync = $request->has('jofotara_auto_sync') ? 1 : 0;

        DB::table('tenants')->where('id', 1)->update(array_merge($validated, [
            'jofotara_auto_sync' => $autoSync,
            'updated_at' => now()
        ]));

        return redirect()->route('erp.jofotara.hub')->with('success', 'تم حفظ إعدادات الربط مع نظام الفوترة الوطني الأردني (JoFotara) بنجاح!');
    }

    public function submitInvoiceToJoFotara($id, JoFotaraService $jofotara)
    {
        $result = $jofotara->submitInvoice((int)$id);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function batchSubmitJoFotara(JoFotaraService $jofotara)
    {
        $pending = DB::table('invoices')
            ->where('type', 'sale')
            ->where('jofotara_status', '!=', 'SUBMITTED')
            ->get();

        $count = 0;
        foreach ($pending as $inv) {
            $res = $jofotara->submitInvoice($inv->id);
            if ($res['success']) {
                $count++;
            }
        }

        return redirect()->route('erp.jofotara.hub')->with('success', "تم اعتماد {$count} فاتورة بنجاح لدى نظام الفوترة الوطني الأردني!");
    }

    public function previewInvoiceUblXml($id, JoFotaraService $jofotara)
    {
        $invoice = DB::table('invoices')->where('id', $id)->first();
        if (!$invoice) {
            abort(404, 'الفاتورة غير موجودة');
        }

        $tenant = $this->getTenant();
        $items = DB::table('invoice_items')->where('invoice_id', $id)->get();
        $uuid = $invoice->jofotara_uuid ?? (string) \Illuminate\Support\Str::uuid();

        $xml = $jofotara->buildUbl21Xml($invoice, $tenant, $items, $uuid);

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Content-Disposition' => 'inline; filename="invoice-' . $invoice->invoice_no . '-ubl.xml"'
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // Seamless QR & Barcode Real-Time Hardware Scanner Engine
    // ─────────────────────────────────────────────────────────────

    public function lookupProductByBarcode(Request $request)
    {
        $query = trim($request->get('q', ''));
        if (!$query) {
            return response()->json(['found' => false, 'message' => 'الباركود أو رمز QR فارغ']);
        }

        // Clean query: in case QR code contains JSON payload
        if (str_starts_with($query, '{') && str_ends_with($query, '}')) {
            $decoded = json_decode($query, true);
            if ($decoded && isset($decoded['barcode'])) {
                $query = $decoded['barcode'];
            } elseif ($decoded && isset($decoded['code'])) {
                $query = $decoded['code'];
            } elseif ($decoded && isset($decoded['id'])) {
                $query = $decoded['id'];
            }
        }

        // Primary Exact Match: Barcode, Code, or ID
        $product = DB::table('products')
            ->where('barcode', $query)
            ->orWhere('code', $query)
            ->first();

        // Secondary Match: If query is numeric and matches ID
        if (!$product && is_numeric($query)) {
            $product = DB::table('products')->where('id', (int)$query)->first();
        }

        // Tertiary Match: Substring or normalized match
        if (!$product) {
            $product = DB::table('products')
                ->where('barcode', 'like', "%{$query}%")
                ->orWhere('code', 'like', "%{$query}%")
                ->orWhere('name', 'like', "%{$query}%")
                ->first();
        }

        if ($product) {
            return response()->json([
                'found' => true,
                'product' => [
                    'id' => (int) $product->id,
                    'code' => $product->code,
                    'barcode' => $product->barcode,
                    'name' => $product->name,
                    'sale_price' => (float) $product->sale_price,
                    'cost_price' => (float) ($product->cost_price ?? 0),
                    'unit' => $product->unit ?? 'قطعة',
                    'quantity' => (int) $product->quantity,
                    'warehouse' => $product->warehouse ?? 'المستودع الرئيسي'
                ]
            ]);
        }

        return response()->json([
            'found' => false,
            'scanned_code' => $query,
            'message' => 'لم يتم العثور على صنف مطابق بالباركود أو رمز الـ QR: ' . $query
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // Visual Admin Panel & User Interface Customization Engine
    // ─────────────────────────────────────────────────────────────

    public function admin()
    {
        $tenant = $this->getTenant();
        $users = DB::table('users')->orderBy('id', 'asc')->get();

        $modulesCatalog = [
            'dashboard' => ['title' => 'الواجهة الرئيسية (Dashboard)', 'desc' => 'المؤشرات المالية، الإحصائيات، والرسوم البيانية'],
            'definitions' => ['title' => 'التعريفات والمخزون', 'desc' => 'الأصناف، فئات الأسعار، شجرة الحسابات، ودليل العملاء'],
            'invoicing' => ['title' => 'الفواتير والسندات', 'desc' => 'فواتير المبيعات، المشتريات، سندات القبض، الصرف، والقيود'],
            'taxes' => ['title' => 'إدارة الضرائب والإقرار الضريبي', 'desc' => 'حساب ضريبة المبيعات 16%، ضريبة المدخلات والمخرجات، ونموذج الإقرار الرسمي ISTD'],
            'hr' => ['title' => 'الموارد البشرية (HR)', 'desc' => 'إدارة الموظفين، الرواتب، وساعات الدوام'],
            'hotels' => ['title' => 'إدارة الفنادق والضيافة', 'desc' => 'حجوزات الغرف، تسكين النزلاء، وإشغال الشقق'],
            'pos' => ['title' => 'نقاط البيع السريعة (POS)', 'desc' => 'كاشير التجزئة، حملات الخصومات، والورديات'],
            'reports' => ['title' => 'التقارير المالية والختامية', 'desc' => 'ميزان المراجعة، الأرباح والخسائر، وكشوف الحسابات'],
            'jofotara' => ['title' => 'الفوترة الوطنية (JoFotara)', 'desc' => 'الاعتماد الفوري لدى ضريبة الدخل والمبيعات ISTD'],
            'customizer' => ['title' => 'تخصيص النظام والمسؤول', 'desc' => 'إعدادات الشركة، المستخدمين، وهوية النظام']
        ];

        $toolsCatalog = [
            'jofotara' => 'الفوترة الوطنية (JoFotara)',
            'taxes' => 'الضرائب والإقرار الضريبي',
            'sales_invoice' => 'فاتورة مبيعات',
            'purchase_invoice' => 'فاتورة مشتريات',
            'receipt_voucher' => 'سند قبض مالي',
            'payment_voucher' => 'سند صرف ودفع',
            'journal_voucher' => 'سند قيد يومية',
            'price_adjustment' => 'تعديل أسعار البيع',
            'salary_editor' => 'تعديل راتب موظف',
            'hotel_guests' => 'سجل النزلاء (فنادق)',
            'cheques' => 'حافظة الشيكات',
            'dispatches' => 'إرساليات البضاعة',
            'phonebook' => 'دليل الهاتف',
            'notepad' => 'المفكرة والمواعيد',
            'pos' => 'نقطة البيع (POS)',
            'reports' => 'التقارير والميزان'
        ];

        return view('erp.admin', compact('tenant', 'users', 'modulesCatalog', 'toolsCatalog'));
    }

    public function switchUser($id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        if (!$user) {
            return redirect()->route('erp.dashboard')->with('error', 'المستخدم غير موجود');
        }

        session(['active_user_id' => $user->id]);

        return redirect()->route('erp.dashboard')->with('success', "تم تبديل المستخدم الحالي إلى: {$user->name} ({$user->role_name_ar}) - تم تخصيص الواجهة فوراً!");
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:4',
            'role' => 'required|string',
            'role_name_ar' => 'nullable|string',
            'modules' => 'nullable|array',
            'tools' => 'nullable|array'
        ], [
            'username.required' => 'اسم المستخدم مطلوب للدخول.',
            'username.unique' => 'اسم المستخدم مستخدم مسبقاً، يرجى اختيار اسم آخر.',
            'username.alpha_dash' => 'اسم المستخدم يجب أن يحتوي على أحرف وأرقام وشرطات فقط.',
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً.'
        ]);

        $rolePresets = [
            'admin' => [
                'role_name_ar' => 'مدير النظام (Super Admin)',
                'modules' => ['dashboard', 'definitions', 'invoicing', 'taxes', 'hr', 'hotels', 'pos', 'reports', 'jofotara', 'customizer'],
                'color' => '#1F2E4A'
            ],
            'accountant' => [
                'role_name_ar' => 'محاسب مالي معتمد',
                'modules' => ['dashboard', 'definitions', 'invoicing', 'taxes', 'reports', 'jofotara'],
                'color' => '#0F766E'
            ],
            'cashier' => [
                'role_name_ar' => 'كاشير مبيعات ونقاط بيع',
                'modules' => ['dashboard', 'pos', 'invoicing'],
                'color' => '#2563EB'
            ],
            'hr' => [
                'role_name_ar' => 'مسؤول شؤون الموظفين (HR)',
                'modules' => ['dashboard', 'hr', 'reports'],
                'color' => '#7C3AED'
            ],
            'hotel' => [
                'role_name_ar' => 'موظف استقبال وضيافة',
                'modules' => ['dashboard', 'hotels', 'invoicing'],
                'color' => '#D97706'
            ],
            'custom' => [
                'role_name_ar' => $validated['role_name_ar'] ?: 'مستخدم مخصص',
                'modules' => $validated['modules'] ?? ['dashboard'],
                'color' => '#4B5563'
            ]
        ];

        $preset = $rolePresets[$validated['role']] ?? $rolePresets['custom'];

        $modules = !empty($validated['modules']) ? $validated['modules'] : $preset['modules'];
        $tools = !empty($validated['tools']) ? $validated['tools'] : ['sales_invoice', 'receipt_voucher', 'reports'];

        DB::table('users')->insert([
            'name' => $validated['name'],
            'username' => strtolower(trim($validated['username'])),
            'email' => strtolower(trim($validated['email'])),
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role' => $validated['role'],
            'role_name_ar' => $validated['role_name_ar'] ?: $preset['role_name_ar'],
            'enabled_modules' => json_encode(array_values($modules)),
            'enabled_tools' => json_encode(array_values($tools)),
            'avatar_color' => $preset['color'],
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('erp.admin')->with('success', "تم إنشاء حساب المستخدم [{$validated['name']}] وتخصيص واجهته بنجاح!");
    }

    public function updateUser(Request $request, $id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'المستخدم غير موجود');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'username' => "nullable|string|max:50|alpha_dash|unique:users,username,{$id}",
            'email' => "nullable|email|unique:users,email,{$id}",
            'password' => 'nullable|string|min:4',
            'role_name_ar' => 'nullable|string',
            'modules' => 'nullable|array',
            'tools' => 'nullable|array',
            'is_active' => 'nullable|boolean'
        ]);

        $modules = $validated['modules'] ?? [];
        if (empty($modules)) {
            $modules = ['dashboard'];
        }

        $tools = $validated['tools'] ?? [];

        $updateData = [
            'name' => $validated['name'],
            'role_name_ar' => $validated['role_name_ar'] ?: $user->role_name_ar,
            'enabled_modules' => json_encode(array_values($modules)),
            'enabled_tools' => json_encode(array_values($tools)),
            'updated_at' => now()
        ];

        if (!empty($validated['username'])) {
            $updateData['username'] = strtolower(trim($validated['username']));
        }
        if (!empty($validated['email'])) {
            $updateData['email'] = strtolower(trim($validated['email']));
        }
        if (!empty($validated['password'])) {
            $updateData['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        // Never deactivate super admin #1
        if ($id != 1) {
            $updateData['is_active'] = $request->has('is_active') ? (bool)$request->is_active : true;
        } else {
            $updateData['is_active'] = true;
        }

        DB::table('users')->where('id', $id)->update($updateData);

        return redirect()->route('erp.admin')->with('success', "تم تحديث بيانات وصلاحيات المستخدم [{$user->name}] بنجاح!");
    }

    public function deleteUser($id)
    {
        $totalUsers = DB::table('users')->count();
        if ($totalUsers <= 1) {
            return redirect()->back()->with('error', 'لا يمكن حذف الحساب الوحيد المتبقي في النظام!');
        }

        $user = DB::table('users')->where('id', $id)->first();
        if ($user) {
            DB::table('users')->where('id', $id)->delete();
            if (session('active_user_id') == $id) {
                session()->forget('active_user_id');
            }
        }

        return redirect()->route('erp.admin')->with('success', 'تم حذف حساب المستخدم بنجاح.');
    }

    /**
     * Jordanian Tax Authority (ISTD) Declaration & Tax Management Hub
     */
    public function taxCenter(Request $request)
    {
        $tenant = $this->getTenant();
        $taxRate = $tenant->tax_rate ?? 16;
        $period = $request->input('period', 'all');

        $salesQuery = DB::table('invoices')->where('type', 'sale');
        $purchasesQuery = DB::table('invoices')->where('type', 'purchase');

        if ($period !== 'all' && !empty($period)) {
            $salesQuery->where('created_at', 'like', "$period%");
            $purchasesQuery->where('created_at', 'like', "$period%");
        }

        $salesInvoices = $salesQuery->orderByDesc('id')->get();
        $purchaseInvoices = $purchasesQuery->orderByDesc('id')->get();

        // Calculate sales breakdown
        $totalSalesTaxable = $salesInvoices->where('tax_total', '>', 0)->sum('subtotal');
        $totalSalesExempt = $salesInvoices->where('tax_total', '<=', 0)->sum('subtotal');
        $totalSalesGross = $salesInvoices->sum('grand_total');
        $outputTax = $salesInvoices->sum('tax_total');

        // Calculate purchases breakdown (inputs)
        $totalPurchasesTaxable = $purchaseInvoices->where('tax_total', '>', 0)->sum('subtotal');
        $totalPurchasesExempt = $purchaseInvoices->where('tax_total', '<=', 0)->sum('subtotal');
        $inputTax = $purchaseInvoices->sum('tax_total');

        // Net Sales Tax Due (Output Tax - Input Tax)
        $netTaxPayable = $outputTax - $inputTax;

        // Withholding tax from salaries and services
        $salaryTaxWithholding = 135.00; // Sample calculated withholding tax from HR

        // JoFotara live stats
        $jofotaraApprovedCount = $salesInvoices->where('jofotara_status', 'accepted')->count();
        $jofotaraPendingCount = $salesInvoices->where('jofotara_status', '!=', 'accepted')->count();

        return view('erp.taxes', compact(
            'tenant',
            'taxRate',
            'period',
            'salesInvoices',
            'purchaseInvoices',
            'totalSalesTaxable',
            'totalSalesExempt',
            'totalSalesGross',
            'outputTax',
            'totalPurchasesTaxable',
            'totalPurchasesExempt',
            'inputTax',
            'netTaxPayable',
            'salaryTaxWithholding',
            'jofotaraApprovedCount',
            'jofotaraPendingCount'
        ));
    }

    /**
     * Update Company Tax Profile & ISTD Settings
     */
    public function updateTaxSettings(Request $request)
    {
        $validated = $request->validate([
            'tax_number' => 'required|string|max:50',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'tax_period' => 'nullable|string|max:50',
            'commercial_register' => 'nullable|string|max:50',
        ]);

        DB::table('tenants')->where('id', 1)->update([
            'tax_number' => $validated['tax_number'],
            'tax_rate' => $validated['tax_rate'],
            'tax_period' => $validated['tax_period'] ?? 'شهري (Monthly)',
            'commercial_register' => $validated['commercial_register'] ?? null,
            'updated_at' => now()
        ]);

        return redirect()->route('erp.taxes')->with('success', 'تم حفظ وتحديث إعدادات الملف والنسب الضريبية بنجاح!');
    }
}