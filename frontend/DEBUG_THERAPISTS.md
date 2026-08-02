# Debug: مشكلة عرض المعالجين

## المشكلة
المعالجين الموجودين في قاعدة البيانات لا يتم عرضهم في الموقع العام في صفحة الأخصائيين.

## الحلول المطبقة

### 1. تحديث `TherapistList.vue`
- ✅ استبدال `fetch` بـ `api` من utils
- ✅ إضافة معالجة أفضل للبيانات
- ✅ إضافة حالات Loading و Error
- ✅ إضافة console.log للتحقق من البيانات

### 2. التحقق من API Response
API يجب أن يرجع البيانات بهذا الشكل:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name_ar": "...",
      "name_en": "...",
      "avatar": "...",
      "user": {
        "avatar": "..."
      },
      ...
    }
  ],
  "meta": {
    "current_page": 1,
    "total": 10,
    "per_page": 10
  }
}
```

### 3. خطوات التحقق

1. **افتح Developer Console** في المتصفح (F12)
2. **اذهب إلى صفحة الأخصائيين** (`/Specialists`)
3. **تحقق من Console Logs:**
   - يجب أن ترى: `Therapists API Response:`
   - يجب أن ترى: `Therapists data extracted: X therapists`
   - إذا كان X = 0، فالمشكلة في API أو البيانات

4. **تحقق من Network Tab:**
   - ابحث عن request إلى `/api/therapists`
   - تحقق من Response
   - تأكد من أن status code = 200
   - تحقق من بنية البيانات المرتجعة

### 4. حلول محتملة

#### إذا كانت البيانات فارغة:
- تحقق من أن هناك معالجين في قاعدة البيانات
- تحقق من أن المعالجين لديهم `status = 'active'` (إذا كان هناك filter)
- تحقق من أن العلاقة `user` محملة في الـ controller

#### إذا كان هناك خطأ في API:
- تحقق من Laravel logs: `backend/storage/logs/laravel.log`
- تحقق من CORS settings
- تحقق من أن API route يعمل: `GET /api/therapists`

#### إذا كانت البيانات موجودة لكن لا تظهر:
- تحقق من console logs للبيانات المعالجة
- تحقق من أن `therapists` array ليس فارغاً
- تحقق من filters في `TherapistListContent`

### 5. اختبار API مباشرة

افتح في المتصفح:
```
http://localhost:8000/api/therapists
```

يجب أن ترى JSON response مع البيانات.

### 6. إصلاحات إضافية محتملة

إذا كانت المشكلة مستمرة، قد نحتاج إلى:
- إضافة filter لـ `status = 'active'` في API
- التأكد من أن `user` relationship محمّل
- إضافة fallback للبيانات المفقودة


