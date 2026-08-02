<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SessionActivity;
use App\Models\ProgramSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SessionActivityController extends Controller
{
    /**
     * عرض أنشطة الجلسة
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = SessionActivity::query();

            if ($request->has('session_id')) {
                $query->where('session_id', $request->session_id);
            }

            $query->orderBy('created_at');
            $activities = $query->get();

            return response()->json([
                'success' => true,
                'data' => $activities
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading activities: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحميل الأنشطة'
            ], 500);
        }
    }

    /**
     * عرض نشاط محدد
     */
    public function show($id): JsonResponse
    {
        try {
            $activity = SessionActivity::find($id);
            
            if (!$activity) {
                return response()->json([
                    'success' => false,
                    'message' => 'النشاط غير موجود'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $activity
            ]);
        } catch (\Exception $e) {
            \Log::error('Error showing activity: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء عرض النشاط'
            ], 500);
        }
    }

    /**
     * إنشاء نشاط جديد
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:program_sessions,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'activity_type' => 'required|in:text,video,audio,file,form,exercise,reflection_questions,quiz',
            'instructions_ar' => 'nullable|string',
            'instructions_en' => 'nullable|string',
            'content_ar' => 'nullable|string',
            'content_en' => 'nullable|string',
            'media_url' => 'nullable|string',
            'media_type' => 'nullable|in:video,audio,image',
            'duration_minutes' => 'nullable|integer|min:1',
            'activity_config' => 'nullable|array',
            'activity_order' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
            'scale_id' => 'nullable|exists:psychological_scales,id',
            'is_mandatory' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = $validator->validated();
            $data['id'] = (string) Str::uuid();
            $data['is_mandatory'] = $request->boolean('is_mandatory', true);
            $data['is_active'] = $request->boolean('is_active', true);
            $data['activity_order'] = $data['activity_order'] ?? 1;

            $activity = SessionActivity::create($data);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء النشاط بنجاح',
                'data' => $activity
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating activity: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'فشل في إنشاء النشاط',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث نشاط
     */
    public function update(Request $request, $id): JsonResponse
    {
        $activity = SessionActivity::find($id);
        
        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'النشاط غير موجود'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name_ar' => 'sometimes|required|string|max:255',
            'name_en' => 'sometimes|required|string|max:255',
            'activity_type' => 'sometimes|required|in:text,video,audio,file,form,exercise,reflection_questions,quiz',
            'instructions_ar' => 'nullable|string',
            'instructions_en' => 'nullable|string',
            'content_ar' => 'nullable|string',
            'content_en' => 'nullable|string',
            'media_url' => 'nullable|string',
            'media_type' => 'nullable|in:video,audio,image',
            'duration_minutes' => 'nullable|integer|min:1',
            'activity_config' => 'nullable|array',
            'activity_order' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
            'scale_id' => 'nullable|exists:psychological_scales,id',
            'is_mandatory' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $activity->update($validator->validated());
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث النشاط بنجاح',
                'data' => $activity
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating activity: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'فشل في تحديث النشاط',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف نشاط
     */
    public function destroy($id): JsonResponse
    {
        $activity = SessionActivity::find($id);
        
        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'النشاط غير موجود'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $activity->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف النشاط بنجاح'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting activity: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'فشل في حذف النشاط',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تغيير حالة النشاط (إجباري/اختياري)
     */
    public function toggleMandatory($id): JsonResponse
    {
        $activity = SessionActivity::find($id);
        
        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'النشاط غير موجود'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $activity->update([
                'is_mandatory' => !$activity->is_mandatory
            ]);
            DB::commit();

            $status = $activity->is_mandatory ? 
                     (app()->getLocale() === 'ar' ? 'إجباري' : 'Mandatory') : 
                     (app()->getLocale() === 'ar' ? 'اختياري' : 'Optional');

            return response()->json([
                'success' => true,
                'message' => "تم تغيير حالة النشاط إلى '{$status}'",
                'data' => $activity
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error toggling activity mandatory status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'فشل في تغيير حالة النشاط'
            ], 500);
        }
    }

    /**
     * إحصائيات أنشطة الجلسة
     */
    public function statistics($sessionId): JsonResponse
    {
        try {
            $session = ProgramSession::with('activities')->find($sessionId);
            
            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'الجلسة غير موجودة'
                ], 404);
            }

            $activities = $session->activities;
            $totalActivities = $activities->count();
            $mandatoryActivities = $activities->where('is_mandatory', true)->count();
            $optionalActivities = $activities->where('is_mandatory', false)->count();

            // توزيع الأنشطة حسب النوع
            $activityTypes = $activities->groupBy('activity_type')
                ->map(function ($group) {
                    return $group->count();
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'total_activities' => $totalActivities,
                    'mandatory_activities' => $mandatoryActivities,
                    'optional_activities' => $optionalActivities,
                    'activity_types' => $activityTypes,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading activity statistics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحميل إحصائيات الأنشطة'
            ], 500);
        }
    }
}