<?php

namespace App\Services\DocumentTypesService;

use App\Http\Requests\DocumentTypesRequests\DocumentTypesRequest;
use App\Http\Resources\DocumentTypesResource;
use App\Models\DocumentType;
use Illuminate\Http\Request;

class DocumentTypesService
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documentTypes = DocumentType::paginate(20);
        return response()->success("Success",  DocumentTypesResource::collection($documentTypes));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentType $documentType)
    {
        return response()->success('Success', new DocumentTypesResource($documentType));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DocumentTypesRequest $request, DocumentType $documentType)
    {
        $documentType->update($request->validated());
        return response()->success('Success', new DocumentTypesResource($documentType));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
