<?php

declare(strict_types=1);

namespace Arara\Resources;

use Arara\DTOs\Template\TemplateData;
use Arara\Exceptions\ApiException;

final class Templates extends AbstractResource
{
    /**
     * Lista todos os templates disponíveis.
     *
     * @param array<string, mixed> $query
     * @return array<TemplateData>
     */
    public function list(array $query = []): array
    {
        $response = $this->client->get('/v1/templates', $query);

        if (! $response->isSuccessful()) {
            throw ApiException::fromResponse($response);
        }

        $data = $response->json('data') ?? $response->json();

        if (! is_array($data)) {
            return [];
        }

        return array_map(
            fn (array $template) => TemplateData::fromArray($template),
            $data,
        );
    }

    /**
     * Obtém um template específico pelo nome.
     */
    public function get(string $name): TemplateData
    {
        $response = $this->client->get("/v1/templates/{$name}");

        if (! $response->isSuccessful()) {
            throw ApiException::fromResponse($response);
        }

        $data = $response->json('data') ?? $response->json();

        /** @var array<string, mixed> $data */
        return TemplateData::fromArray($data);
    }
}
