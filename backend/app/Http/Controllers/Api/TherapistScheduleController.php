<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TherapistScheduleResource;
use App\Models\Therapist;
use App\Models\TherapistSchedule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TherapistScheduleController extends Controller
{
    /**
     * عرض جدول المعالج
     */
    public function index(Therapist $therapist): JsonResponse
    {
        $schedules = $therapist->schedules;

        return response()->json([
            'data' => TherapistScheduleResource::collection($schedules)
        ]);
    }

    /**
     * تخزين موعد جديد
     */
    public function store(Request $request, Therapist $therapist): JsonResponse
    {
        $validated = $request->validate([
            'day' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'available' => 'boolean',
            'recurrence' => 'in:weekly,biweekly,monthly',
            'slot_duration' => 'integer|min:1'
        ]);

        // التحقق من عدم وجود تداخل في المواعيد
        $conflictingSchedule = $therapist->schedules()
            ->where('day', $validated['day'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                      });
            })
            ->first();

        if ($conflictingSchedule) {
            return response()->json([
                'message' => 'Schedule conflict detected'
            ], 422);
        }

        $schedule = $therapist->schedules()->create($validated);

        return response()->json([
            'message' => 'Schedule added successfully',
            'data' => new TherapistScheduleResource($schedule)
        ], 201);
    }

    /**
     * تحديث الموعد
     */
    public function update(Request $request, Therapist $therapist, TherapistSchedule $schedule): JsonResponse
    {
        $validated = $request->validate([
            'day' => 'sometimes|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'available' => 'boolean',
            'recurrence' => 'in:weekly,biweekly,monthly',
            'slot_duration' => 'integer|min:1'
        ]);

        $schedule->update($validated);

        return response()->json([
            'message' => 'Schedule updated successfully',
            'data' => new TherapistScheduleResource($schedule)
        ]);
    }

    /**
     * حذف الموعد
     */
    public function destroy(Therapist $therapist, TherapistSchedule $schedule): JsonResponse
    {
        $schedule->delete();

        return response()->json([
            'message' => 'Schedule deleted successfully'
        ]);
    }
}