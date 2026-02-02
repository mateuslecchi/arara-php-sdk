<?php

declare(strict_types=1);

namespace Arara\DTOs\Message;

final readonly class SendMessageData
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        public string $to,
        public string $templateName,
        public array $parameters = [],
        public string $language = 'pt_BR',
    ) {
    }

    /**
     * @param array{
     *     to: string,
     *     template_name?: string,
     *     templateName?: string,
     *     parameters?: array<string, mixed>,
     *     language?: string
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            to: $data['to'],
            templateName: $data['template_name'] ?? $data['templateName'] ?? '',
            parameters: $data['parameters'] ?? [],
            language: $data['language'] ?? 'pt_BR',
        );
    }

    /**
     * @return array{
     *     to: string,
     *     template_name: string,
     *     parameters: array<string, mixed>,
     *     language: string
     * }
     */
    public function toArray(): array
    {
        return [
            'to' => $this->to,
            'template_name' => $this->templateName,
            'parameters' => $this->parameters,
            'language' => $this->language,
        ];
    }
}
