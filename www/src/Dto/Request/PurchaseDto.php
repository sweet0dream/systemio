<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class PurchaseDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        public int $product,
        #[Assert\NotBlank]
        #[Assert\Regex(
            pattern: '/^(DE|IT|GR|FR)[A-Z0-9]+$/',
            message: 'Invalid tax number format'
        )]
        public string $taxNumber,
        #[Assert\Type('string')]
        public string $couponCode,
        #[Assert\NotBlank]
        #[Assert\Choice(['paypal', 'stripe'])]
        public string $paymentProcessor,
    ) {
    }
}
