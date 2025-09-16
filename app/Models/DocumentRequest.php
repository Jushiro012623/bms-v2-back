<?php

namespace App\Models;

use App\Http\Filters\QueryFilters;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DocumentRequest extends Model
{
    /** @use HasFactory<\Database\Factories\DocumentRequestFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'doc_type_id', 'purpose', 'status', 'request_date', 'release_date', 'notes'];

    /**
     * Get the user that owns the DocumentRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the issuedDocuments associated with the DocumentRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function issuedDocuments(): HasOne
    {
        return $this->hasOne(IssuedDocument::class);
    }


    /**
     * Get all of the payments for the DocumentRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the documentType that owns the DocumentRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'doc_type_id');
    }

    public function scopeFilters(Builder $builder, QueryFilters $filters)
    {
        return $filters->apply($builder);
    }

    // Status codes
    public const STATUS_PENDING  = 1;
    public const STATUS_APPROVED = 2;
    public const STATUS_REJECTED = 3;
    public const STATUS_RELEASED = 4;

    // Mapping code → text
    public const STATUS_TEXT = [
        self::STATUS_PENDING  => 'pending',
        self::STATUS_APPROVED => 'approved',
        self::STATUS_REJECTED => 'rejected',
        self::STATUS_RELEASED => 'released',
    ];

    // Optional helper accessor
    public function getStatusTextAttribute(): string
    {
        return self::STATUS_TEXT[$this->status] ?? 'Unknown';
    }
}
