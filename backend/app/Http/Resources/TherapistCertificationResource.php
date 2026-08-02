<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TherapistCertificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'therapist_id' => $this->therapist_id,
            'name' => $this->name,
            'issuing_authority' => $this->issuing_authority,
            'year_obtained' => $this->year_obtained,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}