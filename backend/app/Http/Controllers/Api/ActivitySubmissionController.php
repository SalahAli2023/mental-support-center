<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivitySubmission;
use App\Models\SessionActivity;
use App\Http\Resources\ActivitySubmissionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ActivitySubmissionController extends Controller
{
    /**
     * عرض جميع التقديمات
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        
    }

    /**
     * تقديم نشاط جديد
     */
    public function store(Request $request, $activityId = null): JsonResponse
    {
        
    }

    /**
     * تحديث تقديم
     */
    public function update(Request $request, $id): JsonResponse
    {
        
    }

    /**
     * إضافة/تحديث تعليق المعالج
     */

    /**
     * حذف تقديم
     */
    public function destroy($id): JsonResponse
    {
        
    }

  
}