<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage; // ✅ إضافة هذا

class UserController extends Controller
{
    /**
     * Ensure only admins can access user management endpoints.
     */
    protected function authorizeAdmin(Request $request): void
    {
        $authUser = $request->user();

        if (!$authUser || !$authUser->isAdmin()) {
            abort(403, 'Unauthorized. Only admins can manage users.');
        }
    }

    /**
     * عرض قائمة المستخدمين مع إمكانية البحث والتصفية
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        try {
            $query = User::query();

            // البحث بالاسم أو البريد الإلكتروني
            if ($request->has('q') && $request->q) {
                $searchTerm = $request->q;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('email', 'like', "%{$searchTerm}%");
                });
            }

            // التصفية حسب الدور
            if ($request->has('role') && $request->role) {
                $query->where('role', $request->role);
            }

            // الترتيب حسب أحدث المستخدمين
            $query->orderBy('created_at', 'desc');

            $users = $query->get();

            return response()->json([
                'success' => true,
                'data' => UserResource::collection($users),
                'total' => $users->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * إنشاء مستخدم جديد
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'role' => ['required', Rule::in(['Admin', 'Therapist', 'Client'])],
                'phone' => 'nullable|string|max:20',
                'bio' => 'nullable|string',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // ✅ إضافة الصورة
            ]);

            // ✅ معالجة الصورة
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'phone' => $validated['phone'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'avatar' => $avatarPath, // ✅ حفظ الصورة
                'joined_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => new UserResource($user)
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض مستخدم محدد
     */
    public function show(Request $request, User $user): JsonResponse
    {
        $this->authorizeAdmin($request);

        try {
            return response()->json([
                'success' => true,
                'data' => new UserResource($user)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث مستخدم
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $this->authorizeAdmin($request);

        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($user->id)],
                'password' => 'sometimes|nullable|string|min:8',
                'role' => ['sometimes', 'required', Rule::in(['Admin', 'Therapist', 'Client'])],
                'phone' => 'nullable|string|max:20',
                'bio' => 'nullable|string',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // ✅ إضافة الصورة
            ]);

            $updateData = [
                'name' => $validated['name'] ?? $user->name,
                'email' => $validated['email'] ?? $user->email,
                'role' => $validated['role'] ?? $user->role,
                'phone' => $validated['phone'] ?? $user->phone,
                'bio' => $validated['bio'] ?? $user->bio,
            ];

            // ✅ معالجة الصورة
            if ($request->hasFile('avatar')) {
                // حذف الصورة القديمة
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // حفظ الصورة الجديدة
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $updateData['avatar'] = $avatarPath;
            }

            // تحديث كلمة المرور فقط إذا تم تقديمها
            if (isset($validated['password']) && $validated['password']) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => new UserResource($user)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف مستخدم
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->authorizeAdmin($request);

        try {
            // منع حذف المستخدم الحالي
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete your own account'
                ], 422);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * إحصائيات المستخدمين
     */
    public function stats(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        try {
            $totalUsers = User::count();
            $totalClients = User::where('role', 'Client')->count();
            $totalTherapists = User::where('role', 'Therapist')->count();
            $totalAdmins = User::where('role', 'Admin')->count();

            // المستخدمين الجدد (آخر 7 أيام)
            $newUsers = User::where('created_at', '>=', now()->subDays(7))->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_users' => $totalUsers,
                    'total_clients' => $totalClients,
                    'total_therapists' => $totalTherapists,
                    'total_admins' => $totalAdmins,
                    'new_users' => $newUsers,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
