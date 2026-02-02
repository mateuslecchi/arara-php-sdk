<?php

declare(strict_types=1);

namespace Arara\Contracts;

interface ResourceInterface
{
    public function getClient(): HttpClientInterface;
}
