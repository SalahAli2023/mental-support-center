<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Support\MediaHelper;

class TherapistReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $client = $this->whenLoaded('client');

        return [
            'id' => $this->id,
            'session_id' => $this->session_id,
            'rating' => (int) $this->rating,
            'comment' => $this->comment,
            'client' => $client ? [
                'id' => $client->id,
                'name' => $client->name,
                'avatar' => MediaHelper::toPublicUrl($client->avatar),
            ] : null,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}

