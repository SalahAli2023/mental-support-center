<?php

namespace App\Http\Controllers;

use App\Models\QuestionOption;
use App\Http\Resources\QuestionOptionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class QuestionOptionController extends Controller
{
    /**
     * عرض جميع خيارات السؤال
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = QuestionOption::query();

        // التصفية حسب السؤال
        if ($request->has('question_id')) {
            $query->where('question_id', $request->question_id);
        }

        // التحميل مع العلاقات
        $query->with(['question']);

        // الترتيب
        $query->orderBy('option_order');

        $options = $query->paginate($request->get('per_page', 20));

        return QuestionOptionResource::collection($options);
    }

    /**
     * إنشاء خيار جديد
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question_id' => 'required|exists:scale_questions,id',
            'option_text_ar' => 'required|string|max:500',
            'option_text_en' => 'required|string|max:500',
            'score_value' => 'required|integer',
            'option_order' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $option = QuestionOption::create($validated);

            DB::commit();

            return response()->json([
                'message' => 'تم إنشاء الخيار بنجاح',
                'data' => new QuestionOptionResource($option->load(['question']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'فشل في إنشاء الخيار',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض خيار محدد
     */
    public function show(QuestionOption $questionOption): QuestionOptionResource
    {
        $questionOption->load(['question']);

        return new QuestionOptionResource($questionOption);
    }

    /**
     * تحديث خيار
     */
    public function update(Request $request, QuestionOption $questionOption): JsonResponse
    {
        $validated = $request->validate([
            'option_text_ar' => 'sometimes|required|string|max:500',
            'option_text_en' => 'sometimes|required|string|max:500',
            'score_value' => 'sometimes|required|integer',
            'option_order' => 'sometimes|required|integer|min:0',
        ]);

        $questionOption->update($validated);

        return response()->json([
            'message' => 'تم تحديث الخيار بنجاح',
            'data' => new QuestionOptionResource($questionOption->fresh()->load(['question']))
        ]);
    }

    /**
     * حذف خيار
     */
    public function destroy(QuestionOption $questionOption): JsonResponse
    {
        DB::beginTransaction();

        try {
            $questionOption->delete();

            DB::commit();

            return response()->json([
                'message' => 'تم حذف الخيار بنجاح'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'فشل في حذف الخيار',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * إنشاء عدة خيارات دفعة واحدة
     */
    public function bulkStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question_id' => 'required|exists:scale_questions,id',
            'options' => 'required|array|min:1',
            'options.*.option_text_ar' => 'required|string|max:500',
            'options.*.option_text_en' => 'required|string|max:500',
            'options.*.score_value' => 'required|integer',
            'options.*.option_order' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $options = [];

            foreach ($validated['options'] as $optionData) {
                $optionData['question_id'] = $validated['question_id'];
                $option = QuestionOption::create($optionData);
                $options[] = $option;
            }

            DB::commit();

            return response()->json([
                'message' => 'تم إنشاء الخيارات بنجاح',
                'data' => QuestionOptionResource::collection(QuestionOption::whereIn('id', collect($options)->pluck('id'))->with(['question'])->get())
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'فشل في إنشاء الخيارات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * إعادة ترتيب الخيارات
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'options' => 'required|array|min:1',
            'options.*.id' => 'required|exists:question_options,id',
            'options.*.option_order' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            foreach ($validated['options'] as $item) {
                QuestionOption::where('id', $item['id'])->update([
                    'option_order' => $item['option_order']
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'تم إعادة ترتيب الخيارات بنجاح'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'فشل في إعادة ترتيب الخيارات',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}