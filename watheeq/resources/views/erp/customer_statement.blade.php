@extends('layouts.app')

@section('title', 'كشف حساب عميل مفصل | وثيق ERP')

@section('content')
<div class="space-y-4 font-sans text-xs" dir="rtl">

    <!-- Action Bar -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-3 no-print">
        <div>
            <h2 class="text-base font-black text-[#1F2E4A]">كشف حساب مفصل لحركات الذمم (Statement of Account)</h2>
            <p class="text-[11px] text-gray-500">حركات الفواتير، المقبوضات، المردودات، ورصيد الذمة القائم للعميل: <span class="font-bold text-gray-900">{{ $contact->name }}</span></p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-1.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold shadow-sm transition">
                طباعة كشف الحساب (A4)
            </button>
            <a href="{{ route('erp.contacts') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-bold border transition">
                العودة لدليل العملاء
            </a>
        </div>
    </div>

    <!-- Official Printable Document Header -->
    <div id="printableA4Area" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
        
        <!-- Corporate Header -->
        <div class="border-b-2 border-[#1F2E4A] pb-4 flex justify-between items-start">
            <div>
                <h1 class="text-xl font-black text-[#1F2E4A]">{{ $tenant->name ?? 'وثيق لإدارة الأعمال' }}</h1>
                <p class="text-xs text-gray-600 font-medium">{{ $tenant->name_en ?? 'National ERP System' }}</p>
                <p class="text-[11px] text-gray-500 mt-1">الرقم الضريبي: {{ $tenant->tax_number ?? '1004829103' }} | السجل التجاري: {{ $tenant->commercial_record ?? '29381-CR' }}</p>
            </div>
            <div class="text-left font-mono">
                <span class="px-2.5 py-1 bg-[#1F2E4A]/10 text-[#1F2E4A] font-bold rounded text-xs block mb-1">كشف حساب ذمة عميل</span>
                <span class="text-xs text-gray-500">التاريخ: {{ date('Y-m-d') }}</span>
            </div>
        </div>

        <!-- Contact Info Card -->
        <div class="bg-[#F8F9FA] p-3.5 rounded-xl border border-gray-200 grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
            <div>
                <span class="text-gray-500 block text-[10px]">كود العميل:</span>
                <span class="font-bold font-mono text-gray-800">{{ $contact->code }}</span>
            </div>
            <div>
                <span class="text-gray-500 block text-[10px]">اسم العميل / الشركة:</span>
                <span class="font-bold text-gray-900">{{ $contact->name }}</span>
            </div>
            <div>
                <span class="text-gray-500 block text-[10px]">رقم الهاتف / الاتصال:</span>
                <span class="font-bold font-mono text-gray-800">{{ $contact->phone ?? '-' }}</span>
            </div>
            <div>
                <span class="text-gray-500 block text-[10px]">سقف حد الدين الائتماني:</span>
                <span class="font-bold font-mono text-rose-700">{{ number_format($contact->credit_limit, 2) }} {{ $tenant->currency ?? 'JOD' }}</span>
            </div>
        </div>

        <!-- Transactions Ledger Table -->
        <div class="overflow-x-auto border rounded-xl">
            <table class="w-full text-right text-xs">
                <thead class="bg-[#1F2E4A] text-white font-bold text-[11px]">
                    <tr>
                        <th class="py-2.5 px-3">التاريخ</th>
                        <th class="py-2.5 px-3">رقم الحركة / المستند</th>
                        <th class="py-2.5 px-3">نوع الحركة</th>
                        <th class="py-2.5 px-3">البيان والشرح التفصيلي</th>
                        <th class="py-2.5 px-3 text-left font-mono">مدين (+) مبيعات</th>
                        <th class="py-2.5 px-3 text-left font-mono">دائن (-) مقبوضات</th>
                        <th class="py-2.5 px-3 text-left font-mono">الرصيد التراكمي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 font-medium">
                    @php
                        $runningBalance = 0;
                    @endphp
                    
                    <!-- Invoices (Debits) -->
                    @foreach($invoices as $inv)
                        @php
                            $runningBalance += $inv->grand_total;
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-2 px-3 font-mono text-gray-600">{{ $inv->date }}</td>
                            <td class="py-2 px-3 font-mono font-bold text-[#1F2E4A]">{{ $inv->invoice_no }}</td>
                            <td class="py-2 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">فاتورة مبيعات</span>
                            </td>
                            <td class="py-2 px-3 text-gray-700">{{ $inv->notes ?? 'إصدار فاتورة مبيعات ضريبية' }}</td>
                            <td class="py-2 px-3 text-left font-mono font-bold text-gray-900">{{ number_format($inv->grand_total, 2) }}</td>
                            <td class="py-2 px-3 text-left font-mono text-gray-400">-</td>
                            <td class="py-2 px-3 text-left font-mono font-black text-[#1F2E4A] bg-[#1F2E4A]/5">{{ number_format($runningBalance, 2) }}</td>
                        </tr>
                    @endforeach

                    <!-- Receipts (Credits) -->
                    @foreach($vouchers as $vch)
                        @php
                            $runningBalance -= $vch->amount;
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-2 px-3 font-mono text-gray-600">{{ $vch->date }}</td>
                            <td class="py-2 px-3 font-mono font-bold text-[#1F2E4A]">{{ $vch->voucher_no }}</td>
                            <td class="py-2 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">سند قبض مالي</span>
                            </td>
                            <td class="py-2 px-3 text-gray-700">{{ $vch->statement }}</td>
                            <td class="py-2 px-3 text-left font-mono text-gray-400">-</td>
                            <td class="py-2 px-3 text-left font-mono font-bold text-emerald-700">{{ number_format($vch->amount, 2) }}</td>
                            <td class="py-2 px-3 text-left font-mono font-black text-[#1F2E4A] bg-[#1F2E4A]/5">{{ number_format($runningBalance, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Closing Balance Summary Box -->
        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-3">
            <div class="text-xs text-gray-600">
                <span class="font-bold">الموقف المالي النهائي:</span> الرصيد المستحق بذمة العميل حتى تاريخ {{ date('Y-m-d') }}
            </div>
            <div class="flex items-center gap-3">
                <span class="font-black text-sm text-gray-800">الرصيد الصافي القائم:</span>
                <span class="font-mono font-black text-xl text-[#1F2E4A] bg-[#1F2E4A]/10 px-4 py-1 rounded-xl">
                    {{ number_format($runningBalance, 2) }} {{ $tenant->currency ?? 'JOD' }}
                </span>
            </div>
        </div>

    </div>

</div>
@endsection


