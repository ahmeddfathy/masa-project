<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchProductsRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'search' => 'nullable|string|max:100',
      'category' => 'nullable|exists:categories,id',
      'min_price' => 'nullable|numeric|min:0',
      'max_price' => 'nullable|numeric|min:0',
      'sort' => 'nullable|in:price_asc,price_desc,newest,popular',
      'per_page' => 'nullable|integer|min:1|max:100'
    ];
  }

  public function filters(): array
  {
    return array_filter([
      'category_id' => $this->category,
      'in_stock' => $this->boolean('in_stock'),
    ]);
  }
}
