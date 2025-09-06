<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalDocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'document_type' => $this->document_type,
            'document_date' => optional($this->document_date)->toDateString(),
            'patient' => $this->whenLoaded('patient', function () {
                return ['id' => $this->patient->id, 'full_name' => $this->patient->full_name];
            }),
            'created_by' => $this->whenLoaded('createdBy', function () {
                return ['id' => $this->createdBy->id, 'full_name' => $this->createdBy->full_name];
            }),
            'summary' => $this->summary,
        ];
    }
}
