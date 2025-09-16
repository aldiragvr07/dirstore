<?php
declare(strict_types=1);

namespace App\Drivers\Shipping;

use App\Data\CartData;
use App\Data\RegionData;
use App\Data\ShippingData;
use App\Data\ShippingServiceData;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\DataCollection;
use App\Contract\ShippingDriverInterface;

class APIKurirShippingDriver implements ShippingDriverInterface
{
    public readonly string $driver;

    public function __construct()
    {
        $this->driver = 'apikurir';
    }
    /** @return DataCollection<ShippingServiceData> */
    public function getServices() : DataCollection
    {
        return ShippingServiceData::collect([
            ['driver' => $this->driver,
            'code'  => 'jne-reguler',
            'courier'   => 'JNE',
            'service'   => 'Reguler'
        ],
            ['driver' => $this->driver,
            'code'  => 'jne-reguler-express',
            'courier'   => 'JNE',
            'service'   => 'Express'
        ],
        [
            'driver' => $this->driver,
            'code'  => 'jne-same-day',
            'courier'   => 'JNE',
            'service'   => 'Sameday'
        ],
        [
            'driver' => $this->driver,
            'code'  => 'ninja-xpress-reguler',
            'courier'   => 'Ninja Xpress',
            'service'   => 'Reguler'
        ],
        [
            'driver' => $this->driver,
            'code'  => 'ninja-xpress-express',
            'courier'   => 'Ninja Xpress',
            'service'   => 'Express'
        ],
        [
            'driver' => $this->driver,
            'code'  => 'ninja-xpress-cargo',
            'courier'   => 'Ninja Xpress',
            'service'   => 'Cargo'
        ],
        [
            'driver' => $this->driver,
            'code'  => 'ninja-xpress-instant',
            'courier'   => 'Ninja Xpress',
            'service'   => 'Instant'
        ],
        [
            'driver' => $this->driver,
            'code'  => 'grab-instant',
            'courier'   => 'Grab',
            'service'   => 'Instant'
        ]
        ], DataCollection::class);
    }

    public function getRate(
        RegionData $origin,
        RegionData $destination,
        CartData $cart,
        ShippingServiceData $shipping_service
    ) : ?ShippingData
    {
        $response = Http::withBasicAuth(
            config('shipping.apikurir.username'),
            config('shipping.apikurir.password')           
        )->post('https://sandbox.apikurir.id/shipments/v1/open-api/rates',[
            'isUseInsurance' => true,
            'isPickup'  => true,
            'isCod'    => false,
            'dimensions' => [10, 10, 10],
            'weight'    => $cart->total_weight,
            'packagePrice'  => $cart->total,
            'origin' => [
                'postalCode' => $origin->postal_code,
            ],
            'destination' => [
                'postalCode' => $destination->postal_code
            ],
            'logistics' => [$shipping_service->courier],
            'services' => [$shipping_service->service]
        ]);
        $data = $response->collect('data')->flatten(1)->values()->first();
        if (empty($data)){
            return null;
        }

        $est = data_get($data, 'minDuration') . ' - ' . data_get($data, 'maxDuration') . ' ' . data_get($data, 'durationType');
        return new ShippingData(
            $this->driver,
            $shipping_service->courier,
            $shipping_service->service,
            $est,
            data_get($data, 'price'),
            data_get($data, 'weight'),
            $origin,
            $destination,
            data_get($data, 'logoUrl')
        );
    }
}