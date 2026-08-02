<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserMessageResource;
use App\Models\UserMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = UserMessage::with('responder')->orderByDesc('created_at');

        if ($request->filled('message_type') && in_array($request->message_type, UserMessage::TYPES, true)) {
            $query->where('message_type', $request->message_type);
        }

        if ($request->filled('status') && in_array($request->status, UserMessage::STATUSES, true)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('is_read')) {
            $query->where('is_read', $request->boolean('is_read'));
        }

        if ($request->filled('is_public')) {
            $query->where('is_public', $request->boolean('is_public'));
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $perPage = min((int) $request->get('per_page', 15), 100);

        return UserMessageResource::collection($query->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message_type' => ['required', Rule::in(UserMessage::TYPES)],
            'message' => ['required', 'string'],
        ]);

        $message = UserMessage::create($validated);

        return response()->json([
            'message' => 'تم إرسال رسالتك بنجاح، سنقوم بالرد قريباً.',
            'data' => new UserMessageResource($message),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserMessage $userMessage): UserMessageResource
    {
        return new UserMessageResource($userMessage);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserMessage $userMessage): UserMessageResource
    {
        $validated = $request->validate([
            'status' => ['sometimes', Rule::in(UserMessage::STATUSES)],
            'is_read' => ['sometimes', 'boolean'],
            'response' => ['sometimes', 'nullable', 'string'],
            'is_public' => ['sometimes', 'boolean'],
        ]);

        if (array_key_exists('status', $validated)) {
            $userMessage->status = $validated['status'];
        }

        if (array_key_exists('is_read', $validated)) {
            $userMessage->is_read = $validated['is_read'];
            $userMessage->read_at = $validated['is_read'] ? now() : null;
        }

        if (array_key_exists('response', $validated)) {
            $userMessage->response = $validated['response'];
            if ($validated['response']) {
                $userMessage->responded_by = $request->user()?->id;
                $userMessage->responded_at = now();
            } else {
                $userMessage->responded_by = null;
                $userMessage->responded_at = null;
                $userMessage->is_public = false;
                $userMessage->public_at = null;
            }
        }

        if (array_key_exists('is_public', $validated)) {
            if ($userMessage->message_type !== 'inquiry') {
                throw new HttpException(422, 'يمكن نشر الاستفسارات فقط.');
            }
            if (empty($userMessage->response)) {
                throw new HttpException(422, 'يجب إضافة رد قبل النشر في الموقع.');
            }
            $userMessage->is_public = $validated['is_public'];
            $userMessage->public_at = $validated['is_public'] ? now() : null;
        }

        $userMessage->save();

        return new UserMessageResource($userMessage->fresh('responder'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserMessage $userMessage): JsonResponse
    {
        $userMessage->delete();

        return response()->json(['message' => 'تم حذف الرسالة بنجاح.']);
    }

    public function publicFaqs()
    {
        $faqs = UserMessage::query()
            ->where('message_type', 'inquiry')
            ->where('is_public', true)
            ->whereNotNull('response')
            ->orderByDesc('public_at')
            ->limit(20)
            ->get([
                'id',
                'name',
                'subject',
                'message',
                'response',
                'public_at',
            ]);

        return response()->json([
            'data' => $faqs->map(function ($faq) {
                return [
                    'id' => $faq->id,
                    'question' => $faq->subject ?: $faq->message,
                    'answer' => $faq->response,
                    'published_at' => $faq->public_at?->toDateTimeString(),
                ];
            }),
        ]);
    }
}

