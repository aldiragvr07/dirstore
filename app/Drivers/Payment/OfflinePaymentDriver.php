<?php
declare(strict_types=1);

namespace App\Drivers\Payment;

use App\Data\PaymentData;
use Spatie\LaravelData\DataCollection;
use App\Contract\PaymentDriverInterface;

class OfflinePaymentDriver implements PaymentDriverInterface
{
    public readonly string $driver;

    public function __construct()
    {
        $this->driver = 'offline';
    }
    public function getMethods() : DataCollection
    {
        return PaymentData::collect([
            PaymentData::from([
                'driver' => $this->driver,
                'method' => 'bca-bank-transfer',
                'label' => 'Bank Transfer - BCA',
                'payload' => [
                    'account_number' => '123123123',
                    'account_holder_name' => 'Aldira Givari'
                ]
            ])
                ], DataCollection::class
            );
    }

    public function process($sales_order){

    }

    public function shouldShowPayNowButton($sales_order) : bool
    {
        return false;
    }
    public function getRedirectUrl($sales_order) : ?string
    {
        return null;
    }
}