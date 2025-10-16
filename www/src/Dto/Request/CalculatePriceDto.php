<?php

namespace App\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CalculatePriceDto
{
    public function __construct(
        #[Assert\NotBlank]
        public ?int $product,
        #[Assert\NotBlank]
        #[Assert\Regex(
            pattern: '/^(DE|IT|GR|FR)[A-Z0-9]+$/',
            message: 'Invalid tax number format'
        )]
        public string $taxNumber,
        public ?string $couponCode = null,
    ) {
    }
}
