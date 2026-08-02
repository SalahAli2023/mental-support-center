<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'subject' => $this->subject,
            'message_type' => $this->message_type,
            'message_type_label' => $this->getMessageTypeLabel(),
            'message' => $this->message,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'admin_notes' => $this->admin_notes,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'created_at_human' => $this->created_at->diffForHumans(),
        ];
    }

    private function getMessageTypeLabel()
    {
        $types = [
            'inquiry' => 'استفسار',
            'complaint' => 'شكوى', 
            'suggestion' => 'اقتراح',
            'support' => 'دعم فني',
            'other' => 'أخرى'
        ];
        
        return $types[$this->message_type] ?? $this->message_type;
    }

    private function getStatusLabel()
    {
        $statuses = [
            'new' => 'جديد',
            'in_progress' => 'قيد المعالجة',
            'resolved' => 'تم الحل',
            'closed' => 'مغلق'
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }
}
