<?php

namespace App\Http\Controllers;

use App\Models\PsychologicalScale;
use App\Models\Category;
use App\Models\QuestionOption;
use App\Models\UserAssessment;
use App\Http\Resources\PsychologicalScaleResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\AssessmentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class FrontendScaleController extends Controller
{
    /**
     * عرض جميع المقاييس النشطة للصفحة الرئيسية
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = PsychologicalScale::where('is_active', true);

        // التحميل مع العلاقات الأساسية فقط (لتحسين الأداء)
        $query->with(['category']);

        // التصفية حسب الفئة
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // البحث في الصفحة الرئيسية
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('description_ar', 'like', "%{$search}%")
                  ->orWhere('description_en', 'like', "%{$search}%");
            });
        }

        // الترتيب المناسب للصفحة الرئيسية
        $query->orderBy('created_at', 'desc');

        $scales = $query->paginate($request->get('per_page', 12));

        return PsychologicalScaleResource::collection($scales);
    }

    /**
     * عرض مقياس محدد للصفحة الرئيسية
     */
    public function show($id): PsychologicalScaleResource
    {
        $scale = PsychologicalScale::where('id', $id)
            ->where('is_active', true)
            ->with([
                'category',
                'questions' => function($query) {
                    $query->orderBy('question_order')
                          ->with(['options' => function($query) {
                              $query->orderBy('option_order');
                          }]);
                },
                'interpretations'
            ])
            ->firstOrFail();

        return new PsychologicalScaleResource($scale);
    }

    /**
     * 🔥 FIXED: إرسال إجابات الاختبار - يحفظ مباشرة إذا كان مسجلاً، يطلب تسجيل إذا كان غير مسجل
     */
    public function submitTest(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:scale_questions,id',
            'answers.*.option_id' => 'required|exists:question_options,id',
        ]);

        $scale = PsychologicalScale::where('id', $id)
            ->where('is_active', true)
            ->with(['interpretations'])
            ->firstOrFail();

        // حساب النتيجة
        $totalScore = 0;
        
        foreach ($validated['answers'] as $answer) {
            $option = QuestionOption::find($answer['option_id']);
            if ($option) {
                $totalScore += $option->score_value;
            }
        }

        // الحصول على التفسير المناسب
        $interpretation = $scale->interpretations
            ->where('min_score', '<=', $totalScore)
            ->where('max_score', '>=', $totalScore)
            ->first();

        // 🔥 FIX: التحقق من المستخدم المصادق عليه باستخدام auth()
        $user = auth()->user();
        
        if ($user) {
            // 🔥 المستخدم مسجل - حفظ مباشر في user_assessments
            $assessment = UserAssessment::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'scale_id' => $id,
                'total_score' => $totalScore,
                'interpretation_level' => $interpretation ? $interpretation->interpretation_label_ar : 'غير معروف',
                'assessment_data' => $validated['answers'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'completed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ النتيجة بنجاح',
                'data' => [
                    'score' => $totalScore,
                    'max_score' => $scale->max_score,
                    'interpretation' => $interpretation ? new \App\Http\Resources\ResultInterpretationResource($interpretation) : null,
                    'scale' => new PsychologicalScaleResource($scale),
                    'assessment' => new AssessmentResource($assessment)
                ]
            ]);
        } else {
            // 🔥 المستخدم غير مسجل - تخزين مؤقت وطلب التسجيل
            $sessionKey = 'assessment_' . Str::random(20);
            $expiryTime = now()->addMinutes(30);

            $temporaryAssessment = [
                'scale_id' => $id,
                'answers' => $validated['answers'],
                'total_score' => $totalScore,
                'calculated_at' => now()->timestamp,
                'session_key' => $sessionKey
            ];

            Cache::put($sessionKey, $temporaryAssessment, 30 * 60);

            return response()->json([
                'success' => true,
                'message' => 'يجب تسجيل الدخول لمشاهدة النتيجة',
                'requires_login' => true,
                'data' => [
                    'scale_id' => $id,
                    'scale_name' => $scale->name_ar,
                    'preview' => [
                        'score_range' => '0-' . $scale->max_score,
                        'has_interpretation' => !is_null($interpretation),
                        'questions_count' => count($validated['answers'])
                    ],
                    'login_url' => '/api/frontend/login',
                    'register_url' => '/api/frontend/register',
                    'save_url' => '/api/frontend/scales/' . $id . '/save-result',
                    'session_key' => $sessionKey,
                    'expires_at' => $expiryTime->toISOString()
                ]
            ]);
        }
    }

    /**
     * حفظ النتيجة بعد تسجيل الدخول (للمستخدمين غير المسجلين سابقاً)
     */
    public function saveAssessmentResult(Request $request, $id): JsonResponse
    {
        $request->validate([
            'session_key' => 'required|string'
        ]);

        // استرجاع البيانات المؤقتة من الكاش
        $sessionKey = $request->session_key;
        $temporaryAssessment = Cache::get($sessionKey);

        if (!$temporaryAssessment || $temporaryAssessment['scale_id'] !== $id) {
            return response()->json([
                'success' => false,
                'message' => 'انتهت صلاحية الإجابات أو لم يتم العثور عليها'
            ], 404);
        }

        // التحقق من أن الوقت لم ينتهي (30 دقيقة)
        $expiryTime = $temporaryAssessment['calculated_at'] + (30 * 60);
        if (now()->timestamp > $expiryTime) {
            Cache::forget($sessionKey);
            return response()->json([
                'success' => false,
                'message' => 'انتهت صلاحية الإجابات، يرجى إعادة الاختبار'
            ], 410);
        }

        $scale = PsychologicalScale::where('id', $id)
            ->where('is_active', true)
            ->with(['interpretations'])
            ->firstOrFail();

        $interpretation = $scale->interpretations
            ->where('min_score', '<=', $temporaryAssessment['total_score'])
            ->where('max_score', '>=', $temporaryAssessment['total_score'])
            ->first();

        // حفظ التقييم في قاعدة البيانات
        $assessment = UserAssessment::create([
            'id' => Str::uuid(),
            'user_id' => auth()->user()->id, // 🔥 FIX: استخدام auth()->user()
            'scale_id' => $id,
            'total_score' => $temporaryAssessment['total_score'],
            'interpretation_level' => $interpretation ? $interpretation->interpretation_label_ar : 'غير معروف',
            'assessment_data' => $temporaryAssessment['answers'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'completed_at' => now(),
        ]);

        // مسح البيانات المؤقتة من الكاش
        Cache::forget($sessionKey);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ النتيجة بنجاح',
            'data' => [
                'score' => $temporaryAssessment['total_score'],
                'max_score' => $scale->max_score,
                'interpretation' => $interpretation ? new \App\Http\Resources\ResultInterpretationResource($interpretation) : null,
                'scale' => new PsychologicalScaleResource($scale),
                'assessment' => new AssessmentResource($assessment)
            ]
        ]);
    }

    /**
     * 🔥 NEW: مسار خاص للمستخدمين غير المسجلين (بدون حماية)
     */
    public function submitTestPublic(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:scale_questions,id',
            'answers.*.option_id' => 'required|exists:question_options,id',
        ]);

        $scale = PsychologicalScale::where('id', $id)
            ->where('is_active', true)
            ->with(['interpretations'])
            ->firstOrFail();

        // حساب النتيجة
        $totalScore = 0;
        
        foreach ($validated['answers'] as $answer) {
            $option = QuestionOption::find($answer['option_id']);
            if ($option) {
                $totalScore += $option->score_value;
            }
        }

        // الحصول على التفسير المناسب
        $interpretation = $scale->interpretations
            ->where('min_score', '<=', $totalScore)
            ->where('max_score', '>=', $totalScore)
            ->first();

        // 🔥 هذا المسار للمستخدمين غير المسجلين فقط
        $sessionKey = 'assessment_' . Str::random(20);
        $expiryTime = now()->addMinutes(30);

        $temporaryAssessment = [
            'scale_id' => $id,
            'answers' => $validated['answers'],
            'total_score' => $totalScore,
            'calculated_at' => now()->timestamp,
            'session_key' => $sessionKey
        ];

        Cache::put($sessionKey, $temporaryAssessment, 30 * 60);

        return response()->json([
            'success' => true,
            'message' => 'يجب تسجيل الدخول لمشاهدة النتيجة',
            'requires_login' => true,
            'data' => [
                'scale_id' => $id,
                'scale_name' => $scale->name_ar,
                'preview' => [
                    'score_range' => '0-' . $scale->max_score,
                    'has_interpretation' => !is_null($interpretation),
                    'questions_count' => count($validated['answers'])
                ],
                'login_url' => '/api/frontend/login',
                'register_url' => '/api/frontend/register',
                'save_url' => '/api/frontend/scales/' . $id . '/save-result',
                'session_key' => $sessionKey,
                'expires_at' => $expiryTime->toISOString()
            ]
        ]);
    }

    /**
     * المقاييس الشعبية للصفحة الرئيسية
     */
    public function popular(): AnonymousResourceCollection
    {
        $scales = PsychologicalScale::where('is_active', true)
            ->with(['category'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return PsychologicalScaleResource::collection($scales);
    }

    /**
     * المقاييس حسب الفئة للصفحة الرئيسية
     */
    public function byCategory($categoryId): AnonymousResourceCollection
    {
        $scales = PsychologicalScale::where('category_id', $categoryId)
            ->where('is_active', true)
            ->with(['category'])
            ->orderBy('name_ar')
            ->get();

        return PsychologicalScaleResource::collection($scales);
    }

    /**
     * الحصول على مقياس كامل للصفحة الرئيسية
     */
    public function getFullScale($id): PsychologicalScaleResource
    {
        $scale = PsychologicalScale::where('id', $id)
            ->where('is_active', true)
            ->with(['questions' => function($query) {
                $query->orderBy('question_order')
                      ->with(['options' => function($query) {
                          $query->orderBy('option_order');
                      }]);
            }, 'interpretations'])
            ->firstOrFail();

        return new PsychologicalScaleResource($scale);
    }

    /**
     * الحصول على الفئات النشطة للصفحة الرئيسية
     */
    public function categories(): AnonymousResourceCollection
    {
        $categories = Category::where('is_active', true)
            ->withCount(['psychologicalScales' => function($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('name_ar')
            ->get();

        return CategoryResource::collection($categories);
    }
}