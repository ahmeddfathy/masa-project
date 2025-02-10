<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
  public function scopeSearch(Builder $query, string $term = ''): Builder
  {
    $searchableFields = $this->searchableFields ?? [];

    return $query->where(function ($query) use ($term, $searchableFields) {
      foreach ($searchableFields as $field) {
        $query->orWhere($field, 'LIKE', "%{$term}%");
      }
    });
  }

  public function scopeFilter(Builder $query, array $filters = []): Builder
  {
    $filterableFields = $this->filterableFields ?? [];

    foreach ($filters as $field => $value) {
      if (in_array($field, $filterableFields) && !empty($value)) {
        if (is_array($value)) {
          $query->whereIn($field, $value);
        } else {
          $query->where($field, $value);
        }
      }
    }

    return $query;
  }
}
