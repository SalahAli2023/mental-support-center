<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a single initial admin user (if not exists).
        $admin = User::firstOrCreate(
            ['email' => 'admin@therapy-platform.com'],
            [
                'name' => 'Platform Administrator',
                'password' => Hash::make('Therapy@2025!Secure'),
                'role' => 'Admin',
                'joined_at' => now(),
            ]
        );

        // Seed all core data needed for the platform
        $this->call([
            // محتوى الموقع الأمامي
            ArticleCategorySeeder::class,
            ArticleSeeder::class,
            CategoriesTableSeeder::class,
            EventsTableSeeder::class,
            LegalResourceCategorySeeder::class,
            LegalResourceSeeder::class,
            LibraryCategorySeeder::class,
            LibraryItemSeeder::class,
            PsychologicalScalesTableSeeder::class,
            ScaleQuestionsTableSeeder::class,
            QuestionOptionsTableSeeder::class,
            ResultInterpretationsTableSeeder::class,
            SiteStatisticSeeder::class,
            SpecializationSeeder::class,
            TherapistSeeder::class,
            TherapistScheduleSeeder::class,
            TherapistQualificationSeeder::class,
            TherapistCertificationSeeder::class,
            UserAssessmentsSeeder::class,
            // ProgramSeeder::class,
            ProgramPhaseSeeder::class,
            ProgramSessionSeeder::class,
            SessionActivitySeeder::class,
            SessionHomeworkSeeder::class,
            ProgramAssessmentSeeder::class,
            UserProgramSeeder::class,
            ActivityProgressSeeder::class,
            HomeworkSubmissionSeeder::class,
        ]);
    }
}
