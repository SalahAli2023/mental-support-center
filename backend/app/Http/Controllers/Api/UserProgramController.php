<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserProgram;
use App\Models\Program;
use App\Models\User;
use App\Http\Resources\UserProgramResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserProgramController extends Controller
{
    /**
     * عرض جميع التسجيلات (للمشرفين)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = UserProgram::with(['user:id,name,email', 'program:id,name_ar,name_en']);

            // Filter by search term (user name or email)
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            // Filter by program
            if ($request->has('program_id') && $request->program_id) {
                $query->where('program_id', $request->program_id);
            }

            $query->orderBy('created_at', 'desc');

            $perPage = $request->get('per_page', 10);
            $enrollments = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $enrollments->items(),
                'meta' => [
                    'current_page' => $enrollments->currentPage(),
                    'last_page' => $enrollments->lastPage(),
                    'per_page' => $enrollments->perPage(),
                    'total' => $enrollments->total(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading enrollments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحميل التسجيلات'
            ], 500);
        }
    }
    
    // ==================== 2. إنشاء تسجيل جديد ====================
    
    public function store(Request $request): JsonResponse
    {
       
    }
    
    // ==================== 3. عرض تسجيل محدد ====================
    
    public function show($id): JsonResponse
    {
       
    }
    
    // ==================== 4. تحديث تسجيل ====================
    
    public function update(Request $request, $id): JsonResponse
    {
       
    }
    
    // ==================== 5. حذف تسجيل ====================
    
    public function destroy($id): JsonResponse
    {
       
    }
    
    // ==================== 6. تغيير حالة التسجيل ====================
    
    public function changeStatus(Request $request, $id): JsonResponse
    {
        
    }
    
    // ==================== 7. تحديث التقدم ====================
    
    
    // ==================== 8. إحصائيات التسجيلات ====================
    

    
    // ==================== 9. تسجيلات المستخدم الحالي ====================
    
 
    
    // ==================== 10. التسجيل في برنامج من قبل المستخدم ====================
    
   
    
    // ==================== 11. إلغاء التسجيل ====================
    
  
}