<?php

namespace App\DataFixtures;

use App\Entity\Coupon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CouponFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $coupons = [
            ['code' => 'P10', 'type' => 'percentage', 'value' => 10],
            ['code' => 'P100', 'type' => 'percentage', 'value' => 100],
            ['code' => 'D15', 'type' => 'fixed', 'value' => 15],
            ['code' => 'D5', 'type' => 'fixed', 'value' => 5],
        ];

        foreach ($coupons as $couponData) {
            $coupon = new Coupon();
            $coupon->setCode($couponData['code']);
            $coupon->setType($couponData['type']);
            $coupon->setValue($couponData['value']);
            $manager->persist($coupon);
        }

        $manager->flush();
    }
}
