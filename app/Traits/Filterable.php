<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

trait Filterable
{
    public function scopeFilter($query, array $filters)
    {
        foreach ($filters as $key => $value) {
            if (empty($value)) {
                continue;
            }

            if (method_exists($this, 'filter' . ucfirst($key))) {
                $this->{'filter' . ucfirst($key)}($query, $value);
            }
        }

        return $query;
    }

    public function scopeSearch($query, string $term, array $searchFields = [])
    {
        if (empty($searchFields)) {
            $searchFields = $this->getSearchableFields();
        }

        return $query->where(function ($q) use ($term, $searchFields) {
            foreach ($searchFields as $field) {
                $q->orWhere($field, 'like', "%{$term}%");
            }
        });
    }

    protected function getSearchableFields(): array
    {
        return property_exists($this, 'searchable') ? $this->searchable : [];
    }
}
