<?php

namespace Database\Seeders;

use App\Models\SiteStatistic;
use Illuminate\Database\Seeder;

class SiteStatisticSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            [
                'key' => 'countries',
                'label_ar' => 'دول',
                'label_en' => 'Countries',
                'value' => 12,
                'display_order' => 1,
                'icon' => 'Statistics/دولة.png',
            ],
            [
                'key' => 'questions',
                'label_ar' => 'أسئلة',
                'label_en' => 'Questions',
                'value' => 4500,
                'display_order' => 2,
                'icon' => 'Statistics/سؤال.png',
            ],
            [
                'key' => 'sessions',
                'label_ar' => 'جلسات',
                'label_en' => 'Sessions',
                'value' => 2105,
                'display_order' => 3,
                'icon' => 'Statistics/جلسات.png',
            ],
            [
                'key' => 'users',
                'label_ar' => 'مستخدمون',
                'label_en' => 'Users',
                'value' => 3320,
                'display_order' => 4,
                'icon' => 'Statistics/مستخدمون.png',
            ],
        ];

        foreach ($defaults as $stat) {
            SiteStatistic::updateOrCreate(
                ['key' => $stat['key']],
                $stat
            );
        }
    }
}




