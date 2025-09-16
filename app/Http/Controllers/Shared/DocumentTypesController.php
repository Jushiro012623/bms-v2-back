<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Services\DocumentTypesService\DocumentTypesService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DocumentTypesController extends Controller
{
    public function __construct(private DocumentTypesService $documentTypesService) 
    {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->documentTypesService->index();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        throw new NotFoundHttpException('Not Found');
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentType $documentType)
    {
        return $this->documentTypesService->show($documentType);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        throw new NotFoundHttpException('Not Found');
    }
}
