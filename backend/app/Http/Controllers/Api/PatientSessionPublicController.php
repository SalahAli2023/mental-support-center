<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientSession;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PatientSessionPublicController extends Controller
{
    /**
     * إرجاع نظرة عامة على جلسات المريض لواجهة المستخدم العامة.
     */
    public function overview(Request $request, Patient $patient)
    {
        $limit = (int) min($request->integer('limit', 4), 12);
        $historyLimit = (int) min($request->integer('history_limit', 4), 20);

        $patient->load('user');

        $upcomingSessions = $patient->sessions()
            ->with(['therapist.user'])
            ->where('session_date', '>=', now()->startOfDay())
            ->orderBy('session_date')
            ->orderBy('session_time')
            ->limit($limit)
            ->get();

        $previousSessions = $patient->sessions()
            ->with(['therapist.user'])
            ->where(function ($query) {
                $query->where('session_date', '<', now()->startOfDay())
                      ->orWhere('status', 'completed');
            })
            ->orderBy('session_date', 'desc')
            ->orderBy('session_time', 'desc')
            ->limit($historyLimit)
            ->get();

        $summary = [
            'id' => $patient->id,
            'name' => $patient->name,
            'age' => $patient->age,
            'avatar' => $patient->avatar,
            'totalSessions' => $patient->sessions()->count(),
            'completedSessions' => $patient->sessions()->where('status', 'completed')->count(),
            'attendanceRate' => $this->calculateAttendanceRate($patient),
            'progress' => $patient->getOverallProgress(),
            'therapist' => $this->resolvePrimaryTherapist($upcomingSessions, $previousSessions),
        ];

        $stats = [
            'scheduled' => $patient->sessions()->where('status', 'scheduled')->count(),
            'completed' => $patient->sessions()->where('status', 'completed')->count(),
            'cancelled' => $patient->sessions()->where('status', 'cancelled')->count(),
        ];

        return response()->json([
            'patient' => $summary,
            'stats' => $stats,
            'upcoming_sessions' => $this->formatSessions($upcomingSessions),
            'previous_sessions' => $this->formatSessions($previousSessions, true),
        ]);
    }

    /**
     * عرض جلسة محددة للواجهة العامة.
     */
    public function show(Patient $patient, PatientSession $session)
    {
        abort_unless($session->patient_id === $patient->id, 404, 'Session not found for patient');

        $session->load(['therapist', 'therapist.user', 'patient.user']);

        return response()->json([
            'session' => $this->formatSession($session)
        ]);
    }

    /**
     * حساب معدل الحضور للجلسات المكتملة.
     */
    private function calculateAttendanceRate(Patient $patient): int
    {
        $total = $patient->sessions()->count();

        if ($total === 0) {
            return 0;
        }

        $completed = $patient->sessions()->where('status', 'completed')->count();

        return (int) round(($completed / $total) * 100);
    }

    /**
     * تجهيز بيانات الجلسات للإرجاع.
     */
    private function formatSessions(Collection $sessions, bool $isPast = false): array
    {
        return $sessions->map(fn (PatientSession $session) => $this->formatSession($session, $isPast))
            ->values()
            ->all();
    }

    /**
     * تحديد المعالج الأساسي للعرض في البطاقة الجانبية.
     */
    private function resolvePrimaryTherapist(Collection $upcoming, Collection $previous): ?array
    {
        $session = $upcoming->first() ?? $previous->first();

        if (!$session || !$session->therapist) {
            return null;
        }

        return [
            'id' => $session->therapist->id,
            'name' => $session->therapist->name_ar ?? $session->therapist->name_en,
            'avatar' => $session->therapist->user?->avatar,
            'specialty' => $session->therapist->specialty_ar ?? $session->therapist->specialty_en,
        ];
    }

    private function formatSession(PatientSession $session, bool $isPast = false): array
    {
        return [
            'id' => $session->id,
            'title' => $session->title,
            'type' => $session->type_text,
            'status' => $session->status,
            'statusText' => $session->status_text,
            'date' => $session->session_date->format('Y-m-d'),
            'time' => $session->session_time,
            'duration' => $session->duration ?? 60,
            'progress' => $session->progress,
            'description' => $session->notes,
            'therapist' => $session->therapist ? [
                'id' => $session->therapist->id,
                'name' => $session->therapist->name_ar ?? $session->therapist->name_en,
                'avatar' => $session->therapist->user?->avatar,
                'specialty' => $session->therapist->specialty_ar ?? $session->therapist->specialty_en,
                'rating' => $session->therapist->rating,
            ] : null,
            'isActiveNow' => $session->is_active_now,
            'timeRemaining' => $session->time_remaining,
            'canJoin' => $session->is_active_now && $session->status === 'scheduled',
            'showNotesLink' => $isPast,
            'video' => [
                'provider' => $session->video_provider,
                'meeting_id' => $session->video_meeting_id,
                'password' => $session->video_password,
                'join_url' => $session->video_join_url,
                'start_url' => $session->video_start_url,
            ],
            'patient' => $session->patient ? [
                'id' => $session->patient->id,
                'name' => $session->patient->user?->name,
                'email' => $session->patient->user?->email,
            ] : null,
        ];
    }
}

