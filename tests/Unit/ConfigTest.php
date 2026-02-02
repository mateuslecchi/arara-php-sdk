<?php

declare(strict_types=1);

namespace Arara\Tests\Unit;

use Arara\Config;
use Arara\Tests\TestCase;

final class ConfigTest extends TestCase
{
    public function test_can_create_config_with_defaults(): void
    {
        $config = new Config(apiKey: 'test-api-key');

        $this->assertSame('test-api-key', $config->apiKey);
        $this->assertSame(Config::DEFAULT_BASE_URL, $config->baseUrl);
        $this->assertSame(Config::DEFAULT_TIMEOUT, $config->timeout);
        $this->assertSame(Config::DEFAULT_RETRY_TIMES, $config->retryTimes);
        $this->assertSame(Config::DEFAULT_RETRY_DELAY, $config->retryDelay);
    }

    public function test_can_create_config_with_custom_values(): void
    {
        $config = new Config(
            apiKey: 'test-api-key',
            baseUrl: 'https://custom.api.com',
            timeout: 60,
            retryTimes: 5,
            retryDelay: 200,
        );

        $this->assertSame('test-api-key', $config->apiKey);
        $this->assertSame('https://custom.api.com', $config->baseUrl);
        $this->assertSame(60, $config->timeout);
        $this->assertSame(5, $config->retryTimes);
        $this->assertSame(200, $config->retryDelay);
    }

    public function test_can_create_config_from_array(): void
    {
        $config = Config::fromArray('test-api-key', [
            'base_url' => 'https://custom.api.com',
            'timeout' => 60,
            'retry' => [
                'times' => 5,
                'delay' => 200,
            ],
        ]);

        $this->assertSame('test-api-key', $config->apiKey);
        $this->assertSame('https://custom.api.com', $config->baseUrl);
        $this->assertSame(60, $config->timeout);
        $this->assertSame(5, $config->retryTimes);
        $this->assertSame(200, $config->retryDelay);
    }

    public function test_from_array_uses_defaults_for_missing_options(): void
    {
        $config = Config::fromArray('test-api-key', []);

        $this->assertSame(Config::DEFAULT_BASE_URL, $config->baseUrl);
        $this->assertSame(Config::DEFAULT_TIMEOUT, $config->timeout);
        $this->assertSame(Config::DEFAULT_RETRY_TIMES, $config->retryTimes);
        $this->assertSame(Config::DEFAULT_RETRY_DELAY, $config->retryDelay);
    }
}
