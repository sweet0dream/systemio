<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\Coupon;
use App\Enum\TaxCountryEnum;
use App\Repository\CouponRepository;
use InvalidArgumentException;

readonly class PriceCalculate
{
    public function __construct(
        private CouponRepository $couponRepository
    ) {
    }

    public function calculate(
        Product $product,
        string $taxNumber,
        ?string $couponCode = null
    ): float {
        $price = $product->getPrice();

        // Apply coupon discount if provided
        if ($couponCode) {
            $coupon = $this->couponRepository->findOneBy(['code' => $couponCode]);
            if (!$coupon) {
                throw new InvalidArgumentException('Invalid coupon code');
            }

            $price = $this->applyCoupon($price, $coupon);
        }

        // Apply tax
        $taxRate = $this->getTaxRate($taxNumber);
        $price = $price * (1 + $taxRate / 100);

        return round($price, 2);
    }

    private function applyCoupon(float $price, Coupon $coupon): float
    {
        if ($coupon->getType() === 'percentage') {
            return $price * (1 - $coupon->getValue() / 100);
        } elseif ($coupon->getType() === 'fixed') {
            return max(0, $price - $coupon->getValue());
        }

        throw new InvalidArgumentException('Invalid coupon type');
    }

    private function getTaxRate(string $taxNumber): float
    {
        $countryCode = substr($taxNumber, 0, 2);

        $taxRates = array_column(TaxCountryEnum::cases(), 'value', 'name');

        if (!isset($taxRates[$countryCode])) {
            throw new InvalidArgumentException('Invalid tax number country code');
        }

        return $taxRates[$countryCode];
    }
}
