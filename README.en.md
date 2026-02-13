# Arara PHP SDK

PHP SDK for integrating with the [Arara](https://ararahq.com) API.

[![PHP Version](https://img.shields.io/badge/php-%5E8.4-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

> **[Leia em Português](README.md)**

## Current SDK scope

At the current state, the SDK exposes:

- `Arara\Config` for auth and transport settings.
- `Arara\Arara` as the main client.
- `Arara::sendMessage()` to send messages through `POST /messages`.
- Specific exceptions for HTTP error handling (400, 401, 404, 422, 500).

## Requirements

- PHP 8.4+
- JSON extension
- Composer

## Installation

```bash
composer require arara/arara-php-sdk
```

For complete API documentation, visit [docs.ararahq.com](https://docs.ararahq.com).

## Quick start

### Create config and client

```php
use Arara\Arara;
use Arara\Config;

$config = new Config(
    apiKey: 'your-api-key',
);

$arara = new Arara($config);
```

### Send message

```php
$response = $arara->sendMessage(
    receiver: 'whatsapp:+5511999999999',
    templateName: 'order_confirmation',
    variables: [
        'orderId' => '12345',
        'amount' => '$199.90',
    ],
);
```

`sendMessage()` returns an `array` with the API JSON response.

### Available configuration

```php
use Arara\Config;

$config = new Config(
    apiKey: 'your-api-key',
    baseUrl: 'https://api.ararahq.com/api/v1',
    timeout: 30,
    retryTimes: 3,
    retryDelayMs: 100,
);
```

`Config` parameters:

- `apiKey` (string): Bearer auth token.
- `baseUrl` (string): API base URL.
- `timeout` (int): request timeout in seconds.
- `retryTimes` (int): retry count (defined in config, not yet automatically applied by the client).
- `retryDelayMs` (int): retry delay in milliseconds (defined in config, not yet automatically applied by the client).

### Inject custom HTTP client (optional)

```php
use Arara\Arara;
use Arara\Config;
use GuzzleHttp\Client;

$config = new Config(apiKey: 'your-api-key');
$http = new Client();

$arara = new Arara($config, $http);
```

When a custom `Client` is injected, it is used directly.

## Error handling

The SDK throws specific exceptions for each HTTP error type:

| Exception | HTTP Status |
|-----------|------------|
| `BadRequestException` | 400 |
| `AuthenticationException` | 401 |
| `NotFoundException` | 404 |
| `ValidationException` | 422 |
| `InternalServerException` | 500 |
| `AraraException` | Others |

All extend `AraraException`, which exposes `statusCode`, `response` (decoded body), and `getMessage()`.

```php
use Arara\Exceptions\AraraException;
use Arara\Exceptions\AuthenticationException;
use Arara\Exceptions\ValidationException;

try {
    $arara->sendMessage('whatsapp:+5511999999999', 'welcome');
} catch (AuthenticationException $e) {
    // Invalid API key (401)
    echo $e->getMessage();
} catch (ValidationException $e) {
    // Invalid parameters (422)
    print_r($e->response); // API error details
} catch (AraraException $e) {
    // Any other error
    echo "Error {$e->statusCode}: {$e->getMessage()}";
}
```

## Used endpoint

- `POST /messages`

Payload sent by `sendMessage()`:

```json
{
  "receiver": "whatsapp:+5511999999999",
  "templateName": "order_confirmation",
  "variables": {}
}
```

## Development

```bash
composer install
composer test
composer analyse
composer format
composer check
```

## License

MIT. See [LICENSE](LICENSE).
