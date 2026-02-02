<?php

declare(strict_types=1);

namespace Arara\Exceptions;

use Arara\Http\Response;

class ApiException extends AraraException
{
    /**
     * @param array<string, mixed>|null $errors
     */
    public function __construct(
        string $message,
        public readonly int $statusCode,
        public readonly ?array $errors = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        return new self(
            message: $body['message'] ?? 'Erro na API',
            statusCode: $response->status(),
            errors: $body['errors'] ?? null,
        );
    }
}
