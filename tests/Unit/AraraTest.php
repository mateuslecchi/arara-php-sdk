<?php

declare(strict_types=1);

namespace Arara\Tests\Unit;

use Arara\Arara;
use Arara\AraraClient;
use Arara\Resources\Messages;
use Arara\Resources\Templates;
use Arara\Resources\Webhooks;
use Arara\Tests\TestCase;

final class AraraTest extends TestCase
{
    public function test_can_create_instance(): void
    {
        $arara = new Arara('test-api-key');

        $this->assertInstanceOf(Arara::class, $arara);
    }

    public function test_can_create_instance_with_options(): void
    {
        $arara = new Arara('test-api-key', [
            'base_url' => 'https://custom.api.com',
            'timeout' => 60,
        ]);

        $this->assertInstanceOf(Arara::class, $arara);
    }

    public function test_can_get_messages_resource(): void
    {
        $arara = new Arara('test-api-key');

        $messages = $arara->messages();

        $this->assertInstanceOf(Messages::class, $messages);
    }

    public function test_messages_resource_is_cached(): void
    {
        $arara = new Arara('test-api-key');

        $messages1 = $arara->messages();
        $messages2 = $arara->messages();

        $this->assertSame($messages1, $messages2);
    }

    public function test_can_get_templates_resource(): void
    {
        $arara = new Arara('test-api-key');

        $templates = $arara->templates();

        $this->assertInstanceOf(Templates::class, $templates);
    }

    public function test_templates_resource_is_cached(): void
    {
        $arara = new Arara('test-api-key');

        $templates1 = $arara->templates();
        $templates2 = $arara->templates();

        $this->assertSame($templates1, $templates2);
    }

    public function test_can_get_webhooks_resource(): void
    {
        $arara = new Arara('test-api-key');

        $webhooks = $arara->webhooks();

        $this->assertInstanceOf(Webhooks::class, $webhooks);
    }

    public function test_webhooks_resource_is_cached(): void
    {
        $arara = new Arara('test-api-key');

        $webhooks1 = $arara->webhooks();
        $webhooks2 = $arara->webhooks();

        $this->assertSame($webhooks1, $webhooks2);
    }

    public function test_can_get_client(): void
    {
        $arara = new Arara('test-api-key');

        $client = $arara->getClient();

        $this->assertInstanceOf(AraraClient::class, $client);
    }
}
