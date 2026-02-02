<?php

declare(strict_types=1);

namespace Arara\Tests\Unit\Resources;

use Arara\Contracts\HttpClientInterface;
use Arara\DTOs\Message\MessageResponse;
use Arara\DTOs\Message\SendMessageData;
use Arara\Exceptions\ApiException;
use Arara\Http\Response;
use Arara\Resources\Messages;
use Arara\Tests\TestCase;
use Mockery;
use Mockery\MockInterface;

final class MessagesTest extends TestCase
{
    private HttpClientInterface&MockInterface $client;

    private Messages $messages;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Mockery::mock(HttpClientInterface::class);
        $this->messages = new Messages($this->client);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_can_send_message_with_dto(): void
    {
        $data = new SendMessageData(
            to: '5511999999999',
            templateName: 'order_confirmation',
            parameters: ['nome' => 'João'],
        );

        $this->client
            ->shouldReceive('post')
            ->once()
            ->with('/v1/messages/send', $data->toArray())
            ->andReturn(new Response(
                statusCode: 200,
                body: json_encode([
                    'message_id' => 'msg-123',
                    'status' => 'sent',
                ]),
            ));

        $response = $this->messages->send($data);

        $this->assertInstanceOf(MessageResponse::class, $response);
        $this->assertSame('msg-123', $response->messageId);
        $this->assertSame('sent', $response->status);
    }

    public function test_can_send_message_with_array(): void
    {
        $this->client
            ->shouldReceive('post')
            ->once()
            ->with('/v1/messages/send', Mockery::type('array'))
            ->andReturn(new Response(
                statusCode: 200,
                body: json_encode([
                    'message_id' => 'msg-123',
                    'status' => 'sent',
                ]),
            ));

        $response = $this->messages->send([
            'to' => '5511999999999',
            'template_name' => 'order_confirmation',
            'parameters' => ['nome' => 'João'],
        ]);

        $this->assertInstanceOf(MessageResponse::class, $response);
    }

    public function test_throws_exception_on_error(): void
    {
        $this->client
            ->shouldReceive('post')
            ->once()
            ->andReturn(new Response(
                statusCode: 400,
                body: json_encode([
                    'message' => 'Invalid template',
                    'errors' => ['template_name' => 'Template not found'],
                ]),
            ));

        $this->expectException(ApiException::class);

        $this->messages->send([
            'to' => '5511999999999',
            'template_name' => 'invalid_template',
        ]);
    }
}
