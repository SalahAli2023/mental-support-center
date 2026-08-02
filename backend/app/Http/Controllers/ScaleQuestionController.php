<?php

namespace App\Http\Controllers;

use App\Models\ScaleQuestion;
use App\Http\Resources\ScaleQuestionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class ScaleQuestionController extends Controller
{
    /**
     * عرض جميع أسئلة المقياس
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = ScaleQuestion::query();

        // التصفية حسب المقياس
        if ($request->has('scale_id')) {
            $query->where('scale_id', $request->scale_id);
        }

        // التحميل مع العلاقات
        $query->with(['scale', 'options']);

        // الترتيب
        $query->orderBy('question_order');

        $questions = $query->paginate($request->get('per_page', 20));

        return ScaleQuestionResource::collection($questions);
    }

    /**
     * إنشاء سؤال جديد
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scale_id' => 'required|exists:psychological_scales,id',
            'question_text_ar' => 'required|string',
            'question_text_en' => 'required|string',
            'question_order' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $question = ScaleQuestion::create($validated);

            DB::commit();

            return response()->json([
                'message' => 'تم إنشاء السؤال بنجاح',
                'data' => new ScaleQuestionResource($question->load(['scale', 'options']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'فشل في إنشاء السؤال',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض سؤال محدد
     */
    public function show(ScaleQuestion $scaleQuestion): ScaleQuestionResource
    {
        $scaleQuestion->load(['scale', 'options']);

        return new ScaleQuestionResource($scaleQuestion);
    }

    /**
     * تحديث سؤال
     */
    public function update(Request $request, ScaleQuestion $scaleQuestion): JsonResponse
    {
        $validated = $request->validate([
            'question_text_ar' => 'sometimes|required|string',
            'question_text_en' => 'sometimes|required|string',
            'question_order' => 'sometimes|required|integer|min:0',
        ]);

        $scaleQuestion->update($validated);

        return response()->json([
            'message' => 'تم تحديث السؤال بنجاح',
            'data' => new ScaleQuestionResource($scaleQuestion->fresh()->load(['scale', 'options']))
        ]);
    }

    /**
     * حذف سؤال
     */
    public function destroy(ScaleQuestion $scaleQuestion): JsonResponse
    {
        DB::beginTransaction();

        try {
            $scaleQuestion->delete();

            DB::commit();

            return response()->json([
                'message' => 'تم حذف السؤال بنجاح'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'فشل في حذف السؤال',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * إنشاء عدة أسئلة دفعة واحدة
     */
    public function bulkStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scale_id' => 'required|exists:psychological_scales,id',
            'questions' => 'required|array|min:1',
            'questions.*.question_text_ar' => 'required|string',
            'questions.*.question_text_en' => 'required|string',
            'questions.*.question_order' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $questions = [];

            foreach ($validated['questions'] as $questionData) {
                $questionData['scale_id'] = $validated['scale_id'];
                $question = ScaleQuestion::create($questionData);
                $questions[] = $question;
            }

            DB::commit();

            return response()->json([
                'message' => 'تم إنشاء الأسئلة بنجاح',
                'data' => ScaleQuestionResource::collection(ScaleQuestion::whereIn('id', collect($questions)->pluck('id'))->with(['scale', 'options'])->get())
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'فشل في إنشاء الأسئلة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * إعادة ترتيب الأسئلة
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'questions' => 'required|array|min:1',
            'questions.*.id' => 'required|exists:scale_questions,id',
            'questions.*.question_order' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            foreach ($validated['questions'] as $item) {
                ScaleQuestion::where('id', $item['id'])->update([
                    'question_order' => $item['question_order']
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'تم إعادة ترتيب الأسئلة بنجاح'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'فشل في إعادة ترتيب الأسئلة',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}