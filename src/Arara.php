<?php

declare(strict_types=1);

namespace Arara;

use Arara\Contracts\HttpClientInterface;
use Arara\Resources\Messages;
use Arara\Resources\Templates;
use Arara\Resources\Webhooks;

final class Arara
{
    private AraraClient $client;

    private ?Messages $messages = null;

    private ?Templates $templates = null;

    private ?Webhooks $webhooks = null;

    /**
     * @param array{
     *     base_url?: string,
     *     timeout?: int,
     *     retry?: array{times?: int, delay?: int}
     * } $options
     */
    public function __construct(
        string $apiKey,
        array $options = [],
        ?HttpClientInterface $httpClient = null,
    ) {
        $config = Config::fromArray($apiKey, $options);
        $this->client = new AraraClient($config, $httpClient);
    }

    public function messages(): Messages
    {
        return $this->messages ??= new Messages($this->client);
    }

    public function templates(): Templates
    {
        return $this->templates ??= new Templates($this->client);
    }

    public function webhooks(): Webhooks
    {
        return $this->webhooks ??= new Webhooks($this->client);
    }

    public function getClient(): AraraClient
    {
        return $this->client;
    }
}
