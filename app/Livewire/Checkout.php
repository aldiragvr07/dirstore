<?php

namespace App\Livewire;

use App\Contract\CartServiceInterface;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Number;

class Checkout extends Component
{
    public array $data = [
        'full_name' => null,
        'email' => null,
        'phone' => null,
        'address_line' => null
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
            'data.phone' => ['required', 'min:7', 'max:250', ],
            'data.shipping_line' => ['required', 'min:3', 'max:250']
        ];
    }

    public function calculateTotal()
    {
        data_set($this->summeries, 'sub_total', $this->cart->total);
        data_set($this->summeries, 'sub_total_formatted', $this->cart->total_formatted);

        $shipping_cost = 0;
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
