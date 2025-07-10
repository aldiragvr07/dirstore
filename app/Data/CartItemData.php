<?php
declare(strict_types=1);

namespace App\Data;

use App\Models\Product;
use Spatie\LaravelData\Data;
use Livewire\Attributes\Computed;

class CartItemData extends Data
{
    public function __construct(
        public string $sku,
        public int $quantity,
        public float $price,
        public int $weight
    ) {}


    #[Computed()]
    public function product()
    {
        return ProductData::fromModel(
            Product::where('sku', $this->sku)->first()
        );
    }
}
