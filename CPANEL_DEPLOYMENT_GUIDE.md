# دليل نشر وتثبيت منظومة وثيق على سيرفر cPanel (HostGator)
### النطاق الفرعي: `https://watheeq.dctechjo.com`
### قاعدة البيانات: `juygbnte_dctechjo_watheeq`

---

## 📋 الخطوات العملية خطوة بخطوة:

### الخطوة 1: إنشاء قاعدة البيانات في cPanel
1. ادخل إلى لوحة تحكم **cPanel** في HostGator.
2. توجه إلى **MySQL Databases**.
3. قم بإنشاء قاعدة بيانات باسم: `juygbnte_dctechjo_watheeq`.
4. تأكد من إضافة المستخدم `juygbnte_admin` إلى قاعدة البيانات وإعطائه **ALL PRIVILEGES**.

---

### الخطوة 2: إنشاء النطاق الفرعي (Subdomain)
1. في cPanel اذهب إلى **Domains** أو **Subdomains**.
2. أضف النطاق الفرعي:
   * **Subdomain:** `watheeq`
   * **Domain:** `dctechjo.com`
   * **Document Root:** 
     * *إذا كان بإمكانك تحديده:* اختر `public_html/watheeq/public` أو `watheeq.dctechjo.com/public`
     * *إذا تم إنشاؤه افتراضياً كـ `watheeq.dctechjo.com`:* لا تقلق، فقد وضعنا لك ملف `.htaccess` يقوم بالتحويل تلقائياً إلى مجلد `public`.

---

### الخطوة 3: رفع ملفات المشروع عبر Git أو File Manager

#### الخيار (أ) - عبر Git Version Control في cPanel (الأسهل والأسرع):
1. افتح **Git Version Control** في cPanel.
2. اضغط **Create**.
3. أدخل رابط مستودع GitHub:
   ```
   https://github.com/AmmarAl-saadi/WATHEEQ.git
   ```
4. حدد مسار المجلد (مثلاً `watheeq.dctechjo.com`).
5. اضغط **Create**.

#### الخيار (ب) - رفع ملف ZIP عبر File Manager:
1. قم بضغط مجلد `watheeq` بصيغة ZIP (بدون مجلد vendor أو node_modules لتوفير الحجم).
2. ارفعه إلى مسار النطاق الفرعي وفك الضغط.
3. قم بتشغيل `composer install` عبر الـ Terminal.

---

### الخطوة 4: ضبط ملف البيئة `.env`
1. داخل مجلد المشروع على السيرفر، انسخ محتويات ملف `.env.production` وضعه في ملف باسم **`.env`**.
2. تأكد من احتواء الملف على الإعدادات التالية:
```env
APP_NAME="وثيق - Watheeq ERP"
APP_ENV=production
APP_KEY=base64:EeXKx9otmhwX0yMh9EUB0gPwugynWY3qoat1hSmBUwA=
APP_DEBUG=false
APP_URL=https://watheeq.dctechjo.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=juygbnte_dctechjo_watheeq
DB_USERNAME=juygbnte_admin
DB_PASSWORD=Omar1992$$
```

---

### الخطوة 5: تهيئة الجداول وتشغيل المايجريشن
من خلال الـ **Terminal** داخل cPanel (أو عبر SSH)، ادخل لمجلد المشروع واكتب:
```bash
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### الخطوة 6: تسجيل الدخول
افتح المتصفح على:
👉 **`https://watheeq.dctechjo.com/login`**

* **البريد الإلكتروني:** `admin@dctechjo.com`
* **كلمة المرور:** `Omar1992$$`
