# مواصفات تطوير الـ APIs للفرونت (مطابقة Figma — طبقة الباك اند فقط)

هذا المستند يحدد **التعديلات المقترحة على شكل مراحل** للموديولات الثلاثة:

- `Modules/Category`
- `Modules/Product`
- `Modules/Auth`

مع الإشارة إلى أن **الطلبات (Order)** و**الدفع (Payment)** ستكون في موديولات منفصلة لاحقاً ولا تُفصَّل هنا إلا كحدود نطاق.

المرجع الوظيفي: شاشات المتجر في Figma (قائمة منتجات بفلاتر وترتيب، تفاصيل منتج، تسجيل/دخول، فئات، إلخ) دون بناء واجهة في هذا المشروع.

---

## 1. الوضع الحالي (Baseline)

> **حالة التنفيذ الحالية:**  
> - ✅ تم تنفيذ **Phase Category** بالكامل (بما يشمل الشجرة، الـ breadcrumb، وفلاتر صفحة القسم).  
> - ✅ تم تنفيذ **Phase Product** بالكامل (فلاتر متقدمة + related products + view counter endpoint).  
> - ⏳ المتبقي: **Phase Auth** + تحسينات الجودة العامة.

### 1.1 `Modules/Category/Routes/api.php`

| المسار | الوصف |
|--------|--------|
| `GET /api/v1/categories` | يدعم: `search`, `is_active`, `per_page`, `tree`, `sidebar`, `max_depth`, `parent_id`, `include=children` |
| `GET /api/v1/categories/{slug}` | صفحة فئة كاملة مع `filters`, `brands`, `sorting_options`, `products` (pagination) + فلاتر متقدمة |
| `GET /api/v1/categories/{slug}/breadcrumb` | سلسلة breadcrumb من الجذر إلى الفئة الحالية |

### 1.2 `Modules/Product/Routes/api.php`

| المسار | الوصف |
|--------|--------|
| `GET /api/v1/products` | يدعم: `category`, `brand`, `price_min`, `price_max`, `sort`, `per_page`, `is_new`, `is_featured`/`featured`, `color`, `size`, `material`, `search`/`q` |
| `GET /api/v1/products/{slug}` | تفاصيل المنتج + `related_products` |
| `POST /api/v1/products/{slug}/view` | زيادة عداد المشاهدات `views_count` |

### 1.3 `Modules/Auth/Routes/api.php`

| المسار | الوصف |
|--------|--------|
| `POST /api/v1/auth/login` | عميل فقط (`type !== admin`)، يتطلب بريداً مفعّلاً |
| `POST /api/v1/auth/register` | إنشاء عميل + OTP للتحقق + إصدار token |
| `POST /api/v1/auth/verify` | تأكيد البريد بالكود |
| `POST /api/v1/auth/resend-verification-code` | إعادة إرسال الكود |
| `POST /api/v1/auth/forgot-password` | طلب إعادة تعيين |
| `POST /api/v1/auth/reset-password` | إعادة تعيين بكلمة مرور |
| `GET /api/v1/auth/profile` | `auth:sanctum` — يعيد **`$request->user()` خام** |
| `POST /api/v1/auth/logout` | `auth:sanctum` |

---

## 2. خارج النطاق الحالي (يُذكر للتنسيق مع الفرونت)

- سلّة، Checkout، عناوين شحن، خيارات تغليف، ملخص طلب — **موديول Order (لاحقاً)**.
- بوابات دفع، حالة الدفع، Webhooks — **موديول Payment (لاحقاً)**.
- قائمة أمنيات (Wishlist) كموارد مستقلة — غير موجودة؛ يمكن لاحقاً (موديول مستخدم/منتجات مفضلة) مع **نفس `auth:sanctum`**.

---

## 3. اتفاقيات موحّدة للفرونت (توثيق — تطبيق حسب الحاجة)

| البند | القيمة المقترحة |
|--------|------------------|
| Base URL | `/api/v1` |
| مصادقة | Header: `Authorization: Bearer {token}` |
| ترقيم Laravel | استجابة `data`, `links`, `meta` كما في `ProductController::paginationPayload` |
| أخطاء التحقق | `422` + `message` + `errors` (سلوك FormRequest الافتراضي) |
| تنسيق السعر للعرض | الفرونت يطبّق العملة من إعداداته؛ الباك اند يُرجع أرقاماً عشرية (`float`) كما في `ProductResource` |

---

## المرحلة 1 — Category API (قائمة الفئات + شجرة/قائمة التنقل + صفحة الفئة)

**الحالة:** ✅ **تم التنفيذ**

**الهدف:** دعم الـ Header/Sidebar في Figma (تصنيفات، مسارات Breadcrumb، وربما صور مصغرة للقائمة) وصفحة فئة كاملة بفلاتر وترتيب يعملان من الـ API.

