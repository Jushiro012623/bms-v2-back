<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class IssuedDocument extends Model
{
    /** @use HasFactory<\Database\Factories\IssuedDocumentFactory> */
    use HasFactory;

    protected $fillable = ['request_id', 'document_number', 'signed_by', 'issued_at', 'qr_code'];

    /**
     * Get the requestedDocuments associated with the IssuedDocument
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function requestedDocuments(): HasOne
    {
        return $this->hasOne(DocumentRequest::class);
    }
}
