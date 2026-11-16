<?php

namespace App\Services;

use Carbon\CarbonInterface;
use InvalidArgumentException;

class ProductPricing
{
    /**
     * @param  iterable<array{discount_percent: string}>  $promotions
     * @return array{unit_price_cents: int, regular_price_cents: int, wholesale_price_cents: ?int, wholesale_min_qty: ?int, promotion_applied: bool}
     */
    public function quote(
        int $regularPriceCents,
        ?int $wholesalePriceCents,
        ?int $wholesaleMinQty,
        int $quantity,
        iterable $promotions,
    ): array {
        if ($regularPriceCents < 0 || ($wholesalePriceCents !== null && $wholesalePriceCents < 0)) {
            throw new InvalidArgumentException('Prices cannot be negative.');
        }

        $eligiblePrices = [$regularPriceCents];
        $promotionPrices = [];

        if ($wholesalePriceCents !== null && $wholesaleMinQty !== null && $quantity >= $wholesaleMinQty) {
            $eligiblePrices[] = $wholesalePriceCents;
        }

        $promotionPriceCents = $this->bestPromotionPrice($regularPriceCents, $promotions);
        if ($promotionPriceCents !== null) {
            $promotionPrices[] = $promotionPriceCents;
        }
        array_push($eligiblePrices, ...$promotionPrices);

        $unitPriceCents = min($eligiblePrices);

        return [
            'unit_price_cents' => $unitPriceCents,
            'regular_price_cents' => $regularPriceCents,
            'wholesale_price_cents' => $wholesalePriceCents,
            'wholesale_min_qty' => $wholesaleMinQty,
            'promotion_applied' => $promotionPrices !== [] && min($promotionPrices) === $unitPriceCents && $unitPriceCents < $regularPriceCents,
        ];
    }

    /** @param iterable<array{discount_percent: string}> $promotions */
    public function bestPromotionPrice(int $regularPriceCents, iterable $promotions): ?int
    {
        $prices = [];

        foreach ($promotions as $promotion) {
            $prices[] = $this->promotionalPrice($regularPriceCents, $promotion['discount_percent']);
        }

        return $prices === [] ? null : min($prices);
    }

    /** @return array<int, array{discount_percent: string}> */
    public function activePromotions(iterable $promotions, CarbonInterface $date): array
    {
        $today = $date->toDateString();
        $active = [];

        foreach ($promotions as $promotion) {
            $startsOn = $promotion['starts_on'] instanceof CarbonInterface
                ? $promotion['starts_on']->toDateString()
                : (string) $promotion['starts_on'];
            $endsOn = $promotion['ends_on'] instanceof CarbonInterface
                ? $promotion['ends_on']->toDateString()
                : (string) $promotion['ends_on'];

            if ($startsOn <= $today && $endsOn >= $today) {
                $active[] = ['discount_percent' => (string) $promotion['discount_percent']];
            }
        }

        return $active;
    }

    private function promotionalPrice(int $regularPriceCents, string $discountPercent): int
    {
        if ($regularPriceCents < 0) {
            throw new InvalidArgumentException('Prices cannot be negative.');
        }

        $scaledPercent = $this->scalePercent($discountPercent);
        $denominator = 1_000_000;
        if ($scaledPercent < 0 || $scaledPercent > $denominator) {
            throw new InvalidArgumentException('Promotion discounts must be between 0 and 100 percent.');
        }

        $numerator = $regularPriceCents * ($denominator - $scaledPercent);

        return intdiv($numerator + intdiv($denominator, 2), $denominator);
    }

    private function scalePercent(string $percent): int
    {
        [$whole, $fraction] = array_pad(explode('.', $percent, 2), 2, '');
        $fraction = substr(str_pad($fraction, 4, '0'), 0, 4);

        return ((int) $whole * 10_000) + (int) $fraction;
    }
}
