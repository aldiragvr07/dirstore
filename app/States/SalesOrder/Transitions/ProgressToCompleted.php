<?php

declare(strict_types=1);

namespace App\States\SalesOrder\Transitions;

use App\Models\SalesOrder;
use App\Data\SalesOrderData;
use App\Events\SalesOrderCompletedEvent;
use App\States\SalesOrder\Cancel;
use Spatie\ModelStates\Transition;
use App\States\SalesOrder\Completed;
use Illuminate\Validation\Rules\Can;

class ProgressToCompleted extends Transition
{
    public function __construct(
        private SalesOrder $sales_order
    )
    {
        
    }

    public function handle()
    {
        $this->sales_order->update([
            'status' => Completed::class
        ]);

        event(new SalesOrderCompletedEvent(
            SalesOrderData::fromModel($this->sales_order)
        ));

        return $this->sales_order;
    }
}