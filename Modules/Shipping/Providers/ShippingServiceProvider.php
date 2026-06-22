<?php

namespace Modules\Shipping\Providers;

use Modules\Shipping\Method;
use Illuminate\Support\ServiceProvider;
use Modules\Shipping\Facades\ShippingMethod;

class ShippingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerFreeShipping();
        $this->registerLocalPickup();
        $this->registerFlatRate();

        // Регистрируем методы Новой Почты
        $this->registerNovaPoshtaBranch();
        $this->registerNovaPoshtaAddress();
        $this->registerNovaPoshtaPostomat();
    }


    private function registerFreeShipping()
    {
        if (!setting('free_shipping_enabled')) {
            return;
        }

        ShippingMethod::register('free_shipping', function () {
            return new Method('free_shipping', setting('free_shipping_label'), 0);
        });
    }


    private function registerLocalPickup()
    {
        if (!setting('local_pickup_enabled')) {
            return;
        }

        ShippingMethod::register('local_pickup', function () {
            return new Method('local_pickup', setting('local_pickup_label'), setting('local_pickup_cost') ?? 0);
        });
    }


    private function registerFlatRate()
    {
        if (!setting('flat_rate_enabled')) {
            return;
        }

        ShippingMethod::register('flat_rate', function () {
            return new Method('flat_rate', setting('flat_rate_label'), setting('flat_rate_cost') ?? 0);
        });
    }

    private function registerNovaPoshtaBranch()
    {
        if (!setting('novaPoshta_enabled') || !setting('nova_poshta_branch_enabled')) {
            return;
        }

        ShippingMethod::register('nova_poshta_branch', function () {
            return new Method('nova_poshta_branch', setting('nova_poshta_branch_label', 'Новая Почта (в отделение)'), setting('nova_poshta_branch_cost') ?? 0);
        });
    }

    private function registerNovaPoshtaAddress()
    {
        if (!setting('novaPoshta_enabled') || !setting('nova_poshta_address_enabled')) {
            return;
        }

        ShippingMethod::register('nova_poshta_address', function () {
            return new Method('nova_poshta_address', setting('nova_poshta_address_label', 'Новая Почта (курьером)'), setting('nova_poshta_address_cost') ?? 0);
        });
    }

    private function registerNovaPoshtaPostomat()
    {
        if (!setting('novaPoshta_enabled') || !setting('nova_poshta_postomat_enabled')) {
            return;
        }

        ShippingMethod::register('nova_poshta_postomat', function () {
            return new Method('nova_poshta_postomat', setting('nova_poshta_postomat_label', 'Новая Почта (в почтомат)'), setting('nova_poshta_postomat_cost') ?? 0);
        });
    }
}
