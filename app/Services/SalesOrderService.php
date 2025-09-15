<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SalesOrder;
use App\Data\SalesOrderData;
use App\Events\ShippingReceiptNumberUpdatedEvent;

class SalesOrderService
{
    public function updateShippingReceipt(SalesOrderData $sales_order, string $number) : SalesOrderData
    {
        $query = SalesOrder::query()->where('trx_id', $sales_order->trx_id)->first();

        $query->update([
            'shipping_receipt_number' => $number
        ]);

        $data = SalesOrderData::fromModel(
            $query->refresh()
        );

        event(new ShippingReceiptNumberUpdatedEvent($data));
        
        return $data;
    }

    public function updateShippingPayload(SalesOrderData $sales_order, array $payload) : SalesOrderData
    {
        SalesOrder::where('trx_id', $sales_order->trx_id)->update([
            'payment_payload' => array_merge($sales_order->payment->payload, $payload)
        ]);

        return SalesOrderData::from(
            SalesOrder::where('trx_id', $sales_order->trx_id)->first()
        );
    }
}