<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    /**
     * عرض قائمة المرضى مع التصفية
     */
    public function index(Request $request)
    {
        $query = Patient::with(['user', 'conditions', 'user.userPrograms.program'])
            ->withCount('conditions');

        // التصفية بالبحث
        if ($request->has('search') && $request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        // التصفية بالحالة
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // التصفية بالحالة الصحية
        if ($request->has('condition') && $request->condition) {
            $query->whereHas('conditions', function($q) use ($request) {
                $q->where('condition_ar', 'like', "%{$request->condition}%")
                  ->orWhere('condition_en', 'like', "%{$request->condition}%");
            });
        }

        // التقسيم الصفحي
        $perPage = $request->get('per_page', 9);
        $patients = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'patients' => $this->formatPatientsData($patients->items()),
            'pagination' => [
                'current_page' => $patients->currentPage(),
                'total_pages' => $patients->lastPage(),
                'total_items' => $patients->total(),
                'per_page' => $patients->perPage(),
            ]
        ]);
    }

    /**
     * عرض بيانات مريض محدد
     */
    public function show($id)
    {
        try {
            $patient = Patient::with(['user', 'conditions', 'user.userPrograms.program'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'patient' => $this->formatPatientData($patient)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'المريض غير موجود'
            ], 404);
        }
    }

    /**
     * إضافة مريض جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'login.username' => 'required|string|unique:users,name',
            'login.password' => 'required|string|min:6',
            'profile.name.ar' => 'required|string',
            'profile.name.en' => 'required|string',
            'profile.email' => 'required|email|unique:users,email',
            'profile.phone' => 'required|string',
            'profile.dateOfBirth' => 'nullable|date',
            'profile.gender' => ['nullable', Rule::in(['male', 'female'])],
            'profile.address.ar' => 'nullable|string',
            'profile.address.en' => 'nullable|string',
            'medical.therapyGoals.ar' => 'nullable|string',
            'medical.therapyGoals.en' => 'nullable|string',
            'medical.status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        DB::beginTransaction();
        try {
            // إنشاء المستخدم
            $user = User::create([
                'name' => $validated['profile']['name']['ar'],
                'email' => $validated['profile']['email'],
                'password' => Hash::make($validated['login']['password']),
                'role' => 'Client',
                'phone' => $validated['profile']['phone'],
                'status' => 'active',
                'joined_at' => now(),
            ]);

            // إنشاء المريض
            $patient = Patient::create([
                'user_id' => $user->id,
                'date_of_birth' => $validated['profile']['dateOfBirth'] ?? null,
                'gender' => $validated['profile']['gender'] ?? null,
                'address_ar' => $validated['profile']['address']['ar'] ?? null,
                'address_en' => $validated['profile']['address']['en'] ?? null,
                'therapy_goals_ar' => $validated['medical']['therapyGoals']['ar'] ?? null,
                'therapy_goals_en' => $validated['medical']['therapyGoals']['en'] ?? null,
                'status' => $validated['medical']['status'],
            ]);

            // إضافة الحالات الصحية
            if ($request->has('medical.conditions.ar') && is_array($request->medical['conditions']['ar'])) {
                foreach ($request->medical['conditions']['ar'] as $index => $conditionAr) {
                    $conditionEn = $request->medical['conditions']['en'][$index] ?? $conditionAr;
                    
                    $patient->conditions()->create([
                        'condition_ar' => $conditionAr,
                        'condition_en' => $conditionEn,
                        'diagnosis_date' => now(),
                        'severity' => 'mild',
                        'is_active' => true,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة المريض بنجاح',
                'patient' => $this->formatPatientData($patient->load(['user', 'conditions']))
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة المريض',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث بيانات مريض
     */
    public function update(Request $request, $id)
    {
        $patient = Patient::with('user')->findOrFail($id);

        $validated = $request->validate([
            'profile.name.ar' => 'required|string',
            'profile.name.en' => 'required|string',
            'profile.email' => 'required|email|unique:users,email,' . $patient->user_id,
            'profile.phone' => 'required|string',
            'profile.dateOfBirth' => 'nullable|date',
            'profile.gender' => ['nullable', Rule::in(['male', 'female'])],
            'profile.address.ar' => 'nullable|string',
            'profile.address.en' => 'nullable|string',
            'medical.therapyGoals.ar' => 'nullable|string',
            'medical.therapyGoals.en' => 'nullable|string',
            'medical.status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        DB::beginTransaction();
        try {
            // تحديث بيانات المستخدم
            $patient->user->update([
                'name' => $validated['profile']['name']['ar'],
                'email' => $validated['profile']['email'],
                'phone' => $validated['profile']['phone'],
            ]);

            // تحديث بيانات المريض
            $patient->update([
                'date_of_birth' => $validated['profile']['dateOfBirth'] ?? null,
                'gender' => $validated['profile']['gender'] ?? null,
                'address_ar' => $validated['profile']['address']['ar'] ?? null,
                'address_en' => $validated['profile']['address']['en'] ?? null,
                'therapy_goals_ar' => $validated['medical']['therapyGoals']['ar'] ?? null,
                'therapy_goals_en' => $validated['medical']['therapyGoals']['en'] ?? null,
                'status' => $validated['medical']['status'],
            ]);

            // تحديث كلمة المرور إذا تم تقديمها
            if ($request->has('login.password') && $request->login['password']) {
                $patient->user->update([
                    'password' => Hash::make($request->login['password'])
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث بيانات المريض بنجاح',
                'patient' => $this->formatPatientData($patient->load(['user', 'conditions']))
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث بيانات المريض',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف مريض
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $patient = Patient::findOrFail($id);
            $user = $patient->user;

            $patient->conditions()->delete();
            $patient->delete();
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المريض بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المريض',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على إحصائيات المرضى
     */
    public function getStats()
    {
        $stats = [
            'total_patients' => Patient::count(),
            'active_patients' => Patient::where('status', 'active')->count(),
            'inactive_patients' => Patient::where('status', 'inactive')->count(),
            'patients_with_conditions' => Patient::has('conditions')->count(),
            'recent_patients' => Patient::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * تصدير بيانات المرضى
     */
    public function export(Request $request)
    {
        $patients = Patient::with(['user', 'conditions'])->get();

        $exportData = $patients->map(function($patient) {
            return [
                'id' => $patient->id,
                'name' => $patient->user->name,
                'email' => $patient->user->email,
                'phone' => $patient->user->phone,
                'status' => $patient->status,
                'conditions' => $patient->conditions->pluck('condition_ar')->implode(', '),
                'created_at' => $patient->created_at->format('Y-m-d'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $exportData,
            'exported_at' => now()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * تنسيق بيانات المريض
     */
    private function formatPatientData($patient)
    {
        return [
            'id' => $patient->id,
            'user_id' => $patient->user_id,
            'name' => $patient->user->name,
            'email' => $patient->user->email,
            'phone' => $patient->user->phone,
            'dateOfBirth' => $patient->date_of_birth?->format('Y-m-d'),
            'age' => $patient->date_of_birth ? now()->diffInYears($patient->date_of_birth) : null,
            'gender' => $patient->gender,
            'avatar' => $patient->user->avatar,
            'conditions' => $patient->conditions->pluck('condition_ar')->toArray(),
            'totalConditions' => $patient->conditions_count ?? $patient->conditions->count(),
            'status' => $patient->status,
            'address' => $patient->address_ar,
            'therapyGoals' => $patient->therapy_goals_ar,
            'createdAt' => $patient->created_at->format('Y-m-d'),
            'programs' => $this->formatPatientPrograms($patient),
        ];
    }

    /**
     * تنسيق بيانات مجموعة المرضى
     */
    private function formatPatientsData($patients)
    {
        return collect($patients)->map(function($patient) {
            return $this->formatPatientData($patient);
        })->toArray();
    }

    /**
     * تنسيق برامج المريض وتقدمه
     */
    private function formatPatientPrograms($patient): array
    {
        if (!$patient->relationLoaded('user')) {
            $patient->load('user.userPrograms.program');
        } elseif (!$patient->user->relationLoaded('userPrograms')) {
            $patient->user->load('userPrograms.program');
        }

        $userPrograms = $patient->user?->userPrograms ?? collect();

        return $userPrograms->map(function ($userProgram) {
            $program = $userProgram->program;

            return [
                'id' => $userProgram->id,
                'program_id' => $userProgram->program_id,
                'program_name' => $program?->name ?? $program?->name_ar ?? $program?->name_en ?? '',
                'progress' => (int) ($userProgram->progress_percentage ?? 0),
                'status' => $userProgram->status,
                'enrolled_at' => $userProgram->enrollment_date?->format('Y-m-d'),
                'completed_at' => $userProgram->completed_at?->format('Y-m-d'),
            ];
        })->values()->toArray();
    }
}