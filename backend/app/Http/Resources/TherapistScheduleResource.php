<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TherapistScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // تحويل الوقت إلى string بصيغة HH:MM:SS أو HH:MM
        $startTime = $this->start_time;
        $endTime = $this->end_time;
        
        // إذا كان الوقت DateTime object، تحويله إلى string
        if ($startTime instanceof \DateTime || $startTime instanceof \Carbon\Carbon) {
            $startTime = $startTime->format('H:i:s');
        } elseif (is_string($startTime)) {
            // إذا كان string، التأكد من أنه بصيغة صحيحة
            $startTime = substr($startTime, 0, 8); // HH:MM:SS
        }
        
        if ($endTime instanceof \DateTime || $endTime instanceof \Carbon\Carbon) {
            $endTime = $endTime->format('H:i:s');
        } elseif (is_string($endTime)) {
            $endTime = substr($endTime, 0, 8); // HH:MM:SS
        }
        
        return [
            'id' => $this->id,
            'therapist_id' => $this->therapist_id,
            'day' => $this->day,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'available' => $this->available,
            'recurrence' => $this->recurrence,
            'slot_duration' => $this->slot_duration,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}