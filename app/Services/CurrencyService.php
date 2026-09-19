<?php

namespace App\Services;

use InvalidArgumentException;

class CurrencyService
{
    /**
     * Convert an amount from one currency to the base currency
     */
    public function convertToBase(float $amount, string $currency): float
    {
        $baseCurrency = config('currency.base');

        $rates = config('currency.rates');

        if (!isset($rates[$currency])) {
            throw new InvalidArgumentException(
                "Unsupported currency: {$currency}"
            );
        }

        $rate = $rates[$currency];

        return round($amount * $rate, 2);
    }

    /**
     * Get the base currency
     */
    public function baseCurrency(): string
    {
        return config('currency.base');
    }

    /**
     * Get the exchange rate for a currency
     */
    public function rate(string $currency): float
    {
        $rates = config('currency.rates');

        if (!isset($rates[$currency])) {
            throw new InvalidArgumentException(
                "Unsupported currency: {$currency}"
            );
        }

        return (float) $rates[$currency];
    }
}