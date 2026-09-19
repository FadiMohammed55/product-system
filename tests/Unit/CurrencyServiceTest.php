<?php

namespace Tests\Unit;

use App\Services\CurrencyService;
use InvalidArgumentException;
use Tests\TestCase;

class CurrencyServiceTest extends TestCase
{
    public function test_usd_is_converted_to_usd_without_change(): void
    {
        $service = new CurrencyService();

        $result = $service->convertToBase(100, 'USD');

        $this->assertSame(100.00, $result);
    }

    public function test_eur_is_converted_to_usd(): void
    {
        $service = new CurrencyService();

        $result = $service->convertToBase(100, 'EUR');

        $this->assertSame(115.00, $result);
    }

    public function test_ils_is_converted_to_usd(): void
    {
        $service = new CurrencyService();

        $result = $service->convertToBase(100, 'ILS');

        $this->assertSame(33.00, $result);
    }

    public function test_unsupported_currency_throws_exception(): void
    {
        $service = new CurrencyService();

        $this->expectException(InvalidArgumentException::class);

        $service->convertToBase(100, 'GBP');
    }

    public function test_base_currency_is_usd(): void
    {
        $service = new CurrencyService();

        $this->assertSame('USD', $service->baseCurrency());
    }

    public function test_eur_exchange_rate_is_correct(): void
    {
        $service = new CurrencyService();

        $this->assertSame(1.15, $service->rate('EUR'));
    }
}