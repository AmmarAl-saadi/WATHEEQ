@extends('layouts.app')

@section('title', 'معاينة وطباعة الفاتورة | وثيق ERP')

@section('content')
<div class="space-y-4 font-sans text-xs" dir="rtl">

    <!-- Action Bar -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex justify-between items-center no-print">
        <div class="flex items-center gap-3">
            <span class="font-bold text-gray-800 text-sm">معاينة المستند الضريبي:</span>
            <span class="font-mono font-bold text-[#1F2E4A] text-base">{{ $invoice->invoice_no }}</span>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-5 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-lg font-bold shadow-sm transition">
                طباعة الفاتورة (A4)
            </button>
            <a href="{{ route('erp.invoices') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-bold border transition">
                رجوع لقائمة الفواتير
            </a>
        </div>
    </div>

    <!-- Official Printable A4 Corporate Invoice Document -->
    <div id="printableA4Area" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-8 space-y-6 max-w-4xl mx-auto">
        
        <!-- Header -->
        <div class="border-b-2 border-[#1F2E4A] pb-4 flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-black text-[#1F2E4A]">{{ $tenant->name ?? 'وثيق لإدارة الأعمال' }}</h1>
                <p class="text-xs text-gray-600 font-medium">{{ $tenant->name_en ?? 'National ERP System' }}</p>
                <div class="text-[11px] text-gray-500 mt-2 space-y-0.5">
                    <div>الرقم الضريبي الموحد: <span class="font-bold font-mono text-gray-800">{{ $tenant->tax_number ?? '1004829103' }}</span></div>
                    <div>السجل التجاري: <span class="font-bold font-mono text-gray-800">{{ $tenant->commercial_record ?? '29381-CR' }}</span></div>
                    <div>العنوان: عمان - المملكة الأردنية الهاشمية - شارع مكة</div>
                </div>
            </div>

            <div class="text-left">
                <span class="px-3 py-1 bg-[#1F2E4A] text-white font-bold rounded text-xs block mb-2 text-center">
                    {{ $invoice->type === 'sale' ? 'فاتورة مبيعات ضريبية' : 'فاتورة مشتريات وتوريد' }}
                </span>
                <div class="font-mono text-xs space-y-1">
                    <div>رقم الفاتورة: <span class="font-bold text-gray-900">{{ $invoice->invoice_no }}</span></div>
                    <div>تاريخ الإصدار: <span class="text-gray-700">{{ $invoice->date }}</span></div>
                    <div>طريقة الدفع: <span class="font-bold text-gray-800">{{ $invoice->payment_method === 'cash' ? 'نقداً' : 'آجل' }}</span></div>
                </div>
            </div>
        </div>

        <!-- Bill To Box -->
        <div class="bg-[#F8F9FA] p-4 rounded-xl border border-gray-200 grid grid-cols-2 gap-4">
            <div>
                <span class="text-gray-500 block text-[10px] font-bold">اسم العميل / الطرف الثاني:</span>
                <span class="font-bold text-sm text-gray-900">{{ $invoice->contact_name }}</span>
            </div>
            <div>
                <span class="text-gray-500 block text-[10px] font-bold">حالة السداد:</span>
                <span class="font-bold font-mono text-xs text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full inline-block mt-0.5">
                    {{ $invoice->status === 'paid' ? 'مدفوعة بالكامل' : 'غير مدفوعة' }}
                </span>
            </div>
        </div>

        <!-- Line Items -->
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs border">
                <thead class="bg-[#1F2E4A] text-white font-bold text-[11px]">
                    <tr>
                        <th class="py-2.5 px-3 text-center w-10">#</th>
                        <th class="py-2.5 px-3">اسم الصنف والمواصفات</th>
                        <th class="py-2.5 px-3 text-center w-20">الوحدة</th>
                        <th class="py-2.5 px-3 text-center w-20">الكمية</th>
                        <th class="py-2.5 px-3 text-left font-mono w-28">السعر الفردي</th>
                        <th class="py-2.5 px-3 text-left font-mono w-28">المجموع الصافي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($items as $idx => $item)
                        <tr>
                            <td class="py-2 px-3 text-center font-mono text-gray-400">{{ $idx + 1 }}</td>
                            <td class="py-2 px-3 font-bold text-gray-900">{{ $item->item_name }}</td>
                            <td class="py-2 px-3 text-center text-gray-600">{{ $item->unit }}</td>
                            <td class="py-2 px-3 text-center font-mono font-bold">{{ $item->quantity }}</td>
                            <td class="py-2 px-3 text-left font-mono">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-2 px-3 text-left font-mono font-bold text-gray-900">{{ number_format($item->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-2 px-3 text-center font-mono text-gray-400">1</td>
                            <td class="py-2 px-3 font-bold text-gray-900">مشتريات وبضائع تجارية معتمدة</td>
                            <td class="py-2 px-3 text-center text-gray-600">دفعة</td>
                            <td class="py-2 px-3 text-center font-mono font-bold">1</td>
                            <td class="py-2 px-3 text-left font-mono">{{ number_format($invoice->subtotal, 2) }}</td>
                            <td class="py-2 px-3 text-left font-mono font-bold text-gray-900">{{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Totals & JoFotara Tax Clearance Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center border-t border-b py-4">
            <!-- JoFotara QR & Clearance Stamp -->
            <div class="p-3 bg-[#F8F9FA] rounded-xl border border-[#CED4DA] flex items-center gap-4">
                @if(!empty($invoice->jofotara_qr))
                    <img src="{{ $invoice->jofotara_qr }}" alt="JoFotara QR" class="w-24 h-24 border p-1 rounded bg-white shadow-sm shrink-0">
                @else
                    <div class="w-24 h-24 border border-dashed border-gray-300 rounded flex items-center justify-center text-[10px] text-gray-400 text-center p-1 shrink-0">
                        رمز الاستجابة السريع (JoFotara QR)
                    </div>
                @endif
                <div class="space-y-1 text-[11px] text-gray-700">
                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-[#1F2E4A] text-white">
                        نظام الفوترة الوطني الأردني (JoFotara - ISTD)
                    </span>
                    <div class="font-mono text-[10px] text-gray-600">
                        UUID: <span class="font-bold text-gray-900 select-all">{{ $invoice->jofotara_uuid ?? 'PENDING_CLEARANCE' }}</span>
                    </div>
                    <div class="text-[10px] text-gray-500">
                        امسح الرمز عبر تطبيق <strong class="text-gray-800">سند (Sanad)</strong> أو بوابة الضريبة للتحقق من اعتماد الفاتورة.
                    </div>
                </div>
            </div>

            <!-- Totals Summary Grid -->
            <div class="w-full bg-[#F8F9FA] p-4 rounded-xl border border-[#CED4DA] space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-gray-600">المجموع قبل الضريبة:</span>
                    <span class="font-mono font-bold text-gray-900">{{ number_format($invoice->subtotal, 2) }} {{ $tenant->currency ?? 'JOD' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">ضريبة المبيعات العامة ({{ $tenant->tax_rate ?? 16 }}%):</span>
                    <span class="font-mono font-bold text-[#1F2E4A]">{{ number_format($invoice->tax_total, 2) }} {{ $tenant->currency ?? 'JOD' }}</span>
                </div>
                <div class="border-t border-[#CED4DA] pt-2 flex justify-between items-center text-sm font-black">
                    <span class="text-gray-900">الإجمالي النهائي المستحق:</span>
                    <span class="font-mono text-base text-[#1F2E4A]">{{ number_format($invoice->grand_total, 2) }} {{ $tenant->currency ?? 'JOD' }}</span>
                </div>
            </div>
        </div>

        <!-- Invoice Terms & Footer -->
        <div class="border-t pt-4 text-[11px] text-gray-500 leading-relaxed">
            <p class="font-bold text-gray-700 mb-1">الشروط والأحكام:</p>
            <p>{{ $tenant->invoice_notes ?? 'البضاعة المباعة خاضعة للشروط والضمان وسياسة الاستبدال خلال 3 أيام بموجب الفاتورة الرسمية.' }}</p>
        </div>

    </div>

</div>
@endsection


