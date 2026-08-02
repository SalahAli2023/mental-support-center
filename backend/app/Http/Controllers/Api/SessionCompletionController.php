<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SessionCompletion;
use App\Models\ProgramSession;
use App\Models\User;
use App\Models\UserProgram;
use App\Http\Resources\SessionCompletionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SessionCompletionController extends Controller
{
    // ==================== 1. عرض جميع إكمالات الجلسات ====================
    
    public function index(Request $request): JsonResponse
    {
    }
    
    // ==================== 2. إنشاء إكمال جلسة جديد ====================
    
    public function store(Request $request): JsonResponse
    {
      
    }
    
    // ==================== 3. عرض إكمال جلسة محدد ====================
    
    public function show($id): JsonResponse
    {
       
    }
    
    // ==================== 4. تحديث إكمال جلسة ====================
    
    public function update(Request $request, $id): JsonResponse
    {
       
    }
    
    // ==================== 5. حذف إكمال جلسة ====================
    
    public function destroy($id): JsonResponse
    {
        
    }
    
    
   
}