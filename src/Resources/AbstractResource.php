<?php

declare(strict_types=1);

namespace Arara\Resources;

use Arara\Contracts\HttpClientInterface;
use Arara\Contracts\ResourceInterface;

abstract class AbstractResource implements ResourceInterface
{
    public function __construct(
        protected readonly HttpClientInterface $client,
    ) {
    }

    public function getClient(): HttpClientInterface
    {
        return $this->client;
    }
}
