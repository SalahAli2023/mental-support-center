<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgramSession;
use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProgramSessionController extends Controller
{
    /**
     * عرض جلسات البرنامج
     */
    /**
     * عرض جلسات البرنامج
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = ProgramSession::query();
            
            // ✅ إذا جاء من مسار /programs/{id}/sessions
            if ($request->route('id')) {
                $query->where('program_id', $request->route('id'));
            }
            
            // ✅ إذا جاء مع معامل program_id
            if ($request->has('program_id')) {
                $query->where('program_id', $request->program_id);
            }
            
            // ✅ فلترة حسب البرنامج المحدد
            if ($request->programId) {
                $query->where('program_id', $request->programId);
            }
            
            // ✅ فلترة حسب المرحلة
            if ($request->has('phase_id')) {
                $query->where('phase_id', $request->phase_id);
            }

            $query->with(['activities', 'phase', 'homework']);
            $query->orderBy('session_order');

            $sessions = $query->get();

            return response()->json([
                'success' => true,
                'data' => $sessions
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading sessions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحميل الجلسات'
            ], 500);
        }
    }
    /**
     * إنشاء جلسة جديدة
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'program_id' => 'required|exists:programs,id',
            'phase_id' => 'nullable|exists:program_phases,id',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'session_order' => 'required|integer|min:1',
            'goal_ar' => 'nullable|string',
            'goal_en' => 'nullable|string',
            'duration' => 'nullable|integer|min:1',
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
            $data = $validator->validated();
            $data['id'] = (string) Str::uuid();
            if (!isset($data['duration']) || !$data['duration']) {
                $program = Program::find($data['program_id']);
                if ($program?->session_duration_minutes) {
                    $data['duration'] = $program->session_duration_minutes;
                }
            }

            $session = ProgramSession::create($data);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الجلسة بنجاح',
                'data' => $session
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الجلسة'
            ], 500);
        }
    }

    /**
     * عرض جلسة محددة
     */
    public function show($id): JsonResponse
    {
        try {
            $session = ProgramSession::with(['activities', 'phase', 'homework'])->find($id);
            
            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'الجلسة غير موجودة'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $session
            ]);

        } catch (\Exception $e) {
            \Log::error('Error showing session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء عرض الجلسة'
            ], 500);
        }
    }

    /**
     * تحديث جلسة
     */
    public function update(Request $request, $id): JsonResponse
    {
        $session = ProgramSession::find($id);
        
        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'الجلسة غير موجودة'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'phase_id' => 'nullable|exists:program_phases,id',
            'title_ar' => 'sometimes|required|string|max:255',
            'title_en' => 'sometimes|required|string|max:255',
            'session_order' => 'sometimes|integer|min:1',
            'goal_ar' => 'nullable|string',
            'goal_en' => 'nullable|string',
            'duration' => 'nullable|integer|min:1',
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
            $session->update($validator->validated());
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الجلسة بنجاح',
                'data' => $session
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الجلسة'
            ], 500);
        }
    }

    /**
     * حذف جلسة
     */
    public function destroy($id): JsonResponse
    {
        $session = ProgramSession::find($id);
        
        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'الجلسة غير موجودة'
            ], 404);
        }

        DB::beginTransaction();
        try {
            // حذف الأنشطة المرتبطة
            $session->activities()->delete();
            $session->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الجلسة بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الجلسة'
            ], 500);
        }
    }
}