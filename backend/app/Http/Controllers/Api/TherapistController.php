<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TherapistResource;
use App\Models\Therapist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Support\MediaHelper;

class TherapistController extends Controller
{
/**
 * عرض قائمة المعالجين
 */
public function index(Request $request): JsonResponse
{
    $query = Therapist::with(['user', 'qualifications', 'certifications', 'schedules']);

    // التصفية حسب التخصص
    if ($request->has('specialty')) {
        $query->where('specialty_en', 'like', '%' . $request->specialty . '%')
              ->orWhere('specialty_ar', 'like', '%' . $request->specialty . '%');
    }

    // التصفية حسب الحالة
    if ($request->has('status')) {
        $query->where('status', $request->status);
    }

    // التصفية حسب الجنس
    if ($request->has('gender')) {
        $query->where('gender', $request->gender);
    }

    // الترتيب حسب التقييم
    if ($request->has('sort_by_rating')) {
        $query->orderBy('rating', 'desc');
    }

    $therapists = $query->paginate($request->get('per_page', 10));

    return response()->json([
        'success' => true,
        'data' => TherapistResource::collection($therapists->items()),
        'meta' => [
            'current_page' => $therapists->currentPage(),
            'last_page' => $therapists->lastPage(),
            'total' => $therapists->total(),
            'per_page' => $therapists->perPage(),
        ]
    ]);
}


   /**
 * عرض معالج محدد
 */
    public function show($id): JsonResponse
    {
        $therapist = Therapist::with([
            'user',
            'qualifications',
            'certifications',
            'schedules',
            'reviews' => function ($query) {
                $query->latest()
                    ->with('client')
                    ->limit(20);
            },
        ])->find($id);
        
        if (!$therapist) {
            return response()->json([
                'success' => false,
                'message' => 'المعالج غير موجود'
            ], 404);
        }
        
        // تسجيل للتحقق من تحميل schedules و qualifications
        \Log::info('Therapist data loaded', [
            'therapist_id' => $therapist->id,
            'schedules_count' => $therapist->schedules->count(),
            'qualifications_count' => $therapist->qualifications->count(),
            'qualifications' => $therapist->qualifications->map(function ($qualification) {
                return [
                    'id' => $qualification->id,
                    'name_ar' => $qualification->name_ar,
                    'name_en' => $qualification->name_en,
                    'institution_ar' => $qualification->institution_ar,
                    'institution_en' => $qualification->institution_en,
                    'year' => $qualification->year
                ];
            })->toArray(),
            'schedules' => $therapist->schedules->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'day' => $schedule->day,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'available' => $schedule->available
                ];
            })->toArray()
        ]);
        
        return response()->json([
            'success' => true,
            'data' => new TherapistResource($therapist)
        ]);
    }
    /**
     * تخزين معالج جديد
     */
    public function store(Request $request): JsonResponse
    {
        $authUser = $request->user();
        if (!$authUser || !$authUser->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admins can create therapists.'
            ], 403);
        }

        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:20',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'methodologies_ar' => 'nullable|array',
            'methodologies_en' => 'nullable|array',
            'specialty_ar' => 'required|string|max:255',
            'specialty_en' => 'required|string|max:255',
            'session_duration' => 'integer|min:1',
            'experience' => 'integer|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'bio_ar' => 'nullable|string',
            'bio_en' => 'nullable|string',
            'status' => 'in:active,busy,away',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120' // 5MB max
        ]);

        // معالجة رفع الصورة
        \Log::info('Therapist store request', [
            'has_avatar' => $request->hasFile('avatar'),
            'files' => array_keys($request->allFiles())
        ]);
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $storedPath = $request->file('avatar')->store('therapists/avatars', 'public');
            $avatarPath = MediaHelper::normalizePath($storedPath);
        }

        // إنشاء المستخدم أولاً
        $user = User::create([
            'name' => $validated['name_en'], // أو name_ar حسب التفضيل
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'Therapist',
            'phone' => $validated['phone'],
            'avatar' => $avatarPath,
            'joined_at' => now(),
        ]);

        // إنشاء المعالج مرتبط بالمستخدم
        $therapistData = array_merge($validated, ['user_id' => $user->id]);
        unset($therapistData['email'], $therapistData['password']); // إزالة بيانات المستخدم

        $therapist = Therapist::create($therapistData);
        $therapist->load(['user', 'qualifications', 'certifications', 'schedules']);

        return response()->json([
            'message' => 'Therapist created successfully',
            'data' => new TherapistResource($therapist)
        ], 201);
    }

  /**
 * تحديث المعالج
 */
