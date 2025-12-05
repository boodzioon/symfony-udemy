<?php

namespace App\Services;

class PromotionCalculator
{
    public function calculatePriceAfterPromotion(float ... $prices) : float
    {
        $start = 0;

        foreach ($prices as $price) {
            $start += $price;
        }

        $start -= $start * $this->getPromotionPercentage() / 100;

        return $start;
    }

    public function getPromotionPercentage()
    {
        return (int) \file_get_contents('var/promotion_percentage.txt');
    }
}