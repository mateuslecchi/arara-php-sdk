<?php

declare(strict_types=1);

namespace Arara\Tests\Unit\DTOs;

use Arara\DTOs\Message\SendMessageData;
use Arara\Tests\TestCase;

final class SendMessageDataTest extends TestCase
{
    public function test_can_create_instance(): void
    {
        $data = new SendMessageData(
            to: '5511999999999',
            templateName: 'order_confirmation',
            parameters: ['nome' => 'João'],
            language: 'pt_BR',
        );

        $this->assertSame('5511999999999', $data->to);
        $this->assertSame('order_confirmation', $data->templateName);
        $this->assertSame(['nome' => 'João'], $data->parameters);
        $this->assertSame('pt_BR', $data->language);
    }

    public function test_uses_default_values(): void
    {
        $data = new SendMessageData(
            to: '5511999999999',
            templateName: 'order_confirmation',
        );

        $this->assertSame([], $data->parameters);
        $this->assertSame('pt_BR', $data->language);
    }

    public function test_can_create_from_array(): void
    {
        $data = SendMessageData::fromArray([
            'to' => '5511999999999',
            'template_name' => 'order_confirmation',
            'parameters' => ['nome' => 'João'],
            'language' => 'en_US',
        ]);

        $this->assertSame('5511999999999', $data->to);
        $this->assertSame('order_confirmation', $data->templateName);
        $this->assertSame(['nome' => 'João'], $data->parameters);
        $this->assertSame('en_US', $data->language);
    }

    public function test_can_create_from_array_with_camel_case(): void
    {
        $data = SendMessageData::fromArray([
            'to' => '5511999999999',
            'templateName' => 'order_confirmation',
        ]);

        $this->assertSame('order_confirmation', $data->templateName);
    }

    public function test_can_convert_to_array(): void
    {
        $data = new SendMessageData(
            to: '5511999999999',
            templateName: 'order_confirmation',
            parameters: ['nome' => 'João'],
            language: 'pt_BR',
        );

        $array = $data->toArray();

        $this->assertSame([
            'to' => '5511999999999',
            'template_name' => 'order_confirmation',
            'parameters' => ['nome' => 'João'],
            'language' => 'pt_BR',
        ], $array);
    }
}
