<?php

namespace Modules\Payment\Providers;

use Modules\Payment\Gateways\COD;
use Modules\Payment\Facades\Gateway;
use Modules\Payment\Gateways\LiqPay;
use Modules\Payment\Gateways\Monobank;
use Modules\Payment\Gateways\PayPal;
use Illuminate\Support\ServiceProvider;
use Modules\Payment\Gateways\BankTransfer;
use Modules\Payment\Gateways\CheckPayment;

class PaymentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerPayPalExpress();
        $this->registerCashOnDelivery();
        $this->registerBankTransfer();
        $this->registerCheckPayment();
        $this->registerMonobank();
        $this->registerLiqPay();
    }

    public function register()
    {
    }

    private function registerPayPalExpress()
    {
        if ($this->enabled('paypal')) {
            Gateway::register('paypal', new PayPal());
        }
    }

    private function registerCashOnDelivery()
    {
        if ($this->enabled('cod')) {
            Gateway::register('cod', new COD());
        }
    }

    private function registerBankTransfer()
    {
        if ($this->enabled('bank_transfer')) {
            Gateway::register('bank_transfer', new BankTransfer());
        }
    }

    private function registerCheckPayment()
    {
        if ($this->enabled('check_payment')) {
            Gateway::register('check_payment', new CheckPayment());
        }
    }

    private function registerMonobank()
    {
        if ($this->enabled('monobank')) {
            Gateway::register('monobank', new Monobank());
        }
    }

    private function registerLiqPay()
    {
        if ($this->enabled('liqpay')) {
            Gateway::register('liqpay', new LiqPay());
        }
    }

    private function enabled($paymentMethod)
    {
        if (app('inAdminPanel')) {
            return true;
        }

        return (bool) setting("{$paymentMethod}_enabled");
    }
}
