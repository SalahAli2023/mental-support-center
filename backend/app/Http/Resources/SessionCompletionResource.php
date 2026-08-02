<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SessionCompletionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $locale = app()->getLocale();
        $isArabic = $locale === 'ar';
        
        return [
            'id' => $this->id,
            'session_id' => $this->session_id,
            'user_id' => $this->user_id,
            
            // معلومات الجلسة
            'session' => $this->whenLoaded('session', function () use ($isArabic) {
                return [
                    'id' => $this->session->id,
                    'title' => $isArabic ? $this->session->title_ar : $this->session->title_en,
                    'description' => $isArabic ? $this->session->goal_ar : $this->session->goal_en,
                    'session_order' => $this->session->session_order,
                    'duration' => $this->session->duration,
                    'image_url' => $this->session->image_url,
                    'program' => $this->session->program ? [
                        'id' => $this->session->program->id,
                        'name' => $isArabic ? $this->session->program->name_ar : $this->session->program->name_en
                    ] : null
                ];
            }),
            
            // معلومات المستخدم
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'avatar' => $this->user->avatar,
                ];
            }),
            
            // بيانات الإكمال
            'is_completed' => $this->is_completed,
            'completed_at' => $this->completed_at ? $this->completed_at->format('Y-m-d H:i:s') : null,
            'completed_at_formatted' => $this->completed_at ? $this->completed_at->translatedFormat('d F Y \عند H:i') : null,
            
            // معلومات إضافية
            'completion_time_hours' => $this->completion_time,
            'completion_status' => $isArabic ? $this->completion_status : 
                ($this->is_completed ? 'Completed' : 'Incomplete'),
            'is_late' => $this->created_at && $this->completed_at ? 
                $this->created_at->diffInDays($this->completed_at) > 7 : false,
            
            // التقدم
            'progress' => $this->when($this->session && $this->session->program, function () {
                $totalSessions = $this->session->program->sessions()->count();
                $completedSessions = SessionCompletion::where('user_id', $this->user_id)
                    ->whereHas('session', function($query) {
                        $query->where('program_id', $this->session->program_id);
                    })
                    ->where('is_completed', true)
                    ->count();
                
                return [
                    'completed_sessions' => $completedSessions,
                    'total_sessions' => $totalSessions,
                    'percentage' => $totalSessions > 0 ? round(($completedSessions / $totalSessions) * 100, 2) : 0,
                ];
            }),
            
            // التواريخ
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
            'created_at_formatted' => $this->created_at ? $this->created_at->translatedFormat('d F Y') : null,
        ];
    }
    
    /**
     * Customize the outgoing response for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        $response->header('Content-Type', 'application/json; charset=utf-8');
    }
}