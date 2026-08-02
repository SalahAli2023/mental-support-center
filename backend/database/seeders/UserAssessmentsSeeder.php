<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\UserAssessment;
use App\Models\PsychologicalScale;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserAssessmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // تعطيل فحص المفاتيح الخارجية حسب نوع قاعدة البيانات
        $driver = DB::getDriverName();
        
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            UserAssessment::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
            // استخدام delete مع SQLite لتجنب مشاكل truncate
            UserAssessment::query()->delete();
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            // لقواعد البيانات الأخرى، استخدم delete بدلاً من truncate
            UserAssessment::query()->delete();
        }

        // الحصول على المستخدمين (إذا كان لديك نظام مستخدمين)
        $users = User::take(10)->get();
        
        // إذا لم يكن هناك مستخدمين، أنشئ بعض المستخدمين التجريبية
        if ($users->isEmpty()) {
            $users = User::factory()->count(5)->create();
        }

        // الحصول على المقاييس النشطة
        $scales = PsychologicalScale::where('is_active', true)->get();

        if ($scales->isEmpty()) {
            $this->command->warn('⚠️  لا توجد مقاييس نشطة. يرجى تشغيل PsychologicalScalesSeeder أولاً.');
            return;
        }

        $this->command->info('🎯 بدء إنشاء التقييمات...');

        // إنشاء تقييمات لكل مستخدم
        foreach ($users as $user) {
            $userAssessmentsCount = rand(2, 5); // 2-5 تقييم لكل مستخدم
            
            for ($i = 0; $i < $userAssessmentsCount; $i++) {
                // اختيار مقياس عشوائي مع التحقق
                if ($scales->count() > 0) {
                    $randomScale = $scales->random();
                    
                    try {
                        UserAssessment::factory()
                            ->forUser($user->id)
                            ->forScale($randomScale->id)
                            ->create();
                    } catch (\Exception $e) {
                        $this->command->warn("   ⚠️  خطأ في إنشاء تقييم للمقياس {$randomScale->id}: " . $e->getMessage());
                        continue;
                    }
                }
            }

            $this->command->info("✅ تم إنشاء تقييمات للمستخدم {$user->name}");
        }

        // إنشاء بعض التقييمات الإضافية (إذا كانت هناك مقاييس)
        if ($scales->count() > 0) {
            try {
                $additionalCount = min(10, $scales->count() * 2);
                UserAssessment::factory()
                    ->count($additionalCount)
                    ->create();
            } catch (\Exception $e) {
                $this->command->warn("   ⚠️  خطأ في إنشاء التقييمات الإضافية: " . $e->getMessage());
            }
        }

        $totalAssessments = UserAssessment::count();
        $this->command->info("🎉 تم إنشاء {$totalAssessments} تقييم بنجاح!");
        
        $this->command->info('📊 إحصائيات التقييمات:');
        $this->command->info('   - إجمالي التقييمات: ' . $totalAssessments);
        $this->command->info('   - متوسط النتائج: ' . round(UserAssessment::avg('total_score'), 2));
        $this->command->info('   - أحدث تقييم: ' . UserAssessment::max('completed_at'));
    }
}
