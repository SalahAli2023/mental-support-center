<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TherapistQualificationResource;
use App\Models\Therapist;
use App\Models\TherapistQualification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TherapistQualificationController extends Controller
{
    /**
     * عرض مؤهلات المعالج
     */
    public function index(Therapist $therapist): JsonResponse
    {
        $qualifications = $therapist->qualifications;

        return response()->json([
            'data' => TherapistQualificationResource::collection($qualifications)
        ]);
    }

    /**
     * تخزين مؤهل جديد
     */
    public function store(Request $request, Therapist $therapist): JsonResponse
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'institution_ar' => 'required|string|max:255',
            'institution_en' => 'required|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . date('Y')
        ]);

        $qualification = $therapist->qualifications()->create($validated);

        return response()->json([
            'message' => 'Qualification added successfully',
            'data' => new TherapistQualificationResource($qualification)
        ], 201);
    }

    /**
     * تحديث المؤهل
     */
    public function update(Request $request, Therapist $therapist, TherapistQualification $qualification): JsonResponse
    {
        $validated = $request->validate([
            'name_ar' => 'sometimes|string|max:255',
            'name_en' => 'sometimes|string|max:255',
            'institution_ar' => 'sometimes|string|max:255',
            'institution_en' => 'sometimes|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . date('Y')
        ]);

        $qualification->update($validated);

        return response()->json([
            'message' => 'Qualification updated successfully',
            'data' => new TherapistQualificationResource($qualification)
        ]);
    }

    /**
     * حذف المؤهل
     */
    public function destroy(Therapist $therapist, TherapistQualification $qualification): JsonResponse
    {
        $qualification->delete();

        return response()->json([
            'message' => 'Qualification deleted successfully'
        ]);
    }
}