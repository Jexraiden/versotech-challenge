<?php

namespace Tests\Unit;

use App\Services\ProductPricing;
use Carbon\CarbonImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ProductPricingTest extends TestCase
{
    public function test_wholesale_price_becomes_eligible_at_the_minimum_quantity(): void
    {
        $pricing = new ProductPricing;
        $below = $pricing->quote(1000, 850, 10, 9, []);
        $atMinimum = $pricing->quote(1000, 850, 10, 10, []);

        $this->assertSame(1000, $below['unit_price_cents']);
        $this->assertSame(850, $atMinimum['unit_price_cents']);
    }

    public function test_promotion_competes_with_wholesale_and_rounds_half_up_from_regular(): void
    {
        $pricing = new ProductPricing;
        $quote = $pricing->quote(1001, 900, 10, 10, [['discount_percent' => '15.0000']]);

        $this->assertSame(851, $quote['unit_price_cents']);
        $this->assertTrue($quote['promotion_applied']);
    }

    public function test_full_discount_results_in_zero_cents_and_never_a_negative_price(): void
    {
        $pricing = new ProductPricing;
        $quote = $pricing->quote(1000, null, null, 1, [['discount_percent' => '100.0000']]);

        $this->assertSame(0, $quote['unit_price_cents']);
        $this->assertGreaterThanOrEqual(0, $quote['unit_price_cents']);
    }

    public function test_negative_prices_are_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ProductPricing)->quote(-1, null, null, 1, []);
    }

    public function test_discount_above_one_hundred_percent_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new ProductPricing)->quote(1000, null, null, 1, [['discount_percent' => '100.0001']]);
    }

    public function test_lowest_of_multiple_active_promotions_is_used(): void
    {
        $pricing = new ProductPricing;
        $quote = $pricing->quote(1001, null, null, 1, [
            ['discount_percent' => '10.0000'],
            ['discount_percent' => '25.0000'],
            ['discount_percent' => '5.0000'],
        ]);

        $this->assertSame(751, $quote['unit_price_cents']);
        $this->assertTrue($quote['promotion_applied']);
    }

    public function test_promotion_does_not_accumulate_with_wholesale_price(): void
    {
        $pricing = new ProductPricing;
        $quote = $pricing->quote(1000, 700, 10, 10, [['discount_percent' => '20.0000']]);

        $this->assertSame(700, $quote['unit_price_cents']);
        $this->assertFalse($quote['promotion_applied']);
    }

    public function test_date_range_is_inclusive_and_excludes_future_or_expired_promotions(): void
    {
        $pricing = new ProductPricing;
        $today = CarbonImmutable::parse('2026-11-16', 'America/Sao_Paulo');
        $active = $pricing->activePromotions([
            ['discount_percent' => '10.0000', 'starts_on' => '2026-11-16', 'ends_on' => '2026-11-16'],
            ['discount_percent' => '20.0000', 'starts_on' => '2026-11-17', 'ends_on' => '2026-11-20'],
            ['discount_percent' => '30.0000', 'starts_on' => '2026-11-10', 'ends_on' => '2026-11-15'],
        ], $today);

        $this->assertSame([['discount_percent' => '10.0000']], $active);
    }
}
