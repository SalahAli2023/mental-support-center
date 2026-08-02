<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20|unique:users',
            'country_code' => 'required|string|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في التسجيل',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'country_code' => $request->country_code,
            // Force new users created via public registration to be clients
            'role' => 'Client',
            'joined_at' => now(),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الحساب بنجاح',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ]
        ], 201);
    }

    /**
     * Login user and create token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['البريد الإلكتروني أو كلمة المرور غير صحيحة.'],
            ]);
        }

        // للفرونتند: السماح للعملاء فقط
        if ($request->is('api/frontend/*') && !$user->isClient()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مسموح بالدخول من هذه الواجهة'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ]
        ]);
    }

    /**
     * Logout user (Revoke the token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح',
        ]);
    }

    /**
     * Get authenticated user.
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($request->user())
            ]
        ]);
    }

    /**
     * تسجيل مستخدم من الفرونتند (بدون حاجة لتأكيد كلمة المرور)
     */
    public function frontendRegister(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email|required_without:phone',
            'phone' => 'nullable|string|max:20|unique:users,phone|required_without:email',
            'password' => 'required|string|min:6',
            // المعلومات الإضافية
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date|before:today',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'governorate' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'marital_status' => 'required|in:single,married,divorced,widowed',
            'education_level' => 'required|in:elementary,middle,high_school,diploma,bachelor,graduate',
            'employment_status' => 'required|in:student,government_employee,private_employee,unemployed,housewife,retired',
            'profession' => 'nullable|string|max:255',
            'monthly_income' => 'required|in:less_than_60k,61k_to_120k,121k_to_200k,201k_to_350k,more_than_351k',
            'platform_purposes' => 'required|array|min:1',
            'platform_purposes.*' => 'in:information_resources,self_assessment,psychological_consultation,electronic_programs,other',
            'terms_accepted' => 'required|boolean',
            'privacy_accepted' => 'required|boolean',
            'info_accuracy_confirmed' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في التسجيل',
                'errors' => $validator->errors()
            ], 422);
        }

        // التحقق من أن جميع الموافقات صحيحة
        if (!$request->boolean('terms_accepted') || !$request->boolean('privacy_accepted') || !$request->boolean('info_accuracy_confirmed')) {
            return response()->json([
                'success' => false,
                'message' => 'يجب الموافقة على جميع الشروط',
                'errors' => [
                    'terms_accepted' => ['يجب الموافقة على شروط الخدمة'],
                    'privacy_accepted' => ['يجب الموافقة على سياسة الخصوصية'],
                    'info_accuracy_confirmed' => ['يجب الإقرار بصحة المعلومات']
                ]
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone ?? null,
            'password' => Hash::make($request->password),
            'role' => 'Client',
            'joined_at' => now(),
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'country' => $request->country,
            'city' => $request->city,
            'governorate' => $request->governorate,
            'district' => $request->district,
            'marital_status' => $request->marital_status,
            'education_level' => $request->education_level,
            'employment_status' => $request->employment_status,
            'profession' => $request->profession ?? null,
            'monthly_income' => $request->monthly_income,
            'platform_purposes' => $request->platform_purposes,
            'terms_accepted' => true,
            'privacy_accepted' => true,
            'info_accuracy_confirmed' => true,
        ]);

        $token = $user->createToken('frontend_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الحساب بنجاح',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token
            ]
        ], 201);
    }

    /**
     * تسجيل الدخول من الفرونتند
     */
    public function frontendLogin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات الدخول غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'
            ], 401);
        }
        // السماح بالدخول من هذه الواجهة للعملاء والمعالجين فقط (وليس الأدمن)
        if (!$user->isClient() && !$user->isTherapist()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مسموح بالدخول من هذه الواجهة'
            ], 403);
        }

        $token = $user->createToken('frontend_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token
            ]
        ]);
    }

    /**
     * Update authenticated user's profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date|before:today',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'governorate' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'education_level' => 'nullable|in:elementary,middle,high_school,diploma,bachelor,graduate',
            'employment_status' => 'nullable|in:student,government_employee,private_employee,unemployed,housewife,retired',
            'profession' => 'nullable|string|max:255',
            'monthly_income' => 'nullable|in:less_than_60k,61k_to_120k,121k_to_200k,201k_to_350k,more_than_351k',
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
            'gender' => $validated['gender'] ?? $user->gender,
            'date_of_birth' => $validated['date_of_birth'] ?? $user->date_of_birth,
            'country' => $validated['country'] ?? $user->country,
            'city' => $validated['city'] ?? $user->city,
            'governorate' => $validated['governorate'] ?? $user->governorate,
            'district' => $validated['district'] ?? $user->district,
            'marital_status' => $validated['marital_status'] ?? $user->marital_status,
            'education_level' => $validated['education_level'] ?? $user->education_level,
            'employment_status' => $validated['employment_status'] ?? $user->employment_status,
            'profession' => $validated['profession'] ?? $user->profession,
            'monthly_income' => $validated['monthly_income'] ?? $user->monthly_income,
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'data' => [
                'user' => new UserResource($user->fresh())
            ]
        ]);
    }
}