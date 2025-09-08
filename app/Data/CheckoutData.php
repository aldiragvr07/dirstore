<?php

namespace App\Data;

use App\Data\CustomerData;
use Pest\ArchPresets\Custom;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Computed;

class CheckoutData extends Data
{
    #[Computed]
    public float $sub_total;

    #[Computed]
    public float $shipping_cost;

    #[Computed]
    public float $grand_total;

    public function __construct(
        public CustomerData $customer,
        public string $address_line,
        public RegionData $origin,
        public RegionData $destination,
        public CartData $cart,
        public ShippingData $shipping,
        public PaymentData $payment

    ) {
        $this->sub_total =$cart->total;
        $this->shipping_cost = $shipping->cost;
        $this->grand_total = $this->sub_total + $this->shipping_cost;
    }
}