### 1.1 تحسين `GET /api/v1/categories`

| التعديل | التفاصيل |
|---------|-----------|
| معاملات اختيارية | `parent_id` أو `root_only=true` لإرجاع الجذر فقط؛ `with_children` لتحميل مستوى واحد من الأبناء |
| تضمين الأبناء في المورد | توسيع `CategoryResource` بـ `children` عند الطلب (لتقليل الحجم: `?include=children`) |
| ترتيب ثابت | الإبقاء على `sort_order` ثم `name` |

**المطبق فعلياً:**
- حقول DB جديدة للفئات: `show_in_sidebar`, `menu_order`.
- `GET /api/v1/categories` يدعم الشجرة (`tree=1`) والتصفية الملاحية (`sidebar=1`) وعمق الشجرة (`max_depth`) وتحديد الأب (`parent_id`) وتحميل أبناء مستوى واحد (`include=children`).
- `CategoryResource` أصبح يرجع: `image_url`, `banner_url`, `icon_url`, `show_in_sidebar`, `menu_order`, `children_count` و`children` عند الطلب.

### 1.2 تحسين `GET /api/v1/categories/{slug}`

| التعديل | التفاصيل |
|---------|-----------|
| استعلامات Query | `sort` (نفس قيم `IndexProductRequest`: `latest`, `price_asc`, `price_desc`, `best_seller`, `trending`) |
| فلترة | `brand` (slug أو id)،`material` (slug قيمة خاصية)،`color` (slug قيمة خاصية)،`price_min`،`price_max` |
| ترقيم | `page`, `per_page` |
| ملء `filters` | مصفوفة خيارات جاهزة للـ UI: على الأقل **Brand**, **Material**, **Color** مع `key`, `label`, `values[]` (slug + label + عدد منتجات اختياري) ضمن نطاق المنتجات المرتبطة بالفئة |
| ملء `brands` | قائمة علامات متوفرة داخل هذه الفئة (مطابقة شريط الفلتر في Figma) |
| توحيد شكل `products` | إما إبقاء نفس شكل الترقيم الحالي أو لفّ النتيجة في `data` موحّد مع `GET /products` لتسهيل مكوّن واحد في الفرونت |

**المطبق فعلياً:**
- `CategoryFilterService` أصبح يرجع `filters` و`brands` ديناميكياً من المنتجات داخل الفئة (ومع descendants افتراضياً).
- يدعم Query params: `include_descendants`, `sort`, `brand`, `color`, `size`, `material`, `price_min`, `price_max`, `per_page`, `page`.
- الفلاتر مبنية عبر جداول attributes/values الخاصة بالـ variants.

### 1.3 (اختياري) مسار إضافي للـ Breadcrumb

| مسار مقترح | الغرض |
|-------------|--------|
| `GET /api/v1/categories/{slug}/breadcrumb` | سلسلة من `{ id, name, slug }` من الجذر إلى الفئة الحالية |

**المطبق فعلياً:** endpoint breadcrumb موجود ويستخدم `path` لإخراج السلسلة من الجذر حتى الفئة.

---

## المرحلة 2 — Product API (قوائم متعددة السياقات + تفاصيل + منتجات ذات صلة)

**الحالة:** ✅ **تم التنفيذ**

**الهدف:** صفحات مثل New Arrival، Best Seller، Brand/Prada، والشبكة العامة بنفس شكل البطاقة؛ وتفاصيل المنتج مع Related كما في Figma.

### 2.1 تحسين `GET /api/v1/products`

| معامل جديد / تعديل | الغرض (من Figma) |
|---------------------|-------------------|
| `is_new=true` | صفحة "New Arrival" (`is_new` موجود في الموديل والمورد) |
| `is_featured=true` أو `featured=1` | أقسام مميزة (إن وُجدت في التصميم) |
| `material`, `color` | فلترة بنفس منطق الفئة (قيم slug لخصائص variant) |
| `q` أو `search` | بحث نصي بسيط (اسم/وصف) لأيقونة Search في الهيدر |
| توحيد `sort` | التأكد أن `best_seller` و `trending` معرّفة بوضوح في التوثيق |

**المطبق فعلياً:**
- `IndexProductRequest` يدعم المعاملات الجديدة: `is_new`, `is_featured`, `featured`, `color`, `size`, `material`, `search`, `q`.
- `ProductFilterService` يدعم:
  - فلترة `is_new` و`is_featured`.
  - فلترة attributes (`color`, `size`, `material`) عبر variants/attributeValues/attribute code.
  - بحث نصي في `name`, `short_description`, `description`.

### 2.2 تحسين `GET /api/v1/products/{slug}`

