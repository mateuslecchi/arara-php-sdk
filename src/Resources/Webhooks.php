<?php

declare(strict_types=1);

namespace Arara\Resources;

use Arara\DTOs\Webhook\WebhookData;
use Arara\Exceptions\ApiException;

final class Webhooks extends AbstractResource
{
    /**
     * Lista todos os webhooks configurados.
     *
     * @param array<string, mixed> $query
     * @return array<WebhookData>
     */
    public function list(array $query = []): array
    {
        $response = $this->client->get('/v1/webhooks', $query);

        if (! $response->isSuccessful()) {
            throw ApiException::fromResponse($response);
        }

        $data = $response->json('data') ?? $response->json();

        if (! is_array($data)) {
            return [];
        }

        return array_map(
            fn (array $webhook) => WebhookData::fromArray($webhook),
            $data,
        );
    }

    /**
     * Cria um novo webhook.
     *
     * @param WebhookData|array{
     *     url: string,
     *     events: array<string>,
     *     secret?: string|null
     * } $data
     */
    public function create(WebhookData|array $data): WebhookData
    {
        if (is_array($data)) {
            $data = WebhookData::fromArray($data);
        }

        $response = $this->client->post('/v1/webhooks', $data->toArray());

        if (! $response->isSuccessful()) {
            throw ApiException::fromResponse($response);
        }

        $responseData = $response->json('data') ?? $response->json();

        /** @var array<string, mixed> $responseData */
        return WebhookData::fromArray($responseData);
    }

    /**
     * Obtém um webhook específico pelo ID.
     */
    public function get(string $id): WebhookData
    {
        $response = $this->client->get("/v1/webhooks/{$id}");

        if (! $response->isSuccessful()) {
            throw ApiException::fromResponse($response);
        }

        $data = $response->json('data') ?? $response->json();

        /** @var array<string, mixed> $data */
        return WebhookData::fromArray($data);
    }

    /**
     * Atualiza um webhook existente.
     *
     * @param WebhookData|array{
     *     url?: string,
     *     events?: array<string>,
     *     secret?: string|null
     * } $data
     */
    public function update(string $id, WebhookData|array $data): WebhookData
    {
        if (is_array($data)) {
            $data = WebhookData::fromArray($data);
        }

        $response = $this->client->patch("/v1/webhooks/{$id}", $data->toArray());

        if (! $response->isSuccessful()) {
            throw ApiException::fromResponse($response);
        }

        $responseData = $response->json('data') ?? $response->json();

        /** @var array<string, mixed> $responseData */
        return WebhookData::fromArray($responseData);
    }

    /**
     * Remove um webhook.
     */
    public function delete(string $id): bool
    {
        $response = $this->client->delete("/v1/webhooks/{$id}");

        if (! $response->isSuccessful()) {
            throw ApiException::fromResponse($response);
        }

        return true;
    }
}
