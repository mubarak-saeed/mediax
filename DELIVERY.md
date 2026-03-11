# MediaX — دليل النشر والتسليم

## ملخص المشروع

| البند | القيمة |
|-------|--------|
| الإطار | Laravel 12.53 |
| لوحة الإدارة | Filament v5 |
| الدفع | Stripe via Laravel Cashier v16 |
| المصادقة | Email/Password + Google OAuth (Socialite) |
| البحث | Laravel Scout (database driver) |
| الواجهة | Blade + Livewire 4 + Tailwind CSS v4 |
| PHP | 8.2+ |
| قاعدة البيانات | MySQL |

---

## 1. متطلبات السيرفر

- PHP 8.2+ مع الإضافات: `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `intl`, `curl`, `fileinfo`
- MySQL 8.0+
- Composer 2.x
- Node.js 20+ و npm (للبناء فقط)
- SSL Certificate (مطلوب للإنتاج)

---

## 2. خطوات النشر

```bash
# 1. رفع الملفات ونسخ الإعدادات
cp .env.example .env
# ثم تعبئة جميع المتغيرات في .env (انظر القسم التالي)

# 2. تثبيت التبعيات
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 3. توليد مفتاح التطبيق
php artisan key:generate

# 4. تهيئة قاعدة البيانات
php artisan migrate --force

# 5. بذر البيانات الأولية (الأقسام والخطط والأدمن)
php artisan db:seed --force

# 6. ربط التخزين العام
php artisan storage:link

# 7. تحسين الأداء
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan icons:cache

# 8. ضبط الصلاحيات (Linux)
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 3. متغيرات البيئة المطلوبة (.env)

### أساسي
| المتغير | الوصف | مثال |
|---------|-------|------|
| `APP_NAME` | اسم التطبيق | `MediaX` |
| `APP_ENV` | **يجب أن يكون** `production` | `production` |
| `APP_DEBUG` | **يجب أن يكون** `false` | `false` |
| `APP_URL` | الرابط الكامل مع HTTPS | `https://mediax.com` |
| `APP_KEY` | يُولَّد تلقائيًا | `php artisan key:generate` |

### قاعدة البيانات
| المتغير | الوصف |
|---------|-------|
| `DB_HOST` | عنوان خادم MySQL |
| `DB_DATABASE` | اسم قاعدة البيانات |
| `DB_USERNAME` | اسم المستخدم |
| `DB_PASSWORD` | **كلمة مرور قوية** |

### الجلسات
| المتغير | القيمة الموصاة |
|---------|---------------|
| `SESSION_DRIVER` | `database` |
| `SESSION_ENCRYPT` | `true` |
| `SESSION_SECURE_COOKIE` | `true` (مع HTTPS) |

### البريد
| المتغير | الوصف |
|---------|-------|
| `MAIL_MAILER` | `smtp` |
| `MAIL_HOST` | خادم SMTP |
| `MAIL_USERNAME` | اسم المستخدم |
| `MAIL_PASSWORD` | كلمة المرور |
| `MAIL_FROM_ADDRESS` | البريد المرسل |

### Stripe (الدفع)
| المتغير | الوصف |
|---------|-------|
| `STRIPE_KEY` | المفتاح العام (`pk_live_...`) |
| `STRIPE_SECRET` | المفتاح السري (`sk_live_...`) |
| `STRIPE_WEBHOOK_SECRET` | سر الـ Webhook (`whsec_...`) |
| `STRIPE_PRICE_BASIC` | معرف سعر الخطة الأساسية |
| `STRIPE_PRICE_PRO` | معرف سعر خطة برو |
| `STRIPE_PRICE_PREMIUM` | معرف سعر خطة بريميوم |
| `CASHIER_CURRENCY` | العملة (مثال: `usd`) |

### Google OAuth
| المتغير | الوصف |
|---------|-------|
| `GOOGLE_CLIENT_ID` | معرف عميل Google |
| `GOOGLE_CLIENT_SECRET` | سر عميل Google |

### كلمة مرور الأدمن الأولية
| المتغير | الوصف |
|---------|-------|
| `ADMIN_PASSWORD` | كلمة مرور الأدمن عند البذر الأول (**غيّرها فورًا**) |

