<?php

namespace Modules\Order\Providers;

use Modules\Order\Events\OrderStatusChanged;
use Modules\Order\Listeners\SendOrderStatusChangedSms;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Order\Listeners\UpdateCustomerGroupByOrdersTotal;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        OrderStatusChanged::class => [
            SendOrderStatusChangedSms::class,
            UpdateCustomerGroupByOrdersTotal::class,
        ],
    ];
}
