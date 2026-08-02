<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProgramController extends Controller
{
    /**
     * عرض جميع البرامج
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Program::with(['scale', 'sessions']);

            // التصفية حسب الحالة
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // البحث (باللغتين)
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name_ar', 'like', "%{$search}%")
                      ->orWhere('name_en', 'like', "%{$search}%")
                      ->orWhere('description_ar', 'like', "%{$search}%")
                      ->orWhere('description_en', 'like', "%{$search}%");
                });
            }

            // الترتيب
            $sortField = $request->get('sort_field', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortField, $sortDirection);

            // الترقيم
            $perPage = $request->get('per_page', 10);
            
            if ($request->has('all') && $request->boolean('all')) {
                $programs = $query->get();
                return response()->json([
                    'success' => true,
                    'data' => $programs,
                    'total' => $programs->count()
                ]);
            }

            $programs = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $programs->items(),
                'meta' => [
                    'current_page' => $programs->currentPage(),
                    'per_page' => $programs->perPage(),
                    'total' => $programs->total(),
                    'last_page' => $programs->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading programs: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحميل البرامج'
            ], 500);
        }
    }

    /**
     * عرض برنامج محدد
     */
    public function show($id): JsonResponse
    {
        try {
            $program = Program::with(['scale', 'sessions.activities'])->find($id);

            if (!$program) {
                return response()->json([
                    'success' => false,
                    'message' => 'البرنامج غير موجود'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $program
            ]);

        } catch (\Exception $e) {
            \Log::error('Error showing program: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء عرض البرنامج'
            ], 500);
        }
    }

    /**
     * إنشاء برنامج جديد
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'target_category_ar' => 'nullable|string|max:255',
            'target_category_en' => 'nullable|string|max:255',
            'duration' => 'nullable|integer|min:1',
            'max_duration_days' => 'nullable|integer|min:1',
            'session_duration_minutes' => 'nullable|integer|min:1',
            'session_gap_hours' => 'nullable|integer|min:0',
            'activity_gap_hours' => 'nullable|integer|min:0',
            'status' => 'nullable|in:active,inactive,draft',
            'scale_id' => 'nullable|exists:psychological_scales,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
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
            $data['status'] = $data['status'] ?? 'draft';

            // ✅ معالجة الصورة - باستخدام disk 'media'
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $data['id'] . '.' . $image->getClientOriginalExtension();
                
                // ✅ حفظ في media disk
                $path = $image->storeAs('programs', $imageName, 'media');
                $data['image_url'] = 'media/programs/' . $imageName;
                
                \Log::info('Image saved:', [
                    'path' => $path,
                    'image_url' => $data['image_url'],
                    'full_url' => url('media/programs/' . $imageName)
                ]);
            }

            $program = Program::create($data);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء البرنامج بنجاح',
                'data' => $program,
                'image_info' => isset($data['image_url']) ? [
                    'url' => $data['image_url'],
                    'full_url' => url($data['image_url'])
                ] : null
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating program: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء البرنامج: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث برنامج
     */
    public function update(Request $request, $id): JsonResponse
    {
        $program = Program::find($id);
        
        if (!$program) {
            return response()->json([
                'success' => false,
                'message' => 'البرنامج غير موجود'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name_ar' => 'sometimes|required|string|max:255',
            'name_en' => 'sometimes|required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'target_category_ar' => 'nullable|string|max:255',
            'target_category_en' => 'nullable|string|max:255',
            'duration' => 'nullable|integer|min:1',
            'max_duration_days' => 'nullable|integer|min:1',
            'session_duration_minutes' => 'nullable|integer|min:1',
            'session_gap_hours' => 'nullable|integer|min:0',
            'activity_gap_hours' => 'nullable|integer|min:0',
            'status' => 'nullable|in:active,inactive,draft',
            'scale_id' => 'nullable|exists:psychological_scales,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
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

            // ✅ معالجة الصورة - باستخدام disk 'media'
            if ($request->hasFile('image')) {
                // حذف الصورة القديمة إذا كانت موجودة
                if ($program->image_url && file_exists(public_path($program->image_url))) {
                    \Log::info('Deleting old image:', ['path' => public_path($program->image_url)]);
                    unlink(public_path($program->image_url));
                }
                
                $image = $request->file('image');
                $imageName = time() . '_' . $program->id . '.' . $image->getClientOriginalExtension();
                
                // ✅ حفظ في media disk
                $path = $image->storeAs('programs', $imageName, 'media');
                $data['image_url'] = 'media/programs/' . $imageName;
                
                \Log::info('New image saved:', [
                    'path' => $path,
                    'image_url' => $data['image_url'],
                    'full_url' => url($data['image_url'])
                ]);
            }

            $program->update($data);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث البرنامج بنجاح',
                'data' => $program,
                'image_full_url' => $program->image_url ? url($program->image_url) : null
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating program: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث البرنامج: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف برنامج
     */
    public function destroy($id): JsonResponse
    {
        $program = Program::find($id);
        
        if (!$program) {
            return response()->json([
                'success' => false,
                'message' => 'البرنامج غير موجود'
            ], 404);
        }

        DB::beginTransaction();
        try {
            // ✅ حذف الصورة من media disk
            if ($program->image_url && file_exists(public_path($program->image_url))) {
                \Log::info('Deleting program image:', ['path' => public_path($program->image_url)]);
                unlink(public_path($program->image_url));
            }

            // حذف الجلسات والأنشطة المرتبطة
            foreach ($program->sessions as $session) {
                $session->activities()->delete();
            }
            
            $program->sessions()->delete();
            $program->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف البرنامج بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting program: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف البرنامج: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تغيير حالة البرنامج
     */
    public function changeStatus(Request $request, $id): JsonResponse
    {
        $program = Program::find($id);
        
        if (!$program) {
            return response()->json([
                'success' => false,
                'message' => 'البرنامج غير موجود'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,inactive,draft',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $program->update(['status' => $request->status]);

        $statusLabels = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'draft' => 'مسودة',
        ];

        return response()->json([
            'success' => true,
            'message' => "تم تغيير حالة البرنامج إلى '{$statusLabels[$request->status]}'",
            'data' => $program
        ]);
    }

    /**
     * عرض جلسات البرنامج
     */
    public function sessions($id): JsonResponse
    {
        try {
            $program = Program::find($id);
            
            if (!$program) {
                return response()->json([
                    'success' => false,
                    'message' => 'البرنامج غير موجود'
                ], 404);
            }

            $sessions = $program->sessions()->with('activities')->get();

            return response()->json([
                'success' => true,
                'data' => $sessions
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading program sessions: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحميل جلسات البرنامج'
            ], 500);
        }
    }
}