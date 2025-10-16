<?php

namespace App\Factory;

use InvalidArgumentException;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class PaymentProcessorFactory
{
    public function create(string $type): StripePaymentProcessor|PaypalPaymentProcessor
    {
        return match ($type) {
            'paypal' => new PaypalPaymentProcessor(),
            'stripe' => new StripePaymentProcessor(),
            default => throw new InvalidArgumentException('Invalid payment type'),
        };
    }
}
