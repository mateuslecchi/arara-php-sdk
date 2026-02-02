<?php

declare(strict_types=1);

namespace Arara\Tests\Unit\DTOs;

use Arara\DTOs\Message\MessageResponse;
use Arara\Tests\TestCase;

final class MessageResponseTest extends TestCase
{
    public function test_can_create_instance(): void
    {
        $response = new MessageResponse(
            messageId: 'msg-123',
            status: 'sent',
            to: '5511999999999',
            timestamp: '2024-01-01T00:00:00Z',
        );

        $this->assertSame('msg-123', $response->messageId);
        $this->assertSame('sent', $response->status);
        $this->assertSame('5511999999999', $response->to);
        $this->assertSame('2024-01-01T00:00:00Z', $response->timestamp);
    }

    public function test_can_create_from_array(): void
    {
        $response = MessageResponse::fromArray([
            'message_id' => 'msg-123',
            'status' => 'sent',
            'to' => '5511999999999',
            'timestamp' => '2024-01-01T00:00:00Z',
        ]);

        $this->assertSame('msg-123', $response->messageId);
        $this->assertSame('sent', $response->status);
    }

    public function test_can_create_from_array_with_alternative_keys(): void
    {
        $response = MessageResponse::fromArray([
            'id' => 'msg-123',
            'status' => 'sent',
            'created_at' => '2024-01-01T00:00:00Z',
        ]);

        $this->assertSame('msg-123', $response->messageId);
        $this->assertSame('2024-01-01T00:00:00Z', $response->timestamp);
    }

    public function test_is_successful_returns_true_for_sent_status(): void
    {
        $response = new MessageResponse(messageId: 'msg-123', status: 'sent');

        $this->assertTrue($response->isSuccessful());
    }

    public function test_is_successful_returns_true_for_delivered_status(): void
    {
        $response = new MessageResponse(messageId: 'msg-123', status: 'delivered');

        $this->assertTrue($response->isSuccessful());
    }

    public function test_is_successful_returns_true_for_queued_status(): void
    {
        $response = new MessageResponse(messageId: 'msg-123', status: 'queued');

        $this->assertTrue($response->isSuccessful());
    }

    public function test_is_successful_returns_false_for_failed_status(): void
    {
        $response = new MessageResponse(messageId: 'msg-123', status: 'failed');

        $this->assertFalse($response->isSuccessful());
    }

    public function test_message_id_method(): void
    {
        $response = new MessageResponse(messageId: 'msg-123', status: 'sent');

        $this->assertSame('msg-123', $response->messageId());
    }

    public function test_can_convert_to_array(): void
    {
        $response = new MessageResponse(
            messageId: 'msg-123',
            status: 'sent',
            to: '5511999999999',
            timestamp: '2024-01-01T00:00:00Z',
        );

        $array = $response->toArray();

        $this->assertSame([
            'message_id' => 'msg-123',
            'status' => 'sent',
            'to' => '5511999999999',
            'timestamp' => '2024-01-01T00:00:00Z',
        ], $array);
    }
}
