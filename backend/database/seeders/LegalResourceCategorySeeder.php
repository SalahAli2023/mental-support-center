<?php

namespace Database\Seeders;

use App\Models\LegalResourceCategory;
use Illuminate\Database\Seeder;

class LegalResourceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['key' => 'family', 'name_ar' => 'الأسرة', 'name_en' => 'Family'],
            ['key' => 'education', 'name_ar' => 'التعليم', 'name_en' => 'Education'],
            ['key' => 'health', 'name_ar' => 'الصحة', 'name_en' => 'Health'],
            ['key' => 'employment', 'name_ar' => 'العمل', 'name_en' => 'Employment'],
            ['key' => 'protection', 'name_ar' => 'الحماية', 'name_en' => 'Protection'],
            ['key' => 'political_participation', 'name_ar' => 'المشاركة السياسية', 'name_en' => 'Political Participation'],
            ['key' => 'nationality', 'name_ar' => 'الجنسية', 'name_en' => 'Nationality'],
            ['key' => 'marriage', 'name_ar' => 'الزواج', 'name_en' => 'Marriage'],
            ['key' => 'maintenance', 'name_ar' => 'النفقة', 'name_en' => 'Maintenance'],
            ['key' => 'custody', 'name_ar' => 'الحضانة', 'name_en' => 'Custody'],
            ['key' => 'visitation', 'name_ar' => 'الزيارة', 'name_en' => 'Visitation'],
            ['key' => 'domestic_violence', 'name_ar' => 'العنف الأسري', 'name_en' => 'Domestic Violence'],
            ['key' => 'reporting', 'name_ar' => 'الإبلاغ', 'name_en' => 'Reporting'],
            ['key' => 'penalties', 'name_ar' => 'العقوبات', 'name_en' => 'Penalties'],
            ['key' => 'women_protection', 'name_ar' => 'حماية المرأة', 'name_en' => 'Women Protection'],
            ['key' => 'maternity_leave', 'name_ar' => 'إجازة الأمومة', 'name_en' => 'Maternity Leave'],
            ['key' => 'breastfeeding', 'name_ar' => 'الرضاعة', 'name_en' => 'Breastfeeding'],
            ['key' => 'protection_from_dismissal', 'name_ar' => 'الحماية من الفصل', 'name_en' => 'Protection from Dismissal'],
            ['key' => 'right_to_life', 'name_ar' => 'الحق في الحياة', 'name_en' => 'Right to Life'],
            ['key' => 'identity', 'name_ar' => 'الهوية', 'name_en' => 'Identity'],
            ['key' => 'child_protection', 'name_ar' => 'حماية الطفل', 'name_en' => 'Child Protection'],
        ];

        foreach ($categories as $category) {
            LegalResourceCategory::create($category);
        }
    }
}


