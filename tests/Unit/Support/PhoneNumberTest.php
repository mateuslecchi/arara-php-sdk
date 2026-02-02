<?php

declare(strict_types=1);

namespace Arara\Tests\Unit\Support;

use Arara\Support\PhoneNumber;
use Arara\Tests\TestCase;

final class PhoneNumberTest extends TestCase
{
    public function test_format_with_digits_only(): void
    {
        $this->assertSame('+5511999999999', PhoneNumber::format('11999999999'));
    }

    public function test_format_with_formatted_number(): void
    {
        $this->assertSame('+5511999999999', PhoneNumber::format('(11) 99999-9999'));
    }

    public function test_format_with_country_code(): void
    {
        $this->assertSame('+5511999999999', PhoneNumber::format('+5511999999999'));
    }

    public function test_format_with_country_code_without_plus(): void
    {
        $this->assertSame('+5511999999999', PhoneNumber::format('5511999999999'));
    }

    public function test_is_valid_with_valid_mobile(): void
    {
        $this->assertTrue(PhoneNumber::isValid('11999999999'));
    }

    public function test_is_valid_with_valid_landline(): void
    {
        $this->assertTrue(PhoneNumber::isValid('1133334444'));
    }

    public function test_is_valid_with_country_code(): void
    {
        $this->assertTrue(PhoneNumber::isValid('5511999999999'));
    }

    public function test_is_valid_with_invalid_short_number(): void
    {
        $this->assertFalse(PhoneNumber::isValid('1199999999'));
    }

    public function test_is_valid_with_invalid_ddd(): void
    {
        $this->assertFalse(PhoneNumber::isValid('00999999999'));
    }

    public function test_is_mobile_returns_true_for_mobile(): void
    {
        $this->assertTrue(PhoneNumber::isMobile('11999999999'));
    }

    public function test_is_mobile_returns_false_for_landline(): void
    {
        $this->assertFalse(PhoneNumber::isMobile('1133334444'));
    }

    public function test_is_mobile_with_country_code(): void
    {
        $this->assertTrue(PhoneNumber::isMobile('5511999999999'));
    }

    public function test_is_landline_returns_true_for_landline(): void
    {
        $this->assertTrue(PhoneNumber::isLandline('1133334444'));
    }

    public function test_is_landline_returns_false_for_mobile(): void
    {
        $this->assertFalse(PhoneNumber::isLandline('11999999999'));
    }

    public function test_is_landline_with_country_code(): void
    {
        $this->assertTrue(PhoneNumber::isLandline('551133334444'));
    }

    public function test_extract_digits(): void
    {
        $this->assertSame('5511999999999', PhoneNumber::extractDigits('+55 (11) 99999-9999'));
    }

    public function test_format_for_display_mobile(): void
    {
        $this->assertSame('(11) 99999-9999', PhoneNumber::formatForDisplay('5511999999999'));
    }

    public function test_format_for_display_landline(): void
    {
        $this->assertSame('(11) 3333-4444', PhoneNumber::formatForDisplay('551133334444'));
    }

    public function test_format_for_display_without_country_code(): void
    {
        $this->assertSame('(11) 99999-9999', PhoneNumber::formatForDisplay('11999999999'));
    }
}
