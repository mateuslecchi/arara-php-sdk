<?php

declare(strict_types=1);

namespace Arara\Contracts;

use Arara\Http\Response;

interface HttpClientInterface
{
    /**
     * @param array<string, mixed> $query
     */
    public function get(string $uri, array $query = []): Response;

    /**
     * @param array<string, mixed> $data
     */
    public function post(string $uri, array $data = []): Response;

    /**
     * @param array<string, mixed> $data
     */
    public function put(string $uri, array $data = []): Response;

    /**
     * @param array<string, mixed> $data
     */
    public function patch(string $uri, array $data = []): Response;

    /**
     * @param array<string, mixed> $data
     */
    public function delete(string $uri, array $data = []): Response;
}
