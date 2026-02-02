<?php

declare(strict_types=1);

namespace Arara\Support;

final class PhoneNumber
{
    private const COUNTRY_CODE = '55';
    private const MOBILE_PREFIX = '9';

    /**
     * Formata um número de telefone brasileiro para o padrão E.164.
     *
     * @example PhoneNumber::format('11999999999') // +5511999999999
     * @example PhoneNumber::format('(11) 99999-9999') // +5511999999999
     * @example PhoneNumber::format('+5511999999999') // +5511999999999
     */
    public static function format(string $phone): string
    {
        $digits = self::extractDigits($phone);

        if (str_starts_with($digits, self::COUNTRY_CODE) && strlen($digits) >= 12) {
            return '+' . $digits;
        }

        return '+' . self::COUNTRY_CODE . $digits;
    }

    /**
     * Valida se um número de telefone brasileiro é válido.
     *
     * @example PhoneNumber::isValid('11999999999') // true
     * @example PhoneNumber::isValid('1199999999') // false (faltando dígito)
     */
    public static function isValid(string $phone): bool
    {
        $digits = self::extractDigits($phone);

        if (str_starts_with($digits, self::COUNTRY_CODE)) {
            $digits = substr($digits, 2);
        }

        // Telefone brasileiro: DDD (2 dígitos) + número (8-9 dígitos)
        if (strlen($digits) < 10 || strlen($digits) > 11) {
            return false;
        }

        // Valida DDD (11-99)
        $ddd = (int) substr($digits, 0, 2);
        if ($ddd < 11 || $ddd > 99) {
            return false;
        }

        // Se tem 10 dígitos e começa com 9 após DDD, é inválido
        // (celulares brasileiros têm 11 dígitos, começando com 9)
        if (strlen($digits) === 10 && $digits[2] === self::MOBILE_PREFIX) {
            return false;
        }

        return true;
    }

    /**
     * Verifica se o número é um celular.
     *
     * @example PhoneNumber::isMobile('11999999999') // true
     * @example PhoneNumber::isMobile('1133334444') // false
     */
    public static function isMobile(string $phone): bool
    {
        if (! self::isValid($phone)) {
            return false;
        }

        $digits = self::extractDigits($phone);

        if (str_starts_with($digits, self::COUNTRY_CODE)) {
            $digits = substr($digits, 2);
        }

        // Celular tem 11 dígitos e começa com 9 após o DDD
        if (strlen($digits) !== 11) {
            return false;
        }

        return $digits[2] === self::MOBILE_PREFIX;
    }

    /**
     * Verifica se o número é um telefone fixo.
     *
     * @example PhoneNumber::isLandline('1133334444') // true
     * @example PhoneNumber::isLandline('11999999999') // false
     */
    public static function isLandline(string $phone): bool
    {
        if (! self::isValid($phone)) {
            return false;
        }

        $digits = self::extractDigits($phone);

        if (str_starts_with($digits, self::COUNTRY_CODE)) {
            $digits = substr($digits, 2);
        }

        // Telefone fixo tem 10 dígitos
        return strlen($digits) === 10;
    }

    /**
     * Extrai apenas os dígitos de uma string.
     */
    public static function extractDigits(string $phone): string
    {
        return preg_replace('/\D/', '', $phone) ?? '';
    }

    /**
     * Formata um número para exibição no formato brasileiro.
     *
     * @example PhoneNumber::formatForDisplay('5511999999999') // (11) 99999-9999
     * @example PhoneNumber::formatForDisplay('551133334444') // (11) 3333-4444
     */
    public static function formatForDisplay(string $phone): string
    {
        $digits = self::extractDigits($phone);

        if (str_starts_with($digits, self::COUNTRY_CODE)) {
            $digits = substr($digits, 2);
        }

        $ddd = substr($digits, 0, 2);

        if (strlen($digits) === 11) {
            // Celular: (XX) XXXXX-XXXX
            $part1 = substr($digits, 2, 5);
            $part2 = substr($digits, 7, 4);

            return "({$ddd}) {$part1}-{$part2}";
        }

        if (strlen($digits) === 10) {
            // Fixo: (XX) XXXX-XXXX
            $part1 = substr($digits, 2, 4);
            $part2 = substr($digits, 6, 4);

            return "({$ddd}) {$part1}-{$part2}";
        }

        return $phone;
    }
}
