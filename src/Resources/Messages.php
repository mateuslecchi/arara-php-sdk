<?php

declare(strict_types=1);

namespace Arara\Resources;

use Arara\DTOs\Message\MessageResponse;
use Arara\DTOs\Message\SendMessageData;
use Arara\Exceptions\ApiException;

final class Messages extends AbstractResource
{
    /**
     * Envia uma mensagem de template via WhatsApp.
     *
     * @param SendMessageData|array{
     *     to: string,
     *     template_name?: string,
     *     templateName?: string,
     *     parameters?: array<string, mixed>,
     *     language?: string
     * } $data
     */
    public function send(SendMessageData|array $data): MessageResponse
    {
        if (is_array($data)) {
            $data = SendMessageData::fromArray($data);
        }

        $response = $this->client->post('/v1/messages/send', $data->toArray());

        if (! $response->isSuccessful()) {
            throw ApiException::fromResponse($response);
        }

        /** @var array<string, mixed> $json */
        $json = $response->json();

        return MessageResponse::fromArray($json);
    }
}
