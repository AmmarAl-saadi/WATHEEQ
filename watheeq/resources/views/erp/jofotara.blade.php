@extends('layouts.app')

@section('title', 'نظام الفوترة الوطني الأردني (JoFotara) | الربط الإلكتروني المباشر ISTD')

@section('content')
<div class="space-y-6" dir="rtl">

    <!-- Header & Status Ribbon -->
    <div class="bg-white p-5 rounded-2xl border border-[#CED4DA] shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">دائرة ضريبة الدخل والمبيعات الأردنية (ISTD)</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ ($tenant->jofotara_environment ?? 'sandbox') === 'production' ? 'bg-[#343A40] text-white' : 'bg-amber-100 text-amber-800' }}">
                    {{ ($tenant->jofotara_environment ?? 'sandbox') === 'production' ? 'بيئة الإنتاج المباشرة (Live ISTD)' : 'البيئة التجريبية (Sandbox Test)' }}
                </span>
            </div>
            <h1 class="text-xl font-black text-gray-900 mt-1">نظام الفوترة الوطني الأردني (JoFotara) - نموذج الاعتماد الفوري (CTC)</h1>
            <p class="text-xs text-gray-500">الربط البرمجي المباشر مع بوابة الفوترة الأردنية الرسمية واعتماد الفواتير وفق معيار UBL 2.1 ورمز الاستجابة السريع لتطبيق سند (Sanad)</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="https://portal.jofotara.gov.jo/" target="_blank" rel="noopener" class="px-4 py-2 bg-[#343A40] hover:bg-black text-white rounded-xl text-xs font-bold transition shadow-sm">
                بوابة الفوترة الوطنية (portal.jofotara.gov.jo)
            </a>
            <form action="{{ route('erp.jofotara.batch.submit') }}" method="POST" onsubmit="return confirm('هل تريد إرسال كافة الفواتير المتبقية للاعتماد لدى ضريبة الدخل والمبيعات؟')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                    اعتماد كافة الفواتير المعلقة
                </button>
            </form>
        </div>
    </div>

    <!-- KPI Statistics Counters -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-[#CED4DA] shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">إجمالي فواتير المبيعات</div>
            <div class="text-2xl font-black text-[#1F2E4A]">{{ $totalSalesCount }} <span class="text-xs font-normal text-gray-500">فاتورة</span></div>
            <div class="text-[11px] text-gray-400 mt-1">المسجلة في النظام</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#CED4DA] shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">الفواتير المعتمدة ضريبياً (SUBMITTED)</div>
            <div class="text-2xl font-black text-[#1F2E4A]">{{ $submittedCount }} <span class="text-xs font-normal text-gray-500">معتمدة</span></div>
            <div class="text-[11px] text-gray-500 mt-1">نسبة الامتثال {{ $totalSalesCount > 0 ? round(($submittedCount / $totalSalesCount) * 100) : 0 }}%</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#CED4DA] shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">فواتير قيد الإرسال (PENDING)</div>
            <div class="text-2xl font-black text-[#343A40]">{{ $pendingCount }} <span class="text-xs font-normal text-gray-500">معلقة</span></div>
            <div class="text-[11px] text-amber-700 mt-1">بحاجة للاعتماد الإلكتروني</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-[#CED4DA] shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">الرقم الضريبي للمكلف (TIN)</div>
            <div class="text-lg font-mono font-black text-[#1F2E4A] truncate">{{ $tenant->tax_number ?? '1004829103' }}</div>
            <div class="text-[11px] text-gray-400 mt-1">السجل التجاري: {{ $tenant->commercial_record ?? '29381-CR' }}</div>
        </div>
    </div>

    <!-- Two Columns: Technical Settings + Integration Guide -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Column 1: API Configuration Settings Form (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-[#CED4DA] shadow-sm p-6 space-y-4">
            <div class="border-b border-[#CED4DA] pb-3 flex justify-between items-center">
                <div>
                    <h2 class="text-sm font-black text-[#1F2E4A]">إعدادات الربط البرمجي (API Credentials & Settings)</h2>
                    <p class="text-xs text-gray-500">البيانات الخاصة بحساب المنشأة الصادرة من بوابة الفوترة الوطنية الأردنية</p>
                </div>
                <span class="px-2.5 py-1 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A]">REST API / UBL 2.1</span>
            </div>

            <form action="{{ route('erp.jofotara.settings.update') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">بيئة التشغيل (Environment):</label>
                        <select name="jofotara_environment" class="w-full p-2.5 border border-[#CED4DA] rounded-lg bg-[#F8F9FA] font-bold">
                            <option value="sandbox" {{ ($tenant->jofotara_environment ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>البيئة التجريبية (Sandbox / Testing)</option>
                            <option value="production" {{ ($tenant->jofotara_environment ?? 'sandbox') === 'production' ? 'selected' : '' }}>البيئة الرسمية الحية (ISTD Production)</option>
                        </select>
                        <span class="text-[10px] text-gray-400 mt-0.5 block">في البيئة التجريبية يتم محاكاة الاعتماد الكامل لضمان الجاهزية</span>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1">رقم النشاط الضريبي (Activity No):</label>
                        <input type="text" name="jofotara_activity_no" value="{{ $tenant->jofotara_activity_no ?? '1' }}" required class="w-full p-2.5 border border-[#CED4DA] rounded-lg bg-[#F8F9FA]">
                        <span class="text-[10px] text-gray-400 mt-0.5 block">رقم مصدر الدخل أو الفرع المسجل لدى الضريبة (الافتراضي 1)</span>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">معرف العميل (Client ID):</label>
                    <input type="text" name="jofotara_client_id" value="{{ $tenant->jofotara_client_id ?? '' }}" placeholder="أدخل Client-Id المستخرج من بوابة jofotara" class="w-full p-2.5 border border-[#CED4DA] rounded-lg bg-[#F8F9FA]">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">المفتاح السري (Secret Key):</label>
                    <input type="password" name="jofotara_secret_key" value="{{ $tenant->jofotara_secret_key ?? '' }}" placeholder="أدخل Secret-Key المستخرج من بوابة jofotara" class="w-full p-2.5 border border-[#CED4DA] rounded-lg bg-[#F8F9FA]">
                </div>

                <div class="p-3 bg-[#F8F9FA] rounded-xl border border-[#CED4DA]">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="jofotara_auto_sync" value="1" {{ !empty($tenant->jofotara_auto_sync) ? 'checked' : '' }} class="rounded border-gray-300 text-[#1F2E4A] focus:ring-[#1F2E4A]">
                        <span class="font-bold text-gray-800">التزامن الفوري التلقائي (Real-Time Auto Clearance)</span>
                    </label>
                    <p class="text-[11px] text-gray-500 mr-5 mt-1">عند تفعيل هذا الخيار، سيقوم النظام تلقائياً بإرسال كل فاتورة مبيعات جديدة إلى خوادم الفوترة الوطنية فور إصدارها والحصول على رمز الاستجابة السريع (QR Code) دون تدخل يدوي.</p>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-6 py-2.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white font-bold rounded-xl shadow transition">
                        حفظ بيانات الربط والتفعيل
                    </button>
                </div>
            </form>
        </div>

        <!-- Column 2: Legal & Procedural Guide (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-[#CED4DA] shadow-sm p-6 space-y-4">
            <div class="border-b border-[#CED4DA] pb-3">
                <h2 class="text-sm font-black text-[#1F2E4A]">دليل ربط المنشأة خطوة بخطوة مع ISTD</h2>
                <p class="text-xs text-gray-500">الإجراءات الرسمية المعتمدة لدى دائرة ضريبة الدخل والمبيعات</p>
            </div>

            <div class="space-y-3 text-xs text-gray-700 leading-relaxed">
                <div class="flex gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-[#1F2E4A] text-white flex items-center justify-center font-bold text-[10px] shrink-0">1</span>
                    <div>
                        <strong class="text-gray-900 block">الدخول إلى بوابة الفوترة:</strong>
                        سجل الدخول على <a href="https://portal.jofotara.gov.jo/" target="_blank" class="text-[#1F2E4A] font-bold underline">portal.jofotara.gov.jo</a> باستخدام الرقم الضريبي للمنشأة وكلمة المرور.
                    </div>
                </div>

                <div class="flex gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-[#1F2E4A] text-white flex items-center justify-center font-bold text-[10px] shrink-0">2</span>
                    <div>
                        <strong class="text-gray-900 block">إضافة جهاز الربط البرمجي (API Device):</strong>
                        من القائمة الجانبية في البوابة، اختر "إدارة الأجهزة / الربط الإلكتروني"، ثم انقر على "إضافة نظام/جهاز جديد".
                    </div>
                </div>

                <div class="flex gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-[#1F2E4A] text-white flex items-center justify-center font-bold text-[10px] shrink-0">3</span>
                    <div>
                        <strong class="text-gray-900 block">توليد مفاتيح الربط:</strong>
                        انسخ كلاً من <code class="bg-gray-100 px-1 py-0.5 rounded font-mono text-[#1F2E4A]">Client-Id</code> و <code class="bg-gray-100 px-1 py-0.5 rounded font-mono text-[#1F2E4A]">Secret-Key</code> والصقهما في النموذج المقابل هنا.
                    </div>
                </div>

                <div class="flex gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-[#1F2E4A] text-white flex items-center justify-center font-bold text-[10px] shrink-0">4</span>
                    <div>
                        <strong class="text-gray-900 block">التدقيق والاعتماد الفوري:</strong>
                        يقوم هذا النظام ببناء ملف XML قياسي وفق معيار UBL 2.1 المعتمد دولياً وأردنياً، وتشفيره بـ Base64 وإرساله عبر HTTPS REST API، واستقبال المعرف الفريد (UUID) والباركود الضريبي للطباعة.
                    </div>
                </div>

                <div class="flex gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-[#1F2E4A] text-white flex items-center justify-center font-bold text-[10px] shrink-0">5</span>
                    <div>
                        <strong class="text-gray-900 block">فحص تطبيق "سند" (Sanad App):</strong>
                        عند طباعة أي فاتورة معتمدة، يحتوي الـ QR Code على البيانات التوثيقية المعتمدة ويمكن مسحها عبر تطبيق سند الحكومي للتحقق الفوري.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Live Invoices Clearance Datagrid -->
    <div class="bg-white rounded-2xl border border-[#CED4DA] shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 border-b border-[#CED4DA] pb-3">
            <div>
                <h3 class="text-sm font-black text-[#1F2E4A]">سجل فواتير المبيعات وحالة الاعتماد في JoFotara</h3>
                <p class="text-xs text-gray-500">متابعة فورية للمعرف الضريبي (UUID) وحالة الربط لكل فاتورة مبيعات</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500 font-medium">عرض أحدث الفواتير المسجلة</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-[#1F2E4A] text-white font-bold">
                    <tr>
                        <th class="py-3 px-4">رقم الفاتورة</th>
                        <th class="py-3 px-4">العميل / الطرف الثاني</th>
                        <th class="py-3 px-4">تاريخ الإصدار</th>
                        <th class="py-3 px-4">المبلغ شامل الضريبة</th>
                        <th class="py-3 px-4">حالة الفوترة الوطنية</th>
                        <th class="py-3 px-4">المعرف الضريبي المعتمد (JoFotara UUID)</th>
                        <th class="py-3 px-4 text-center">إجراءات الاعتماد والمعاينة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#CED4DA]">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 font-mono font-bold text-[#1F2E4A]">
                                {{ $inv->invoice_no }}
                            </td>
                            <td class="py-3 px-4 font-bold text-gray-800">
                                {{ $inv->contact_name }}
                            </td>
                            <td class="py-3 px-4 font-mono text-gray-600">
                                {{ $inv->date }}
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-gray-900">
                                {{ number_format($inv->grand_total, 2) }} {{ $tenant->currency ?? 'JOD' }}
                            </td>
                            <td class="py-3 px-4">
                                @if($inv->jofotara_status === 'SUBMITTED')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/30">
                                        معتمدة ضريبياً (SUBMITTED)
                                    </span>
                                @elseif($inv->jofotara_status === 'REJECTED')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                        مرفوضة من الضريبة
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                        قيد الإرسال (PENDING)
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono text-[11px] text-gray-600">
                                @if(!empty($inv->jofotara_uuid))
                                    <span class="bg-gray-100 px-2 py-0.5 rounded text-[10px] select-all font-mono" title="{{ $inv->jofotara_uuid }}">
                                        {{ Str::limit($inv->jofotara_uuid, 20) }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-[10px]">لم يتم توليد UUID بعد</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    @if($inv->jofotara_status !== 'SUBMITTED')
                                        <form action="{{ route('erp.invoices.jofotara.submit', $inv->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded text-[11px] font-bold transition">
                                                إرسال واعتماد
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[10px] text-gray-500 font-bold">معتمدة بنجاح</span>
                                    @endif

                                    <a href="{{ route('erp.invoices.ubl.xml', $inv->id) }}" target="_blank" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-[11px] font-mono border" title="معاينة كود UBL 2.1 XML">
                                        XML
                                    </a>

                                    <a href="{{ route('erp.invoices.print', $inv->id) }}" target="_blank" class="px-2.5 py-1 bg-[#343A40] hover:bg-black text-white rounded text-[11px] font-bold" title="طباعة الفاتورة مع QR Code">
                                        معاينة الفاتورة
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400 text-xs">لا توجد فواتير مبيعات مسجلة في النظام حالياً</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

