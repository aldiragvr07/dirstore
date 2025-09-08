<?php 
declare(strict_types=1);

namespace App\Contract;
use App\Data\CartItemData;
use App\Data\CartData;

interface CartServiceInterface
{
    public function addOrUpdate(CartItemData $item) : void;
    public function remove(string $sku) : void;
    public function getItemBySku(string $sku) : ?CartItemData;
    public function clear() : void;
    public function all() : CartData;
}