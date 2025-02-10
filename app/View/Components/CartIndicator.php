<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Session;

class CartIndicator extends Component
{
  public $itemCount;
  public $total;

  public function __construct()
  {
    $cart = Session::get('cart', []);
    $this->itemCount = count($cart);

    // Calculate total if needed
    $this->total = 0;
    if ($this->itemCount > 0) {
      $products = \App\Models\Product::whereIn('id', array_keys($cart))->get();
      foreach ($products as $product) {
        $this->total += $product->price * $cart[$product->id];
      }
    }
  }

  public function render()
  {
    return view('components.cart-indicator');
  }
}
