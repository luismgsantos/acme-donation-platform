<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(
            \App\Contracts\PaymentGatewayInterface::class,
            \App\PaymentGateways\DummyPaymentGateway::class
        );
    }
}
