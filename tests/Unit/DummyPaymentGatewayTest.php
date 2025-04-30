<?php

use App\Contracts\PaymentGatewayInterface;
use App\PaymentGateways\DummyPaymentGateway;

describe('DummyPaymentGateway', function () {
    it('can be instantiated', function () {
        $gateway = new DummyPaymentGateway;

        expect($gateway)->toBeInstanceOf(PaymentGatewayInterface::class);
    });

    it('successfully charges a given_amount', function () {
        $gateway = new DummyPaymentGateway;

        $response = $gateway->charge(100);

        expect($response)->toBeTrue();
    });
});
