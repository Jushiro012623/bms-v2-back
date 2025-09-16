<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentTypesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "fee" => $this->fee,
            "requirements" => $this->requirements,
            "status" => $this->status_text,
            $this->mergeWhen($request->routeIs('document-types.show'), function () {
                return [
                    'created_at' => $this->created_at,
                    'updated_at' => $this->updated_at,
                ];
            })
        ];
    }
}
