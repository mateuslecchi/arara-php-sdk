<?php

declare(strict_types=1);

namespace Arara\Http;

use Arara\Config;
use Arara\Contracts\HttpClientInterface;
use Arara\Exceptions\ApiException;
use Arara\Exceptions\AuthenticationException;
use Arara\Exceptions\RateLimitException;
use Arara\Exceptions\ValidationException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;

final class GuzzleHttpClient implements HttpClientInterface
{
    private Client $client;

    public function __construct(
        private readonly Config $config,
    ) {
        $this->client = new Client([
            'base_uri' => $this->config->baseUrl,
            'timeout' => $this->config->timeout,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->config->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * @param array<string, mixed> $query
     */
    public function get(string $uri, array $query = []): Response
    {
        return $this->request('GET', $uri, ['query' => $query]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function post(string $uri, array $data = []): Response
    {
        return $this->request('POST', $uri, ['json' => $data]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function put(string $uri, array $data = []): Response
    {
        return $this->request('PUT', $uri, ['json' => $data]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function patch(string $uri, array $data = []): Response
    {
        return $this->request('PATCH', $uri, ['json' => $data]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function delete(string $uri, array $data = []): Response
    {
        return $this->request('DELETE', $uri, ['json' => $data]);
    }

    /**
     * @param array<string, mixed> $options
     */
    private function request(string $method, string $uri, array $options = []): Response
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $this->config->retryTimes) {
            try {
                $response = $this->client->request($method, $uri, $options);

                return Response::fromPsrResponse($response);
            } catch (ClientException $e) {
                $response = Response::fromPsrResponse($e->getResponse());
                $this->handleClientException($response);
            } catch (ServerException $e) {
                $lastException = $e;
                $attempt++;

                if ($attempt < $this->config->retryTimes) {
                    usleep($this->config->retryDelay * 1000);
                }
            } catch (GuzzleException $e) {
                $lastException = $e;
                $attempt++;

                if ($attempt < $this->config->retryTimes) {
                    usleep($this->config->retryDelay * 1000);
                }
            }
        }

        if ($lastException instanceof ServerException) {
            $response = Response::fromPsrResponse($lastException->getResponse());
            throw ApiException::fromResponse($response);
        }

        throw new ApiException(
            message: $lastException?->getMessage() ?? 'Erro desconhecido',
            statusCode: 0,
        );
    }

    private function handleClientException(Response $response): never
    {
        match ($response->status()) {
            401 => throw AuthenticationException::fromResponse($response),
            422 => throw ValidationException::fromResponse($response),
            429 => throw RateLimitException::fromResponse($response),
            default => throw ApiException::fromResponse($response),
        };
    }
}
