<?php

declare(strict_types=1);

namespace Arara\DTOs\Message;

final readonly class MessageResponse
{
    public function __construct(
        public ?string $messageId,
        public string $status,
        public ?string $to = null,
        public ?string $timestamp = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            messageId: $data['message_id'] ?? $data['messageId'] ?? $data['id'] ?? null,
            status: $data['status'] ?? 'unknown',
            to: $data['to'] ?? null,
            timestamp: $data['timestamp'] ?? $data['created_at'] ?? null,
        );
    }

    public function isSuccessful(): bool
    {
        return in_array($this->status, ['sent', 'delivered', 'queued', 'accepted'], true);
    }

    public function messageId(): ?string
    {
        return $this->messageId;
    }

    /**
     * @return array{
     *     message_id: string|null,
     *     status: string,
     *     to: string|null,
     *     timestamp: string|null
     * }
     */
    public function toArray(): array
    {
        return [
            'message_id' => $this->messageId,
            'status' => $this->status,
            'to' => $this->to,
            'timestamp' => $this->timestamp,
        ];
    }
}
