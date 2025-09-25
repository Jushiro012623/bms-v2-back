<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    /** @use HasFactory<\Database\Factories\DocumentTypeFactory> */
    use HasFactory;

    protected $fillable = ['requirements', 'fee', 'description', 'name', 'status'];

    
    /**
     * Get all of the request for the DocumentType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requestedDocuments(): HasMany
    {
        return $this->hasMany(DocumentRequest::class);
    }

    public function getStatusTextAttribute()
    {
        return $this->status == 1 ? 'active' : 'inactive';
    }
}
