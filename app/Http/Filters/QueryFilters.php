<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Abstract base class for applying dynamic query filters to Eloquent models.
 *
 * Supports:
 * - Sorting by allowed fields
 * - Searching across defined columns and relations
 * - Including related models conditionally
 *
 * Extend this class for each model-specific filter set.
 */
abstract class QueryFilters
{
    /**
     * The Eloquent query builder instance.
     *
     * @var Builder
     */
    protected $builder;

    /**
     * The current HTTP request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * List of sortable attributes.
     * Can be:
     *  - Indexed array: ['name', 'created_at']
     *  - Key-value array: ['createdAt' => 'created_at']
     *
     * @var array
     */
    protected $sortable = [];

    /**
     * List of searchable attributes.
     * Supports dot notation for relations (e.g., "customer.name").
     *
     * @var array
     */
    protected $searchable = [];

    /**
     * List of allowed relations for eager loading.
     *
     * @var array
     */
    protected $relations = [];

    /**
     * Whether search functionality is enabled.
     *
     * @var bool
     */
    protected $enableSearch = true;

    /**
     * Whether including relations via the "include" filter is enabled.
     *
     * @var bool
     */
    protected $enableRelationsIncluding = false;

    /**
     * Create a new QueryFilters instance.
     *
     * @param Request $request The current HTTP request instance.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Applies all filter methods to the query builder based on request parameters.
     *
     * @param Builder $builder The Eloquent query builder instance.
     * @return Builder The filtered query builder.
     */
    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->request->all() as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $builder;
    }

    /**
     * Applies sorting to the query builder based on the 'sort' parameter.
     *
     * Supports:
     * - Multiple comma-separated attributes
     * - Optional '-' prefix for descending order
     * - Only allows attributes listed in $sortable
     *
     * @param string $value The sort parameter value from the request (e.g., "-createdAt,name").
     * @return void
     */
    protected function sort($value): void
    {
        $sortAttributes = explode(",", $value);

        foreach ($sortAttributes as $sortAttribute) {
            $direction = "asc";

            if (strpos($sortAttribute, "-") === 0) {
                $direction = "desc";
                $sortAttribute = substr($sortAttribute, 1);
            }

            if (! in_array($sortAttribute, $this->sortable, true) && ! array_key_exists($sortAttribute, $this->sortable)) {
                continue;
            }

            $columnName = $this->sortable[$sortAttribute] ?? $sortAttribute;
            $this->builder->orderBy($columnName, $direction);
        }
    }

    /**
     * Applies search to the query builder across all defined searchable fields.
     *
     * Supports:
     * - Direct column search (e.g., "name")
     * - Relation column search using dot notation (e.g., "customer.name")
     *
     * @param string $value The search query string.
     * @return Builder|null
     */
    protected function search($value)
    {
        if ($this->enableSearch && trim($value) !== '') {
            return $this->builder->where(function ($query) use ($value) {
                foreach ($this->searchable as $field) {
                    if (str_contains($field, '.')) {
                        [$relation, $column] = explode('.', $field, 2);
                        $query->orWhereHas($relation, function ($q) use ($column, $value) {
                            $q->where($column, 'like', '%' . $value . '%');
                        });
                    } else {
                        $query->orWhere($field, 'like', '%' . $value . '%');
                    }
                }
            });
        }
    }

    /**
     * Conditionally includes allowed relations in the query.
     *
     * Example:
     *   ?include=customer,orders
     *
     * @param string|null $value Comma-separated list of relation names to include.
     * @return Builder
     */
    protected function include($value)
    {
        if (!$value || !$this->enableRelationsIncluding) {
            return $this->builder;
        }

        $requested = explode(',', $value);
        $allowed = array_values(array_intersect($this->relations, $requested));

        return $this->builder->with($allowed);
    }
}
