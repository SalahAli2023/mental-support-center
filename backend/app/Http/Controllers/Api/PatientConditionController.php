<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientCondition;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class PatientConditionController extends Controller
{
    /**
     * عرض جميع الحالات الصحية لمريض معين
     */
    public function index(Request $request, $patientId)
    {
        $patient = Patient::findOrFail($patientId);
        
        $conditions = $patient->conditions()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'conditions' => $conditions
        ]);
    }

    /**
     * إضافة حالة صحية جديدة لمريض
     */
    public function store(Request $request, $patientId)
    {
        $patient = Patient::findOrFail($patientId);

        $validated = $request->validate([
            'condition_ar' => 'required|string|max:255',
            'condition_en' => 'required|string|max:255',
            'diagnosis_date' => 'nullable|date',
            'severity' => ['required', Rule::in(['mild', 'moderate', 'severe'])],
            'treatment_plan_ar' => 'nullable|string',
            'treatment_plan_en' => 'nullable|string',
            'medications_ar' => 'nullable|string',
            'medications_en' => 'nullable|string',
            'is_active' => 'boolean',
            'notes_ar' => 'nullable|string',
            'notes_en' => 'nullable|string',
        ]);

        // استخدام القيم الافتراضية للحقول غير المطلوبة
        $conditionData = [
            'condition_ar' => $validated['condition_ar'],
            'condition_en' => $validated['condition_en'],
            'diagnosis_date' => $validated['diagnosis_date'] ?? now(),
            'severity' => $validated['severity'],
            'treatment_plan_ar' => $validated['treatment_plan_ar'] ?? null,
            'treatment_plan_en' => $validated['treatment_plan_en'] ?? null,
            'medications_ar' => $validated['medications_ar'] ?? null,
            'medications_en' => $validated['medications_en'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'notes_ar' => $validated['notes_ar'] ?? null,
            'notes_en' => $validated['notes_en'] ?? null,
        ];

        $condition = $patient->conditions()->create($conditionData);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الحالة الصحية بنجاح',
            'condition' => $condition
        ], 201);
    }

    /**
     * تحديث حالة صحية
     */
    public function update(Request $request, $patientId, $conditionId)
    {
        $condition = PatientCondition::where('patient_id', $patientId)
            ->findOrFail($conditionId);

        $validated = $request->validate([
            'condition_ar' => 'sometimes|required|string|max:255',
            'condition_en' => 'sometimes|required|string|max:255',
            'diagnosis_date' => 'nullable|date',
            'severity' => ['sometimes', 'required', Rule::in(['mild', 'moderate', 'severe'])],
            'treatment_plan_ar' => 'nullable|string',
            'treatment_plan_en' => 'nullable|string',
            'medications_ar' => 'nullable|string',
            'medications_en' => 'nullable|string',
            'is_active' => 'boolean',
            'notes_ar' => 'nullable|string',
            'notes_en' => 'nullable|string',
        ]);

        $condition->update([
            'condition_ar' => $validated['condition_ar'] ?? $condition->condition_ar,
            'condition_en' => $validated['condition_en'] ?? $condition->condition_en,
            'diagnosis_date' => $validated['diagnosis_date'] ?? $condition->diagnosis_date,
            'severity' => $validated['severity'] ?? $condition->severity,
            'treatment_plan_ar' => $validated['treatment_plan_ar'] ?? $condition->treatment_plan_ar,
            'treatment_plan_en' => $validated['treatment_plan_en'] ?? $condition->treatment_plan_en,
            'medications_ar' => $validated['medications_ar'] ?? $condition->medications_ar,
            'medications_en' => $validated['medications_en'] ?? $condition->medications_en,
            'is_active' => $validated['is_active'] ?? $condition->is_active,
            'notes_ar' => $validated['notes_ar'] ?? $condition->notes_ar,
            'notes_en' => $validated['notes_en'] ?? $condition->notes_en,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الحالة الصحية بنجاح',
            'condition' => $condition->fresh()
        ]);
    }

    /**
     * حذف حالة صحية
     */
    public function destroy($patientId, $conditionId)
    {
        $condition = PatientCondition::where('patient_id', $patientId)
            ->findOrFail($conditionId);

        $condition->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الحالة الصحية بنجاح'
        ]);
    }

    /**
     * تفعيل/تعطيل حالة صحية
     */
    public function toggleStatus($patientId, $conditionId)
    {
        $condition = PatientCondition::where('patient_id', $patientId)
            ->findOrFail($conditionId);

        $condition->update([
            'is_active' => !$condition->is_active
        ]);

        $status = $condition->is_active ? 'مفعلة' : 'معطلة';

        return response()->json([
            'success' => true,
            'message' => "تم {$status} الحالة الصحية بنجاح",
            'condition' => $condition
        ]);
    }

    /**
     * الحصول على إحصائيات الحالات الصحية لمريض
     */
    public function getStats($patientId)
    {
        $patient = Patient::findOrFail($patientId);

        $stats = [
            'total_conditions' => $patient->conditions()->count(),
            'active_conditions' => $patient->conditions()->where('is_active', true)->count(),
            'mild_conditions' => $patient->conditions()->where('severity', 'mild')->count(),
            'moderate_conditions' => $patient->conditions()->where('severity', 'moderate')->count(),
            'severe_conditions' => $patient->conditions()->where('severity', 'severe')->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * استيراد حالات صحية جماعية
     */
    public function bulkImport(Request $request, $patientId)
    {
        $patient = Patient::findOrFail($patientId);

        $request->validate([
            'conditions' => 'required|array',
            'conditions.*.condition_ar' => 'required|string|max:255',
            'conditions.*.condition_en' => 'required|string|max:255',
            'conditions.*.severity' => ['required', Rule::in(['mild', 'moderate', 'severe'])],
        ]);

        DB::beginTransaction();
        try {
            $imported = [];

            foreach ($request->conditions as $conditionData) {
                $condition = $patient->conditions()->create([
                    'condition_ar' => $conditionData['condition_ar'],
                    'condition_en' => $conditionData['condition_en'],
                    'severity' => $conditionData['severity'],
                    'diagnosis_date' => $conditionData['diagnosis_date'] ?? now(),
                    'is_active' => $conditionData['is_active'] ?? true,
                ]);

                $imported[] = $condition;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "تم استيراد " . count($imported) . " حالة صحية",
                'imported' => $imported
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء استيراد الحالات الصحية',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تصدير الحالات الصحية لتقرير
     */
    public function export($patientId)
    {
        $patient = Patient::with('user')->findOrFail($patientId);
        
        $conditions = $patient->conditions()
            ->orderBy('severity')
            ->orderBy('created_at', 'desc')
            ->get();

        $reportData = [
            'patient' => [
                'name' => $patient->user->name,
                'email' => $patient->user->email,
                'phone' => $patient->user->phone,
            ],
            'conditions' => $conditions,
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'total_conditions' => $conditions->count(),
        ];

        return response()->json([
            'success' => true,
            'report' => $reportData
        ]);
    }
}