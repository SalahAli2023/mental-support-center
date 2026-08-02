<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Support\MediaHelper;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_published')) {
            $query->where('is_published', $request->boolean('is_published'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title_ar', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%");
            });
        }

        $events = $query->orderBy('date', 'desc')->paginate($request->get('per_page', 15));

        return EventResource::collection($events)->response();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'type' => 'required|string',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'date' => 'required|date',
        ]);

        $event = Event::create([
            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,
            'slug' => Str::slug($request->title_en),
            'type' => $request->type,
            'description_ar' => $request->description_ar,
            'description_en' => $request->description_en,
            'full_description_ar' => $request->full_description_ar,
            'full_description_en' => $request->full_description_en,
            'date' => $request->date,
            'duration' => $request->duration,
            'location_ar' => $request->location_ar,
            'location_en' => $request->location_en,
            'topics_ar' => $request->topics_ar,
            'topics_en' => $request->topics_en,
            'speakers' => $request->speakers,
            'is_published' => $request->boolean('is_published', false),
        ]);

        if ($request->hasFile('media')) {
            $path = $request->file('media')->store('events', 'public');
            $normalizedPath = MediaHelper::normalizePath($path);
            $event->media = $normalizedPath;
            $event->save();
            
            \Log::info('Event media saved', [
                'event_id' => $event->id,
                'original_path' => $path,
                'normalized_path' => $normalizedPath,
                'public_url' => MediaHelper::toPublicUrl($normalizedPath)
            ]);
        }

        $event->refresh();
        return (new EventResource($event))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        return new EventResource($event);
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'title_ar' => 'sometimes|string|max:255',
            'title_en' => 'sometimes|string|max:255',
            'type' => 'sometimes|string',
            'description_ar' => 'sometimes|string',
            'description_en' => 'sometimes|string',
            'date' => 'sometimes|date',
        ]);

        $event->update($request->all());

        if ($request->hasFile('media')) {
            // حذف الصورة القديمة إن وجدت
            if ($event->media) {
                \Storage::disk('public')->delete($event->media);
            }
            
            $path = $request->file('media')->store('events', 'public');
            $normalizedPath = MediaHelper::normalizePath($path);
            $event->media = $normalizedPath;
            $event->save();
            
            \Log::info('Event media updated', [
                'event_id' => $event->id,
                'original_path' => $path,
                'normalized_path' => $normalizedPath,
                'public_url' => MediaHelper::toPublicUrl($normalizedPath)
            ]);
        }

        $event->refresh();
        return new EventResource($event);
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }
}
