<?php

namespace App\Livewire;

use App\Models\Region;
use Livewire\Component;
use App\Data\RegionData;
use App\Data\ShippingData;
use Illuminate\Support\Number;
use Illuminate\Support\Collection;
use App\Services\RegionQueryService;
use Illuminate\Support\Facades\Gate;
use App\Contract\CartServiceInterface;
use App\Rules\ValidShippingHash;
use Spatie\LaravelData\DataCollection;
use App\Services\ShippingMethodService;

class Checkout extends Component
{
    public array $data = [
        'full_name' => null,
        'email' => null,
        'phone' => null,
        'address_line' => null,
        'destination_region_code' => null,
        'shipping_hash' => null
    ];

    public array $region_selector = [
        'keyword' => null,
        'region_selected' => null
    ];

    public array $shipping_selector = [
        'shipping_method' => null
    ];

    public array $summeries = [
        'sub_total' => 0,
        'sub_total_formatted' => '-',
        'shipping_total' => 0,
        'shipping_total_formatted' => '-',
        'grand_total' => 0,
        'grand_total_formatted' => '-'
    ];
    public function mount()
    {
        if (!Gate::inspect('is_stock_available')->allowed()) {
            return redirect()->route('cart');
        }

        $this->calculateTotal();
    }

    public function rules()
    {
        return [
            'data.full_name' => ['required', 'min:3', 'max:250'],
            'data.email' => ['required', 'min:3', 'max:250', 'email'],
            'data.phone' => ['required', 'min:7', 'max:13', ],
            'data.shipping_line' => ['required', 'min:3', 'max:250'],
            'data.destination_region_code' => ['required','exists:regions,code'],
            'data.shipping_hash' => ['required', new ValidShippingHash()]
        ];
    }

    public function calculateTotal()
    {
        data_set($this->summeries, 'sub_total', $this->cart->total);
        data_set($this->summeries, 'sub_total_formatted', $this->cart->total_formatted);

        $shipping_cost = $this->shippingMethod?->cost ?? 0;
        data_set($this->summeries, 'shipping_total', $shipping_cost);
        data_set($this->summeries, 'shipping_total_formatted', Number::currency($shipping_cost));

        $grand_total = $this->cart->total + $shipping_cost;
        data_set($this->summeries, 'grand_total', $grand_total);
        data_set($this->summeries, 'grand_total_formatted', Number::currency($grand_total));
    }

    public function getCartProperty(CartServiceInterface $cart)
    {
        return $cart->all();
    }

    public function getRegionsProperty(RegionQueryService $query_service) : DataCollection
    {

        if (!data_get($this->region_selector, 'keyword')) {
            $data = [];
            return new DataCollection(RegionData::class, []);
        }

        return $query_service->SearchRegionByName(
            data_get($this->region_selector, 'keyword')
        );
    }

    public function getRegionProperty(RegionQueryService $query_service) : ?RegionData
    {
        $region_selected = data_get($this->region_selector, 'region_selected');
        if (!$region_selected){
            return null;
        }
        return $query_service->searchRegionByCode($region_selected);
    }

    public function updatedRegionSelectorRegionSelected($value)
    {
        data_set($this->data, 'destination_region_code', $value);
    }

    /** @return DataCollection<ShippingData> */
    public function getShippingMethodsProperty(
        RegionQueryService $region_query,
        ShippingMethodService $shipping_service
    ) : DataCollection|Collection{
        if(!data_get($this->data, 'destination_region_code')) {
            return new DataCollection(ShippingData::class, []);
        }

        $origin_code = config('shipping.shipping_origin_code');

        return $shipping_service->getShippingMethods(
            $region_query->searchRegionByCode($origin_code),
            $region_query->searchRegionByCode(data_get($this->data, 'destination_region_code')),
            $this->cart
        )->toCollection()->groupBy('service');
    }

    public function getShippingMethodProperty(
        ShippingMethodService $shipping_service
    ) : ?ShippingData 
    {
        if (
            empty(data_get($this->data, 'shipping_hash')) ||
            empty(data_get($this->data, 'destination_region_code'))
        ) {
            return null;
        }
        $data = $shipping_service->getShippingMethod(
            data_get($this->data, 'shipping_hash')
        );

        if ($data == null) {
            $this->addError('shipping_hash', "Shipping Cost Missing");
            redirect()->route('checkout');
        }
        return $data;
    }
    

    public function updatedShippingSelectorShippingMethod($value)
    {
        data_set($this->data, 'shipping_hash', $value);
        $this->calculateTotal();
    }
    public function placeAnOrder()
    {
        $this->validate();
        dd($this->data);
    }
    public function render()
    {
        return view('livewire.checkout', [
            'cart' => $this->cart
        ]);
    }
}
