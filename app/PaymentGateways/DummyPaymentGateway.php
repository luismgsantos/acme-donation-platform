<?php

namespace App\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;

class DummyPaymentGateway implements PaymentGatewayInterface
{
    public function charge(float $amount): bool
    {
        return true;
    }
}
