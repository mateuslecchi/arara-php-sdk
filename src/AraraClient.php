<?php

declare(strict_types=1);

namespace Arara;

use Arara\Contracts\HttpClientInterface;
use Arara\Http\GuzzleHttpClient;
use Arara\Http\Response;

final class AraraClient implements HttpClientInterface
{
    private HttpClientInterface $httpClient;

    public function __construct(
        private readonly Config $config,
        ?HttpClientInterface $httpClient = null,
    ) {
        $this->httpClient = $httpClient ?? new GuzzleHttpClient($this->config);
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @param array<string, mixed> $query
     */
    public function get(string $uri, array $query = []): Response
    {
        return $this->httpClient->get($uri, $query);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function post(string $uri, array $data = []): Response
    {
        return $this->httpClient->post($uri, $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function put(string $uri, array $data = []): Response
    {
        return $this->httpClient->put($uri, $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function patch(string $uri, array $data = []): Response
    {
        return $this->httpClient->patch($uri, $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function delete(string $uri, array $data = []): Response
    {
        return $this->httpClient->delete($uri, $data);
    }
}
