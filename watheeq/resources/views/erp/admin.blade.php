@extends('layouts.app')

@section('title', 'لوحة تحكم المسؤول وتخصيص الواجهات | وثيق ERP')

@section('content')
<div class="space-y-6" dir="rtl">

    <!-- Admin Master Banner -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-[#1F2E4A] text-white">لوحة تحكم النظام</span>
                <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-[#1F2E4A] border border-blue-200">إدارة الحسابات وتخصيص الواجهات</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 mt-2">لوحة تحكم المسؤول: تخصيص النظام وحسابات المستخدمين</h1>
            <p class="text-xs text-gray-500 mt-0.5">تحكم كامل وسهل بواجهات كل مستخدم بدون أي حاجة لخبرة تقنية أو برمجية</p>
        </div>
        <div class="flex gap-2.5">
            <a href="{{ route('erp.admin.permissions') }}" class="px-4 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                🛡️ مصفوفة الصلاحيات (حساباتي Manage)
            </a>
            <button onclick="document.getElementById('newUserModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                إضافة مستخدم جديد
            </button>
            <a href="{{ route('erp.jofotara.hub') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-xl text-xs font-bold transition border">
                إعدادات الفوترة (JoFotara)
            </a>
        </div>
    </div>

    <!-- Interactive Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-gray-200 pb-2">
        <button type="button" onclick="switchAdminTab('usersTab')" id="tabBtn_usersTab" class="admin-tab-btn px-4 py-2 rounded-xl text-xs font-bold transition bg-[#1F2E4A] text-white">
            حسابات المستخدمين ({{ $users->count() }})
        </button>
        <button type="button" onclick="switchAdminTab('myInterfaceTab')" id="tabBtn_myInterfaceTab" class="admin-tab-btn px-4 py-2 rounded-xl text-xs font-bold transition bg-gray-100 text-gray-700 hover:bg-gray-200">
            تخصيص واجهتي الحالية ({{ $currentUser->name ?? 'المدير' }})
        </button>
        <button type="button" onclick="switchAdminTab('companyTab')" id="tabBtn_companyTab" class="admin-tab-btn px-4 py-2 rounded-xl text-xs font-bold transition bg-gray-100 text-gray-700 hover:bg-gray-200">
            بيانات المنشأة والضريبة
        </button>
    </div>

    <!-- ── TAB 1: User Accounts & Profiles ── -->
    <div id="usersTab" class="admin-tab-pane space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($users as $u)
                @php
                    $uMods = is_string($u->enabled_modules) ? json_decode($u->enabled_modules, true) : ($u->enabled_modules ?? []);
                    if (!is_array($uMods)) $uMods = [];
                    $isCurrent = ($currentUser->id ?? null) == $u->id;
                @endphp
                <div class="bg-white rounded-2xl border {{ $isCurrent ? 'border-[#1F2E4A] ring-2 ring-[#1F2E4A]/20' : 'border-gray-200' }} p-5 shadow-sm space-y-4 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">{{ $u->name }}</h3>
                                <div class="text-[11px] text-gray-500 font-medium mt-0.5">
                                    اسم الدخول: <span class="font-mono text-[#1F2E4A] font-bold">{{ $u->username ?? $u->email }}</span>
                                    <span class="text-gray-300 mx-1">|</span>
                                    <span>{{ $u->email }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#1F2E4A]/10 text-[#1F2E4A] border border-[#1F2E4A]/20">
                                    {{ $u->role_name_ar }}
                                </span>
                                @if(!$u->is_active)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-100 text-rose-700">معطل</span>
                                @endif
                            </div>
                        </div>

                        <!-- User Active Modules Summary -->
                        <div class="mt-4 pt-3 border-t border-gray-100 space-y-2">
                            <span class="text-[10px] text-gray-400 font-bold block">الأقسام المتاحة في واجهته ({{ count($uMods) }} أقسام):</span>
                            <div class="flex flex-wrap gap-1">
                                @forelse($uMods as $mKey)
                                    <span class="text-[10px] bg-gray-100 text-gray-700 font-bold px-2 py-0.5 rounded">
                                        {{ $modulesCatalog[$mKey]['title'] ?? $mKey }}
                                    </span>
                                @empty
                                    <span class="text-[10px] text-gray-400 font-medium">الواجهة فارغة (يجب تخصيص أقسام)</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- User Actions -->
                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-2">
                        @if(!$isCurrent)
                            <a href="{{ route('erp.users.switch', $u->id) }}" class="flex-1 text-center py-1.5 px-3 bg-blue-50 hover:bg-blue-100 text-[#1F2E4A] font-bold text-xs rounded-xl border border-blue-200 transition">
                                تبديل ومعاينة الواجهة
                            </a>
                        @else
                            <span class="flex-1 text-center py-1.5 px-3 bg-emerald-50 text-emerald-800 font-bold text-xs rounded-xl border border-emerald-200">
                                الحساب النشط حالياً
                            </span>
                        @endif

                        <button type="button" onclick="openEditUserModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ addslashes($u->username ?? '') }}', '{{ addslashes($u->email) }}', '{{ addslashes($u->role_name_ar) }}', {{ json_encode($uMods) }}, {{ $u->is_active ? 'true' : 'false' }})" class="p-1.5 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl border transition">
                            تعديل وتخصيص
                        </button>

                        @if($users->count() > 1 && !$isCurrent)
                            <form action="{{ route('erp.admin.users.delete', $u->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 px-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded-xl border border-rose-200 transition">
                                    حذف
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ── TAB 2: My Current Interface Customizer ── -->
    <div id="myInterfaceTab" class="admin-tab-pane hidden space-y-6">
        <form action="{{ route('erp.admin.users.update', $currentUser->id ?? 1) }}" method="POST" class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-6">
            @csrf
            <div class="border-b pb-4 flex justify-between items-center">
                <div>
                    <h2 class="text-base font-bold text-[#1F2E4A]">تخصيص الواجهة للمستخدم الحالي: {{ $currentUser->name ?? 'المدير' }}</h2>
                    <p class="text-xs text-gray-500">حدد بالضبط التبويبات والأدوات التي ترغب بظهورها في شاشتك اليومية</p>
                </div>
                <input type="hidden" name="name" value="{{ $currentUser->name ?? 'المدير' }}">
                <input type="hidden" name="role_name_ar" value="{{ $currentUser->role_name_ar ?? 'مدير النظام' }}">
                <button type="submit" class="px-5 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                    حفظ واجهتي المخصصة فوراً
                </button>
            </div>

            <!-- Top Navigation Modules Toggles -->
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-[#1F2E4A]">1. التبويبات الرئيسية في الشريط العلوي (Main Navigation Tabs):</span>
                    <span class="text-[11px] text-gray-400">انقر لتفعيل أو إخفاء أي قسم</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($modulesCatalog as $mKey => $mInfo)
                        @php
                            $isChecked = in_array($mKey, $userModules);
                        @endphp
                        <label class="flex items-start gap-3 p-3.5 rounded-xl border transition cursor-pointer {{ $isChecked ? 'bg-blue-50/60 border-[#1F2E4A]/40' : 'bg-gray-50 border-gray-200 hover:bg-white' }}">
                            <input type="checkbox" name="modules[]" value="{{ $mKey }}" {{ $isChecked ? 'checked' : '' }} class="mt-1 w-4 h-4 text-[#1F2E4A] rounded border-gray-300 focus:ring-0">
                            <div>
                                <span class="block text-xs font-bold text-gray-900">{{ $mInfo['title'] }}</span>
                                <span class="block text-[11px] text-gray-500 mt-0.5">{{ $mInfo['desc'] }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Sidebar Tools Toggles -->
            <div class="space-y-3 pt-4 border-t border-gray-100">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-[#1F2E4A]">2. أدوات الشريط الجانبي السريع (Sidebar Quick Tools):</span>
                    <span class="text-[11px] text-gray-400">حدد الروابط المختصرة التي تستخدمها بانتظام</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2.5">
                    @foreach($toolsCatalog as $tKey => $tTitle)
                        @php
                            $isToolChecked = in_array($tKey, $userTools);
                        @endphp
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border transition cursor-pointer {{ $isToolChecked ? 'bg-blue-50/60 border-[#1F2E4A]/30' : 'bg-gray-50 border-gray-200 hover:bg-white' }}">
                            <input type="checkbox" name="tools[]" value="{{ $tKey }}" {{ $isToolChecked ? 'checked' : '' }} class="w-3.5 h-3.5 text-[#1F2E4A] rounded border-gray-300 focus:ring-0">
                            <span class="text-xs font-medium text-gray-800">{{ $tTitle }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                    حفظ وتطبيق الواجهة الآن
                </button>
            </div>
        </form>
    </div>

    <!-- ── TAB 3: Company & Tax Settings ── -->
    <div id="companyTab" class="admin-tab-pane hidden space-y-6">
        <form action="{{ route('erp.customizer.update') }}" method="POST" class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
            @csrf
            <div class="border-b pb-3 flex justify-between items-center">
                <div>
                    <h2 class="text-base font-bold text-[#1F2E4A]">بيانات المنشأة وهوية النظام</h2>
                    <p class="text-xs text-gray-500">تعديل اسم المنشأة، العملة، الأرقام الضريبية ونصوص الفواتير الرسمية</p>
                </div>
                <button type="submit" class="px-5 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white rounded-xl text-xs font-bold transition shadow-sm">
                    حفظ بيانات المنشأة
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold mb-1">اسم المنشأة بالعربية</label>
                    <input type="text" name="name" value="{{ $tenant->name }}" required class="w-full p-2.5 border rounded-xl bg-gray-50">
                </div>
                <div>
                    <label class="block font-semibold mb-1">اسم المنشأة بالإنجليزية</label>
                    <input type="text" name="name_en" value="{{ $tenant->name_en }}" class="w-full p-2.5 border rounded-xl bg-gray-50">
                </div>
                <div>
                    <label class="block font-semibold mb-1">الرقم الضريبي الرسمي</label>
                    <input type="text" name="tax_number" value="{{ $tenant->tax_number }}" class="w-full p-2.5 border rounded-xl bg-gray-50 font-mono">
                </div>
                <div>
                    <label class="block font-semibold mb-1">السجل التجاري</label>
                    <input type="text" name="commercial_record" value="{{ $tenant->commercial_record }}" class="w-full p-2.5 border rounded-xl bg-gray-50 font-mono">
                </div>
                <div>
                    <label class="block font-semibold mb-1">العملة الأساسية</label>
                    <select name="currency" class="w-full p-2.5 border rounded-xl bg-gray-50 font-bold">
                        <option value="JOD" {{ $tenant->currency === 'JOD' ? 'selected' : '' }}>دينار أردني (JOD)</option>
                        <option value="USD" {{ $tenant->currency === 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                        <option value="SAR" {{ $tenant->currency === 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                        <option value="AED" {{ $tenant->currency === 'AED' ? 'selected' : '' }}>درهم إماراتي (AED)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1">نسبة ضريبة المبيعات (%)</label>
                    <input type="number" step="0.01" name="tax_rate" value="{{ $tenant->tax_rate }}" class="w-full p-2.5 border rounded-xl bg-gray-50 font-mono">
                </div>
                <div>
                    <label class="block font-semibold mb-1">نموذج الطباعة الافتراضي</label>
                    <select name="print_format" class="w-full p-2.5 border rounded-xl bg-gray-50">
                        <option value="A4" {{ $tenant->print_format === 'A4' ? 'selected' : '' }}>ورق قياسي A4</option>
                        <option value="Thermal80mm" {{ $tenant->print_format === 'Thermal80mm' ? 'selected' : '' }}>طابعة فواتير حرارية (80mm)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold mb-1">هاتف التواصل</label>
                    <input type="text" name="phone" value="{{ $tenant->phone }}" class="w-full p-2.5 border rounded-xl bg-gray-50">
                </div>
                <div class="md:col-span-2">
                    <label class="block font-semibold mb-1">العنوان الكامل</label>
                    <input type="text" name="address" value="{{ $tenant->address }}" class="w-full p-2.5 border rounded-xl bg-gray-50">
                </div>
                <div class="md:col-span-2">
                    <label class="block font-semibold mb-1">شروط وملاحظات الفاتورة الثابتة أسفل الصفحة</label>
                    <textarea name="invoice_notes" rows="2" class="w-full p-2.5 border rounded-xl bg-gray-50">{{ $tenant->invoice_notes }}</textarea>
                </div>
            </div>

            <div class="pt-3 border-t flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-[#1F2E4A] hover:bg-[#343A40] text-white text-xs font-bold rounded-xl shadow">
                    حفظ التعديلات في قاعدة البيانات
                </button>
            </div>
        </form>
    </div>

</div>

<!-- ── MODAL: Create New User ── -->
<div id="newUserModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl" dir="rtl">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-sm text-[#1F2E4A]">إضافة حساب مستخدم جديد وتخصيص واجهته</h3>
            <button type="button" onclick="document.getElementById('newUserModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700 font-bold text-xs">[إغلاق]</button>
        </div>

        <form action="{{ route('erp.admin.users.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold mb-1">اسم الموظف / المستخدم</label>
                <input type="text" name="name" required placeholder="مثال: أحمد عبد الله" class="w-full p-2.5 border rounded-xl bg-gray-50 font-bold">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-semibold mb-1">اسم الدخول (Username)</label>
                    <input type="text" name="username" required placeholder="cashier1" class="w-full p-2.5 border rounded-xl bg-gray-50 font-mono font-bold" dir="ltr">
                </div>
                <div>
                    <label class="block font-semibold mb-1">البريد الإلكتروني</label>
                    <input type="email" name="email" required placeholder="ahmad@watheeq.jo" class="w-full p-2.5 border rounded-xl bg-gray-50 font-mono" dir="ltr">
                </div>
                <div>
                    <label class="block font-semibold mb-1">كلمة المرور</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full p-2.5 border rounded-xl bg-gray-50 font-mono">
                </div>
            </div>

            <div>
                <label class="block font-semibold mb-1">نوع الدور الوظيفي (تجهيز فوري للواجهة بنقرة واحدة)</label>
                <select name="role" id="newUserRoleSelect" onchange="onRolePresetChange(this.value)" class="w-full p-2.5 border rounded-xl bg-gray-50 font-bold">
                    <option value="admin">مدير النظام (Super Admin - يرى كل شيء)</option>
                    <option value="accountant">محاسب مالي معتمد (الفواتير، السندات، التقارير والفوترة)</option>
                    <option value="cashier">كاشير نقاط بيع (POS والمبيعات السريعة فقط)</option>
                    <option value="hr">مسؤول موارد بشرية (الموظفين، الرواتب، الدوام)</option>
                    <option value="hotel">موظف استقبال وفنادق (حجوزات الغرف وسجل النزلاء)</option>
                    <option value="custom">مخصص يدوي (أنا سأحدد التبويبات بالأسفل)</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold mb-1">المسمى الوظيفي بالعربية (يظهر في الترويسة)</label>
                <input type="text" name="role_name_ar" id="newUserRoleNameAr" value="مدير النظام (Super Admin)" class="w-full p-2.5 border rounded-xl bg-gray-50">
            </div>

            <div class="space-y-2 pt-2 border-t">
                <span class="block font-semibold text-gray-700">تخصيص التبويبات المتاحة له في الشريط العلوي:</span>
                <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto p-2 border rounded-xl bg-gray-50">
                    @foreach($modulesCatalog as $mKey => $mInfo)
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="modules[]" value="{{ $mKey }}" checked class="new-user-mod-check w-3.5 h-3.5 text-[#1F2E4A] rounded">
                            <span class="text-[11px] font-medium">{{ $mInfo['title'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="document.getElementById('newUserModal').classList.add('hidden')" class="px-4 py-2 border rounded-xl hover:bg-gray-100 font-bold">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white font-bold rounded-xl shadow">حفظ وإنشاء الحساب</button>
            </div>
        </form>
    </div>
</div>

<!-- ── MODAL: Edit User Interface ── -->
<div id="editUserModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl" dir="rtl">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-sm text-[#1F2E4A]">تخصيص واجهة المستخدم: <span id="editModalUserName"></span></h3>
            <button type="button" onclick="document.getElementById('editUserModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700 font-bold text-xs">[إغلاق]</button>
        </div>

        <form id="editUserForm" method="POST" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold mb-1">اسم الموظف</label>
                    <input type="text" name="name" id="editUserName" required class="w-full p-2.5 border rounded-xl bg-gray-50 font-bold">
                </div>
                <div>
                    <label class="block font-semibold mb-1">اسم الدخول (Username)</label>
                    <input type="text" name="username" id="editUserUsername" class="w-full p-2.5 border rounded-xl bg-gray-50 font-mono font-bold" dir="ltr">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold mb-1">البريد الإلكتروني</label>
                    <input type="email" name="email" id="editUserEmail" class="w-full p-2.5 border rounded-xl bg-gray-50 font-mono" dir="ltr">
                </div>
                <div>
                    <label class="block font-semibold mb-1">كلمة مرور جديدة (اتركه فارغاً للإبقاء عليها)</label>
                    <input type="password" name="password" placeholder="••••••••" class="w-full p-2.5 border rounded-xl bg-gray-50 font-mono">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold mb-1">المسمى الوظيفي</label>
                    <input type="text" name="role_name_ar" id="editUserRoleName" class="w-full p-2.5 border rounded-xl bg-gray-50">
                </div>
                <div class="flex items-center pt-4">
                    <label class="flex items-center gap-2 cursor-pointer font-bold text-gray-700">
                        <input type="checkbox" name="is_active" id="editUserActive" value="1" class="w-4 h-4 text-[#1F2E4A] rounded">
                        <span>الحساب مفعل ونشط للعمل بالنظام</span>
                    </label>
                </div>
            </div>

            <div class="space-y-2 pt-2 border-t">
                <span class="block font-semibold text-gray-700">التبويبات المسموح بها في شريط التنقل:</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-3 border rounded-xl bg-gray-50">
                    @foreach($modulesCatalog as $mKey => $mInfo)
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="modules[]" value="{{ $mKey }}" id="editMod_{{ $mKey }}" class="w-3.5 h-3.5 text-[#1F2E4A] rounded">
                            <span class="text-[11px] font-medium">{{ $mInfo['title'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="document.getElementById('editUserModal').classList.add('hidden')" class="px-4 py-2 border rounded-xl hover:bg-gray-100 font-bold">إلغاء</button>
                <button type="submit" class="px-5 py-2 bg-[#1F2E4A] hover:bg-[#343A40] text-white font-bold rounded-xl shadow">حفظ التعديلات</button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchAdminTab(tabId) {
        document.querySelectorAll('.admin-tab-pane').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.admin-tab-btn').forEach(el => {
            el.classList.remove('bg-[#1F2E4A]', 'text-white');
            el.classList.add('bg-gray-100', 'text-gray-700');
        });
        document.getElementById(tabId).classList.remove('hidden');
        const activeBtn = document.getElementById('tabBtn_' + tabId);
        activeBtn.classList.remove('bg-gray-100', 'text-gray-700');
        activeBtn.classList.add('bg-[#1F2E4A]', 'text-white');
    }

    const rolePresets = {
        admin: {
            title: 'مدير النظام (Super Admin)',
            modules: ['dashboard', 'definitions', 'invoicing', 'hr', 'hotels', 'pos', 'reports', 'jofotara', 'customizer']
        },
        accountant: {
            title: 'محاسب مالي معتمد',
            modules: ['dashboard', 'definitions', 'invoicing', 'reports', 'jofotara']
        },
        cashier: {
            title: 'كاشير مبيعات ونقاط بيع',
            modules: ['dashboard', 'pos', 'invoicing']
        },
        hr: {
            title: 'مسؤول شؤون الموظفين (HR)',
            modules: ['dashboard', 'hr', 'reports']
        },
        hotel: {
            title: 'موظف استقبال وضيافة',
            modules: ['dashboard', 'hotels', 'invoicing']
        },
        custom: {
            title: 'مستخدم مخصص',
            modules: ['dashboard']
        }
    };

    function onRolePresetChange(role) {
        const preset = rolePresets[role] || rolePresets.custom;
        document.getElementById('newUserRoleNameAr').value = preset.title;

        document.querySelectorAll('.new-user-mod-check').forEach(chk => {
            chk.checked = preset.modules.includes(chk.value);
        });
    }

    function openEditUserModal(userId, userName, userUsername, userEmail, userRole, userModules, isActive) {
        document.getElementById('editModalUserName').textContent = userName;
        document.getElementById('editUserName').value = userName;
        document.getElementById('editUserUsername').value = userUsername || '';
        document.getElementById('editUserEmail').value = userEmail || '';
        document.getElementById('editUserRoleName').value = userRole;
        document.getElementById('editUserActive').checked = (isActive === true || isActive === 'true' || isActive === 1);
        document.getElementById('editUserForm').action = '/admin/users/' + userId;

        // Reset all checkboxes
        document.querySelectorAll('#editUserForm input[type="checkbox"][name="modules[]"]').forEach(chk => {
            chk.checked = userModules.includes(chk.value);
        });

        document.getElementById('editUserModal').classList.remove('hidden');
    }
</script>
@endsection

