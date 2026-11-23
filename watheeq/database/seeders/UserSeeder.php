<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Update Tenant branding
        DB::table('tenants')->where('id', 1)->update([
            'system_name' => 'وثيق',
            'system_tagline' => 'منظومة وثيق لإدارة الأعمال | Watheeq ERP',
            'enabled_modules' => json_encode([
                'dashboard', 'definitions', 'invoicing', 'hr', 'hotels', 'pos', 'reports', 'customizer'
            ]),
            'enabled_tools' => json_encode([
                'jofotara', 'sales_invoice', 'purchase_invoice', 'receipt_voucher',
                'payment_voucher', 'journal_voucher', 'price_adjustment', 'salary_editor',
                'hotel_guests', 'cheques', 'dispatches', 'phonebook', 'notepad', 'pos', 'reports'
            ])
        ]);

        if (DB::table('users')->count() === 0) {
            DB::table('users')->insert([
                [
                    'name' => 'المدير العام',
                    'email' => 'admin@watheeq.jo',
                    'password' => Hash::make('admin123'),
                    'role' => 'admin',
                    'role_name_ar' => 'مدير النظام (Super Admin)',
                    'enabled_modules' => json_encode([
                        'dashboard', 'definitions', 'invoicing', 'hr', 'hotels', 'pos', 'reports', 'customizer'
                    ]),
                    'enabled_tools' => json_encode([
                        'jofotara', 'sales_invoice', 'purchase_invoice', 'receipt_voucher',
                        'payment_voucher', 'journal_voucher', 'price_adjustment', 'salary_editor',
                        'hotel_guests', 'cheques', 'dispatches', 'phonebook', 'notepad', 'pos', 'reports'
                    ]),
                    'is_active' => true,
                    'avatar_color' => '#1F2E4A',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name' => 'محمود السعدي',
                    'email' => 'accountant@watheeq.jo',
                    'password' => Hash::make('user123'),
                    'role' => 'accountant',
                    'role_name_ar' => 'محاسب مالي معتمد',
                    'enabled_modules' => json_encode([
                        'dashboard', 'definitions', 'invoicing', 'reports'
                    ]),
                    'enabled_tools' => json_encode([
                        'jofotara', 'sales_invoice', 'purchase_invoice', 'receipt_voucher',
                        'payment_voucher', 'journal_voucher', 'cheques', 'reports', 'notepad'
                    ]),
                    'is_active' => true,
                    'avatar_color' => '#0F766E',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name' => 'طارق كاشير',
                    'email' => 'cashier@watheeq.jo',
                    'password' => Hash::make('user123'),
                    'role' => 'cashier',
                    'role_name_ar' => 'كاشير مبيعات ونقاط بيع',
                    'enabled_modules' => json_encode([
                        'dashboard', 'pos', 'invoicing'
                    ]),
                    'enabled_tools' => json_encode([
                        'pos', 'sales_invoice', 'receipt_voucher'
                    ]),
                    'is_active' => true,
                    'avatar_color' => '#2563EB',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name' => 'رانية كمال',
                    'email' => 'hr@watheeq.jo',
                    'password' => Hash::make('user123'),
                    'role' => 'hr',
                    'role_name_ar' => 'مسؤولة شؤون الموظفين (HR)',
                    'enabled_modules' => json_encode([
                        'dashboard', 'hr', 'reports'
                    ]),
                    'enabled_tools' => json_encode([
                        'salary_editor', 'phonebook', 'notepad', 'reports'
                    ]),
                    'is_active' => true,
                    'avatar_color' => '#7C3AED',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name' => 'عمر الفندقي',
                    'email' => 'hotel@watheeq.jo',
                    'password' => Hash::make('user123'),
                    'role' => 'hotel',
                    'role_name_ar' => 'موظف استقبال وضيافة',
                    'enabled_modules' => json_encode([
                        'dashboard', 'hotels', 'invoicing'
                    ]),
                    'enabled_tools' => json_encode([
                        'hotel_guests', 'receipt_voucher', 'phonebook', 'notepad'
                    ]),
                    'is_active' => true,
                    'avatar_color' => '#D97706',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }
    }
}

