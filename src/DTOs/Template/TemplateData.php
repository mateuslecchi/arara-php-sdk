<?php

declare(strict_types=1);

namespace Arara\DTOs\Template;

final readonly class TemplateData
{
    /**
     * @param array<string> $components
     * @param array<string> $languages
     */
    public function __construct(
        public ?string $id,
        public string $name,
        public string $status,
        public ?string $category = null,
        public ?string $language = null,
        public array $components = [],
        public array $languages = [],
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'],
            status: $data['status'],
            category: $data['category'] ?? null,
            language: $data['language'] ?? null,
            components: $data['components'] ?? [],
            languages: $data['languages'] ?? [],
            createdAt: $data['created_at'] ?? $data['createdAt'] ?? null,
            updatedAt: $data['updated_at'] ?? $data['updatedAt'] ?? null,
        );
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * @return array{
     *     id: string|null,
     *     name: string,
     *     status: string,
     *     category: string|null,
     *     language: string|null,
     *     components: array<string>,
     *     languages: array<string>,
     *     created_at: string|null,
     *     updated_at: string|null
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'category' => $this->category,
            'language' => $this->language,
            'components' => $this->components,
            'languages' => $this->languages,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