### البحث والسجلات
| المتغير | القيمة الموصاة |
|---------|---------------|
| `SCOUT_DRIVER` | `database` |
| `LOG_LEVEL` | `warning` |
| `QUEUE_CONNECTION` | `database` أو `redis` |

---

## 4. إعداد Stripe Webhook

في لوحة Stripe، أضف Webhook Endpoint:
- **URL:** `https://your-domain.com/stripe/webhook`
- **الأحداث المطلوبة:**
  - `invoice.payment_succeeded`
  - `customer.subscription.updated`
  - `customer.subscription.deleted`

---

## 5. لوحة التحكم (Filament)

- **المسار:** `/admin`
- **الدخول:** مستخدم بدور `admin` أو `superadmin`
- **الإدارة:** الأقسام، الأقسام الفرعية، المحتوى، المواسم والحلقات، المستخدمين، الخطط، المدفوعات، الأنواع

### حسابات الإدارة الافتراضية

| البريد | الدور | ملاحظة |
|--------|-------|--------|
| `superadmin@mediax.com` | مدير النظام | **غيّر كلمة المرور فورًا** |
| `admin@mediax.com` | مدير المنصة | **غيّر كلمة المرور فورًا** |

---

## 6. المهام المجدولة (Cron)

```cron
* * * * * cd /path/to/mediax && php artisan schedule:run >> /dev/null 2>&1
```

---

## 7. ما تم مراجعته وإصلاحه

### إصلاحات حرجة
- **عطل البحث (500):** تفعيل Scout `Searchable` trait في موديل Content
- **تسريب بيانات حساسة:** إزالة جميع المفاتيح والأسرار من `.env.example`
- **كلمات مرور مكشوفة:** استبدال كلمات المرور المكتوبة مباشرة في Seeder بـ `Hash::make()` مع متغير بيئة
- **كود ميت:** إزالة كود غير قابل للوصول في `SubscriptionController` واستعلام مهمل في `SeasonController`

### إصلاحات أمنية
- **ContentPolicy:** استبدال `auth('web')->user()` بالمستخدم الممرر للدالة
- **حماية مسار الملفات:** إضافة فحص path traversal (`..`) في `StreamController` و `StreamEpisodeController`
- **سلامة البيانات:** استخدام `insertOrIgnore()` بدل `insert()` في `ContentViewService` لمنع التكرار
- **خطأ في حذف الملفات:** تصحيح `deleteContentFile()` في `MediaFileCleanupService` لحذف `file_url` بدل `poster_url`

### تنظيف الكود
- إزالة `use function Symfony\Component\Clock\now` غير المستخدم
- إزالة فراغات زائدة وكود ميت
- تصحيح مقارنة `==` إلى `===` في `isSeriesOrMovieSerial()`

---

## 8. الاختبارات

```bash
php artisan test --compact
# النتيجة: 26 اختبار ناجح (48 assertion)
```

---

## 9. قائمة التحقق قبل الإطلاق

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] `APP_URL` يطابق الدومين مع HTTPS
- [ ] `SESSION_ENCRYPT=true`
- [ ] `LOG_LEVEL=warning`
- [ ] كلمة مرور قاعدة البيانات قوية
- [ ] جميع مفاتيح Stripe حية (`pk_live_...`) وليست اختبارية (`pk_test_...`)
- [ ] Stripe Webhook مسجل ومفعّل
- [ ] مفاتيح Google OAuth محدّثة لدومين الإنتاج
- [ ] `npm run build` تم تنفيذه
- [ ] `php artisan storage:link` تم تنفيذه
- [ ] `php artisan config:cache` تم تنفيذه
- [ ] شهادة SSL مفعّلة
- [ ] كلمات مرور الأدمن الافتراضية تم تغييرها
- [ ] `QUEUE_CONNECTION` مضبوط على `database` أو `redis` (وليس `sync`)
- [ ] CRON job مضاف لـ `schedule:run`
- [ ] صلاحيات `storage/` و `bootstrap/cache/` صحيحة

تمت المراجعة والتأكد من جاهزية المشروع للتسليم وفق الحالة الحالية.
