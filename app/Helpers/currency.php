<?php

/**
 * Return the currency symbol for a given ISO 4217 code.
 */
function currencySymbol(string $code): string
{
    return match(strtoupper($code)) {
        'USD' => '$',
        'EUR' => '€',
        'RUB' => '₽',
        'KZT' => '₸',
        'GBP' => '£',
        'CNY' => '¥',
        'AED' => 'د.إ',
        'TRY' => '₺',
        'UZS' => 'сум',
        'JPY' => '¥',
        'KRW' => '₩',
        'INR' => '₹',
        'BRL' => 'R$',
        'CAD' => 'CA$',
        'AUD' => 'A$',
        'CHF' => 'Fr',
        'SEK' => 'kr',
        'NOK' => 'kr',
        'DKK' => 'kr',
        'PLN' => 'zł',
        default => $code,
    };
}

/**
 * Format a monetary amount with the user's currency symbol.
 */
function formatMoney(float|string $amount, string $currency): string
{
    $symbol = currencySymbol($currency);
    $formatted = number_format((float) $amount, 2, '.', ',');
    return $symbol . $formatted;
}

/**
 * All supported ISO currencies for the profile dropdown.
 */
function allCurrencies(): array
{
    return [
        'AED' => 'AED – UAE Dirham',
        'AUD' => 'AUD – Australian Dollar',
        'BRL' => 'BRL – Brazilian Real',
        'CAD' => 'CAD – Canadian Dollar',
        'CHF' => 'CHF – Swiss Franc',
        'CNY' => 'CNY – Chinese Yuan',
        'DKK' => 'DKK – Danish Krone',
        'EUR' => 'EUR – Euro',
        'GBP' => 'GBP – British Pound',
        'INR' => 'INR – Indian Rupee',
        'JPY' => 'JPY – Japanese Yen',
        'KRW' => 'KRW – South Korean Won',
        'KZT' => 'KZT – Kazakhstani Tenge',
        'NOK' => 'NOK – Norwegian Krone',
        'PLN' => 'PLN – Polish Zloty',
        'RUB' => 'RUB – Russian Ruble',
        'SEK' => 'SEK – Swedish Krona',
        'TRY' => 'TRY – Turkish Lira',
        'USD' => 'USD – US Dollar',
        'UZS' => 'UZS – Uzbekistani Som',
    ];
}
