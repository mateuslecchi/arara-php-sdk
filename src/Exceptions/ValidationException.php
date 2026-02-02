<?php

declare(strict_types=1);

namespace Arara\Exceptions;

use Arara\Http\Response;

class ValidationException extends AraraException
{
    /**
     * @param array<string, mixed>|null $errors
     */
    public function __construct(
        string $message = 'Dados inválidos',
        public readonly int $statusCode = 422,
        public readonly ?array $errors = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public static function fromResponse(Response $response): self
    {
        $body = $response->json();

        return new self(
            message: $body['message'] ?? 'Dados inválidos',
            statusCode: $response->status(),
            errors: $body['errors'] ?? null,
        );
    }
}
