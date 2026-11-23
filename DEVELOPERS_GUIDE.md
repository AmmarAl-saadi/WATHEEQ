# Watheeq ERP — Developer Architecture & Team Guide

Welcome to the **Watheeq ERP (منظومة وثيق لإدارة الأعمال)** engineering workspace. This guide explains how the system is structured, how the developer team is divided, and best practices for developing new features.

---

## 🏛 1. Tech Stack
- **Framework:** Laravel 13 (PHP 8.3+)
- **Database:** SQLite (default local development) or MySQL (recommended for production/team environments)
- **Frontend:** Blade Templates + Tailwind CSS + Vanilla JS / HTML5 QR Scanner
- **Integrations:** Jordan e-Invoicing (JoFotara / فواتير ضريبة الدخل والمبيعات) via `app/Services/JoFotaraService.php`

---

## 👥 2. Team Division & Domain Ownership

To prevent merge conflicts and maintain code quality, developers should work within their designated domains:

### 💼 Domain 1: General Ledger & Accounting (`app/Http/Controllers/Accounting/`)
- **Assigned Developer:** Developer 1 (Senior Backend / Financial Lead)
- **Modules Covered:**
  - Chart of Accounts (`/accounts`)
  - Journal Entries & Adjustments (`/journals`)
  - Vouchers: Receipts & Payments (`/vouchers`, `/vouchers/payment-editor`)
  - Cheques Portfolio (`/cheques`)
  - Fixed Assets & Depreciation (`/fixed-assets`)
  - Financial Reports: Trial Balance, P&L, Balance Sheet (`/reports`)
- **Key Blade Views:** `resources/views/erp/accounts.blade.php`, `journal_editor.blade.php`, `vouchers.blade.php`, `cheques.blade.php`, `fixed_assets.blade.php`

### 🛒 Domain 2: Sales, Invoicing & Tax Hub (`app/Http/Controllers/Sales/`)
- **Assigned Developer:** Developer 2 (Invoicing & Integrations)
- **Modules Covered:**
  - Invoicing Hub (`/invoicing-hub`)
  - Sales & Purchase Invoices (`/invoices`, `/invoices/create-editor`)
  - Printable Invoices (`/invoices/{id}/print`)
  - JoFotara e-Invoicing Integration (`/jofotara`, `app/Services/JoFotaraService.php`)
  - Tax Compliance Center (`/taxes`)
- **Key Blade Views:** `invoicing_hub.blade.php`, `invoices.blade.php`, `invoice_editor.blade.php`, `invoice_print.blade.php`, `jofotara.blade.php`, `taxes.blade.php`

### 📦 Domain 3: Inventory, Products & CRM (`app/Http/Controllers/Inventory/`)
- **Assigned Developer:** Developer 3 (Full Stack / Inventory)
- **Modules Covered:**
  - Products & Services Catalog (`/products`, `/products/create-editor`)
  - Barcode Lookup API (`/api/products/barcode`)
  - Stock Count & Inventory Reconcile (`/stock-count`)
  - Dispatches & Transfers (`/dispatches`)
  - Price Lists & Price Adjustments (`/price-lists`, `/inventory/price-adjustment`)
  - Contacts / Customers & Suppliers (`/contacts`, `/contacts/{id}/statement`)
- **Key Blade Views:** `products.blade.php`, `product_editor.blade.php`, `stock_count.blade.php`, `dispatches.blade.php`, `price_lists.blade.php`, `contacts.blade.php`

### 🖥️ Domain 4: Point of Sale (POS) & Counter UI (`app/Http/Controllers/Sales/`)
- **Assigned Developer:** Developer 4 (Frontend / POS Specialist)
- **Modules Covered:**
  - Fast Touch POS Interface (`/pos`)
  - POS Shifts & Z-Report (`/pos/shifts`)
  - POS Dining Tables & Floor Plans (`/pos/tables`)
  - POS Promotional Campaigns (`/pos/campaigns`)
  - Barcode & Camera Scanner Integration (`public/js/watheeq-scanner.js`)
- **Key Blade Views:** `pos.blade.php`, `pos_hub.blade.php`, `pos_shifts.blade.php`, `pos_tables.blade.php`, `pos_campaigns.blade.php`

### 👥 Domain 5: HR, Payroll & Hospitality (`app/Http/Controllers/Hr/` & `Hospitality/`)
- **Assigned Developer:** Developer 5 (Full Stack)
- **Modules Covered:**
  - HR & Employee Profiles (`/hr`)
  - Salary Slips & Deductions (`/hr/salary-editor`)
  - Tax Exemption Registry (`/hr/tax-exempt`)
  - Hotel Rooms, Reservations & Guest Directory (`/hotels`, `/hotels/guests`)
- **Key Blade Views:** `hr.blade.php`, `hr/salary_editor.blade.php`, `hr/tax_exempt.blade.php`, `hotels.blade.php`, `hotels/guests.blade.php`

---

## 🌿 3. Git Branching Strategy

Developers must **NEVER** commit directly to `main`. Follow this workflow:

1. **Pull Latest Changes:**
   ```bash
   git checkout main
   git pull origin main
   ```
2. **Create a Feature Branch:**
   ```bash
   git checkout -b feature/invoice-discount-fix
   # or feature/pos-offline-sync
   # or feature/hr-overtime-calc
   ```
3. **Commit with Clear Messages:**
   ```bash
   git add .
   git commit -m "feat(sales): add discount percentage calculation on invoice lines"
   ```
4. **Push & Open a Pull Request (PR):**
   ```bash
   git push origin feature/invoice-discount-fix
   ```
5. Merge into `main` only after peer review.

---

## 🚀 4. How to Run Locally

```bash
# 1. Install dependencies (if needed)
composer install
npm install

# 2. Environment config
copy .env.example .env
php artisan key:generate

# 3. Database migrations & seeders
php artisan migrate --force
php artisan db:seed

# 4. Start local development server
php artisan serve
```
Or simply double-click `START.bat` in the root folder.
