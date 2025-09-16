<?php

namespace App\Http\Filters;

class DocumentRequestFilters extends QueryFilters
{

    protected $sortable = [
        'document_type' => 'doc_type_id',
        'request_date',
        'release_date',
        'createdAt' => 'created_at',
        'status'
    ];

    protected $enableRelationsIncluding = true;

    protected $relations = ['documentType', 'user'];

    protected $searchable = ['documentType.name', 'documentType.description', 'documentType.requirements', 'purpose', 'status'];

    public function only($value)
    {
        $only = array_filter(explode(',', $value)); // remove empty values
        $only = array_map('intval', $only); // ensure integers

        if (!empty($only)) {
            $this->builder->whereHas('documentType', function ($q) use ($only) {
                $q->whereIn('id', $only);
            });
        }
        return $this->builder;
    }

    public function status($value) {
        return $this->builder->where('status', $value);
    }
    
}
