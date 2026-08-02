<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserMessageResource extends JsonResource
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
            'subject' => $this->subject,
            'message_type' => $this->message_type,
            'message' => $this->message,
            'response' => $this->response,
            'status' => $this->status,
            'is_read' => $this->is_read,
            'read_at' => $this->read_at?->toISOString(),
            'is_public' => $this->is_public,
            'public_at' => $this->public_at?->toISOString(),
            'responded_at' => $this->responded_at?->toISOString(),
            'responder' => $this->whenLoaded('responder', function () {
                return [
                    'id' => $this->responder?->id,
                    'name' => $this->responder?->name,
                    'email' => $this->responder?->email,
                ];
            }),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}

