<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LibraryItemResource;
use App\Http\Resources\LibraryCategoryResource;
use App\Models\LibraryItem;
use App\Models\LibraryCategory;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $query = LibraryItem::with('category')->where('is_published', true);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title_ar', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));

        return LibraryItemResource::collection($items)->response();
    }

    public function show(Request $request, $id)
    {
        $item = LibraryItem::with('category')->findOrFail($id);
        $item->increment('views');
        return new LibraryItemResource($item);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'type' => 'required|in:book,research,guide,article',
            'category_id' => 'required|exists:library_categories,id',
        ]);

        $item = LibraryItem::create($request->all());

        if ($request->hasFile('cover_image')) {
            $item->cover_image = $request->file('cover_image')->store('library', 'public');
            $item->save();
        }

        if ($request->hasFile('file')) {
            $item->file_path = $request->file('file')->store('library/files', 'public');
            $item->file_size = $request->file('file')->getSize();
            $item->save();
        }

        return (new LibraryItemResource($item->load('category')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, $id)
    {
        $item = LibraryItem::findOrFail($id);
        $item->update($request->all());

        if ($request->hasFile('cover_image')) {
            $item->cover_image = $request->file('cover_image')->store('library', 'public');
            $item->save();
        }

        if ($request->hasFile('file')) {
            $item->file_path = $request->file('file')->store('library/files', 'public');
            $item->file_size = $request->file('file')->getSize();
            $item->save();
        }

        return new LibraryItemResource($item->load('category'));
    }

    public function destroy($id)
    {
        $item = LibraryItem::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Library item deleted successfully']);
    }

    public function categories(Request $request)
    {
        $categories = LibraryCategory::all();
        return LibraryCategoryResource::collection($categories)->response();
    }


    // إضافة إلى LibraryController.php

    // زيادة التحميلات
    public function incrementDownloads($id)
    {
        $item = LibraryItem::findOrFail($id);
        $item->increment('downloads');

        // أعد المورد المحدث حتى يمكن للواجهة تحديث العدد مباشرة
        return new LibraryItemResource($item->fresh('category'));
    }

// إضافة تقييم
public function rateItem(Request $request, $id)
{
    $request->validate([
        'rating' => 'required|numeric|min:1|max:5'
    ]);

    $item = LibraryItem::findOrFail($id);
    
    // حساب التقييم الجديد
    $newRatingCount = $item->rating_count + 1;
    $newRating = (($item->rating * $item->rating_count) + $request->rating) / $newRatingCount;
    
    $item->update([
        'rating' => $newRating,
        'rating_count' => $newRatingCount
    ]);

    return new LibraryItemResource($item);
}

// جلب المفضلة
public function favorites(Request $request)
{
    $favoriteIds = $request->get('ids', []);
    
    if (empty($favoriteIds)) {
        return LibraryItemResource::collection(collect());
    }

    $items = LibraryItem::whereIn('id', $favoriteIds)
        ->where('is_published', true)
        ->get();

    return LibraryItemResource::collection($items);
}
}
