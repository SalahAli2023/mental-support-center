<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProgramResource extends JsonResource
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
            'user_id' => $this->user_id,
            'program_id' => $this->program_id,
            
            // معلومات المستخدم
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'phone' => $this->user->phone,
                    'avatar' => $this->user->avatar,
                ];
            }),
            
            // معلومات البرنامج
            'program' => $this->whenLoaded('program', function () use ($isArabic) {
                return [
                    'id' => $this->program->id,
                    'name' => $isArabic ? $this->program->name_ar : $this->program->name_en,
                    'description' => $isArabic ? $this->program->description_ar : $this->program->description_en,
                    'image_url' => $this->program->image_url,
                    'duration' => $this->program->duration,
                    'total_sessions' => $this->program->sessions_count ?? $this->program->sessions()->count(),
                    'scale' => $this->program->scale ? [
                        'id' => $this->program->scale->id,
                        'name' => $isArabic ? $this->program->scale->name_ar : $this->program->scale->name_en
                    ] : null
                ];
            }),
            
            // بيانات التسجيل
            'enrollment_date' => $this->enrollment_date ? $this->enrollment_date->format('Y-m-d H:i:s') : null,
            'enrollment_date_formatted' => $this->enrollment_date ? $this->enrollment_date->translatedFormat('d F Y') : null,
            'progress_percentage' => $this->progress_percentage,
            'status' => $this->status,
            'status_label' => $this->status_label[$locale] ?? $this->status_label['en'],
            
            // معلومات إضافية
            'is_completed' => $this->is_completed,
            'is_in_progress' => $this->is_in_progress,
            'is_active' => $this->status === 'enrolled' || $this->status === 'in_progress',
            
            // تقدم الجلسات
            'sessions_progress' => $this->when($request->has('include_sessions_progress'), function () {
                $completedSessions = $this->sessionCompletions()->count();
                $totalSessions = $this->program->sessions()->count();
                
                return [
                    'completed_sessions' => $completedSessions,
                    'total_sessions' => $totalSessions,
                    'percentage' => $totalSessions > 0 ? round(($completedSessions / $totalSessions) * 100, 2) : 0,
                ];
            }),
            
            // التواريخ
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
            'created_at_formatted' => $this->created_at ? $this->created_at->translatedFormat('d F Y \عند H:i') : null,
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