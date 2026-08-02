<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TherapistCertificationResource;
use App\Models\Therapist;
use App\Models\TherapistCertification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TherapistCertificationController extends Controller
{
    /**
     * عرض شهادات المعالج
     */
    public function index(Therapist $therapist): JsonResponse
    {
        $certifications = $therapist->certifications;

        return response()->json([
            'data' => TherapistCertificationResource::collection($certifications)
        ]);
    }

    /**
     * تخزين شهادة جديدة
     */
    public function store(Request $request, Therapist $therapist): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'issuing_authority' => 'nullable|string|max:255',
            'year_obtained' => 'nullable|integer|min:1900|max:' . date('Y')
        ]);

        $certification = $therapist->certifications()->create($validated);

        return response()->json([
            'message' => 'Certification added successfully',
            'data' => new TherapistCertificationResource($certification)
        ], 201);
    }

    /**
     * تحديث الشهادة
     */
    public function update(Request $request, Therapist $therapist, TherapistCertification $certification): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'issuing_authority' => 'nullable|string|max:255',
            'year_obtained' => 'nullable|integer|min:1900|max:' . date('Y')
        ]);

        $certification->update($validated);

        return response()->json([
            'message' => 'Certification updated successfully',
            'data' => new TherapistCertificationResource($certification)
        ]);
    }

    /**
     * حذف الشهادة
     */
    public function destroy(Therapist $therapist, TherapistCertification $certification): JsonResponse
    {
        $certification->delete();

        return response()->json([
            'message' => 'Certification deleted successfully'
        ]);
    }
}