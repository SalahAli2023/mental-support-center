<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgramAssessment;
use App\Models\ProgramAssessmentResult;
use App\Models\Program;
use App\Models\UserAssessment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProgramAssessmentController extends Controller
{
    /**
     * عرض جميع التقييمات لبرنامج معين
     */
    public function index(Request $request, $programId): JsonResponse
    {
        try {
            $assessments = ProgramAssessment::where('program_id', $programId)
                                           ->with('scale')
                                           ->orderBy('order')
                                           ->get();

            return response()->json([
                'success' => true,
                'data' => $assessments,
                'total' => $assessments->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب التقييمات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * إنشاء تقييم جديد
     */
    public function store(Request $request, $programId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'scale_id' => 'required|exists:psychological_scales,id',
            'assessment_type' => 'required|in:pre,post',
            'is_mandatory' => 'nullable|boolean',
            'order' => 'nullable|integer|min:1',
            'instructions_ar' => 'nullable|string',
            'instructions_en' => 'nullable|string',
            'is_active' => 'nullable|boolean',
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

            // تحديد ترتيب التقييم إذا لم يُحدد
            if (!$request->has('order')) {
                $maxOrder = ProgramAssessment::where('program_id', $programId)->max('order') ?? 0;
                $request->merge(['order' => $maxOrder + 1]);
            }

            $data = $validator->validated();
            $data['id'] = (string) Str::uuid();
            $data['program_id'] = $programId;
            $data['is_mandatory'] = $data['is_mandatory'] ?? true;
            $data['is_active'] = $data['is_active'] ?? true;

            $assessment = ProgramAssessment::create($data);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء التقييم بنجاح',
                'data' => $assessment->load('scale')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء التقييم: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض تقييم محدد
     */
    public function show($programId, $id): JsonResponse
    {
        try {
            $assessment = ProgramAssessment::where('program_id', $programId)
                                           ->with(['scale', 'results.user'])
                                           ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $assessment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على التقييم'
            ], 404);
        }
    }

    /**
     * تحديث تقييم
     */
    public function update(Request $request, $programId, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'scale_id' => 'sometimes|required|exists:psychological_scales,id',
            'assessment_type' => 'sometimes|required|in:pre,post',
            'is_mandatory' => 'nullable|boolean',
            'order' => 'nullable|integer|min:1',
            'instructions_ar' => 'nullable|string',
            'instructions_en' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $assessment = ProgramAssessment::where('program_id', $programId)->findOrFail($id);
            $assessment->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث التقييم بنجاح',
                'data' => $assessment->load('scale')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث التقييم: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف تقييم
     */
    public function destroy($programId, $id): JsonResponse
    {
        try {
            $assessment = ProgramAssessment::where('program_id', $programId)->findOrFail($id);
            $assessment->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف التقييم بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف التقييم: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * حفظ نتيجة تقييم من المستخدم
     */
    public function submitResult(Request $request, $programId, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_assessment_id' => 'required|exists:user_assessments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $assessment = ProgramAssessment::where('program_id', $programId)->findOrFail($id);
            $userId = auth()->id();

            $userAssessment = UserAssessment::findOrFail($request->user_assessment_id);

            // التحقق من أن التقييم يخص المستخدم
            if ($userAssessment->user_id !== $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بالوصول إلى هذا التقييم'
                ], 403);
            }

            // إنشاء أو تحديث نتيجة التقييم
            $result = ProgramAssessmentResult::updateOrCreate(
                [
                    'program_assessment_id' => $assessment->id,
                    'user_id' => $userId
                ],
                [
                    'user_assessment_id' => $userAssessment->id,
                    'total_score' => $userAssessment->total_score,
                    'interpretation_level' => $userAssessment->interpretation_level,
                    'assessment_data' => $userAssessment->assessment_data,
                    'completed_at' => $userAssessment->completed_at ?? now()
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ نتيجة التقييم بنجاح',
                'data' => $result->load('userAssessment')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ النتيجة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على نتائج التقييمات (قبل/بعد) لمستخدم معين
     */
    public function getUserResults($programId, $userId = null): JsonResponse
    {
        try {
            $targetUserId = $userId ?? auth()->id();
            
            $program = Program::findOrFail($programId);
            $preAssessment = $program->preAssessments()->first();
            $postAssessment = $program->postAssessments()->first();

            $results = [
                'pre_assessment' => null,
                'post_assessment' => null,
                'comparison' => null
            ];

            if ($preAssessment) {
                $preResult = ProgramAssessmentResult::where('program_assessment_id', $preAssessment->id)
                                                   ->where('user_id', $targetUserId)
                                                   ->with('userAssessment')
                                                   ->first();
                $results['pre_assessment'] = $preResult;
            }

            if ($postAssessment) {
                $postResult = ProgramAssessmentResult::where('program_assessment_id', $postAssessment->id)
                                                    ->where('user_id', $targetUserId)
                                                    ->with('userAssessment')
                                                    ->first();
                $results['post_assessment'] = $postResult;
            }

            // مقارنة النتائج
            if ($results['pre_assessment'] && $results['post_assessment']) {
                $preScore = $results['pre_assessment']->total_score ?? 0;
                $postScore = $results['post_assessment']->total_score ?? 0;
                $difference = $postScore - $preScore;
                $percentageChange = $preScore > 0 ? round(($difference / $preScore) * 100, 2) : 0;

                $results['comparison'] = [
                    'pre_score' => $preScore,
                    'post_score' => $postScore,
                    'difference' => $difference,
                    'percentage_change' => $percentageChange,
                    'improvement' => $difference < 0 ? true : false // تحسن إذا انخفضت النتيجة (في معظم المقاييس)
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب النتائج: ' . $e->getMessage()
            ], 500);
        }
    }
}




