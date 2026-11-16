<?php

namespace App\Support;

use InvalidArgumentException;
use JsonException;
use RuntimeException;

final class SourceData
{
    /** @return array<mixed> */
    public static function decodeFile(string $path): array
    {
        if (! is_file($path)) {
            throw new RuntimeException("Source data file not found: {$path}");
        }

        return self::decode(file_get_contents($path));
    }

    /** @return array<mixed> */
    public static function decode(string $json): array
    {
        try {
            json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            $decoded = json_decode(self::quoteNumberTokens($json), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidArgumentException('Invalid source JSON: '.$exception->getMessage(), previous: $exception);
        }

        if (! is_array($decoded)) {
            throw new InvalidArgumentException('Source JSON root must be an object or array.');
        }

        return $decoded;
    }

    public static function integer(string $value, string $field): int
    {
        if (! preg_match('/\A-?(?:0|[1-9][0-9]*)\z/', $value)) {
            throw new InvalidArgumentException("{$field} must be an integer.");
        }

        $integer = filter_var($value, FILTER_VALIDATE_INT);

        if ($integer === false) {
            throw new InvalidArgumentException("{$field} is outside the supported integer range.");
        }

        return $integer;
    }

    public static function moneyToCents(string $value, string $field): int
    {
        return self::decimalToScaledInteger($value, 2, $field);
    }

    public static function percentageToDatabaseDecimal(string $value, string $field): string
    {
        $scaled = self::decimalToScaledInteger($value, 4, $field);
        $whole = intdiv($scaled, 10000);
        $fraction = str_pad((string) ($scaled % 10000), 4, '0', STR_PAD_LEFT);

        return "{$whole}.{$fraction}";
    }

    private static function decimalToScaledInteger(string $value, int $targetScale, string $field): int
    {
        if (! preg_match('/\A(\+?)(0|[1-9][0-9]*)(?:\.([0-9]+))?(?:[eE]([+-]?[0-9]+))?\z/', $value, $matches)) {
            throw new InvalidArgumentException("{$field} must be a non-negative decimal number.");
        }

        $fraction = $matches[3] ?? '';
        $exponentText = $matches[4] ?? '0';
        $exponent = filter_var($exponentText, FILTER_VALIDATE_INT);

        if ($exponent === false || abs($exponent) > 100) {
            throw new InvalidArgumentException("{$field} has an unsupported exponent.");
        }

        $digits = ltrim($matches[2].$fraction, '0');
        $digits = $digits === '' ? '0' : $digits;
        $shift = $targetScale - strlen($fraction) + $exponent;

        if ($shift >= 0) {
            if ($shift > 19) {
                throw new InvalidArgumentException("{$field} is outside the supported range.");
            }

            $scaledDigits = $digits.str_repeat('0', $shift);
        } else {
            $discardCount = -$shift;
            $paddedDigits = str_pad($digits, $discardCount + 1, '0', STR_PAD_LEFT);
            $discardedDigits = substr($paddedDigits, -$discardCount);

            if (trim($discardedDigits, '0') !== '') {
                throw new InvalidArgumentException("{$field} has precision smaller than one cent or one ten-thousandth.");
            }

            $scaledDigits = substr($paddedDigits, 0, -$discardCount);
        }

        $scaledDigits = ltrim($scaledDigits, '0');
        $scaledDigits = $scaledDigits === '' ? '0' : $scaledDigits;
        $maxInteger = (string) PHP_INT_MAX;

        if (strlen($scaledDigits) > strlen($maxInteger) || (strlen($scaledDigits) === strlen($maxInteger) && strcmp($scaledDigits, $maxInteger) > 0)) {
            throw new InvalidArgumentException("{$field} is outside the supported integer range.");
        }

        return (int) $scaledDigits;
    }

    private static function quoteNumberTokens(string $json): string
    {
        $result = '';
        $inString = false;
        $length = strlen($json);

        for ($index = 0; $index < $length; $index++) {
            $character = $json[$index];

            if ($inString) {
                $result .= $character;

                if ($character === '\\' && $index + 1 < $length) {
                    $result .= $json[++$index];
                } elseif ($character === '"') {
                    $inString = false;
                }

                continue;
            }

            if ($character === '"') {
                $inString = true;
                $result .= $character;

                continue;
            }

            if ($character === '-' || ctype_digit($character)) {
                preg_match('/\G-?(?:0|[1-9][0-9]*)(?:\.[0-9]+)?(?:[eE][+-]?[0-9]+)?/', $json, $matches, 0, $index);

                if ($matches !== []) {
                    $result .= '"'.$matches[0].'"';
                    $index += strlen($matches[0]) - 1;

                    continue;
                }
            }

            $result .= $character;
        }

        return $result;
    }
}
