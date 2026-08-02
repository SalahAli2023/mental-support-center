<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * عرض جميع الفئات
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Category::query();

        // التصفية حسب الحالة
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // البحث
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        // الترتيب
        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // التحميل مع العلاقات
        $query->withCount('psychologicalScales');

        $categories = $query->paginate($request->get('per_page', 15));

        return CategoryResource::collection($categories);
    }

    /**
     * إنشاء فئة جديدة
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:100',
            'name_en' => 'required|string|max:100',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'message' => 'تم إنشاء الفئة بنجاح',
            'data' => new CategoryResource($category)
        ], 201);
    }

    /**
     * عرض فئة محددة
     */
    public function show(Category $category): CategoryResource
    {
        $category->load(['psychologicalScales' => function($query) {
            $query->where('is_active', true);
        }]);

        return new CategoryResource($category);
    }

    /**
     * تحديث فئة
     */
    public function update(Request $request, $id): JsonResponse
    {
        // البحث عن الفئة باستخدام ID (يدعم UUID)
        $category = Category::find($id);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'الفئة غير موجودة'
            ], 404);
        }

        $validated = $request->validate([
            'name_ar' => 'sometimes|required|string|max:100',
            'name_en' => 'sometimes|required|string|max:100',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الفئة بنجاح',
            'data' => new CategoryResource($category->fresh())
        ]);
    }

    /**
     * حذف فئة
     */
    public function destroy($id): JsonResponse
    {
        // البحث عن الفئة باستخدام ID (يدعم UUID)
        $category = Category::find($id);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'الفئة غير موجودة'
            ], 404);
        }

        // التحقق من وجود مقاييس مرتبطة
        if ($category->psychologicalScales()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف الفئة لأنها تحتوي على مقاييس مرتبطة'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الفئة بنجاح'
        ]);
    }

    /**
     * عرض جميع التصنيفات النشطة (عام - بدون authentication)
     */
    public function indexPublic(Request $request): AnonymousResourceCollection
    {
        $query = Category::where('is_active', true);

        // البحث
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        // التحميل مع العلاقات
        $query->withCount(['psychologicalScales' => function($query) {
            $query->where('is_active', true);
        }]);

        // الترتيب
        $query->orderBy('name_ar');

        $categories = $query->get();

        return CategoryResource::collection($categories);
    }

    /**
     * الفعالة فقط
     */
    public function active(): AnonymousResourceCollection
    {
        $categories = Category::where('is_active', true)
            ->withCount(['psychologicalScales' => function($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('name_ar')
            ->get();

        return CategoryResource::collection($categories);
    }

    /**
     * تفعيل/تعطيل الفئة
     */
    public function toggleStatus($id): JsonResponse
    {
        // البحث عن الفئة باستخدام ID (يدعم UUID)
        $category = Category::find($id);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'الفئة غير موجودة'
            ], 404);
        }

        $category->update([
            'is_active' => !$category->is_active
        ]);

        $status = $category->is_active ? 'مفعل' : 'معطل';

        return response()->json([
            'success' => true,
            'message' => "تم {$status} الفئة بنجاح",
            'data' => new CategoryResource($category)
        ]);
    }
}