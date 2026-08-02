<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LegalResourceResource;
use App\Http\Resources\LegalResourceCategoryResource;
use App\Models\LegalResource;
use App\Models\LegalResourceCategory;
use Illuminate\Http\Request;

class LegalResourceController extends Controller
{
    public function index(Request $request)
    {
        $query = LegalResource::with('category');

        if ($request->has('law_type')) {
            $query->where('law_type', $request->law_type);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('text_ar', 'like', "%{$search}%")
                  ->orWhere('text_en', 'like', "%{$search}%");
            });
        }

        $resources = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return LegalResourceResource::collection($resources)->response();
    }

    public function show(Request $request, $id)
    {
        $resource = LegalResource::with('category')->findOrFail($id);
        return new LegalResourceResource($resource);
    }

    public function store(Request $request)
    {
        $request->validate([
            'law_type' => 'required|string',
            'text_ar' => 'required|string',
            'text_en' => 'required|string',
        ]);

        $resource = LegalResource::create($request->all());

        return (new LegalResourceResource($resource->load('category')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, $id)
    {
        $resource = LegalResource::findOrFail($id);
        $resource->update($request->all());

        return new LegalResourceResource($resource->load('category'));
    }

    public function destroy($id)
    {
        $resource = LegalResource::findOrFail($id);
        $resource->delete();

        return response()->json(['message' => 'Legal resource deleted successfully']);
    }

    // دالة جديدة لجلب الفئات
    public function categories(Request $request)
    {
        $categories = LegalResourceCategory::all();
        return LegalResourceCategoryResource::collection($categories);
    }
}