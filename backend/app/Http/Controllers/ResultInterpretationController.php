<?php

namespace App\Http\Controllers;

use App\Models\ResultInterpretation;
use App\Http\Resources\ResultInterpretationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class ResultInterpretationController extends Controller
{
    /**
     * عرض جميع تفسيرات النتائج
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = ResultInterpretation::query();

        // التصفية حسب المقياس
        if ($request->has('scale_id')) {
            $query->where('scale_id', $request->scale_id);
        }

        // التحميل مع العلاقات
        $query->with(['scale']);

        // الترتيب حسب النقاط الدنيا
        $query->orderBy('min_score');

        $interpretations = $query->paginate($request->get('per_page', 20));

        return ResultInterpretationResource::collection($interpretations);
    }

    /**
     * إنشاء تفسير جديد
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scale_id' => 'required|exists:psychological_scales,id',
            'min_score' => 'required|integer|min:0',
            'max_score' => 'required|integer|min:0|gte:min_score',
            'interpretation_label_ar' => 'required|string|max:100',
            'interpretation_label_en' => 'required|string|max:100',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        // التحقق من عدم تداخل النطاقات
        $overlapping = ResultInterpretation::where('scale_id', $validated['scale_id'])
            ->where(function($query) use ($validated) {
                $query->whereBetween('min_score', [$validated['min_score'], $validated['max_score']])
                      ->orWhereBetween('max_score', [$validated['min_score'], $validated['max_score']])
                      ->orWhere(function($q) use ($validated) {
                          $q->where('min_score', '<=', $validated['min_score'])
                            ->where('max_score', '>=', $validated['max_score']);
                      });
            })
            ->exists();

        if ($overlapping) {
            return response()->json([
                'message' => 'نطاق النقاط يتداخل مع تفسير موجود مسبقاً'
            ], 422);
        }

        DB::beginTransaction();

        try {
            $interpretation = ResultInterpretation::create($validated);

            DB::commit();

            return response()->json([
                'message' => 'تم إنشاء التفسير بنجاح',
                'data' => new ResultInterpretationResource($interpretation->load(['scale']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'فشل في إنشاء التفسير',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض تفسير محدد
     */
    public function show(ResultInterpretation $resultInterpretation): ResultInterpretationResource
    {
        $resultInterpretation->load(['scale']);

        return new ResultInterpretationResource($resultInterpretation);
    }

    /**
     * تحديث تفسير
     */
    public function update(Request $request, ResultInterpretation $resultInterpretation): JsonResponse
    {
        $validated = $request->validate([
            'min_score' => 'sometimes|required|integer|min:0',
            'max_score' => 'sometimes|required|integer|min:0|gte:min_score',
            'interpretation_label_ar' => 'sometimes|required|string|max:100',
            'interpretation_label_en' => 'sometimes|required|string|max:100',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'color' => 'nullable|string|max:20',
        ]);

        // التحقق من عدم تداخل النطاقات (استثناء السجل الحالي)
        if (isset($validated['min_score']) || isset($validated['max_score'])) {
            $minScore = $validated['min_score'] ?? $resultInterpretation->min_score;
            $maxScore = $validated['max_score'] ?? $resultInterpretation->max_score;

            $overlapping = ResultInterpretation::where('scale_id', $resultInterpretation->scale_id)
                ->where('id', '!=', $resultInterpretation->id)
                ->where(function($query) use ($minScore, $maxScore) {
                    $query->whereBetween('min_score', [$minScore, $maxScore])
                          ->orWhereBetween('max_score', [$minScore, $maxScore])
                          ->orWhere(function($q) use ($minScore, $maxScore) {
                              $q->where('min_score', '<=', $minScore)
                                ->where('max_score', '>=', $maxScore);
                          });
                })
                ->exists();

            if ($overlapping) {
                return response()->json([
                    'message' => 'نطاق النقاط يتداخل مع تفسير موجود مسبقاً'
                ], 422);
            }
        }

        $resultInterpretation->update($validated);

        return response()->json([
            'message' => 'تم تحديث التفسير بنجاح',
            'data' => new ResultInterpretationResource($resultInterpretation->fresh()->load(['scale']))
        ]);
    }

    /**
     * حذف تفسير
     */
    public function destroy(ResultInterpretation $resultInterpretation): JsonResponse
    {
        DB::beginTransaction();

        try {
            $resultInterpretation->delete();

            DB::commit();

            return response()->json([
                'message' => 'تم حذف التفسير بنجاح'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'فشل في حذف التفسير',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على التفسير المناسب للنقاط
     */
    public function getInterpretation($scaleId, $score): JsonResponse
    {
        $interpretation = ResultInterpretation::where('scale_id', $scaleId)
            ->forScore($score)
            ->first();

        if (!$interpretation) {
            return response()->json([
                'message' => 'لا يوجد تفسير مناسب لهذه النقاط'
            ], 404);
        }

        return response()->json([
            'data' => new ResultInterpretationResource($interpretation->load(['scale']))
        ]);
    }

    /**
     * إنشاء عدة تفسيرات دفعة واحدة
     */
    public function bulkStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scale_id' => 'required|exists:psychological_scales,id',
            'interpretations' => 'required|array|min:1',
            'interpretations.*.min_score' => 'required|integer|min:0',
            'interpretations.*.max_score' => 'required|integer|min:0|gte:interpretations.*.min_score',
            'interpretations.*.interpretation_label_ar' => 'required|string|max:100',
            'interpretations.*.interpretation_label_en' => 'required|string|max:100',
            'interpretations.*.description_ar' => 'nullable|string',
            'interpretations.*.description_en' => 'nullable|string',
            'interpretations.*.color' => 'nullable|string|max:20',
        ]);

        DB::beginTransaction();

        try {
            $interpretations = [];

            foreach ($validated['interpretations'] as $interpretationData) {
                $interpretationData['scale_id'] = $validated['scale_id'];
                
                // التحقق من عدم التداخل
                $overlapping = ResultInterpretation::where('scale_id', $validated['scale_id'])
                    ->where(function($query) use ($interpretationData) {
                        $query->whereBetween('min_score', [$interpretationData['min_score'], $interpretationData['max_score']])
                              ->orWhereBetween('max_score', [$interpretationData['min_score'], $interpretationData['max_score']])
                              ->orWhere(function($q) use ($interpretationData) {
                                  $q->where('min_score', '<=', $interpretationData['min_score'])
                                    ->where('max_score', '>=', $interpretationData['max_score']);
                              });
                    })
                    ->exists();

                if ($overlapping) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "نطاق النقاط {$interpretationData['min_score']}-{$interpretationData['max_score']} يتداخل مع تفسير موجود مسبقاً"
                    ], 422);
                }

                $interpretation = ResultInterpretation::create($interpretationData);
                $interpretations[] = $interpretation;
            }

            DB::commit();

            return response()->json([
                'message' => 'تم إنشاء التفسيرات بنجاح',
                'data' => ResultInterpretationResource::collection(
                    ResultInterpretation::whereIn('id', collect($interpretations)->pluck('id'))->with(['scale'])->get()
                )
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'فشل في إنشاء التفسيرات',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}