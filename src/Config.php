<?php

declare(strict_types=1);

namespace Arara;

final readonly class Config
{
    public const DEFAULT_BASE_URL = 'https://api.ararahq.com';
    public const DEFAULT_TIMEOUT = 30;
    public const DEFAULT_RETRY_TIMES = 3;
    public const DEFAULT_RETRY_DELAY = 100;

    public function __construct(
        public string $apiKey,
        public string $baseUrl = self::DEFAULT_BASE_URL,
        public int $timeout = self::DEFAULT_TIMEOUT,
        public int $retryTimes = self::DEFAULT_RETRY_TIMES,
        public int $retryDelay = self::DEFAULT_RETRY_DELAY,
    ) {
    }

    /**
     * @param array{
     *     base_url?: string,
     *     timeout?: int,
     *     retry?: array{times?: int, delay?: int}
     * } $options
     */
    public static function fromArray(string $apiKey, array $options = []): self
    {
        return new self(
            apiKey: $apiKey,
            baseUrl: $options['base_url'] ?? self::DEFAULT_BASE_URL,
            timeout: $options['timeout'] ?? self::DEFAULT_TIMEOUT,
            retryTimes: $options['retry']['times'] ?? self::DEFAULT_RETRY_TIMES,
            retryDelay: $options['retry']['delay'] ?? self::DEFAULT_RETRY_DELAY,
        );
    }
}