| التعديل | التفاصيل |
|---------|-----------|
| `related` | ✅ مطبق: `GET /products/{slug}` يرجع `related_products` (استبعاد المنتج الحالي، بحد 8) |
| عداد مشاهدات | ✅ مطبق: `POST /api/v1/products/{slug}/view` يزيد `views_count` ويرجع القيمة المحدثة |

### 2.3 مورد بطاقة موحّد (اختياري لكن مُستحسن)

| التعديل | التفاصيل |
|---------|-----------|
| `ProductCardResource` أو `ProductSummaryResource` | ✅ مطبق: `ProductCardResource` مستخدم في `GET /products` و`related_products` لتقليل حجم payload |

---

## المرحلة 3 — Auth API (تسجيل/دخول مطابق لحقول Figma + ملف شخصي منظم)

**الهدف:** الفرونت يرسل الحقول كما في التصميم (اسم أول/أخير، إظهار token profile متسق) دون كسر العملاء الحاليين إن أمكن.

### 3.1 تسجيل ومستخدم

| التعديل | التفاصيل |
|---------|-----------|
| حقول التسجيل | دعم `first_name` و `last_name` (مع إبقاء `name` اختياري أو مُولَّد تلقائياً `first_name + ' ' + last_name` للتوافق مع الجداول الحالية) |
| `RegisterRequest` / `RegisterService` | تحديث القواعد والحفظ |
| مورد مستخدم | `UserResource` للـ API: `id`, `first_name`, `last_name`, `name`, `email`, `phone`, `email_verified_at`, `type` — واستخدامه في `login`, `register`, `verify`, `profile` |

### 3.2 تسجيل الدخول والملف

| التعديل | التفاصيل |
|---------|-----------|
| `profile` | `return UserResource::make($request->user())` بدلاً من الموديل الخام |
| (اختياري) `PATCH /api/v1/auth/profile` | تحديث الاسم والهاتف لصفحة الحساب لاحقاً |

### 3.3 OAuth (Google / Facebook) — مرحلة فرعية اختيارية

| البند | الملاحظة |
|--------|-----------|
| مسارات مقترحة | `POST /api/v1/auth/social/{provider}` أو package مثل Laravel Socialite + إصدار Sanctum token |
| النطاق | خارج الحد الأدنى إذا كان الفرونت سيستخدم فقط email/password في الإصدار الأول |

### 3.4 سياسات الأمان (مراجعة)

| البند | السلوك الحالي | مقترح للتوثيق للفرونت |
|--------|----------------|------------------------|
| تسجيل الدخول | يتطلب `email_verified_at` | واضح في رسائل الخطأ؛ الفرونت يوجّه لشاشة إدخال الكود بعد `register` |
| الأدمن | مرفوض من API login | متوقع |

---

## المرحلة 4 — جودة API مشتركة (الموديولات الثلاثة)

| البند | الإجراء |
|--------|---------|
| CORS | ضبط `config/cors.php` لنطاق الفرونت |
| `Accept: application/json` | التأكد أن الاستثناءات ترجع JSON للـ API |
| توثيق OpenAPI | ملف `openapi.yaml` أو Scribe/Bump يغطي المسارات أعلاه |
| اختبارات Feature | اختبارات لمسارات Category show (فلاتر + ترقيم)، Product index (فلتر خاصية)، Auth (Register بحقول جديدة) |

---

## 5. خريطة مسارات بعد التنفيذ (مرجع سريع)

```
# Category
GET    /api/v1/categories
GET    /api/v1/categories/{slug}
GET    /api/v1/categories/{slug}/breadcrumb    ← اختياري

# Product
GET    /api/v1/products
GET    /api/v1/products/{slug}
POST   /api/v1/products/{slug}/view            ← اختياري

# Auth
POST   /api/v1/auth/login
POST   /api/v1/auth/register
POST   /api/v1/auth/verify
POST   /api/v1/auth/resend-verification-code
POST   /api/v1/auth/forgot-password
POST   /api/v1/auth/reset-password
GET    /api/v1/auth/profile
PATCH  /api/v1/auth/profile                  ← اختياري
POST   /api/v1/auth/logout
POST   /api/v1/auth/social/{provider}        ← اختياري لاحقاً
```

---

## 6. ترتيب التنفيذ المقترح في السبرينت

1. ✅ **Category:** تم التنفيذ (service + query params + breadcrumb + شجرة + tests).
2. ✅ **Product:** تم التنفيذ (filters + related + product cards + view endpoint + tests).
3. ⏳ **Auth:** `UserResource` + `first_name`/`last_name` + توحيد استجابات الملف الشخصي.
4. ⏳ **تلميع:** توثيق OpenAPI/Postman, CORS, واستكمال سيناريوهات الاختبارات.

---

*آخر تحديث: يعكس التنفيذ الفعلي الحالي لـ `Category` و`Product` + المتبقي في `Auth` وجودة الـ API.*
