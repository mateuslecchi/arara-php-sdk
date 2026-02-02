<?php

declare(strict_types=1);

namespace Arara\Exceptions;

use Arara\Http\Response;

class RateLimitException extends AraraException
{
    public function __construct(
        string $message = 'Rate limit excedido',
        public readonly int $statusCode = 429,
        public readonly ?int $retryAfter = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public static function fromResponse(Response $response): self
    {
        $body = $response->json();
        $retryAfter = $response->header('Retry-After');

        return new self(
            message: $body['message'] ?? 'Rate limit excedido',
            statusCode: $response->status(),
            retryAfter: $retryAfter !== null ? (int) $retryAfter : null,
        );
    }
}
