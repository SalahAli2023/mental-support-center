<?php

namespace App\Http\Controllers\Api;

use App\Models\PsychologicalScale;
use App\Models\ScaleQuestion;
use App\Models\QuestionOption;
use App\Models\ResultInterpretation;
use App\Models\UserAssessment;
use App\Http\Controllers\Controller;
use App\Http\Resources\AssessmentResource;
use App\Http\Resources\ResultResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request):JsonResponse
    {
        $user = Auth::user();
        
        // إذا كان المستخدم مشرفاً، يعرض جميع النتائج
        // وإلا يعرض فقط نتائجه الخاصة
        $query = UserAssessment::with(['psychologicalScale', 'user']);
        
        if ($user && $user->role === 'Admin') {
            // المشرف يرى جميع النتائج
            $assessments = $query->orderByRaw('COALESCE(completed_at, created_at) DESC')
                ->get();
        } else {
            // المستخدم العادي يرى فقط نتائجه
            $assessments = $query->where('user_id', Auth::id())
                ->orderByRaw('COALESCE(completed_at, created_at) DESC')
                ->get();
        }

        // تسجيل عدد النتائج للتحقق
        \Log::info('Assessments count: ' . $assessments->count());
        \Log::info('User role: ' . ($user ? $user->role : 'null'));

        return response()->json([
            'success' => true,
            'data' => AssessmentResource::collection($assessments)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'scale_id' => 'required|exists:psychological_scales,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:scale_questions,id',
            'answers.*.selected_option_id' => 'required|exists:question_options,id',
        ]);

        try {
            DB::beginTransaction();

            $scale = PsychologicalScale::findOrFail($validated['scale_id']);
            $totalScore = $this->calculateTotalScore($validated['answers']);

            // الحصول على التفسير المناسب
            $interpretation = ResultInterpretation::where('scale_id', $scale->id)
                ->where('min_score', '<=', $totalScore)
                ->where('max_score', '>=', $totalScore)
                ->first();

            $assessment = UserAssessment::create([
                'user_id' => Auth::id(),
                'scale_id' => $scale->id,
                'total_score' => $totalScore,
                'interpretation_level' => $interpretation ? $interpretation->interpretation_label : 'غير معروف',
                'assessment_data' => $validated['answers'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'completed_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ التقييم بنجاح',
                'data' => new AssessmentResource($assessment->load('psychologicalScale', 'psychologicalScale.interpretation')),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ التقييم'
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $assessment = UserAssessment::with(['psychologicalScale', 'psychologicalScale.interpretation'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new AssessmentResource($assessment)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
       //
    }

    private function calculateTotalScore(array $answers): int
    {
        $totalScore = 0;

        foreach ($answers as $answer) {
            $option = QuestionOption::find($answer['selected_option_id']);
            if ($option) {
                $totalScore += $option->score_value;
            }
        }

        return $totalScore;
    }

    public function getUserStatistics(): JsonResponse
    {
        $userId = Auth::id();

        $statistics = [
            'total_assessments' => UserAssessment::where('user_id', $userId)->count(),
            'recent_assessments' => UserAssessment::where('user_id', $userId)
                ->with('psychologicalScale')
                ->orderBy('completed_at', 'desc')
                ->limit(5)
                ->get(),
            'average_score' => UserAssessment::where('user_id', $userId)->avg('total_score'),
        ];

        return response()->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    public function getAssessmentResult($id): JsonResponse
    {
        $assessment = UserAssessment::with(['psychologicalScale', 'psychologicalScale.interpretations'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $interpretation = $assessment->psychologicalScale->interpretations
            ->where('min_score', '<=', $assessment->total_score)
            ->where('max_score', '>=', $assessment->total_score)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'assessment' => new AssessmentResource($assessment),
                'detailed_interpretation' => $interpretation
            ]
        ]);
    }
}
