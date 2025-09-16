<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Filters\DocumentRequestFilters;
use App\Http\Requests\DocumentRequest\CreateDocumentRequest;
use App\Http\Requests\DocumentRequest\UpdateDocumentRequest;
use App\Models\DocumentRequest;
use App\Services\DocumentRequestService\ClientDocumentRequestService;
use Illuminate\Http\Request;

class DocumentRequestController extends Controller
{
    public function __construct(private ClientDocumentRequestService $cLientDocumentRequestService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request,DocumentRequestFilters $filters)
    {
        return $this->cLientDocumentRequestService->index($request, $filters);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateDocumentRequest $request)
    {
        return $this->cLientDocumentRequestService->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentRequest $documentRequest)
    {
        return $this->cLientDocumentRequestService->show($documentRequest);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentRequest $request, DocumentRequest $documentRequest)
    {
        // return $request;
        return $this->cLientDocumentRequestService->update($request, $documentRequest);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentRequest $documentRequest)
    {
        return $this->cLientDocumentRequestService->destroy($documentRequest);
    }
}
