<?php

declare(strict_types=1);

namespace Arara\Http;

use Psr\Http\Message\ResponseInterface;

final readonly class Response
{
    /** @var array<string, mixed> */
    private array $decodedBody;

    /**
     * @param array<string, string|null> $headers
     */
    public function __construct(
        private int $statusCode,
        private string $body,
        private array $headers = [],
    ) {
        $decoded = json_decode($this->body, true);
        $this->decodedBody = is_array($decoded) ? $decoded : [];
    }

    public static function fromPsrResponse(ResponseInterface $response): self
    {
        $headers = [];
        foreach ($response->getHeaders() as $name => $values) {
            $headers[strtolower($name)] = $values[0] ?? null;
        }

        return new self(
            statusCode: $response->getStatusCode(),
            body: $response->getBody()->getContents(),
            headers: $headers,
        );
    }

    public function status(): int
    {
        return $this->statusCode;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function json(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->decodedBody;
        }

        return $this->decodedBody[$key] ?? $default;
    }

    public function header(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }

    /**
     * @return array<string, string|null>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    public function isServerError(): bool
    {
        return $this->statusCode >= 500;
    }
}
