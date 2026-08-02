<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgramPhase;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PhaseController extends Controller
{
    /**
     * عرض جميع المراحل لبرنامج معين
     */
    public function index(Request $request, $programId): JsonResponse
    {
        try {
            $query = ProgramPhase::where('program_id', $programId);

            // التصفية حسب الحالة
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            if ($request->has('is_hidden')) {
                $query->where('is_hidden', $request->boolean('is_hidden'));
            }

            // الترتيب
            $query->orderBy('phase_order');

            $phases = $query->with(['sessions'])->get();

            return response()->json([
                'success' => true,
                'data' => $phases,
                'total' => $phases->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب المراحل: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * إنشاء مرحلة جديدة
     */
    public function store(Request $request, $programId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'phase_order' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
            'is_hidden' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $program = Program::findOrFail($programId);

            // تحديد ترتيب المرحلة إذا لم يُحدد
            if (!$request->has('phase_order')) {
                $maxOrder = ProgramPhase::where('program_id', $programId)->max('phase_order') ?? 0;
                $request->merge(['phase_order' => $maxOrder + 1]);
            }

            $data = $validator->validated();
            $data['id'] = (string) Str::uuid();
            $data['program_id'] = $programId;
            $data['is_active'] = $data['is_active'] ?? true;
            $data['is_hidden'] = $data['is_hidden'] ?? false;

            $phase = ProgramPhase::create($data);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء المرحلة بنجاح',
                'data' => $phase->load('sessions')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء المرحلة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض مرحلة محددة
     */
    public function show($programId, $id): JsonResponse
    {
        try {
            $phase = ProgramPhase::where('program_id', $programId)
                                ->with(['sessions.activities', 'sessions.homework'])
                                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $phase
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على المرحلة'
            ], 404);
        }
    }

    /**
     * تحديث مرحلة
     */
    public function update(Request $request, $programId, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name_ar' => 'sometimes|required|string|max:255',
            'name_en' => 'sometimes|required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'phase_order' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
            'is_hidden' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $phase = ProgramPhase::where('program_id', $programId)->findOrFail($id);
            $phase->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث المرحلة بنجاح',
                'data' => $phase->load('sessions')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث المرحلة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف مرحلة
     */
    public function destroy($programId, $id): JsonResponse
    {
        try {
            $phase = ProgramPhase::where('program_id', $programId)->findOrFail($id);
            
            // التحقق من وجود جلسات
            if ($phase->sessions()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف المرحلة لوجود جلسات مرتبطة بها'
                ], 422);
            }

            $phase->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المرحلة بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المرحلة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تغيير ترتيب المراحل (Drag & Drop)
     */
    public function reorder(Request $request, $programId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phases' => 'required|array',
            'phases.*.id' => 'required|uuid|exists:program_phases,id',
            'phases.*.order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($request->phases as $phaseData) {
                ProgramPhase::where('id', $phaseData['id'])
                           ->where('program_id', $programId)
                           ->update(['phase_order' => $phaseData['order']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث ترتيب المراحل بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الترتيب: ' . $e->getMessage()
            ], 500);
        }
    }
}





