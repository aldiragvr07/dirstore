<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductCatalog extends Component
{
    public function render()
    {
        $products = Product::paginate(9);
        // TODO Make a DTO
        return view('livewire.product-catalog', compact('products'));
    }
}