public function update(Request $request, $id): JsonResponse
{
    try {
        $authUser = $request->user();
        if (!$authUser || !$authUser->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admins can update therapists.'
            ], 403);
        }

        $therapist = Therapist::find($id);
        
        if (!$therapist) {
            return response()->json([
                'success' => false,
                'message' => 'المعالج غير موجود'
            ], 404);
        }
        
        $validated = $request->validate([
            'name_ar' => 'sometimes|string|max:255',
            'name_en' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $therapist->user_id,
            'password' => 'nullable|string|min:6',
            'phone' => 'sometimes|string|max:20',
            'title_ar' => 'sometimes|string|max:255',
            'title_en' => 'sometimes|string|max:255',
            'methodologies_ar' => 'nullable|array',
            'methodologies_en' => 'nullable|array',
            'specialty_ar' => 'sometimes|string|max:255',
            'specialty_en' => 'sometimes|string|max:255',
            'session_duration' => 'sometimes|integer|min:1',
            'experience' => 'sometimes|integer|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'bio_ar' => 'nullable|string',
            'bio_en' => 'nullable|string',
            'status' => 'sometimes|in:active,busy,away',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'remove_avatar' => 'nullable|boolean'
        ]);

        // معالجة رفع/حذف الصورة
        \Log::info('Therapist update - Request info', [
            'has_file' => $request->hasFile('avatar'),
            'all_files' => array_keys($request->allFiles()),
            'has_avatar_key' => $request->has('avatar'),
            'remove_avatar' => $request->has('remove_avatar') ? $request->remove_avatar : false,
            'current_avatar' => $therapist->user->avatar ?? null,
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type')
        ]);
        
        if ($request->hasFile('avatar')) {
            // حذف الصورة القديمة إن وجدت
            if ($therapist->user && $therapist->user->avatar) {
                $oldPath = $therapist->user->avatar;
                \Log::info('Deleting old avatar', ['path' => $oldPath]);
                \Storage::disk('public')->delete($oldPath);
            }
            
            // رفع الصورة الجديدة
            $file = $request->file('avatar');
            \Log::info('Uploading new avatar', [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize()
            ]);
            
            $storedPath = $file->store('therapists/avatars', 'public');
            $avatarPath = MediaHelper::normalizePath($storedPath);
            
            \Log::info('Avatar stored', [
                'stored_path' => $storedPath,
                'normalized_path' => $avatarPath,
                'file_exists' => \Storage::disk('public')->exists($storedPath)
            ]);
        } elseif ($request->has('remove_avatar') && $request->remove_avatar) {
            // حذف الصورة
            if ($therapist->user && $therapist->user->avatar) {
                \Storage::disk('public')->delete($therapist->user->avatar);
            }
            $avatarPath = null;
        } else {
            $avatarPath = $therapist->user->avatar ?? null;
        }

        // تحديث بيانات المستخدم
        if ($therapist->user) {
            $userData = [
                'name' => $validated['name_en'] ?? $therapist->user->name,
                'phone' => $validated['phone'] ?? $therapist->user->phone,
            ];

            if (isset($validated['email'])) {
                $userData['email'] = $validated['email'];
            }

            if (isset($validated['password']) && !empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            if ($avatarPath !== null) {
                $userData['avatar'] = $avatarPath;
                \Log::info('Updating user avatar', [
                    'user_id' => $therapist->user->id,
                    'avatar_path' => $avatarPath,
                    'avatar_path_type' => gettype($avatarPath)
                ]);
            }

            $therapist->user->update($userData);
            
            // إعادة تحميل المستخدم من قاعدة البيانات للتحقق من الحفظ
            $therapist->user->refresh();
            
            \Log::info('User updated', [
                'user_id' => $therapist->user->id,
                'avatar_in_db' => $therapist->user->avatar,
                'avatar_url' => $therapist->user->avatar ? MediaHelper::toPublicUrl($therapist->user->avatar) : null
            ]);
        }

        // إزالة بيانات المستخدم من بيانات المعالج
        $therapistData = $validated;
        unset($therapistData['email'], $therapistData['password'], $therapistData['phone'], $therapistData['avatar'], $therapistData['remove_avatar']);

        $therapist->update($therapistData);

        // إعادة تحميل العلاقات قبل إرجاع البيانات
        $therapist->refresh(); // تحديث البيانات من قاعدة البيانات
        $therapist->load(['user', 'qualifications', 'certifications', 'schedules']);
        
        \Log::info('Therapist updated successfully', [
            'therapist_id' => $therapist->id,
            'user_avatar' => $therapist->user->avatar,
            'avatar_url' => $therapist->user->avatar ? MediaHelper::toPublicUrl($therapist->user->avatar) : null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المعالج بنجاح',
            'data' => new TherapistResource($therapist)
        ]);

    } catch (\Exception $e) {
        \Log::error('Error updating therapist: ' . $e->getMessage(), [
            'therapist_id' => $id,
            'error' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء تحديث المعالج: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * حذف المعالج
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $authUser = $request->user();
        if (!$authUser || !$authUser->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admins can delete therapists.'
            ], 403);
        }

        $therapist = Therapist::find($id);
        
        if (!$therapist) {
            return response()->json([
                'success' => false,
                'message' => 'المعالج غير موجود'
            ], 404);
        }
        
        // حذف المستخدم المرتبط أولاً
        if ($therapist->user) {
            $therapist->user->delete();
        }
        
        $therapist->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المعالج بنجاح'
        ]);
    }
}