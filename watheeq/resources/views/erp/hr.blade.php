@extends('layouts.app')

@section('title', 'الموارد البشرية (HR) | وثيق ERP')

@section('content')
<div class="space-y-6" dir="rtl">

    <!-- Header & Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">نظام الموارد البشرية والرواتب</span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">الموارد البشرية</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 mt-1">الموارد البشرية (HR): الموظفين، الرواتب، وساعات الدوام</h1>
            <p class="text-xs text-gray-500">بطاقات الموظفين، سندات الرواتب، اقتطاعات الضمان الاجتماعي الأردني، والإعفاءات الضريبية</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('erp.hr.salary_editor') }}" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                + محطة تعديل راتب
            </a>
            <a href="{{ route('erp.hr.tax_exempt') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                + سند الإعفاءات
            </a>
            <button onclick="document.getElementById('newEmpModal').classList.remove('hidden')" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold transition">
                + إضافة موظف جديد
            </button>
        </div>
    </div>

    <!-- Quick KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">إجمالي الكادر النشط</div>
            <div class="text-2xl font-black text-[#1F2E4A]">{{ $totalEmployees }} <span class="text-xs font-normal text-gray-600">موظف</span></div>
            <div class="text-[11px] text-gray-400 mt-1">مسجلين في الضمان الاجتماعي</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">فاتورة الرواتب الشهرية الصافية</div>
            <div class="text-2xl font-black text-gray-900">{{ number_format($totalPayroll, 2) }} <span class="text-xs font-normal text-gray-600">{{ $tenant->currency ?? 'JOD' }}</span></div>
            <div class="text-[11px] text-gray-400 mt-1">شاملة البدلات بعد الاقتطاعات</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <div class="text-xs text-gray-500 font-semibold mb-1">مساهمة المنشأة في الضمان (14.25%)</div>
            <div class="text-2xl font-black text-emerald-600">{{ number_format($totalPayroll * 0.1425, 2) }} <span class="text-xs font-normal text-gray-600">{{ $tenant->currency ?? 'JOD' }}</span></div>
            <div class="text-[11px] text-gray-400 mt-1">التزام مستحق للمؤسسة العامة للضمان</div>
        </div>
    </div>

    <!-- 8 Master Modules of watheeq HR (مطابقة تماماً لفيديو وثيق الأصلي Frame 105) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Card 1: تعريفات -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">1. تعريفات</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">كادر</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <button type="button" onclick="document.getElementById('newEmpModal').classList.remove('hidden')" class="w-full text-right block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• بطاقة الموظف</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">+ جديد</span>
                </button>
                <a href="#employeesTableSection" class="block p-1 rounded hover:bg-gray-50">• طبيعة العمل والأقسام</a>
                <a href="#employeesTableSection" class="block p-1 rounded hover:bg-gray-50">• الدرجات الوظيفية والجنسية</a>
                <a href="#employeesTableSection" class="block p-1 rounded hover:bg-gray-50">• العهد والمخالفات والجزاءات</a>
                <a href="{{ route('erp.customizer') }}" class="block p-1 rounded hover:bg-gray-50">• المدن والقرى ومعلومات الشركة</a>
            </div>
        </div>

        <!-- Card 2: تعريفات الدوام -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">2. تعريفات الدوام</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">ورديات</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.pos.shifts') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• نظام وساعات الدوام</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">ورديات</span>
                </a>
                <a href="{{ route('erp.hr') }}" class="block p-1 rounded hover:bg-gray-50">• خصائص ضبط الدوام</a>
                <a href="{{ route('erp.hr') }}" class="block p-1 rounded hover:bg-gray-50">• أنواع الأيام والعطل الرسمية</a>
            </div>
        </div>

        <!-- Card 3: سندات الرواتب -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">3. سندات الرواتب</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700">رواتب</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.hr.salary_editor') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• تعديل راتب موظف</span>
                    <span class="text-[9px] text-emerald-600 font-bold">محطة عمل</span>
                </a>
                <a href="{{ route('erp.journals.editor') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• قيد إثبات راتب شهري</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">قيد</span>
                </a>
                <a href="{{ route('erp.hr') }}" class="block p-1 rounded hover:bg-gray-50">• قسيمة راتب (Pay Slip)</a>
            </div>
        </div>

        <!-- Card 4: سندات مالية -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">4. سندات مالية للموظفين</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">مستحقات</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.vouchers.payment_editor') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• سند المدفوعات والخصم</span>
                    <span class="text-[9px] text-emerald-600 font-bold">صرف</span>
                </a>
                <a href="{{ route('erp.vouchers.payment_editor') }}" class="block p-1 rounded hover:bg-gray-50">• سند سلفة على الراتب</a>
                <a href="{{ route('erp.vouchers.payment_editor') }}" class="block p-1 rounded hover:bg-gray-50">• سند مكافأة وعمل إضافي</a>
                <a href="{{ route('erp.vouchers.payment_editor') }}" class="block p-1 rounded hover:bg-gray-50">• سند بدل سكن ومواصلات</a>
                <a href="{{ route('erp.vouchers.payment_editor') }}" class="block p-1 rounded hover:bg-gray-50">• سند تسديد مستحقات</a>
                <a href="{{ route('erp.cheques') }}" class="block p-1 rounded hover:bg-gray-50">• سند تحويل من حساب البنك</a>
            </div>
        </div>

        <!-- Card 5: تعريفات مالية -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">5. تعريفات مالية</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">اقتطاعات</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.hr') }}" class="block p-1 rounded hover:bg-gray-50">• الخصومات والمكافآت</a>
                <a href="{{ route('erp.hr') }}" class="block p-1 rounded hover:bg-gray-50">• شرائح ضريبة الدخل</a>
                <a href="{{ route('erp.hr') }}" class="block p-1 rounded hover:bg-gray-50">• اشتراكات الضمان والتأمين الصحي</a>
                <a href="{{ route('erp.accounts') }}" class="block p-1 rounded hover:bg-gray-50">• حسابات أصول وخصوم الميزانية</a>
            </div>
        </div>

        <!-- Card 6: سندات الضريبة -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">6. سندات الضريبة</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">ضريبة</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.hr.tax_exempt') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• سند الإعفاءات الضريبية</span>
                    <span class="text-[9px] text-emerald-600 font-bold">محطة</span>
                </a>
                <a href="{{ route('erp.journals.editor') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• سند ترحيل الضريبة والرواتب</span>
                    <span class="text-[9px] text-[#1F2E4A] font-bold">ترحيل</span>
                </a>
            </div>
        </div>

        <!-- Card 7: سندات عامة -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">7. سندات عامة</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">خدمة</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.hr') }}" class="block p-1 rounded hover:bg-gray-50">• إنهاء خدمة وتصفية مستحقات موظف</a>
                <a href="{{ route('erp.customizer') }}" class="block p-1 rounded hover:bg-gray-50">• عمليات آلية ومصطلحات HR</a>
            </div>
        </div>

        <!-- Card 8: ضبط الدوام -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm space-y-2.5">
            <div class="flex items-center justify-between border-b pb-1.5">
                <h3 class="text-xs font-black text-[#1F2E4A]">8. ضبط الدوام</h3>
                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">بصمة</span>
            </div>
            <div class="space-y-1 text-xs text-gray-700">
                <a href="{{ route('erp.hr') }}" class="block p-1 rounded hover:bg-gray-50 flex justify-between items-center">
                    <span>• قراءة ساعة البصمة التلقائية</span>
                    <span class="text-[9px] text-gray-400">بصمة</span>
                </a>
                <a href="{{ route('erp.hr') }}" class="block p-1 rounded hover:bg-gray-50">• تسجيل حضور وانصراف يدوي</a>
                <a href="{{ route('erp.hr') }}" class="block p-1 rounded hover:bg-gray-50">• معالج التأخير والغياب اليومي</a>
            </div>
        </div>

    </div>

    <!-- Active Employees Registry Table -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden" id="employeesTableSection">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-[#F8F9FA]">
            <h3 class="font-bold text-gray-900 text-sm">سجل الكادر الوظيفي والرواتب الشهرية</h3>
            <span class="text-xs text-gray-500 font-mono">{{ $employees->count() }} موظف مسجل</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-600">
                    <tr>
                        <th class="p-3">#</th>
                        <th class="p-3">اسم الموظف</th>
                        <th class="p-3">الرقم الوطني</th>
                        <th class="p-3">القسم</th>
                        <th class="p-3">المسمى الوظيفي</th>
                        <th class="p-3">الراتب الأساسي</th>
                        <th class="p-3">اقتطاع الضمان (7.5%)</th>
                        <th class="p-3">صافي الراتب المستحق</th>
                        <th class="p-3 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($employees as $index => $emp)
                        @php
                            $sal = $emp->basic_salary ?? $emp->salary ?? 0;
                            $deduction = $sal * 0.075;
                            $net = $sal - $deduction;
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-3 font-mono font-bold text-gray-400">{{ $index + 1 }}</td>
                            <td class="p-3 font-bold text-gray-900">{{ $emp->name }}</td>
                            <td class="p-3 font-mono text-gray-600">{{ $emp->national_id }}</td>
                            <td class="p-3"><span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-bold text-[11px]">{{ $emp->department }}</span></td>
                            <td class="p-3 text-gray-700">{{ $emp->job_title ?? $emp->position ?? 'موظف' }}</td>
                            <td class="p-3 font-mono font-bold text-gray-900">{{ number_format($sal, 2) }}</td>
                            <td class="p-3 font-mono text-red-600 font-semibold">{{ number_format($deduction, 2) }}</td>
                            <td class="p-3 font-mono font-black text-emerald-700">{{ number_format($net, 2) }}</td>
                            <td class="p-3 text-center">
                                <a href="{{ route('erp.hr.salary_editor') }}" class="text-[#1F2E4A] hover:underline font-bold text-[11px]">تعديل الراتب</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-6 text-center text-gray-400">لا يوجد موظفين مسجلين حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal to Add Employee -->
    <div id="newEmpModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-5 space-y-4 shadow-2xl border border-gray-200">
            <div class="flex justify-between items-center border-b pb-2">
                <h3 class="font-black text-[#1F2E4A] text-base">إضافة موظف جديد للكادر</h3>
                <button type="button" onclick="document.getElementById('newEmpModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 font-bold text-lg">✕</button>
            </div>
            <form method="POST" action="{{ route('erp.hr.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">اسم الموظف الرباعي:</label>
                    <input type="text" name="name" required placeholder="الاسم الكامل" class="w-full border rounded px-3 py-1.5 font-bold">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">الرقم الوطني:</label>
                        <input type="text" name="national_id" required placeholder="10 أرقام" class="w-full border rounded px-3 py-1.5 font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">رقم الهاتف:</label>
                        <input type="text" name="phone" placeholder="079..." class="w-full border rounded px-3 py-1.5 font-mono">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">القسم:</label>
                        <select name="department" class="w-full border rounded px-3 py-1.5 font-bold">
                            <option value="الإدارة العامة">الإدارة العامة</option>
                            <option value="المالية والمحاسبة">المالية والمحاسبة</option>
                            <option value="المبيعات والتسويق">المبيعات والتسويق</option>
                            <option value="المستودعات واللوجستيات">المستودعات واللوجستيات</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">المسمى الوظيفي:</label>
                        <input type="text" name="position" required placeholder="محاسب، بائع..." class="w-full border rounded px-3 py-1.5">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">الراتب الأساسي:</label>
                        <input type="number" step="0.01" name="salary" required placeholder="700.00" class="w-full border rounded px-3 py-1.5 font-mono font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">تاريخ التعيين:</label>
                        <input type="date" name="hire_date" value="{{ date('Y-m-d') }}" class="w-full border rounded px-3 py-1.5 font-mono">
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button type="button" onclick="document.getElementById('newEmpModal').classList.add('hidden')" class="px-4 py-2 border rounded font-bold text-gray-600">إلغاء</button>
                    <button type="submit" class="px-5 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded font-bold">حفظ الموظف</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

