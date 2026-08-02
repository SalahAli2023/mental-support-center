<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Therapist;
use App\Models\TherapistSchedule;

class TherapistScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $therapists = Therapist::all();

        $days = ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $timeSlots = [
            ['09:00', '12:00'],
            ['14:00', '17:00'],
            ['18:00', '21:00']
        ];

        foreach ($therapists as $therapist) {
            $workingDays = array_rand($days, rand(4, 6)); // 4-6 أيام عمل
            
            if (!is_array($workingDays)) {
                $workingDays = [$workingDays];
            }

            foreach ($workingDays as $dayIndex) {
                $day = $days[$dayIndex];
                $timeSlot = $timeSlots[array_rand($timeSlots)];
                
                TherapistSchedule::create([
                    'therapist_id' => $therapist->id,
                    'day' => $day,
                    'start_time' => $timeSlot[0],
                    'end_time' => $timeSlot[1],
                    'available' => true,
                    'recurrence' => 'weekly',
                    'slot_duration' => $therapist->session_duration
                ]);
            }
        }
    }
}