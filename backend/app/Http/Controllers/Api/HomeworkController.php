<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SessionHomework;
use App\Models\HomeworkSubmission;
use App\Models\ProgramSession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HomeworkController extends Controller
{
    /**
     * عرض جميع المهام المنزلية لجلسة معينة
     */
    public function index(Request $request, $sessionId): JsonResponse
    {
        try {
            $query = SessionHomework::where('session_id', $sessionId);

            // التصفية
            if ($request->has('is_mandatory')) {
                $query->where('is_mandatory', $request->boolean('is_mandatory'));
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            $homework = $query->orderBy('homework_order')->get();

            return response()->json([
                'success' => true,
                'data' => $homework,
                'total' => $homework->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب المهام: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * إنشاء مهمة منزلية جديدة
     */
    public function store(Request $request, $sessionId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'instructions_ar' => 'nullable|string',
            'instructions_en' => 'nullable|string',
            'is_mandatory' => 'nullable|boolean',
            'completion_type' => 'required|in:confirmation,text_input,file_upload,form',
            'completion_config' => 'nullable|array',
            'homework_order' => 'nullable|integer|min:1',
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
            $session = ProgramSession::findOrFail($sessionId);

            // تحديد ترتيب المهمة إذا لم يُحدد
            if (!$request->has('homework_order')) {
                $maxOrder = SessionHomework::where('session_id', $sessionId)->max('homework_order') ?? 0;
                $request->merge(['homework_order' => $maxOrder + 1]);
            }

            $data = $validator->validated();
            $data['id'] = (string) Str::uuid();
            $data['session_id'] = $sessionId;
            $data['is_mandatory'] = $data['is_mandatory'] ?? true;
            $data['is_active'] = $data['is_active'] ?? true;

            $homework = SessionHomework::create($data);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء المهمة المنزلية بنجاح',
                'data' => $homework
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء المهمة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض مهمة محددة
     */
    public function show($sessionId, $id): JsonResponse
    {
        try {
            $homework = SessionHomework::where('session_id', $sessionId)
                                       ->with(['submissions.user'])
                                       ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $homework
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على المهمة'
            ], 404);
        }
    }

    /**
     * تحديث مهمة منزلية
     */
    public function update(Request $request, $sessionId, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title_ar' => 'sometimes|required|string|max:255',
            'title_en' => 'sometimes|required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'instructions_ar' => 'nullable|string',
            'instructions_en' => 'nullable|string',
            'is_mandatory' => 'nullable|boolean',
            'completion_type' => 'sometimes|in:confirmation,text_input,file_upload,form',
            'completion_config' => 'nullable|array',
            'homework_order' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $homework = SessionHomework::where('session_id', $sessionId)->findOrFail($id);
            $homework->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث المهمة بنجاح',
                'data' => $homework
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث المهمة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف مهمة منزلية
     */
    public function destroy($sessionId, $id): JsonResponse
    {
        try {
            $homework = SessionHomework::where('session_id', $sessionId)->findOrFail($id);
            $homework->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المهمة بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المهمة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تسليم مهمة منزلية من المستخدم
     */
    public function submit(Request $request, $sessionId, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'submission_text' => 'nullable|string',
            'submission_data' => 'nullable|array',
            'file' => 'nullable|file|max:10240', // 10MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $homework = SessionHomework::where('session_id', $sessionId)->findOrFail($id);
            $userId = auth()->id();

            $data = [
                'id' => (string) Str::uuid(),
                'homework_id' => $homework->id,
                'user_id' => $userId,
                'status' => 'submitted',
                'submitted_at' => now(),
            ];

            if ($request->has('submission_text')) {
                $data['submission_text'] = $request->submission_text;
            }

            if ($request->has('submission_data')) {
                $data['submission_data'] = $request->submission_data;
            }

            // رفع الملف إذا وُجد
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $userId . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('homework', $fileName, 'media');
                $data['file_url'] = 'media/homework/' . $fileName;
            }

            $submission = HomeworkSubmission::updateOrCreate(
                ['homework_id' => $homework->id, 'user_id' => $userId],
                $data
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تسليم المهمة بنجاح',
                'data' => $submission
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسليم المهمة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * مراجعة تسليم مهمة (من الإدارة)
     */
    public function review(Request $request, $sessionId, $homeworkId, $submissionId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,completed',
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $submission = HomeworkSubmission::where('homework_id', $homeworkId)
                                           ->findOrFail($submissionId);

            $submission->update([
                'status' => $request->status,
                'completed_at' => now(),
                'admin_notes' => $request->admin_notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم مراجعة المهمة بنجاح',
                'data' => $submission
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء المراجعة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تغيير ترتيب المهام
     */
    public function reorder(Request $request, $sessionId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'homework' => 'required|array',
            'homework.*.id' => 'required|uuid|exists:session_homework,id',
            'homework.*.order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($request->homework as $homeworkData) {
                SessionHomework::where('id', $homeworkData['id'])
                              ->where('session_id', $sessionId)
                              ->update(['homework_order' => $homeworkData['order']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث ترتيب المهام بنجاح'
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




