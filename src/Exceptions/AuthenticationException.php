<?php

declare(strict_types=1);

namespace Arara\Exceptions;

use Arara\Http\Response;

class AuthenticationException extends AraraException
{
    public function __construct(
        string $message = 'API Key inválida ou ausente',
        public readonly int $statusCode = 401,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        return new self(
            message: $body['message'] ?? 'API Key inválida ou ausente',
            statusCode: $response->status(),
        );
    }
}
