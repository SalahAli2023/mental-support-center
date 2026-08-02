# دليل تشغيل Seeders للبرامج النفسية

## 📋 نظرة عامة

تم إنشاء Seeders شاملة لجميع جداول نظام البرامج النفسية العلاجية لسهولة الاختبار والتطوير.

## 🗂️ Seeders المتوفرة

### 1. **ProgramPhaseSeeder**
- ينشئ 3-5 مراحل لكل برنامج
- المراحل مرتبة بشكل متسلسل
- جميع المراحل نشطة وغير مخفية

### 2. **ProgramSessionSeeder** (محدث)
- ينشئ 5-8 جلسات لكل برنامج
- يوزع الجلسات على المراحل تلقائياً
- يربط الجلسات بالمراحل المناسبة

### 3. **SessionActivitySeeder** (محدث)
- ينشئ 3-6 أنشطة لكل جلسة
- يدعم جميع أنواع الأنشطة (text, video, audio, form, exercise, reflection_questions, quiz)
- أول نشاطين إلزاميين، الباقي اختياري

### 4. **SessionHomeworkSeeder**
- ينشئ 1-3 مهام منزلية لكل جلسة
- أول مهمة إلزامية، الباقي اختياري
- يدعم جميع أنواع الإكمال (confirmation, text_input, file_upload, form)

### 5. **ProgramAssessmentSeeder**
- ينشئ تقييم قبلي وبعدي لكل برنامج
- يربط التقييمات بالمقاييس النفسية الموجودة
- جميع التقييمات إلزامية ونشطة

### 6. **UserProgramSeeder**
- يسجل 1-3 مستخدمين في كل برنامج نشط
- ينشئ حالات متنوعة (enrolled, in_progress, completed, dropped)
- يضيف تقدم عشوائي لكل تسجيل

### 7. **ActivityProgressSeeder**
- ينشئ تقدم للمستخدمين في الأنشطة
- حالات متنوعة (not_started, in_progress, completed, locked)
- يضيف تواريخ بدء وإكمال واقعية

### 8. **HomeworkSubmissionSeeder**
- ينشئ تسليمات للمهام المنزلية
- حالات متنوعة (pending, submitted, completed, approved)
- يضيف نصوص تسليم واقعية

## 🚀 طريقة التشغيل

### تشغيل جميع Seeders (الطريقة الموصى بها)

```bash
cd backend
php artisan db:seed
```

### تشغيل Seeder محدد

```bash
# تشغيل Seeder للمراحل فقط
php artisan db:seed --class=ProgramPhaseSeeder

# تشغيل Seeder للمهام المنزلية فقط
php artisan db:seed --class=SessionHomeworkSeeder

# تشغيل Seeder للتقييمات فقط
php artisan db:seed --class=ProgramAssessmentSeeder
```

### إعادة تعيين قاعدة البيانات وتشغيل Seeders

```bash
# حذف جميع الجداول وإعادة إنشائها مع Seeders
php artisan migrate:fresh --seed

# أو مع إعادة تعيين كامل
php artisan migrate:refresh --seed
```

## 📊 ترتيب التشغيل الموصى به

إذا كنت تريد تشغيل Seeders بشكل منفصل، يجب اتباع هذا الترتيب:

1. **ProgramSeeder** - إنشاء البرامج
2. **ProgramPhaseSeeder** - إنشاء المراحل
3. **ProgramSessionSeeder** - إنشاء الجلسات (يربطها بالمراحل)
4. **SessionActivitySeeder** - إنشاء الأنشطة
5. **SessionHomeworkSeeder** - إنشاء المهام المنزلية
6. **ProgramAssessmentSeeder** - إنشاء التقييمات
7. **UserProgramSeeder** - تسجيل المستخدمين في البرامج
8. **ActivityProgressSeeder** - إنشاء تقدم الأنشطة
9. **HomeworkSubmissionSeeder** - إنشاء تسليمات المهام

## 📈 البيانات المتوقعة

بعد تشغيل جميع Seeders، ستحصل على:

- ✅ 5 برامج
- ✅ 15-25 مرحلة (3-5 لكل برنامج)
- ✅ 25-40 جلسة (5-8 لكل برنامج)
- ✅ 75-240 نشاط (3-6 لكل جلسة)
- ✅ 25-120 مهمة منزلية (1-3 لكل جلسة)
- ✅ 10 تقييمات (قبلي وبعدي لكل برنامج)
- ✅ 10-30 تسجيل مستخدم في البرامج
- ✅ 50-100 سجل تقدم في الأنشطة
- ✅ 25-100 تسليم مهمة منزلية

## ⚠️ ملاحظات مهمة

1. **الاعتماديات**: 
   - يجب تشغيل `ProgramSeeder` قبل `ProgramPhaseSeeder`
   - يجب تشغيل `ProgramPhaseSeeder` قبل `ProgramSessionSeeder`
   - يجب تشغيل `ProgramSessionSeeder` قبل `SessionActivitySeeder` و `SessionHomeworkSeeder`
   - يجب وجود مقاييس نفسية قبل `ProgramAssessmentSeeder`

2. **المستخدمون**: 
   - Seeders التقدم والتسليمات تحتاج مستخدمين (غير Admin)
   - تأكد من وجود مستخدمين في قاعدة البيانات

3. **البيانات العشوائية**: 
   - الأرقام والأحجام عشوائية ضمن النطاق المحدد
   - قد تختلف النتائج في كل تشغيل

## 🧪 اختبار النظام

بعد تشغيل Seeders، يمكنك:

1. **اختبار لوحة التحكم**:
   - عرض البرامج: `/admin/programs`
   - عرض تفاصيل برنامج: `/admin/programs/{id}`
   - إدارة المراحل والجلسات والأنشطة

2. **اختبار واجهة المستخدم**:
   - عرض البرامج: `/programs`
   - عرض برنامج محدد: `/program/{id}`
   - اختبار التدرج والإقفال

3. **اختبار API**:
   - استخدام Postman Collection الموجود
   - اختبار جميع Endpoints

## 🔄 إعادة التشغيل

إذا أردت إعادة تشغيل Seeders:

```bash
# حذف البيانات وإعادة إنشائها
php artisan migrate:fresh --seed
```

أو حذف بيانات محددة:

```sql
-- في قاعدة البيانات
DELETE FROM homework_submissions;
DELETE FROM activity_progress;
DELETE FROM user_programs;
DELETE FROM session_homework;
DELETE FROM session_activities;
DELETE FROM program_sessions;
DELETE FROM program_phases;
DELETE FROM program_assessments;
DELETE FROM programs;
```

ثم قم بتشغيل Seeders مرة أخرى.

## 📝 ملاحظات التطوير

- جميع Seeders تستخدم بيانات واقعية بالعربية والإنجليزية
- البيانات متنوعة لتغطية جميع السيناريوهات
- يمكن تعديل Seeders بسهولة لإضافة المزيد من البيانات

---

**تم إنشاء Seeders بنجاح! 🎉**




