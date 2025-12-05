<?php

namespace App\Tests;

use App\Services\Calculator;
use App\Services\PromotionCalculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    public function testSomething(): void
    {
        $calc = new Calculator;
        $result = $calc->add(1, 2);

        $this->assertTrue(true);
        $this->assertEquals(3, $result);
    }

    public function testPromotionPrice(): void
    {
        $calc = $this->getMockBuilder(PromotionCalculator::class)
            ->setMethods(['getPromotionPercentage'])
            ->getMock();

        $calc->expects($this->any())
            ->method('getPromotionPercentage')
            ->willReturn(20);

        $result = $calc->calculatePriceAfterPromotion(1, 9);
        $this->assertEquals(8, $result);

        $result = $calc->calculatePriceAfterPromotion(10, 20, 45, 25);
        $this->assertEquals(80, $result);
    }
}
