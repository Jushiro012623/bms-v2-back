<?php

namespace App\Services\DocumentRequestService;

use App\Http\Filters\DocumentRequestFilters;
use App\Http\Requests\DocumentRequest\CreateDocumentRequest;
use App\Http\Requests\DocumentRequest\UpdateDocumentRequest;
use App\Http\Resources\DocumentRequestResource;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;

class ClientDocumentRequestService
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, DocumentRequestFilters $filters)
    {
        $documentRequests = DocumentRequest::where('user_id', 1)->filters($filters)
            ->paginate($request->count ?? 10);
            
        return response()->success("Success", DocumentRequestResource::collection($documentRequests));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDocumentRequest $request)
    {
        $documentRequest = DocumentRequest::create([
            ...$request->validated(),
            'user_id' => 1,
        ])->fresh();


        return response()->success("Success", new DocumentRequestResource($documentRequest));
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentRequest $documentRequest)
    {
        return response()->success('Document Request Fetched Successfully', new DocumentRequestResource($documentRequest->load(['user', 'documentType'])));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentRequest $request, DocumentRequest $documentRequest)
    {
        $documentRequest->update($request->validated());
        return response()->success("Success", new DocumentRequestResource($documentRequest));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentRequest $documentRequest)
    {
        return response()->success("Success", new DocumentRequestResource($documentRequest));
    }
}
