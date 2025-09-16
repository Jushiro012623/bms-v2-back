<?php

namespace App\Http\Resources;

use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentRequestResource extends JsonResource
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
            'user' => $this->whenLoaded('user', function(){
                return new UserResource($this->user);
            }),
            'document_type' => $this->whenLoaded('documentType', function(){
                return new DocumentTypesResource($this->documentType);
            }),
            'purpose' => $this->purpose,
            'notes' => $this->notes,
            'status' => $this->status_text,
            'request_date' => $this->request_date,
            'release_date' => $this->release_date,
            $this->mergeWhen($request->routeIs('document-requests.show'), function(){
                return [
                    'created_at' => $this->created_at,
                    'updated_at' => $this->updated_at,
                ];
            })
        ];
    }
}
