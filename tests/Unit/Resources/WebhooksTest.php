<?php

declare(strict_types=1);

namespace Arara\Tests\Unit\Resources;

use Arara\Contracts\HttpClientInterface;
use Arara\DTOs\Webhook\WebhookData;
use Arara\Exceptions\ApiException;
use Arara\Http\Response;
use Arara\Resources\Webhooks;
use Arara\Tests\TestCase;
use Mockery;
use Mockery\MockInterface;

final class WebhooksTest extends TestCase
{
    private HttpClientInterface&MockInterface $client;

    private Webhooks $webhooks;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Mockery::mock(HttpClientInterface::class);
        $this->webhooks = new Webhooks($this->client);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_can_list_webhooks(): void
    {
        $this->client
            ->shouldReceive('get')
            ->once()
            ->with('/v1/webhooks', [])
            ->andReturn(new Response(
                statusCode: 200,
                body: json_encode([
                    'data' => [
                        ['id' => '1', 'url' => 'https://example.com/webhook1', 'events' => ['message.delivered']],
                        ['id' => '2', 'url' => 'https://example.com/webhook2', 'events' => ['message.read']],
                    ],
                ]),
            ));

        $webhooks = $this->webhooks->list();

        $this->assertCount(2, $webhooks);
        $this->assertInstanceOf(WebhookData::class, $webhooks[0]);
        $this->assertSame('https://example.com/webhook1', $webhooks[0]->url);
    }

    public function test_can_create_webhook_with_array(): void
    {
        $this->client
            ->shouldReceive('post')
            ->once()
            ->with('/v1/webhooks', Mockery::type('array'))
            ->andReturn(new Response(
                statusCode: 201,
                body: json_encode([
                    'data' => [
                        'id' => '123',
                        'url' => 'https://example.com/webhook',
                        'events' => ['message.delivered', 'message.read'],
                        'status' => 'active',
                    ],
                ]),
            ));

        $webhook = $this->webhooks->create([
            'url' => 'https://example.com/webhook',
            'events' => ['message.delivered', 'message.read'],
        ]);

        $this->assertInstanceOf(WebhookData::class, $webhook);
        $this->assertSame('123', $webhook->id);
        $this->assertSame('https://example.com/webhook', $webhook->url);
    }

    public function test_can_create_webhook_with_dto(): void
    {
        $data = new WebhookData(
            url: 'https://example.com/webhook',
            events: ['message.delivered'],
        );

        $this->client
            ->shouldReceive('post')
            ->once()
            ->andReturn(new Response(
                statusCode: 201,
                body: json_encode([
                    'data' => [
                        'id' => '123',
                        'url' => 'https://example.com/webhook',
                        'events' => ['message.delivered'],
                    ],
                ]),
            ));

        $webhook = $this->webhooks->create($data);

        $this->assertInstanceOf(WebhookData::class, $webhook);
    }

    public function test_can_get_single_webhook(): void
    {
        $this->client
            ->shouldReceive('get')
            ->once()
            ->with('/v1/webhooks/123')
            ->andReturn(new Response(
                statusCode: 200,
                body: json_encode([
                    'data' => [
                        'id' => '123',
                        'url' => 'https://example.com/webhook',
                        'events' => ['message.delivered'],
                    ],
                ]),
            ));

        $webhook = $this->webhooks->get('123');

        $this->assertInstanceOf(WebhookData::class, $webhook);
        $this->assertSame('123', $webhook->id);
    }

    public function test_can_update_webhook(): void
    {
        $this->client
            ->shouldReceive('patch')
            ->once()
            ->with('/v1/webhooks/123', Mockery::type('array'))
            ->andReturn(new Response(
                statusCode: 200,
                body: json_encode([
                    'data' => [
                        'id' => '123',
                        'url' => 'https://example.com/new-webhook',
                        'events' => ['message.delivered', 'message.read'],
                    ],
                ]),
            ));

        $webhook = $this->webhooks->update('123', [
            'url' => 'https://example.com/new-webhook',
            'events' => ['message.delivered', 'message.read'],
        ]);

        $this->assertInstanceOf(WebhookData::class, $webhook);
        $this->assertSame('https://example.com/new-webhook', $webhook->url);
    }

    public function test_can_delete_webhook(): void
    {
        $this->client
            ->shouldReceive('delete')
            ->once()
            ->with('/v1/webhooks/123')
            ->andReturn(new Response(
                statusCode: 204,
                body: '',
            ));

        $result = $this->webhooks->delete('123');

        $this->assertTrue($result);
    }

    public function test_create_throws_exception_on_error(): void
    {
        $this->client
            ->shouldReceive('post')
            ->once()
            ->andReturn(new Response(
                statusCode: 422,
                body: json_encode([
                    'message' => 'Validation failed',
                    'errors' => ['url' => 'Invalid URL'],
                ]),
            ));

        $this->expectException(ApiException::class);

        $this->webhooks->create([
            'url' => 'invalid-url',
            'events' => ['message.delivered'],
        ]);
    }
}
