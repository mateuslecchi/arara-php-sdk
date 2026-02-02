<?php

declare(strict_types=1);

namespace Arara\DTOs\Webhook;

final readonly class WebhookData
{
    /**
     * @param array<string> $events
     */
    public function __construct(
        public ?string $id = null,
        public ?string $url = null,
        public array $events = [],
        public ?string $secret = null,
        public ?string $status = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
    ) {
    }

    /**
     * @param array{
     *     id?: string|null,
     *     url?: string|null,
     *     events?: array<string>,
     *     secret?: string|null,
     *     status?: string|null,
     *     created_at?: string|null,
     *     createdAt?: string|null,
     *     updated_at?: string|null,
     *     updatedAt?: string|null
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            url: $data['url'] ?? null,
            events: $data['events'] ?? [],
            secret: $data['secret'] ?? null,
            status: $data['status'] ?? null,
            createdAt: $data['created_at'] ?? $data['createdAt'] ?? null,
            updatedAt: $data['updated_at'] ?? $data['updatedAt'] ?? null,
        );
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * @return array{
     *     id?: string|null,
     *     url: string|null,
     *     events: array<string>,
     *     secret?: string|null,
     *     status?: string|null,
     *     created_at?: string|null,
     *     updated_at?: string|null
     * }
     */
    public function toArray(): array
    {
        $data = [
            'url' => $this->url,
            'events' => $this->events,
        ];

        if ($this->id !== null) {
            $data['id'] = $this->id;
        }

        if ($this->secret !== null) {
            $data['secret'] = $this->secret;
        }

        if ($this->status !== null) {
            $data['status'] = $this->status;
        }

        if ($this->createdAt !== null) {
            $data['created_at'] = $this->createdAt;
        }

        if ($this->updatedAt !== null) {
            $data['updated_at'] = $this->updatedAt;
        }

        return $data;
    }
}
