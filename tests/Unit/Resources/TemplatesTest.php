<?php

declare(strict_types=1);

namespace Arara\Tests\Unit\Resources;

use Arara\Contracts\HttpClientInterface;
use Arara\DTOs\Template\TemplateData;
use Arara\Exceptions\ApiException;
use Arara\Http\Response;
use Arara\Resources\Templates;
use Arara\Tests\TestCase;
use Mockery;
use Mockery\MockInterface;

final class TemplatesTest extends TestCase
{
    private HttpClientInterface&MockInterface $client;

    private Templates $templates;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Mockery::mock(HttpClientInterface::class);
        $this->templates = new Templates($this->client);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_can_list_templates(): void
    {
        $this->client
            ->shouldReceive('get')
            ->once()
            ->with('/v1/templates', [])
            ->andReturn(new Response(
                statusCode: 200,
                body: json_encode([
                    'data' => [
                        ['id' => '1', 'name' => 'template_1', 'status' => 'approved'],
                        ['id' => '2', 'name' => 'template_2', 'status' => 'pending'],
                    ],
                ]),
            ));

        $templates = $this->templates->list();

        $this->assertCount(2, $templates);
        $this->assertInstanceOf(TemplateData::class, $templates[0]);
        $this->assertSame('template_1', $templates[0]->name);
        $this->assertSame('approved', $templates[0]->status);
    }

    public function test_list_returns_empty_array_when_no_templates(): void
    {
        $this->client
            ->shouldReceive('get')
            ->once()
            ->andReturn(new Response(
                statusCode: 200,
                body: json_encode(['data' => []]),
            ));

        $templates = $this->templates->list();

        $this->assertEmpty($templates);
    }

    public function test_can_get_single_template(): void
    {
        $this->client
            ->shouldReceive('get')
            ->once()
            ->with('/v1/templates/order_confirmation')
            ->andReturn(new Response(
                statusCode: 200,
                body: json_encode([
                    'data' => [
                        'id' => '1',
                        'name' => 'order_confirmation',
                        'status' => 'approved',
                        'category' => 'marketing',
                    ],
                ]),
            ));

        $template = $this->templates->get('order_confirmation');

        $this->assertInstanceOf(TemplateData::class, $template);
        $this->assertSame('order_confirmation', $template->name);
        $this->assertSame('approved', $template->status);
    }

    public function test_list_throws_exception_on_error(): void
    {
        $this->client
            ->shouldReceive('get')
            ->once()
            ->andReturn(new Response(
                statusCode: 500,
                body: json_encode(['message' => 'Internal server error']),
            ));

        $this->expectException(ApiException::class);

        $this->templates->list();
    }
}
